<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/
class ACL{

	var $dom;
	var $agent;
	
	public function ACL($dom){
		$this->dom = $dom;
	}
	
	public function BuildACL(){
		$dom = $this->dom;
		
		$headerNode   		= $dom->getElementsByTagName('SIF_Header')->item(0);
		$originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
		$originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;

		if ($headerNode->getElementsByTagName('SIF_Timestamp')->item(0)){
			$originalTimestamp   = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;
		} else {
			$originalTimestamp   = Utility::createTimestamp();
		}
		
		$validSourceId = Agent::checkSourceId($originalSourceId);
		if(!$validSourceId){
			ProvisionError::invalidSourceId($originalSourceId, $originalMsgId);
			exit;
		}
		else{
			$this->agent = new Agent($originalSourceId);
			if(!$this->agent->isRegistered()){
				RegisterError::notRegisteredError($originalSourceId, $originalMsgId);
			}
			else{
				$root = new DOMDocument('1.0', 'iso-8859-1');
				
				$acl = $root->createElement('SIF_AgentACL');
				
				
				$provideAccessNode = $root->createElement('SIF_ProvideAccess');
				$provideAccessNode = $this->buildProvideAccess($provideAccessNode, $root);
				$acl->appendChild($provideAccessNode);
				
				$subscribeAccessNode = $root->createElement('SIF_SubscribeAccess');
				$subscribeAccessNode = $this->buildSubscribeAccess($subscribeAccessNode, $root);
				$acl->appendChild($subscribeAccessNode);
				
				$publishAddAccessNode = $root->createElement('SIF_PublishAddAccess');
				$publishAddAccessNode = $this->buildPublishAddAccess($publishAddAccessNode, $root);
				$acl->appendChild($publishAddAccessNode);
				
				$publishChangeAccessNode = $root->createElement('SIF_PublishChangeAccess');
				$publishChangeAccessNode = $this->buildPublishChangeAccess($publishChangeAccessNode, $root);
				$acl->appendChild($publishChangeAccessNode);
				
				$publishDeleteAccessNode = $root->createElement('SIF_PublishDeleteAccess');
				$publishDeleteAccessNode = $this->buildPublishDeleteAccess($publishDeleteAccessNode, $root);
				$acl->appendChild($publishDeleteAccessNode);
				
				$requestAccessNode = $root->createElement('SIF_RequestAccess');
				$requestAccessNode = $this->buildRequestAccess($requestAccessNode, $root);
				$acl->appendChild($requestAccessNode);
				
				$respondAccessNode = $root->createElement('SIF_RespondAccess');
				$respondAccessNode = $this->buildRespondAccess($respondAccessNode, $root);
				$acl->appendChild($respondAccessNode);
				
				$root->appendChild($acl);
				$xml = $root->saveXML($root->documentElement);
				
				return $xml;
			}
		}
	}
	
	private function buildRespondAccess($node, $root){
		$db = Zend_Registry::get('my_db');
		
		$query = 'select
				  o.object_name,
				  c.context_desc
				  from  '.DBConvertor::convertCase('data_object').' o,
				  		'.DBConvertor::convertCase('context').' c,
				  		'.DBConvertor::convertCase('agent_permissions').' a
				  where a.agent_id = '.$this->agent->agentId.' and
				  		a.zone_id = '.$_SESSION['ZONE_ID'].' and
				  		a.context_id = '.$_SESSION['CONTEXT_ID'].'
				 	and a.can_respond = 1
				  	and a.object_id = o.object_id
				  	and a.context_id = c.context_id order by o.object_id';
			
		$result = $db->fetchAll($query);
		$currentObjectName = '';
		$oldNode = null;
		foreach($result as $row){
			
			switch(DB_TYPE) {
				case 'mysql':
					$objectName  = $row->object_name;
					$contextDesc = $row->context_desc;
				break;
				case 'oci8':
					$objectName  = $row->OBJECT_NAME;
					$contextDesc = $row->CONTEXT_DESC;
				break;
			}
			
			if($objectName != $currentObjectName){
				$sifObjectNode = $root->createElement('SIF_Object');
				$sifObjectNode->setAttribute('ObjectName',$objectName);
				$sifContextsNode = $root->createElement('SIF_Contexts');
			}
			else{
				$sifContextsNode = $oldNode;
			}
			
			$sifContextNode = $root->createElement('SIF_Context');
			$text = $root->createTextNode($contextDesc);
			$sifContextNode->appendChild($text);
			$sifContextsNode->appendChild($sifContextNode);
			
			if($objectName != $currentObjectName){
				$sifObjectNode->appendChild($sifContextsNode);
				$node->appendChild($sifObjectNode);
			}
			
			$currentObjectName = $objectName;
			$oldNode = $sifContextsNode;
		}
		return $node;
	}
	
	private function buildRequestAccess($node, $root){
		$db = Zend_Registry::get('my_db');
		
		$query = 'select
				  o.object_name,
				  c.context_desc
				  from
				  '.DBConvertor::convertCase('data_object').' o,
				  '.DBConvertor::convertCase('context').' c,
				  '.DBConvertor::convertCase('agent_permissions').' a
				  where
				  a.agent_id = '.$this->agent->agentId.' and
				  a.zone_id = '.$_SESSION['ZONE_ID'].'   and
				  a.context_id = '.$_SESSION['CONTEXT_ID'].'
				  and a.can_request = 1
				  and a.object_id = o.object_id
				  and a.context_id = c.context_id order by o.object_id';
			
		$result = $db->fetchAll($query);
		$currentObjectName = '';
		$oldNode = null;
		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
					$objectName  = $row->object_name;
					$contextDesc = $row->context_desc;
				break;
				case 'oci8':
					$objectName  = $row->OBJECT_NAME;
					$contextDesc = $row->CONTEXT_DESC;
				break;
			}
			
			if($objectName != $currentObjectName){
				$sifObjectNode = $root->createElement('SIF_Object');
				$sifObjectNode->setAttribute('ObjectName',$objectName);
				$sifContextsNode = $root->createElement('SIF_Contexts');
			}
			else{
				$sifContextsNode = $oldNode;
			}
			
			$sifContextNode = $root->createElement('SIF_Context');
			$text = $root->createTextNode($contextDesc);
			$sifContextNode->appendChild($text);
			$sifContextsNode->appendChild($sifContextNode);
			
			if($objectName != $currentObjectName){
				$sifObjectNode->appendChild($sifContextsNode);
				$node->appendChild($sifObjectNode);
			}
			
			$currentObjectName = $objectName;
			$oldNode = $sifContextsNode;
		}
		return $node;
	}
	
	private function buildPublishDeleteAccess($node, $root){
		$db = Zend_Registry::get('my_db');
		
		$query = 'select
				  o.object_name,
				  c.context_desc
				  from
				  '.DBConvertor::convertCase('data_object').' o,
				  '.DBConvertor::convertCase('context').' c,
				  '.DBConvertor::convertCase('agent_permissions').' a
				  where
				  a.agent_id = '.$this->agent->agentId.'
				  and
				  a.zone_id = '.$_SESSION['ZONE_ID'].'
				  and
				  a.context_id = '.$_SESSION['CONTEXT_ID'].'
				  and a.can_delete = 1
				  and a.object_id = o.object_id
				  and a.context_id = c.context_id order by o.object_id';
			
		$result = $db->fetchAll($query);
		$currentObjectName = '';
		$oldNode = null;
		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
					$objectName  = $row->object_name;
					$contextDesc = $row->context_desc;
				break;
				case 'oci8':
					$objectName  = $row->OBJECT_NAME;
					$contextDesc = $row->CONTEXT_DESC;
				break;
			}
			
			if($objectName != $currentObjectName){
				$sifObjectNode = $root->createElement('SIF_Object');
				$sifObjectNode->setAttribute('ObjectName',$objectName);
				$sifContextsNode = $root->createElement('SIF_Contexts');
			}
			else{
				$sifContextsNode = $oldNode;
			}
			
			$sifContextNode = $root->createElement('SIF_Context');
			$text = $root->createTextNode($contextDesc);
			$sifContextNode->appendChild($text);
			$sifContextsNode->appendChild($sifContextNode);
			
			if($objectName != $currentObjectName){
				$sifObjectNode->appendChild($sifContextsNode);
				$node->appendChild($sifObjectNode);
			}
			
			$currentObjectName = $objectName;
			$oldNode = $sifContextsNode;
		}
		return $node;
	}
	
	private function buildPublishChangeAccess($node, $root){
		$db = Zend_Registry::get('my_db');
		
		$query = 'select
				  o.object_name,
				  c.context_desc
				  from
				  '.DBConvertor::convertCase('data_object').' o,
				  '.DBConvertor::convertCase('context').' c,
				  '.DBConvertor::convertCase('agent_permissions').' a
				  where
				  a.agent_id = '.$this->agent->agentId.'
				  and
				  a.zone_id = '.$_SESSION['ZONE_ID'].'
				  and
				  a.context_id = '.$_SESSION['CONTEXT_ID'].'
				  and a.can_update = 1
				  and a.object_id = o.object_id
				  and a.context_id = c.context_id order by o.object_id';
			
		$result = $db->fetchAll($query);
		$currentObjectName = '';
		$oldNode = null;
		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
					$objectName  = $row->object_name;
					$contextDesc = $row->context_desc;
				break;
				case 'oci8':
					$objectName  = $row->OBJECT_NAME;
					$contextDesc = $row->CONTEXT_DESC;
				break;
			}
			
			if($objectName != $currentObjectName){
				$sifObjectNode = $root->createElement('SIF_Object');
				$sifObjectNode->setAttribute('ObjectName',$objectName);
				$sifContextsNode = $root->createElement('SIF_Contexts');
			}
			else{
				$sifContextsNode = $oldNode;
			}
			
			$sifContextNode = $root->createElement('SIF_Context');
			$text = $root->createTextNode($contextDesc);
			$sifContextNode->appendChild($text);
			$sifContextsNode->appendChild($sifContextNode);
			
			if($objectName != $currentObjectName){
				$sifObjectNode->appendChild($sifContextsNode);
				$node->appendChild($sifObjectNode);
			}
			
			$currentObjectName = $objectName;
			$oldNode = $sifContextsNode;
		}
		return $node;
	}
	
	private function buildPublishAddAccess($node, $root){
		$db = Zend_Registry::get('my_db');
		
		$query = 'select
				  o.object_name,
				  c.context_desc
				  from
				  '.DBConvertor::convertCase('data_object').' o,
				  '.DBConvertor::convertCase('context').' c,
				  '.DBConvertor::convertCase('agent_permissions').' a
				  where
				  a.agent_id = '.$this->agent->agentId.'
				  and
				  a.zone_id = '.$_SESSION['ZONE_ID'].'
				  and
				  a.context_id = '.$_SESSION['CONTEXT_ID'].'
				  and a.can_add = 1
				  and a.object_id = o.object_id
				  and a.context_id = c.context_id order by o.object_id';
			
		$result = $db->fetchAll($query);
		$currentObjectName = '';
		$oldNode = null;
		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
					$objectName  = $row->object_name;
					$contextDesc = $row->context_desc;
				break;
				case 'oci8':
					$objectName  = $row->OBJECT_NAME;
					$contextDesc = $row->CONTEXT_DESC;
				break;
			}
			
			if($objectName != $currentObjectName){
				$sifObjectNode = $root->createElement('SIF_Object');
				$sifObjectNode->setAttribute('ObjectName',$objectName);
				$sifContextsNode = $root->createElement('SIF_Contexts');
			}
			else{
				$sifContextsNode = $oldNode;
			}
			
			$sifContextNode = $root->createElement('SIF_Context');
			$text = $root->createTextNode($contextDesc);
			$sifContextNode->appendChild($text);
			$sifContextsNode->appendChild($sifContextNode);
			
			if($objectName != $currentObjectName){
				$sifObjectNode->appendChild($sifContextsNode);
				$node->appendChild($sifObjectNode);
			}
			
			$currentObjectName = $objectName;
			$oldNode = $sifContextsNode;
		}
		return $node;
	}
	
	private function buildSubscribeAccess($node, $root){
		$db = Zend_Registry::get('my_db');
		
		$query = 'select
				  o.object_name,
				  c.context_desc
				  from
				  '.DBConvertor::convertCase('data_object').' o,
				  '.DBConvertor::convertCase('context').' c,
				  '.DBConvertor::convertCase('agent_permissions').' a
				  where
				  a.agent_id = '.$this->agent->agentId.'
				  and
				  a.zone_id = '.$_SESSION['ZONE_ID'].'
				  and
				  a.context_id = '.$_SESSION['CONTEXT_ID'].'
				  and a.can_subscribe = 1
				  and a.object_id = o.object_id
				  and a.context_id = c.context_id order by o.object_id';
		
		$result = $db->fetchAll($query);
		$currentObjectName = '';
		$oldNode = null;
		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
					$objectName  = $row->object_name;
					$contextDesc = $row->context_desc;
				break;
				case 'oci8':
					$objectName  = $row->OBJECT_NAME;
					$contextDesc = $row->CONTEXT_DESC;
				break;
			}
			
			if($objectName != $currentObjectName){
				$sifObjectNode = $root->createElement('SIF_Object');
				$sifObjectNode->setAttribute('ObjectName',$objectName);
				$sifContextsNode = $root->createElement('SIF_Contexts');
			}
			else{
				$sifContextsNode = $oldNode;
			}
			
			$sifContextNode = $root->createElement('SIF_Context');
			$text = $root->createTextNode($contextDesc);
			$sifContextNode->appendChild($text);
			$sifContextsNode->appendChild($sifContextNode);
			
			if($objectName != $currentObjectName){
				$sifObjectNode->appendChild($sifContextsNode);
				$node->appendChild($sifObjectNode);
			}
			
			$currentObjectName = $objectName;
			$oldNode = $sifContextsNode;
		}
		return $node;
	}
	
	private function buildProvideAccess($node, $root){
		$db = Zend_Registry::get('my_db');
		
		$query = 'select
				  o.object_name,
				  c.context_desc
				  from
				  '.DBConvertor::convertCase('data_object').' o,
				  '.DBConvertor::convertCase('context').' c,
				  '.DBConvertor::convertCase('agent_permissions').' a
				  where
				  a.agent_id = '.$this->agent->agentId.'
				  and
				  a.zone_id = '.$_SESSION['ZONE_ID'].'
				  and
				  a.context_id = '.$_SESSION['CONTEXT_ID'].'
				  and a.can_provide = 1
				  and a.object_id = o.object_id
				  and a.context_id = c.context_id order by o.object_id';
		
		$result = $db->fetchAll($query);
		$currentObjectName = '';
		$oldNode = null;
		foreach($result as $row){
			switch(DB_TYPE) {
				case 'mysql':
					$objectName  = $row->object_name;
					$contextDesc = $row->context_desc;
				break;
				case 'oci8':
					$objectName  = $row->OBJECT_NAME;
					$contextDesc = $row->CONTEXT_DESC;
				break;
			}
			
			if($objectName != $currentObjectName){
				$sifObjectNode = $root->createElement('SIF_Object');
				$sifObjectNode->setAttribute('ObjectName',$objectName);
				$sifContextsNode = $root->createElement('SIF_Contexts');
			}
			else{
				$sifContextsNode = $oldNode;
			}
			
			$sifContextNode = $root->createElement('SIF_Context');
			$text = $root->createTextNode($contextDesc);
			$sifContextNode->appendChild($text);
			$sifContextsNode->appendChild($sifContextNode);
			
			if($objectName != $currentObjectName){
				$sifObjectNode->appendChild($sifContextsNode);
				$node->appendChild($sifObjectNode);
			}
			
			$currentObjectName = $objectName;
			$oldNode = $sifContextsNode;
		}
		return $node;
	}
}

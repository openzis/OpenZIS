<?php

/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Subscribe{

	var $dom;
	var $subscribeObjects = array();
	var $agent;
	var $originalSourceId;
	var $originalMsgId;

	public function Subscribe($dom){
		$this->dom = $dom;
		$this->processSubscribe();
		$this->saveSubscribes();

		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		$sifMessageNode = $dom->getElementsByTagName('SIF_Message')->item(0);
		XmlHelper::buildSuccessMessage($msgId,
									   $timestamp,
									   $this->originalSourceId,
								  	   $this->originalMsgId,
									   0,
									   $originalMsg = null,
									   $desc = null);
	}

	private function saveSubscribes(){
	  $db = Zend_Registry::get('my_db');
	  $agentSub = new AgentSubscriptions($db);
	
	  foreach($this->subscribeObjects as $object){
		  $alreadySubscribed = $this->alreadySubscribed($object->objectId, $object->contextId);
		  if(!$alreadySubscribed){
		  	$data = array(
							DBConvertor::convertCase('agent_id')            => $this->agent->agentId,
							DBConvertor::convertCase('object_type_id')      => $object->objectId,
							DBConvertor::convertCase('subscribe_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
							DBConvertor::convertCase('context_id')          => $object->contextId,
						    DBConvertor::convertCase('zone_id')             => $_SESSION['ZONE_ID']
					     );
			$agentSub->insert($data);
		  }
	  }

	}

	private function alreadySubscribed($objectId, $contextId){
		$db = Zend_Registry::get('my_db');
		

		$query = "select count(*) as NUM_ROWS from 
				  ".DBConvertor::convertCase('agent_subscriptions')."
				  where agent_id = ".$this->agent->agentId."
				  and object_type_id = ".$objectId."
				  and context_id = $contextId
				  and zone_id = ".$_SESSION['ZONE_ID'];
		
		$result = $db->fetchAll($query);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					$num = $row->num_rows;
				break;
				case 'oci8':
					$num = $row->NUM_ROWS;
				break;
			}
		}
		if($num != 0){
			return true;
		}
		else{
			return false;
		}

	}

	private function processSubscribe(){
		$dom = $this->dom;

		$headerNode   		= $dom->getElementsByTagName('SIF_Header')->item(0);
		$originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
		$originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;

		if ($headerNode->getElementsByTagName('SIF_Timestamp')->item(0)){
			$originalTimestamp   = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;
		} else {
			$originalTimestamp   = Utility::createTimestamp();
		}		


		$this->originalSourceId = $originalSourceId;
		$this->originalMsgId    = $originalMsgId;

		$validSourceId = Agent::checkSourceId($originalSourceId);
		if(!$validSourceId){
			ProvisionError::invalidSourceId($originalSourceId, $originalMsgId);
			exit;

		}
		else{
			$agent = new Agent($originalSourceId);
			$this->agent = $agent;
			if(!$this->agent->isRegistered()){
				RegisterError::notRegisteredError($originalSourceId, $originalMsgId);
				exit;
			}
			else{
				$objects = $dom->getElementsByTagName('SIF_Object');
				foreach($objects as $object){
					$objectName = $object->getAttribute('ObjectName');
					$contexts   = $object->getElementsByTagName('SIF_Context');
					if(!DataObject::objectExists($objectName)){
						ProvisionError::invalidObject($originalSourceId, $originalMsgId, $objectName);
						exit;
					}
					else{
						if($contexts->length != 0){
							foreach($contexts as $context){
								if(Context::isValidContext($context->nodeValue))
								{
									$contextId = Context::getContextId($context->nodeValue);
									if(!DataObject::allowedToSubscribe($agent->agentId,$objectName, $contextId)){
										ProvisionError::invalidPermissionToSubscribe($originalSourceId, $originalMsgId, $objectName);
										exit;
									}
									else{
										$dataObject = new DataObject($objectName);
										$dataObject->contextId = $contextId;
										array_push($this->subscribeObjects, $dataObject);
									}//check if allowed to subscribe
								}//check if context is valid
								else
								{
									ProvisionError::contextNotSupportedError($originalSourceId, $originalMsgId);
									exit;
								}
							}//loop through contexts
						}
						else{
							if(!DataObject::allowedToSubscribe($agent->agentId,$objectName)){
								ProvisionError::invalidPermissionToSubscribe($originalSourceId, $originalMsgId, $objectName);
								exit;
							}
							else{
								$dataObject = new DataObject($objectName);
								array_push($this->subscribeObjects, $dataObject);
							}//check if allowed to subscribe
						}// check if there are contexts
					}//check if object is valid
				}//loop through objects
			}//check if registered
		}//check sourceId
	}
}

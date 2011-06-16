<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class UnSubscribe{

	var $dom;
	var $unSubscribeObjects = array();
	var $agent;
	var $originalSourceId;
	var $originalMsgId;
	
	public function UnSubscribe($dom){
		$this->dom = $dom;
		$this->processUnSubscribe();
		$this->saveUnSubscribes();
		
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
	
	private function saveUnSubscribes(){
	  $db = ZitDBAdapter::getDBAdapter();
	  foreach($this->unSubscribeObjects as $object){
		  $db->delete(DBConvertor::convertCase('agent_subscriptions'), 'agent_id = '.$this->agent->agentId.' and context_id = '.$object->contextId.' and object_type_id = '.$object->objectId.' and zone_id = '.$_SESSION['ZONE_ID']);
	  }
	}
	
	private function processUnSubscribe(){
		$dom = $this->dom;
		
		$headerNode   		= $dom->getElementsByTagName('SIF_Header')->item(0);
		$originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
		$originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
		$originalTimestamp  = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;
		
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
										array_push($this->unSubscribeObjects, $dataObject);
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
								array_push($this->unSubscribeObjects, $dataObject);
							}//check if allowed to subscribe	
						}			
					}//check if object is valid
				}//loop through objects
			}//check if registered
		}//check sourceId
	}
}

<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2010  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Provision {

    var $dom;
    var $provideObjects       = array();
    var $subscribeObjects     = array();
    var $publishAddObjects    = array();
    var $publishChangeObjects = array();
    var $publishDeleteObjects = array();
    var $requestObjects       = array();
    var $respondObjects       = array();
    var $originalMsgId;
    var $originalSourceId;
    var $originalTimestamp;
    var $agent;

    public function Provision($dom) {
        $this->dom = $dom;
        $this->processProvision();
    }
    
    private function processProvision() {
			
        $dom = $this->dom;

        $headerNode   		= $dom->getElementsByTagName('SIF_Header')->item(0);
        $this->originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
        $this->originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
        $this->originalTimestamp  = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;

        $validSourceId = Agent::checkSourceId($this->originalSourceId);
        if(!$validSourceId) {
            ProvisionError::invalidSourceId($this->originalSourceId, $this->originalMsgId);
            if(CODELEVEL != 3) {
                exit;
            }
            else {
                return false;
            }
        }
        else {
            $this->agent = new Agent($this->originalSourceId);
            if(!$this->agent->isRegistered()) {
                RegisterError::notRegisteredError($this->originalSourceId, $this->originalMsgId);
            }
            else {
                $provideObjectNode = $dom->getElementsByTagName('SIF_ProvideObjects')->item(0);
                $this->processProvideObjects($provideObjectNode);

                $subscribeObjectNode = $dom->getElementsByTagName('SIF_SubscribeObjects')->item(0);
                $this->processSubscribeObjects($subscribeObjectNode);

                $publishAddObjectNode = $dom->getElementsByTagName('SIF_PublishAddObjects')->item(0);
                $this->processPublishAddObjects($publishAddObjectNode);

                $publishChangeObjectNode = $dom->getElementsByTagName('SIF_PublishChangeObjects')->item(0);
                $this->processPublishChangeObjects($publishChangeObjectNode);

                $publishDeleteObjectNode = $dom->getElementsByTagName('SIF_PublishDeleteObjects')->item(0);
                $this->processPublishDeleteObjects($publishDeleteObjectNode);

                $requestObjectNode = $dom->getElementsByTagName('SIF_RequestObjects')->item(0);
                $this->processRequestObjects($requestObjectNode);

                $respondObjectNode = $dom->getElementsByTagName('SIF_RespondObjects')->item(0);
                $this->processRespondObjects($respondObjectNode);

                $this->saveProvisions();

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
        }
    }

    private function saveProvisions() {
        $db = Zend_Registry::get('my_db');
		$agt_pro = new AgentProvisions($db);
		$agt_sub = new AgentSubscriptions($db);
		$agt_req = new AgentRequester($db);
		$agt_res = new AgentResponder($db);

		$where = 'agent_id = '.$this->agent->agentId.' and zone_id = '.$_SESSION['ZONE_ID'];
		
		$agt_pro->delete($where);
		$agt_sub->delete($where);
		$agt_req->delete($where);
		$agt_res->delete($where);

        if(count($this->requestObjects) > 0 ) {
            foreach($this->requestObjects as $object) {
                $data = array(
                        DBConvertor::convertCase('agent_id')              => $this->agent->agentId,
                        DBConvertor::convertCase('object_type_id')        => $object->objectId,
                        DBConvertor::convertCase('requester_timestamp')   => new zend_db_expr(DBConvertor::convertCurrentTime()),
                        DBConvertor::convertCase('context_id')            => $object->contextId,
                        DBConvertor::convertCase('zone_id')             => $_SESSION['ZONE_ID']
                );
				$agt_req->insert($data);
            }
        }

        if(count($this->respondObjects) > 0 ) {
            foreach($this->respondObjects as $object) {
                $data = array(
                        DBConvertor::convertCase('agent_id')            => $this->agent->agentId,
                        DBConvertor::convertCase('object_type_id')      => $object->objectId,
                        DBConvertor::convertCase('responder_timestamp') => new zend_db_expr(DBConvertor::convertCurrentTime()),
                        DBConvertor::convertCase('context_id')          => $object->contextId,
                        DBConvertor::convertCase('zone_id')             => $_SESSION['ZONE_ID']
                );
				$agt_res->insert($data);
//              $db->insert('agent_responder', $data);
            }
        }

        if(count($this->provideObjects) > 0 ) {
            foreach($this->provideObjects as $object) {
                $data = array(
                        DBConvertor::convertCase('agent_id')            => $this->agent->agentId,
                        DBConvertor::convertCase('object_type_id')      => $object->objectId,
                        DBConvertor::convertCase('provision_timestamp') => new zend_db_expr(DBConvertor::convertCurrentTime()),
                        DBConvertor::convertCase('context_id')          => $object->contextId,
                        DBConvertor::convertCase('zone_id')             => $_SESSION['ZONE_ID']
                );
//                $db->insert('agent_provisions', $data);
				$agt_pro->insert($data);
            }
        }

        if(count($this->subscribeObjects) > 0 ) {
            foreach($this->subscribeObjects as $object) {
                $data = array(
                        DBConvertor::convertCase('agent_id')            => $this->agent->agentId,
                        DBConvertor::convertCase('object_type_id')      => $object->objectId,
                        DBConvertor::convertCase('subscribe_timestamp') => new zend_db_expr(DBConvertor::convertCurrentTime()),
                        DBConvertor::convertCase('context_id')          => $object->contextId,
                        DBConvertor::convertCase('zone_id')             => $_SESSION['ZONE_ID']
                );
//                $db->insert('agent_subscriptions', $data);
				$agt_sub->insert($data);
            }
        }

        if(count($this->publishAddObjects) > 0 ) {
            foreach($this->publishAddObjects as $object) {
                $data = array(
                        DBConvertor::convertCase('publish_add')       => '1'
                );
				$where = 'agent_id = '.$this->agent->agentId.' and object_type_id = '.$object->objectId.' and context_id = '.$object->contextId;
				$agt_pro->update($data, $where);
//                $db->update('agent_provisions', $data, 'agent_id = '.$this->agent->agentId.' and object_type_id = '.$object->objectId.' and context_id = '.$object->contextId);
            }
        }

        if(count($this->publishChangeObjects) > 0 ) {
            foreach($this->publishChangeObjects as $object) {
                $data = array(
                        DBConvertor::convertCase('publish_change')       => '1'
                );
				$where = 'agent_id = '.$this->agent->agentId.' and object_type_id = '.$object->objectId.' and context_id = '.$object->contextId;
				$agt_pro->update($data, $where);
//                $db->update('agent_provisions', $data, 'agent_id = '.$this->agent->agentId.' and object_type_id = '.$object->objectId.' and context_id = '.$object->contextId);
            }
        }

        if(count($this->publishDeleteObjects) > 0 ) {
            foreach($this->publishDeleteObjects as $object) {
                $data = array(
                        DBConvertor::convertCase('publish_delete')       => '1'
                );
				$where = 'agent_id = '.$this->agent->agentId.' and object_type_id = '.$object->objectId.' and context_id = '.$object->contextId;
				$agt_pro->update($data, $where);
				
//                $db->update('agent_provisions', $data, 'agent_id = '.$this->agent->agentId.' and object_type_id = '.$object->objectId.' and context_id = '.$object->contextId);
            }
        }

    }

    private function processRespondObjects($objectNode) {
        $objects = $objectNode->getElementsByTagName('SIF_Object');
        foreach($objects as $object) {
            $objectName = $object->getAttribute('ObjectName');
            $contexts   = $object->getElementsByTagName('SIF_Context');
            if(!DataObject::objectExists($objectName)) {
                ProvisionError::invalidObject($this->originalSourceId, $this->originalMsgId, $objectName);
                if(CODELEVEL != 3) {
                    exit;
                }
                else {
                    return false;
                }
            }
            else {
                if($contexts->length != 0) {
                    foreach($contexts as $context) {
                        $contextId = Context::getContextId($context->nodeValue);
                        if(!DataObject::allowedToRespond($this->agent->agentId,$objectName, $contextId)) {
                            ProvisionError::invalidPermissionToRespond($this->originalSourceId, $this->originalMsgId, $objectName);
                            if(CODELEVEL != 3) {
                                exit;
                            }
                            else {
                                return false;
                            }
                        }
                        else {
                            $dataObject = new DataObject($objectName);
                            $dataObject->contextId = $contextId;
                            array_push($this->respondObjects, $dataObject);
                        }//check if allowed to respond
                    }
                }
                else {
                    if(!DataObject::allowedToRespond($this->agent->agentId,$objectName)) {
                        ProvisionError::invalidPermissionToRespond($this->originalSourceId, $this->originalMsgId, $objectName);
                        if(CODELEVEL != 3) {
                            exit;
                        }
                        else {
                            return false;
                        }
                    }
                    else {
                        $dataObject = new DataObject($objectName);
                        array_push($this->respondObjects, $dataObject);
                    }//check if allowed to respond
                }//check if there are contexts
            }//check if object exist
        }//loop through objects
    }//end processRespondObjects

    private function processRequestObjects($objectNode) {
        $objects = $objectNode->getElementsByTagName('SIF_Object');
        foreach($objects as $object) {
            $objectName = $object->getAttribute('ObjectName');
            $contexts   = $object->getElementsByTagName('SIF_Context');
            if(!DataObject::objectExists($objectName)) {
                ProvisionError::invalidObject($this->originalSourceId, $this->originalMsgId, $objectName);
                if(CODELEVEL != 3) {
                    exit;
                }
                else {
                    return false;
                }
            }
            else {
                if($contexts->length != 0) {
                    foreach($contexts as $context) {
                        $contextId = Context::getContextId($context->nodeValue);
                        if(!DataObject::allowedToRequest($this->agent->agentId,$objectName,$contextId)) {
                            ProvisionError::invalidPermissionToRequest($this->originalSourceId, $this->originalMsgId, $objectName);
                            if(CODELEVEL != 3) {
                                exit;
                            }
                            else {
                                return false;
                            }
                        }
                        else {
                            $dataObject = new DataObject($objectName);
                            $dataObject->contextId = $contextId;
                            array_push($this->requestObjects, $dataObject);
                        }//check if allowed to request
                    }
                }
                else {
                    if(!DataObject::allowedToRequest($this->agent->agentId,$objectName)) {
                        ProvisionError::invalidPermissionToRequest($this->originalSourceId, $this->originalMsgId, $objectName);
                        if(CODELEVEL != 3) {
                            exit;
                        }
                        else {
                            return false;
                        }
                    }
                    else {
                        $dataObject = new DataObject($objectName);
                        array_push($this->requestObjects, $dataObject);
                    }//check if allowed to request
                }//check if there are contexts
            }//check if object exist
        }//loop through objects
    }//end processRequestObjects

    private function processPublishDeleteObjects($objectNode) {
        $objects = $objectNode->getElementsByTagName('SIF_Object');
        foreach($objects as $object) {
            $objectName = $object->getAttribute('ObjectName');
            $contexts   = $object->getElementsByTagName('SIF_Context');
            if(!DataObject::objectExists($objectName)) {
                ProvisionError::invalidObject($this->originalSourceId, $this->originalMsgId, $objectName);
                if(CODELEVEL != 3) {
                    exit;
                }
                else {
                    return false;
                }
            }
            else {
                if($contexts->length != 0) {
                    foreach($contexts as $context) {
                        $contextId = Context::getContextId($context->nodeValue);
                        if(!DataObject::allowedToPublishDelete($this->agent->agentId,$objectName, $contextId)) {
                            ProvisionError::invalidPermissionToPublishDelete($this->originalSourceId, $this->originalMsgId, $objectName);
                            if(CODELEVEL != 3) {
                                exit;
                            }
                            else {
                                return false;
                            }
                        }
                        else {
                            $dataObject = new DataObject($objectName);
                            $dataObject->contextId = $contextId;
                            array_push($this->publishDeleteObjects, $dataObject);
                        }//check if allowed to publish delete
                    }
                }
                else {
                    if(!DataObject::allowedToPublishDelete($this->agent->agentId,$objectName)) {
                        ProvisionError::invalidPermissionToPublishDelete($this->originalSourceId, $this->originalMsgId, $objectName);
                        if(CODELEVEL != 3) {
                            exit;
                        }
                        else {
                            return false;
                        }
                    }
                    else {
                        $dataObject = new DataObject($objectName);
                        array_push($this->publishDeleteObjects, $dataObject);
                    }//check if allowed to publish delete
                }//check if there are contexts
            }//check if object exist
        }//loop through objects
    }//end processPublishChangeObjects

    private function processPublishChangeObjects($objectNode) {
        $objects = $objectNode->getElementsByTagName('SIF_Object');
        foreach($objects as $object) {
            $objectName = $object->getAttribute('ObjectName');
            $contexts   = $object->getElementsByTagName('SIF_Context');
            if(!DataObject::objectExists($objectName)) {
                ProvisionError::invalidObject($this->originalSourceId, $this->originalMsgId, $objectName);
                if(CODELEVEL != 3) {
                    exit;
                }
                else {
                    return false;
                }
            }
            else {
                if($contexts->length != 0) {
                    foreach($contexts as $context) {
                        $contextId = Context::getContextId($context->nodeValue);
                        if(!DataObject::allowedToPublishChange($this->agent->agentId,$objectName, $contextId)) {
                            ProvisionError::invalidPermissionToPublishChange($this->originalSourceId, $this->originalMsgId, $objectName);
                            if(CODELEVEL != 3) {
                                exit;
                            }
                            else {
                                return false;
                            }
                        }
                        else {
                            $dataObject = new DataObject($objectName);
                            $dataObject->contextId = $contextId;
                            array_push($this->publishChangeObjects, $dataObject);
                        }//check if allowed to publish change
                    }
                }
                else {
                    if(!DataObject::allowedToPublishChange($this->agent->agentId,$objectName)) {
                        ProvisionError::invalidPermissionToPublishChange($this->originalSourceId, $this->originalMsgId, $objectName);
                        if(CODELEVEL != 3) {
                            exit;
                        }
                        else {
                            return false;
                        }
                    }
                    else {
                        $dataObject = new DataObject($objectName);
                        array_push($this->publishChangeObjects, $dataObject);
                    }//check if allowed to publish change
                }//check if there are contexts
            }//check if object exist
        }//loop through objects
    }//end processPublishChangeObjects

    private function processPublishAddObjects($objectNode) {
        $objects = $objectNode->getElementsByTagName('SIF_Object');
        foreach($objects as $object) {
            $objectName = $object->getAttribute('ObjectName');
            $contexts   = $object->getElementsByTagName('SIF_Context');
            if(!DataObject::objectExists($objectName)) {
                ProvisionError::invalidObject($this->originalSourceId, $this->originalMsgId, $objectName);
                if(CODELEVEL != 3) {
                    exit;
                }
                else {
                    return false;
                }
            }
            else {
                if($contexts->length != 0) {
                    foreach($contexts as $context) {
                        $contextId = Context::getContextId($context->nodeValue);
                        if(!DataObject::allowedToPublishAdd($this->agent->agentId,$objectName,$contextId)) {
                            ProvisionError::invalidPermissionToPublishAdd($this->originalSourceId, $this->originalMsgId, $objectName);
                            if(CODELEVEL != 3) {
                                exit;
                            }
                            else {
                                return false;
                            }
                        }
                        else {
                            $dataObject = new DataObject($objectName);
                            $dataObject->contextId = $contextId;
                            array_push($this->publishAddObjects, $dataObject);
                        }//check if allowed to publish add
                    }
                }
                else {
                    if(!DataObject::allowedToPublishAdd($this->agent->agentId,$objectName)) {
                        ProvisionError::invalidPermissionToPublishAdd($this->originalSourceId, $this->originalMsgId, $objectName);
                        if(CODELEVEL != 3) {
                            exit;
                        }
                        else {
                            return false;
                        }
                    }
                    else {
                        $dataObject = new DataObject($objectName);
                        array_push($this->publishAddObjects, $dataObject);
                    }//check if allowed to publish add
                }//check if there are contexts
            }//check if object exist
        }//loop through objects
    }//end processPublishAddObjects

    private function processSubscribeObjects($objectNode) {
        $objects = $objectNode->getElementsByTagName('SIF_Object');
        foreach($objects as $object) {
            $objectName = $object->getAttribute('ObjectName');
            $contexts   = $object->getElementsByTagName('SIF_Context');
            if(!DataObject::objectExists($objectName)) {
                ProvisionError::invalidObject($this->originalSourceId, $this->originalMsgId, $objectName);
                if(CODELEVEL != 3) {
                    exit;
                }
                else {
                    return false;
                }
            }
            else {
                if($contexts->length != 0) {
                    foreach($contexts as $context) {
                        $contextId = Context::getContextId($context->nodeValue);
                        if(!DataObject::allowedToSubscribe($this->agent->agentId,$objectName, $contextId)) {
                            ProvisionError::invalidPermissionToSubscribe($this->originalSourceId, $this->originalMsgId, $objectName);
                            if(CODELEVEL != 3) {
                                exit;
                            }
                            else {
                                return false;
                            }
                        }
                        else {
                            $dataObject = new DataObject($objectName);
                            $dataObject->contextId = $contextId;
                            array_push($this->subscribeObjects, $dataObject);
                        }//check if allowed to subscribe
                    }
                }
                else {
                    if(!DataObject::allowedToSubscribe($this->agent->agentId,$objectName)) {
                        ProvisionError::invalidPermissionToSubscribe($this->originalSourceId, $this->originalMsgId, $objectName);
                        if(CODELEVEL != 3) {
                            exit;
                        }
                        else {
                            return false;
                        }
                    }
                    else {
                        $dataObject = new DataObject($objectName);
                        array_push($this->subscribeObjects, $dataObject);
                    }//check if allowed to subscribe
                }//check if there are contexts
            }//check if object exist
        }//loop through objects
    }//end processSubcribeObjects

    private function processProvideObjects($objectNode) {
        $objects    = $objectNode->getElementsByTagName('SIF_Object');
        foreach($objects as $object) {
            $objectName = $object->getAttribute('ObjectName');
            $contexts   = $object->getElementsByTagName('SIF_Context');
            if(!DataObject::objectExists($objectName)) {
                ProvisionError::invalidObject($this->originalSourceId, $this->originalMsgId, $objectName);
                if(CODELEVEL != 3) {
                    exit;
                }
                else {
                    return false;
                }
            }
            else {
                if($contexts->length != 0) {
                    foreach($contexts as $context) {
                        $contextId = Context::getContextId($context->nodeValue);
                        if(DataObject::alreadyProvided($this->agent->agentId, $objectName, $contextId)) {
                            ProvisionError::alreadyProvided($this->originalSourceId, $this->originalMsgId, $objectName);
                            if(CODELEVEL != 3) {
                                exit;
                            }
                            else {
                                return false;
                            }
                        }
                        else {
                            if(!DataObject::allowedToProvide($this->agent->agentId,$objectName, $contextId)) {
                                ProvisionError::invalidPermissionToProvide($this->originalSourceId, $this->originalMsgId, $objectName);
                                if(CODELEVEL != 3) {
                                    exit;
                                }
                                else {
                                    return false;
                                }
                            }
                            else {
                                $dataObject = new DataObject($objectName);
                                $dataObject->contextId = $contextId;
                                array_push($this->provideObjects, $dataObject);
                            }//check if allowed to provide
                        }//check if already provided
                    }
                }
                else {
                    if(DataObject::alreadyProvided($this->agent->agentId, $objectName)) {
                        ProvisionError::alreadyProvided($this->originalSourceId, $this->originalMsgId, $objectName);
                        if(CODELEVEL != 3) {
                            exit;
                        }
                        else {
                            return false;
                        }
                    }
                    else {
                        if(!DataObject::allowedToProvide($this->agent->agentId,$objectName)) {
                            ProvisionError::invalidPermissionToProvide($this->originalSourceId, $this->originalMsgId, $objectName);
                            if(CODELEVEL != 3) {
                                exit;
                            }
                            else {
                                return false;
                            }
                        }
                        else {
                            $dataObject = new DataObject($objectName);
                            array_push($this->provideObjects, $dataObject);
                        }//check if allowed to provide
                    }//check if already provided
                }//check if there are contexts
            }//check if object exist
        }//loop through objects
    }//end processProvideObjects
}//end class


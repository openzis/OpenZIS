<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/
class Event {

    var $dom;

    public function Event($dom) {
        $this->dom = $dom;
        $this->processEvent();
    }

    public static function getActionId($desc) {
        $db = Zend_Registry::get('my_db');

        $query = "select action_id from ".DBConvertor::convertCase('event_actions')." where lower(action_desc) = lower('".$desc."')";
        $result = $db->fetchAll($query);
		switch(DB_TYPE) {
			case 'mysql':
				return  intval($result[0]->action_id);
			break;
			case 'oci8':
				return  intval($result[0]->ACTION_ID);
			break;
		}
    }

    private function processEvent() {
        $dom = $this->dom;

        $headerNode         = $dom->getElementsByTagName('SIF_Header')->item(0);
        $originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
        $originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
        $originalTimestamp  = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;

        $validSourceId = Agent::checkSourceId($originalSourceId);
        if(!$validSourceId) {
            RegisterError::invalidSourceId($agent->sourceId, $originalMsgId);
        }
        else {
            $agent = new Agent($originalSourceId);
            if($agent->isRegistered()) {
                $eventObjectNode = $dom->getElementsByTagName('SIF_EventObject')->item(0);
                $objectName      = $eventObjectNode->getAttribute('ObjectName');
                $objectAction    = $eventObjectNode->getAttribute('Action');
                $objectAction    = strtolower($objectAction);
                if(!DataObject::objectExists($objectName)) {
                    ProvisionError::invalidObject($originalSourceId, $originalMsgId, $objectName);
                    exit;
                }
                else {
                    $allowed = $this->hasPermission($objectName, $objectAction, $agent->agentId);
                    if($allowed) {
                        $provider = DataObject::isProvider($objectName, $agent->agentId);
                        if($provider) {
                            $this->publishEvent($agent->agentId, $objectName, $objectAction, $_SESSION['ZONE_VERSION'], $dom->saveXml($headerNode));
                            $timestamp    = Utility::createTimestamp();
                            $msgId        = Utility::createMessageId();
                            $sifMessageNode = $dom->getElementsByTagName('SIF_Message')->item(0);
                            XmlHelper::buildSuccessMessage($msgId,
                                    $timestamp,
                                    $originalSourceId,
                                    $originalMsgId,
                                    0,
                                    $originalMsg = null,
                                    $desc = null);
                        }
                        else {
                            ProvisionError::notProviderError($originalSourceId, $originalMsgId);
                        }
                    }
                    else {
                        switch($objectAction) {

                            case 'add':
                                ProvisionError::invalidPermissionToPublishAdd($originalSourceId, $originalMsgId, $objectName);
                                break;

                            case 'change':
                                ProvisionError::invalidPermissionToPublishChange($originalSourceId, $originalMsgId, $objectName);
                                break;

                            case 'delete':
                                ProvisionError::invalidPermissionToPublishDelete($originalSourceId, $originalMsgId, $objectName);
                                break;

                        }//switch for error message
                    }//allowed
                }//object exist
            }
            else {
                RegisterError::notRegisteredError($originalSourceId, $originalMsgId);
            }//not registered
        }//valid sourceId
    }//end processEvent

    private function hasPermission($objectName, $action, $agentId) {
        switch($action) {
            case 'add':
                return DataObject::allowedToPublishAdd($agentId, $objectName);
                break;
            case 'change':
                return DataObject::allowedToPublishChange($agentId, $objectName);
                break;
            case 'delete':
                return DataObject::allowedToPublishDelete($agentId, $objectName);
                break;
        }

    }

    private function publishEvent($agentId, $objectName, $objectAction, $version, $header) {
        $db = Zend_Registry::get('my_db');

        $dom = $this->dom;

        $dataObject = new DataObject($objectName);
        $actionId = Event::getActionId($objectAction);
        $query = "select agent_subscriptions.agent_id,
			 			 agent_registered.agent_mode_id,
			 			 agent_registered.sif_version,
			 			 agent_registered.maxbuffersize,
		     			 agent.source_id
		    		from ".DBConvertor::convertCase('agent_subscriptions')."
		   			inner join 
						".DBConvertor::convertCase('agent_registered')." on agent_registered.agent_Id = agent_subscriptions.agent_id
		     			and agent_registered.unregister_timestamp is null
		   			inner join ".DBConvertor::convertCase('agent')." on agent.agent_id = agent_subscriptions.agent_id
		   			where agent_subscriptions.object_type_id = ".$dataObject->objectId." 
		     			and agent_subscriptions.context_id = ".$_SESSION["CONTEXT_ID"]." 
		     			and agent_subscriptions.zone_id = ".$_SESSION["ZONE_ID"]."
		     			and agent_subscriptions.context_id = agent_registered.context_id 
		     			and agent_subscriptions.zone_id = agent_registered.zone_id";
		
        $result = $db->fetchAll($query);
        foreach($result as $row) {
            $error = false;

			switch(DB_TYPE) {
				case 'mysql':
					$agent_id = intval($row->agent_id);
					$maxbuffersize = intval($row->maxbuffersize);
					$source_id = $row->source_id;
					$agent_mode_id = intval($row->agent_mode_id);
					$agent_version = $row->sif_version;
				break;
				case 'oci8':
					$agent_id = intval($row->AGENT_ID);
					$maxbuffersize = intval($row->MAXBUFFERSIZE);
					$source_id = $row->SOURCE_ID;
					$agent_mode_id = intval($row->AGENT_MODE_ID);
					$agent_version = $row->SIF_VERSION;
				break;
			}

//            $filterUtility = new FilterUtility();
//            $filterUtility->FilterCommonElements($dataObject->objectId, $dom, $agent_id);

            $sifMessageNode = $dom->getElementsByTagName('SIF_Message')->item(0);
            $eventXML = $dom->saveXML($sifMessageNode);
	    	$eventXML = str_replace('xmlns="http://www.sifinfo.org/uk/infrastructure/2.x" ', '', $eventXML);
	    	$eventXML = str_replace('xmlns="http://www.sifinfo.org/infrastructure/2.x" ','',$eventXML);
            $eventXML = str_replace('xmlns="http://www.sifinfo.org/infrastructure/1.x" ','',$eventXML);
			$eventXML = str_replace('xmlns:sif="http://www.sifinfo.org/infrastructure/2.x" ','',$eventXML);
            $eventXML = str_replace('xmlns:sif="http://www.sifinfo.org/infrastructure/1.x" ','',$eventXML);
            $messageSize = strlen($eventXML);

            if($messageSize > $maxbuffersize) {
                SifLogEntry::CreateSifLogEvents($header,
                        '4',
                        '2',
                        'Buffer size of agent '.$row->source_id.' is too small to recieve this event [size : '.$row->maxbuffersize.']'
                );
                $error = true;
            }

            if($version != $agent_version) {
                SifLogEntry::CreateSifLogEvents($header,
                        '4',
                        '4',
                        'Version in event not supported by agent '.$source_id
                );
                $error = true;
            }
            if(!$error) {
                $data = array(
                        DBConvertor::convertCase('event_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
                        DBConvertor::convertCase('agent_id_sender') => $agentId,
 						DBConvertor::convertCase('agent_id_rec')    => $agent_id,
                        DBConvertor::convertCase('event_data')      => $eventXML,
 						DBConvertor::convertCase('object_id')       => $dataObject->objectId,
                        DBConvertor::convertCase('action_id')       => $actionId,
 						DBConvertor::convertCase('agent_mode_id')   => $agent_mode_id, 
						DBConvertor::convertCase('zone_id')         => $_SESSION["ZONE_ID"],
						DBConvertor::convertCase('context_id')      => $_SESSION["CONTEXT_ID"]
						);
                $db->insert(DBConvertor::convertCase('event'), $data);
            }
        }
    }
}
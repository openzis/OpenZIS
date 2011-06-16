<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class SystemControl {

    var $xmlDom;

    public function SystemControl($m, $xml=false, $zoneStatus=false) {
        $this->xmlDom = $m->dom;
        $this->processSystemControl($m);
    }

    private function processSystemControl($m) {
        $dom = $this->xmlDom;

        $controlDataNode = $dom->getElementsByTagName('SIF_SystemControlData');
        $controlData     = $controlDataNode->item(0)->childNodes->item(NODE_NUM)->nodeName;

        switch($controlData) {
            case 'SIF_GetZoneStatus':
                $_SESSION['SIF_MESSAGE_TYPE'] = 14;
                $this->processZoneStatus();
                break;

            case 'SIF_Ping':
                $_SESSION['SIF_MESSAGE_TYPE'] = 10;
                $this->processPing();
                break;

            case 'SIF_Sleep':
                $_SESSION['SIF_MESSAGE_TYPE'] = 11;
                $this->processSleep();
                break;

            case 'SIF_Wakeup':
                $_SESSION['SIF_MESSAGE_TYPE'] = 12;
                $this->processWakeup();
                break;

            case 'SIF_GetMessage':
                $_SESSION['SIF_MESSAGE_TYPE'] = 13;
                $this->processGetMessage($m);
                break;

            case 'SIF_GetAgentACL':
                $_SESSION['SIF_MESSAGE_TYPE'] = 15;
                $this->processAclRequest();
                break;

	       case 'SIF_CancelRequest':
	             $_SESSION['SIF_MESSAGE_TYPE'] = 16;
	             $this->processCancelRequest();
	             break;

            default:
                echo '<FATAL_ERROR>INVALID_SYSTEM_CONTROL_REQUEST</FATAL_ERROR>';
                exit;
                break;
        }
    }

    private function processAclRequest() {
        $dom = $this->xmlDom;

        $headerNode   		= $dom->getElementsByTagName('SIF_Header')->item(0);
        $originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
        $originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;

        $acl = new ACL($dom);

        $timestamp    = Utility::createTimestamp();
        $msgId        = Utility::createMessageId();
        XmlHelper::buildSuccessMessage($msgId,
                $timestamp,
                $originalSourceId,
                $originalMsgId,
                0,
                $originalMsg = $acl->BuildACL(),
                $desc = null);
    }


    private function processCancelRequest() {
        $dom = $this->xmlDom;

        $headerNode   		= $dom->getElementsByTagName('SIF_Header')->item(0);
        $originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
        $originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;

#        $acl = new ACL($dom);

        $timestamp    = Utility::createTimestamp();
        $msgId        = Utility::createMessageId();
        XmlHelper::buildSuccessMessage($msgId,
                $timestamp,
                $originalSourceId,
                $originalMsgId,
                0,
                $originalMsg = null,
                $desc = null);
    }

    private function processGetMessage($m) {
        $dom = $this->xmlDom;

        $headerNode          = $m->headerNode;
        $originalMsgId       = $m->msgId;
        $originalSourceId    = $m->sourceId;
        $version             = $m->version;

#        $headerNode   = $dom->getElementsByTagName('SIF_Header')->item(0);
#        $originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
#        $originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
        //$originalTimestamp  = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;

        $validSourceId = Agent::checkSourceId($originalSourceId);
        if(!$validSourceId) {
            RegisterError::invalidSourceId($originalSourceId, $originalMsgId);
        }//if not valid sourceId
        else {
            $agent = new Agent($originalSourceId);
            if(!$agent->isRegistered()) {
                RegisterError::notRegisteredError($originalSourceId, $originalMsgId);
            }
            else {
                $msgId = Utility::createMessageId();
                $eventData = $this->getFirstMessage($agent, $msgId);
                if($eventData != null) {
                    $timestamp    = Utility::createTimestamp();
                    XmlHelper::buildSuccessMessage($msgId,
                            $timestamp,
                            $originalSourceId,
                            $originalMsgId,
                            0,
                            $originalMsg = $eventData,
                            $desc = null);
                }
                else {
                    $timestamp    = Utility::createTimestamp();
                    $msgId        = Utility::createMessageId();
                    XmlHelper::buildSuccessMessage($msgId,
                            $timestamp,
                            $originalSourceId,
                            $originalMsgId,
                            9,
                            $originalMsg = null,
                            $desc = null);
                }//if eventData is not null
            }//if agent is registered
        }//if valid sourceId
    }


    private function getFirstMessage($agent) {
	
	/*
	NOTE:  CHRISTOPHER WHITELEY
	Within this section we have allowed the alteration of how SIF Ack messages are needed.
	If $sif_response, $sif_request, and $sif_event are set to a value of 2 then your system
	will work according to SIF Specifications.  If you do not want to send a SIF_Ack message
	for any of the three message types below you can change the value to 3.
	
	This functionality was done per request of a paid support member.
	
	*/
	
		$sif_response   = 2;
		$sif_request	= 2;
		$sif_event 		= 2;
		$sif_ack	    = 2;
		
		$empty = null;
		$zero  = 0;
		$id = $zero;
		
        $db = Zend_Registry::get('my_db');
		
#		$mysql =   ''
#				.' ('
#				.' Select response_id v, 1 t '
#				.' from ' .DBConvertor::convertCase('response').' where status_id in (1,2) '
#				.' and agent_id_requester = '.$agent->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']
#				.') '
#				.' union all '
#				.' ('
#				.' Select request_id v, 2 t '
#				.' from ' .DBConvertor::convertCase('request').' where status_id in (1,2) '
#				.' and agent_id_responder = '.$agent->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']
#				.') '
#				.' union all '
#				.' ('
#				.'Select event_id v, 3 t '
#				.' from ' .DBConvertor::convertCase('event').' where status_id in (1,2) '
#				.' and agent_id_rec = '.$agent->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']
#				.')'
#				.' ORDER BY t, v LIMIT 1';
				
		$oracle =   'Select v, t from ( Select response_id v, 1 t '
				.' from ' .DBConvertor::convertCase('response').' where status_id in (1,2) '
				.' and agent_id_requester = '.$agent->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']
				.' union all '
				.'Select request_id v, 2 t '
				.'from ' .DBConvertor::convertCase('request') 
				.' where status_id in (1,2) '
				.'   and agent_id_responder = '.$agent->agentId
				.'   and context_id = '.$_SESSION['CONTEXT_ID']
				.'   and zone_id = '.$_SESSION['ZONE_ID']
				.' union all '
				.'Select event_id v, 3 t '
				.' from ' .DBConvertor::convertCase('event').' where status_id in (1,2) '
				.' and agent_id_rec = '.$agent->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']
				.') ORDER BY t, v';
				
		$mysql = 'select id, msg_type, data '
			    .' from ' .DBConvertor::convertCase('messagequeue')
			    .' where agt_id_out = ' . $agent->agentId
			    .' and zone_id = ' . $_SESSION['ZONE_ID']
			    .' and context_id = ' . $_SESSION['CONTEXT_ID']
				.' and status_id in (1,2) '
				.' order by status_id desc, msg_type asc, insert_timestamp asc'
				.' Limit 1';
			    	
		switch(DB_TYPE) {
			case 'mysql':
				$sql = $mysql;
			break;
			case 'oci8':
				$sql = $oracle;
				$db->setLobAsString(true);
			break;
		}		
					
		try {
			$stmt = $db->query($sql);
			$result = $stmt->fetchAll();
		} catch (Zend_Exception $e) {
			ZitLog::writeToErrorLog("[SystemControl]", "Errors:$e->getTraceAsString() \n \n $sql", "Try|Catch block", $_SESSION['ZONE_ID']);
			GeneralError::systemError($xml);
		}
		
		switch(DB_TYPE) {
			case 'mysql':
				$id      = isset($result[0]->id) ? $result[0]->id : $zero;
#				$msgType = isset($result[0]->msg_type) ? $result[0]->msg_type : $zero;
				$msgType = 4;
				$XMLData = isset($result[0]->data) ? $result[0]->data : $zero;
			break;
			case 'oci8':
				$id = isset($result[0]->V) ? $result[0]->V : $zero;
				$msgType = isset($result[0]->T) ? $result[0]->T : $zero;
			break;
		}
		
//		ZitLog::writeToErrorLog("[SystemControl]", "id: $id --> msgType: $msgType\n\n", "Record Found?", $_SESSION['ZONE_ID']);
		
		
		if ($id != $zero){
			switch($msgType) {
				case 1:
					/*****
					ORACLE ONLY
					$SIF_ACK is used to set the status of the message read. The SIF Standard states that you should 
					set this value to 2 so that the item can be requested again if an error happens. Setting the item
					to a 3 will allow you not to send a SIF_Ack Message.
					*****/
					$response = new Responses($db);
					$where = "response_id = $id";
					$result2 = $response->fetchAll($where);
					foreach($result2 as $row) {
						switch(DB_TYPE) {
							case 'mysql':
								$status_id	  = $row->status_id;
								$responseData = $row->response_data;
							break;
							case 'oci8':
								$status_id	    = $row->STATUS_ID;
								$responseData   = $row->RESPONSE_DATA;
							break;
						}
					}
					$dom = new DomDocument();
		            $dom->loadXML($responseData);
		            $headerNode = $dom->getElementsByTagName('SIF_Header')->item(0);
		            $msgId = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
					
					$data = array(DBConvertor::convertCase('status_id') => $sif_response, DBConvertor::convertCase('msg_id') => $msgId);
					$n = $response->update($data, 'response_id = '.$id);
					
		            return $responseData;
					
				break;
				case 2:
					/*****
					ORACLE ONLY
					$SIF_ACK is used to set the status of the message read. The SIF Standard states that you should 
					set this value to 2 so that the item can be requested again if an error happens. Setting the item
					to a 3 will allow you not to send a SIF_Ack Message.
					*****/
					$request = new Requests($db);
					$where = "request_id = $id";
					$result = $request->fetchAll($where);
					foreach($result as $row) {
						switch(DB_TYPE) {
							case 'mysql':
								$status_id	  = $row->status_id;
								$requestData = $row->request_data;
							break;
							case 'oci8':
								$status_id	  = $row->STATUS_ID;
								$messageDataXML = $row->REQUEST_DATA;
							//	$requestData = $messageDataXML->read($messageDataXML->size());
								$requestData = $messageDataXML;
							break;
						}
					}

					$dom = new DomDocument();
		            $dom->loadXML($requestData);
		            $headerNode = $dom->getElementsByTagName('SIF_Header')->item(0);
		            $msgId = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
		
					$data = array(DBConvertor::convertCase('status_id') => $sif_request, DBConvertor::convertCase('msg_id') => $msgId);
			        $n = $request->update($data, 'request_id = '.$id);
		            
					return $requestData;
		
				break;
				case 3:
				/*****
				ORACLE ONLY
				$SIF_ACK is used to set the status of the message read. The SIF Standard states that you should 
				set this value to 2 so that the item can be requested again if an error happens. Setting the item
				to a 3 will allow you not to send a SIF_Ack Message.
				*****/
					$event = new Events($db);
					$where = "event_id = $id";
					$result = $event->fetchAll($where);
					foreach($result as $row) {
						switch(DB_TYPE) {
							case 'mysql':
								$status_id = $row->status_id;
								$eventData = $row->event_data;
							break;
							case 'oci8':
								$status_id = $row->STATUS_ID;
								$eventData = $row->EVENT_DATA;
							break;
						}
					}
					$dom = new DomDocument();
		            $dom->loadXML($eventData);
		            $headerNode = $dom->getElementsByTagName('SIF_Header')->item(0);
		            $msgId = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;

					$data = array(DBConvertor::convertCase('status_id') => $sif_event, DBConvertor::convertCase('msg_id') => $msgId);
					$n = $event->update($data, 'event_id = '.$id);
					
		            return $eventData;
					
				break;
				case 4:
					$message = new MessageQueues($db);
					/*****
					$SIF_ACK is used to set the status of the message read. The SIF Standard states that you should 
					set this value to 2 so that the item can be requested again if an error happens. Setting the item
					to a 3 will allow you not to send a SIF_Ack Message.
					*****/
					$data = array(DBConvertor::convertCase('status_id') => $sif_ack);
			        $n = $message->update($data, 'id = '.$id);
		            
					return $XMLData;
				break;
			}
			
		}else{
			return null;
		}

}

/*
    private function getFirstMessage($agent) {
        $db = ZitDBAdapter::getDBAdapter();

		$responses = new Responses($db);
		$where = 'agent_id_requester = '.$agent->agentId.' and (status_id = 1 or status_id = 2) and zone_id = '.$_SESSION["ZONE_ID"].' and context_id = '.$_SESSION["CONTEXT_ID"];

        $select = $responses->select()->where($where)->order(array('response_id'))->limit(1,0);
        $result = $responses->fetchAll($select);
		
		switch(DB_TYPE) {
			case 'mysql':
				$responseData = $result[0]->response_data;
		        $responseId   = intval($result[0]->response_id);
			break;
			case 'oci8':
				$responseDataXML = $result[0]->RESPONSE_DATA;
				$responseData = $responseDataXML->read($responseDataXML->size());
		        $responseId   = intval($result[0]->RESPONSE_ID);
			break;
		}
		
		$stmt->closeCursor();
		$stmt = Null;

        if($responseId != null) {
            $dom = new DomDocument();
            $dom->loadXML($responseData);
            $headerNode = $dom->getElementsByTagName('SIF_Header')->item(0);
            $msgId = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
            $data = array(DBConvertor::convertCase('status_id') => '2', DBConvertor::convertCase('msg_id') => $msgId);
            $n = $db->update(DBConvertor::convertCase('response'), $data, 'response_id = '.$responseId);
            return $responseData;
        }
        else {
            $select1 = $db->select()
                    ->from(array(DBConvertor::convertCase('r') => DBConvertor::convertCase('request')), array('request_data', 'request_id'))
                    ->where('agent_id_responder = '.$agent->agentId.' and (status_id = 1 or status_id = 2) and zone_id = '.$_SESSION["ZONE_ID"].' and context_id = '.$_SESSION["CONTEXT_ID"])
                    ->order(array('request_id'))
                    ->limit(1,0);

            $stmt1 = $select1->query();
            $result1 = $stmt1->fetchAll();
			$stmt1->closeCursor();
			$stmt1 = Null;
			
			switch(DB_TYPE) {
				case 'mysql':
					$requestData = $result1[0]->request_data;
		            $requestId   = intval($result1[0]->request_id);
				break;
				case 'oci8':
					$responseDataXML = $result[0]->RESPONSE_DATA;
					$responseData = $responseDataXML->read($responseDataXML->size());
		            $requestId   = intval($result1[0]->REQUEST_ID);
				break;
			}

            if($requestId != null) {
                $dom = new DomDocument();
                $dom->loadXML($requestData);
                $headerNode = $dom->getElementsByTagName('SIF_Header')->item(0);
                $msgId = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
                $data = array(DBConvertor::convertCase('status_id') => '2', DBConvertor::convertCase('msg_id') => $msgId);
                $n = $db->update(DBConvertor::convertCase('request'), $data, 'request_id = '.$requestId);
                return $requestData;
            }
            else {
                if($agent->frozen == 0) {
                    $select2 = $db->select();
                    $select2->from(array(DBConvertor::convertCase('e') => DBConvertor::convertCase('event')), array('event_data', 'event_id'));
					$select2->where('agent_id_rec = '.$agent->agentId.' and (status_id = 1 or status_id = 2) and zone_id = '.$_SESSION["ZONE_ID"].' and context_id = '.$_SESSION["CONTEXT_ID"]);
                    $select2->order(array('event_id'))
                            ->limit(1,0);
                    $stmt2 = $select2->query();
                    $result2 = $stmt2->fetchAll();
					$stmt2->closeCursor();
					$stmt2 = Null;

                    $eventData = $result2[0]->event_data;
                    $eventId   = intval($result2[0]->event_id);

					switch(DB_TYPE) {
						case 'mysql':
							$eventData = $result2[0]->event_data;
		                    $eventId   = intval($result2[0]->event_id);
						break;
						case 'oci8':
							$eventDataXML = $result2[0]->EVENT_DATA;
		                    $eventId   = intval($result2[0]->EVENT_ID);
							$eventData = $eventDataXML->read($eventDataXML->size());
						break;
					}

                    if($eventId != null) {
                        $dom = new DomDocument();
                        $dom->loadXML($eventData);
                        $headerNode = $dom->getElementsByTagName('SIF_Header')->item(0);
                        $msgId = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
                        $data = array(DBConvertor::convertCase('status_id') => '2', DBConvertor::convertCase('msg_id') => $msgId);
                        $n = $db->update(DBConvertor::convertCase('event'), $data, 'event_id = '.$eventId);
                    }
                    return $eventData;
                }
                else {
                    return null;
                }
            }
        }
    }

*/

    private function processWakeup() {
        $dom = $this->xmlDom;
        $headerNode   = $dom->getElementsByTagName('SIF_Header')->item(0);
        $originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
        $originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
        //$originalTimestamp  = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;

        $validSourceId = Agent::checkSourceId($originalSourceId);
        if(!$validSourceId) {
            RegisterError::invalidSourceId($originalSourceId, $originalMsgId);
        }
        else {
            $agent = new Agent($originalSourceId);
            if(!$agent->isRegistered()) {
                RegisterError::notRegisteredError($originalSourceId, $originalMsgId);
            }
            else {
                $agent->wakeup();
                $timestamp    = Utility::createTimestamp();
                $msgId        = Utility::createMessageId();
                XmlHelper::buildSuccessMessage($msgId,
                        $timestamp,
                        $originalSourceId,
                        $originalMsgId,
                        0,
                        $originalMsg = null,
                        $desc = null);
            }
        }
    }

    private function processSleep() {
        $dom = $this->xmlDom;
        $headerNode   = $dom->getElementsByTagName('SIF_Header')->item(0);
        $originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
        $originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
        //$originalTimestamp  = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;

        $validSourceId = Agent::checkSourceId($originalSourceId);
        if(!$validSourceId) {
            RegisterError::invalidSourceId($originalSourceId, $originalMsgId);
        }
        else {
            $agent = new Agent($originalSourceId);
            if(!$agent->isRegistered()) {
                RegisterError::notRegisteredError($originalSourceId, $originalMsgId);
            }
            else {
                $agent->putToSleep();
                $timestamp    = Utility::createTimestamp();
                $msgId        = Utility::createMessageId();
                XmlHelper::buildSuccessMessage($msgId,
                        $timestamp,
                        $originalSourceId,
                        $originalMsgId,
                        0,
                        $originalMsg = null,
                        $desc = null);
            }
        }
    }

    private function processPing() {
	    $_SESSION['SIF_MESSAGE_TYPE'] = 10;

        $dom = $this->xmlDom;
        $headerNode   = $dom->getElementsByTagName('SIF_Header')->item(0);
        $originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
        $originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
        //$originalTimestamp  = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;
		
        $validSourceId = Agent::checkSourceId($originalSourceId);
        $agent = new Agent($originalSourceId);
        if(!$validSourceId) {
            RegisterError::invalidSourceId($originalSourceId, $originalMsgId);
        }
        else {
            $timestamp    = Utility::createTimestamp();
            $msgId        = Utility::createMessageId();

            if(ZIT::isAsleep()) {
                XmlHelper::buildSuccessMessage($msgId,
                        $timestamp,
                        $originalSourceId,
                        $originalMsgId,
                        8,
                        $originalMsg = null,
                        $desc = 'Receiver is sleeping');
            }
            else {
                XmlHelper::buildSuccessMessage($msgId,
                        $timestamp,
                        $originalSourceId,
                        $originalMsgId,
                        0,
                        $originalMsg = null,
                        $desc = null);
            }
        }
    }

    private function processZoneStatus() {

        $dom = $this->xmlDom;
        $headerNode   = $dom->getElementsByTagName('SIF_Header')->item(0);
        $originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
        $originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;

        $validSourceId = Agent::checkSourceId($originalSourceId);
        if(!$validSourceId) {
            RegisterError::invalidSourceId($originalSourceId, $originalMsgId);
        }
        else {
            $dom = new DOMDocument('1.0', 'iso-8859-1');

            $zoneStatusNode = $dom->createElement('SIF_ZoneStatus');
            $zoneStatusNode->setAttribute('ZoneId',$_SESSION['ZONE_NAME']);
            $dom->appendChild($zoneStatusNode);

            $zoneNameNode   = $dom->createElement('SIF_Name');
            $zoneNameNode->appendChild($dom->createTextNode($_SESSION['ZONE_DESC']));
            $zoneStatusNode->appendChild($zoneNameNode);

            $sifVendorNode = $dom->createElement('SIF_Vendor');

            $sifVendorName = $dom->createElement('SIF_Name');
            $sifVendorName->appendChild($dom->createTextNode(VENDOR_NAME));
            $sifVendorNode->appendChild($sifVendorName);

            $sifVendorProduct = $dom->createElement('SIF_Product');
            $sifVendorProduct->appendChild($dom->createTextNode(VENDOR_PRODUCT));
            $sifVendorNode->appendChild($sifVendorProduct);

            $sifVendorVersion = $dom->createElement('SIF_Version');
            $sifVendorVersion->appendChild($dom->createTextNode(VENDOR_VERSION));
            $sifVendorNode->appendChild($sifVendorVersion);

            $zoneStatusNode->appendChild($sifVendorNode);

            $sifProvidersNode = $dom->createElement('SIF_Providers');
            $sifProvidersNode = $this->buildProviders($sifProvidersNode,$dom);
            $zoneStatusNode->appendChild($sifProvidersNode);

            $sifSubscribersNode = $dom->createElement('SIF_Subscribers');
            $sifSubscribersNode = $this->buildSubscribers($sifSubscribersNode, $dom);
            $zoneStatusNode->appendChild($sifSubscribersNode);

            $sifNodes = $dom->createElement('SIF_SIFNodes');
            $sifSubscribersNode = $this->buildNodeList($sifNodes, $dom);
            $zoneStatusNode->appendChild($sifNodes);

            /*$sifSupportedAuth = $dom->createElement('SIF_SupportedAuthentication');
			$sifProtocolName  = $dom->createElement('SIF_ProtocolName');
			$sifSupportedAuth->appendChild($sifProtocolName);
			$zoneStatusNode->appendChild($sifSupportedAuth);*/

            $sifSupportedProtocols = $dom->createElement('SIF_SupportedProtocols');
            $sifProtocol           = $dom->createElement('SIF_Protocol');
            $sifProtocol->setAttribute('Type','HTTP');
            $sifProtocol->setAttribute('Secure','No');
            $sifUrl                = $dom->createElement('SIF_URL');
            $sifUrl->appendChild($dom->createTextNode(ZIT::getZitUrl()));
            $sifProtocol->appendChild($sifUrl);
            $sifSupportedProtocols->appendChild($sifProtocol);
            $zoneStatusNode->appendChild($sifSupportedProtocols);

            $sifSupportedVersions = $dom->createElement('SIF_SupportedVersions');
            $versions = ZIT::getVersions();
            foreach($versions as $version) {
                $sifVersion = $dom->createElement('SIF_Version');
                $sifVersion->appendChild($dom->createTextNode($version['VERSION']));
                $sifSupportedVersions->appendChild($sifVersion);
            }
            $zoneStatusNode->appendChild($sifSupportedVersions);

            $sifAdminUrl = $dom->createElement('SIF_AdministrationURL');
            $sifAdminUrl->appendChild($dom->createTextNode(ZIT::getAdminUrl()));
            $zoneStatusNode->appendChild($sifAdminUrl);

            $sifContexts = $dom->createElement('SIF_Contexts');
            $sifContexts = $this->buildContexts($sifContexts, $dom);
            $zoneStatusNode->appendChild($sifContexts);

            $xml = $dom->saveXML($dom->documentElement);

            $msgId = Utility::createMessageId();
            $timestamp    = Utility::createTimestamp();
            XmlHelper::buildSuccessMessage($msgId,
                    $timestamp,
                    $originalSourceId,
                    $originalMsgId,
                    0,
                    $originalMsg = $xml,
                    $desc = null);
        }
    }//end processZoneStatus

    private function buildContexts($domElement, $dom) {
        $db = ZitDBAdapter::getDBAdapter();
		$contexts = new Contexts($db);
 //       $query = "select context_desc from context";
 //       $result = $db->fetchAll($query);
        $result = $contexts->fetchAll();
		foreach($result as $row) {
            $contextNode = $dom->createElement('SIF_Context');
			switch(DB_TYPE) {
				case 'mysql':
					$contextNode->appendChild($dom->createTextNode($row->context_desc));
				break;
				case 'oci8':
					$contextNode->appendChild($dom->createTextNode($row->CONTEXT_DESC));
				break;
			}
			$domElement->appendChild($contextNode);
        }
        return $domElement;
    }

    private function buildNodeList($domElement, $dom) {
        $db = ZitDBAdapter::getDBAdapter();

        $query = "select
				  ar.agent_id,
				  ar.callback_url,
				  am.mode_desc,
				  ar.asleep,
				  ar.protocol_type,
				  ar.secure,
				  ar.sif_version,
				  ar.maxbuffersize,
				  a.agent_name,
				  a.source_id
				  from 
				  ".DBConvertor::convertCase('agent_modes')." am, 
				  ".DBConvertor::convertCase('agent_registered')." ar, 
				  ".DBConvertor::convertCase('agent')." a
				  where 
				  ar.zone_id = ".$_SESSION['ZONE_ID']."
				  and
				  ar.context_id = ".$_SESSION['CONTEXT_ID']."
				  and
				  ar.agent_mode_id = am.agent_mode_id
				  and a.agent_id = ar.agent_id
				  and ar.unregister_timestamp is null";
        $result = $db->fetchAll($query);
        foreach($result as $row) {
	
			switch(DB_TYPE) {
				case 'mysql':
					$agt = $row->agent_id;
					$sid = $row->source_id;
					$desc = $row->mode_desc;
					$type = $row->protocol_type;
					$url = $row->callback_url;
					$ver = $row->sif_version;
					$size = $row->maxbuffersize;
					$secure = $row->secure;
					$sleep = $row->asleep;
				break;
				case 'oci8':
					$agt = $row->AGENT_ID;
					$sid = $row->SOURCE_ID;
					$desc = $row->MODE_DESC;
					$type = $row->PROTOCOL_TYPE;
					$url = $row->CALLBACK_URL;
					$ver = $row->SIF_VERSION;
					$size = $row->MAXBUFFERSIZE;
					$secure = $row->SECURE;
					$sleep = $row->ASLEEP;
				break;
			}
			
            $sifNode = $dom->createElement('SIF_SIFNode');
            $sifNode->setAttribute('Type','Agent');

            $sifName = $dom->createElement('SIF_Name');
            $sifName->appendChild($dom->createTextNode($agt));

            $sifSourceId = $dom->createElement('SIF_SourceId');
            $sifSourceId->appendChild($dom->createTextNode($sid));

            $sifMode = $dom->createElement('SIF_Mode');
            $sifMode->appendChild($dom->createTextNode($desc));

            $sifProtocol = $dom->createElement('SIF_Protocol');
            $sifProtocol->setAttribute('Type',$type);
            if($secure == 1) {
                $sifProtocol->setAttribute('Secure','Yes');
            }else {
                $sifProtocol->setAttribute('Secure','No');
            }
            $sifUrl = $dom->createElement('SIF_URL');
            $sifUrl->appendChild($dom->createTextNode(urldecode($url)));
            $sifProtocol->appendChild($sifUrl);

            $sifVersionList = $dom->createElement('SIF_VersionList');
            $sifVersion = $dom->createElement('SIF_Version');
            $sifVersion->appendChild($dom->createTextNode($ver));
            $sifVersionList->appendChild($sifVersion);

            $sifBuffer = $dom->createElement('SIF_MaxBufferSize');
            $sifBuffer->appendChild($dom->createTextNode($size));

            $sifSleeping = $dom->createElement('SIF_Sleeping');
            if($sleep == 1) {
                $sifSleeping->appendChild($dom->createTextNode('Yes'));
            }else {
                $sifSleeping->appendChild($dom->createTextNode('No'));
            }

            $sifNode->appendChild($sifName);
            $sifNode->appendChild($sifSourceId);
            $sifNode->appendChild($sifMode);
            $sifNode->appendChild($sifProtocol);
            $sifNode->appendChild($sifVersionList);
            $sifNode->appendChild($sifBuffer);
            $sifNode->appendChild($sifSleeping);
            $domElement->appendChild($sifNode);
        }//get all agents
        return $domElement;
    }//end buildNodeList

    private function buildSubscribers($domElement, $dom) {
        $db = ZitDBAdapter::getDBAdapter();

        $query = "select distinct
				  a.agent_id,
				  a.source_id
				  from ".DBConvertor::convertCase('agent_subscriptions')." s, 
				  ".DBConvertor::convertCase('agent')." a 
				  where
				  s.zone_id = ".$_SESSION['ZONE_ID']."
				  and
				  s.context_id = ".$_SESSION['CONTEXT_ID']."
				  and
				  a.agent_id = s.agent_id";
				
				
        $result = $db->fetchAll($query);
        foreach($result as $row) {
            $sifSubscriberNode = $dom->createElement('SIF_Subscriber');

			switch(DB_TYPE) {
				case 'mysql':
					$sifSubscriberNode->setAttribute('SourceId',$row->source_id);
					$agt = $row->agent_id;
				break;
				case 'oci8':
					$sifSubscriberNode->setAttribute('SourceId',$row->SOURCE_ID);
					$agt = $row->AGENT_ID;
				break;
			}
            
            $query2 = "select
					   o.object_name,
					   c.context_desc
					   from
					   ".DBConvertor::convertCase('data_object')." o,
					   ".DBConvertor::convertCase('agent_subscriptions')." s,
					   ".DBConvertor::convertCase('context')." c
					   where
					   s.zone_id = ".$_SESSION['ZONE_ID']."
					   and
					   s.context_id = ".$_SESSION['CONTEXT_ID']."
					   and
					   s.agent_id = ".$agt."
					   and o.object_id = s.object_type_id
					   and s.context_id = c.context_id";
            $objectListNode = $dom->createElement('SIF_ObjectList');
            $sifSubscriberNode->appendChild($objectListNode);
            $result2 = $db->fetchAll($query2);
            $currentObjectName = '';
            $oldNode = null;
            foreach($result2 as $row2) {
				switch(DB_TYPE) {
					case 'mysql':
						$objectName  = $row2->object_name;
		                $contextDesc = $row2->context_desc;
					break;
					case 'oci8':
						$objectName  = $row2->OBJECT_NAME;
		                $contextDesc = $row2->CONTEXT_DESC;
					break;
				}

                if($objectName != $currentObjectName) {
                    $sifObjectNode = $dom->createElement('SIF_Object');
                    $sifObjectNode->setAttribute('ObjectName',$objectName);
                    $sifContextsNode = $dom->createElement('SIF_Contexts');
                }
                else {
                    $sifContextsNode = $oldNode;
                }

                $sifContextNode = $dom->createElement('SIF_Context');
                $text = $dom->createTextNode($contextDesc);
                $sifContextNode->appendChild($text);
                $sifContextsNode->appendChild($sifContextNode);

                if($objectName != $currentObjectName) {
                    $sifObjectNode->appendChild($sifContextsNode);
                    $objectListNode->appendChild($sifObjectNode);
                }
                $currentObjectName = $objectName;
                $oldNode = $sifContextsNode;


            }//get all objects
            $domElement->appendChild($sifSubscriberNode);
        }//get all agent providers
        return $domElement;
    }//buildSubscribers

    private function buildProviders($domElement, $dom) {
        $db = ZitDBAdapter::getDBAdapter();

        $query = "select distinct
				 a.agent_id,
				 a.source_id
				 from
				 ".DBConvertor::convertCase('agent_provisions')." p,
				 ".DBConvertor::convertCase('agent')." a
				 where p.zone_id = ".$_SESSION['ZONE_ID']."
				 and p.context_id = ".$_SESSION['CONTEXT_ID']."
				 and a.agent_id = p.agent_id";

        $result = $db->fetchAll($query);
        foreach($result as $row) {
            $sifProviderNode = $dom->createElement('SIF_Provider');

			switch(DB_TYPE) {
				case 'mysql':
					$sifProviderNode->setAttribute('SourceId',$row->source_id);
					$agt = $row->agent_id;
				break;
				case 'oci8':
					$sifProviderNode->setAttribute('SourceId',$row->SOURCE_ID);
					$agt = $row->AGENT_ID;
				break;
			}

            $query2 = "select
					   o.object_name,
					   c.context_desc
					   from
					   ".DBConvertor::convertCase('data_object')." o,
					   ".DBConvertor::convertCase('agent_provisions')." a,
					   ".DBConvertor::convertCase('context')." c
					   where
					   a.zone_id = ".$_SESSION['ZONE_ID']."
					   and
					   a.context_id = ".$_SESSION['CONTEXT_ID']."
					   and
					   a.agent_id = ".$agt."
					   and o.object_id = a.object_type_id
					   and a.context_id = c.context_id order by o.object_id";
					
            $objectListNode = $dom->createElement('SIF_ObjectList');
            $sifProviderNode->appendChild($objectListNode);
            $result2 = $db->fetchAll($query2);
            $currentObjectName = '';
            $oldNode = null;
            foreach($result2 as $row2) {
                
				switch(DB_TYPE) {
					case 'mysql':
						$objectName  = $row2->object_name;
		                $contextDesc = $row2->context_desc;
					break;
					case 'oci8':
						$objectName  = $row2->OBJECT_NAME;
		                $contextDesc = $row2->CONTEXT_DESC;
					break;
				}

                if($objectName != $currentObjectName) {
                    $sifObjectNode = $dom->createElement('SIF_Object');
                    $sifObjectNode->setAttribute('ObjectName',$objectName);
                    $sifExtednedQueryNode = $dom->createElement('SIF_ExtendedQuerySupport');
                    $sifExtednedQueryNode->appendChild($dom->createTextNode('false'));
                    $sifObjectNode->appendChild($sifExtednedQueryNode);
                    $sifContextsNode = $dom->createElement('SIF_Contexts');
                }
                else {
                    $sifContextsNode = $oldNode;
                }

                $sifContextNode = $dom->createElement('SIF_Context');
                $text = $dom->createTextNode($contextDesc);
                $sifContextNode->appendChild($text);
                $sifContextsNode->appendChild($sifContextNode);

                if($objectName != $currentObjectName) {
                    $sifObjectNode->appendChild($sifContextsNode);
                    $objectListNode->appendChild($sifObjectNode);
                }
                $currentObjectName = $objectName;
                $oldNode = $sifContextsNode;

            }//get all objects
            $domElement->appendChild($sifProviderNode);
        }//get all agent providers
        return $domElement;
    }//end buildProviders
}//end class

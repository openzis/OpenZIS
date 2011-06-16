<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement.
*/

class Request {
	
	var $msgObj;
    var $dom;
    var $originalSourceId;
    var $originalMsgId;
	var $headerNode;

    public function Request($m) {
        $this->dom = $m->dom;
		$this->msgObj = $m;
        $this->processRequest($m);
		
    }

    private function processRequest($m) {

        $dom = $m->dom;

        $this->originalSourceId = $m->sourceId;
		$originalSourceId = $m->sourceId;
        $this->originalMsgId = $m->msgId;
        $originalMsgId = $m->msgId;
		$headerNode = $m->headerNode;
		$this->headerNode = $headerNode;

        $validSourceId = Agent::checkSourceId($originalSourceId);
        if(!$validSourceId) {
            RegisterError::invalidSourceId($agent->sourceId, $originalMsgId);
            exit;
        }
        else {
            $agent = new Agent($originalSourceId);
            if(!$agent->isRegistered()) {
                RegisterError::notRegisteredError($originalSourceId, $originalMsgId);
                exit;
			}

            else {
                $sentBufferSize = $dom->getElementsByTagName('SIF_MaxBufferSize')->item(0)->nodeValue;
                if(!Agent::bufferSizeAllowed($sentBufferSize, $agent->agentId)) {
                    $timestamp    = Utility::createTimestamp();
                    $msgId        = Utility::createMessageId();
                    XmlHelper::buildSuccessMessage($msgId,
                            $timestamp,
                            $this->originalSourceId,
                            $this->originalMsgId,
                            0,
                            $originalMsg = null,
                            $desc = null);
                }
                else {
					$messageNode = $dom->getElementsByTagName('SIF_Message');
					$len = $messageNode->item(0)->childNodes->item(0)->childNodes->length - 1;
					$Mode= $messageNode->item(0)->childNodes->item(0)->childNodes->item($len)->nodeName;
					if ($Mode == 'SIF_Query'){
						$sifObjectNode = $dom->getElementsByTagName('SIF_QueryObject')->item(0);
	                    $objectName = $sifObjectNode->getAttribute('ObjectName');	
					}
					else {
						$sifObjectNode = $dom->getElementsByTagName('SIF_From')->item(0);
	                    $objectName = $sifObjectNode->getAttribute('ObjectName');
					}

                    if(!DataObject::objectExists($objectName)) {
                        RequestError::invalidObject($originalSourceId, $originalMsgId, $objectName);
                        exit;
                    }
                    else {
                        if(!DataObject::allowedToRequest($agent->agentId,$objectName)) {
                            ProvisionError::invalidPermissionToRequest($originalSourceId, $originalMsgId, $objectName);
                            exit;
                        }
                        else {
                            $sifDestinationId = $dom->getElementsByTagName('SIF_DestinationId');
                            if($sifDestinationId->length != 0) {
                                $sourceId = $sifDestinationId->item(0)->nodeValue;
                                if(!DataObject::validResponder($objectName, $sourceId)) {
                                    RequestError::invalidProvider($originalSourceId, $originalMsgId);
                                }
                                else {
                                    $this->setupRequest($objectName, $sourceId, $agent);
                                }//check if destination id is valid
                            }
                            else {
                                $this->setupRequest($objectName, $sourceId=null, $agent);
                            }//check if there is a destination id
                        }//check if allowed to request
                    }//check if allowed to request
                }//check object exist
            }//check if registered
        }//check sourceId
    }// end processRequest



    private function setupRequest($objectName, $sourceId, $agent) {

        $db = ZitDBAdapter::getDBAdapter();
		$dom = $this->dom;
        $providerId = null;
        
        if($sourceId != null){
            $agent = new Agent($sourceId);
            $providerId = $agent->agentId;
        } else {
            $providerId = DataObject::getProviderId($objectName);
        }

        if($providerId == 0) {
            RequestError::invalidProvider($this->originalSourceId,$this->originalMsgId);
        } else {
            $error = false;
            $sifMessageNode = $dom->getElementsByTagName('SIF_Message')->item(0);

            $eventXML = $dom->saveXML($sifMessageNode);
            $eventXML = str_replace('xmlns:sif="http://www.sifinfo.org/infrastructure/2.x" ','',$eventXML);
            $eventXML = str_replace('xmlns="http://www.sifinfo.org/infrastructure/2.x" ','',$eventXML);
            $eventXML = str_replace('xmlns:sif="http://www.sifinfo.org/infrastructure/1.x" ','',$eventXML);
            $eventXML = str_replace('xmlns="http://www.sifinfo.org/infrastructure/1.x" ','',$eventXML);
            $eventXML = str_replace('xmlns="http://www.sifinfo.org/uk/infrastructure/2.x" ','',$eventXML);
            $eventXML = str_replace('xmlns="http://www.sifinfo.org/au/infrastructure/2.x" ','',$eventXML);
			$eventXML = str_replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ','',$eventXML);

            $messageId  = $dom->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
            $bufferSize = $dom->getElementsByTagName('SIF_MaxBufferSize')->item(0)->nodeValue;

			/*
			PUSH all messages to the ZONE Version:
			DO NOT Check Message Version:
			*/
            $version    = $_SESSION['ZONE_VERSION'];

            $query = "select
                            agent_registered.sif_version,
                            agent_registered.maxbuffersize,
                            agent_registered.agent_mode_id,
                            agent.source_id
                      from ".DBConvertor::convertCase('agent_registered')." 
                      inner join ".DBConvertor::convertCase('agent')." on agent.agent_id = agent_registered.agent_id
                      where agent_registered.agent_id = $providerId and agent_registered.unregister_timestamp is null
                      and   agent_registered.zone_id = ".$_SESSION["ZONE_ID"]."
                      and   agent_registered.context_id = ".$_SESSION["CONTEXT_ID"];

            $result = $db->fetchAll($query);
			
				switch(DB_TYPE) {
					case 'mysql':
						$agentModeId =   $result[0]->agent_mode_id;
						$maxbuffersize = intval($result[0]->maxbuffersize);
						$source_id = 	 $result[0]->source_id;
						$sif_version = 	 $result[0]->sif_version;
					break;
					case 'oci8':
						$agentModeId =   $result[0]->AGENT_MODE_ID;
						$maxbuffersize = intval($result[0]->MAXBUFFERSIZE);
						$source_id =     $result[0]->SOURCE_ID;
						$sif_version =   $result[0]->SIF_VERSION;
					break;
				}

            $messageSize = strlen($eventXML);

            if($messageSize > $maxbuffersize) {
                $error = true;
				$header = $this->headerNode;
                SifLogEntry::CreateSifLogEvents($header,
                        '4',
                        '2',
                        'Buffer size of agent '.$source_id.' is too small to recieve this request [size : '.$maxbuffersize.']'
                );
            }

/*
            if($version != '2.*') {
                if($version != $sif_version) {
                    $header = $this->headerNode;
					$error = true;
                    SifLogEntry::CreateSifLogEvents($header,
                            '4',
                            '4',
                            'Version in request not supported by agent '.$source_id
                    );
                }
            } else {
				$version = $_SESSION['ZONE_VERSION'];
			}
*/
            if(!$error) {
                $dataObject = new DataObject($objectName);

#				$request = new Requests($db);
#                $data = array(
#                        DBConvertor::convertCase('request_msg_id')      => $messageId,
#                        DBConvertor::convertCase('request_timestamp')   => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
#                        DBConvertor::convertCase('agent_id_requester')  => $agent->agentId,
#                        DBConvertor::convertCase('agent_id_responder')  => intval($providerId),
#                        DBConvertor::convertCase('request_data')        => $eventXML,
#                        DBConvertor::convertCase('max_buffer_size')     => $bufferSize,
#                        DBConvertor::convertCase('version')				=> $version,
#                        DBConvertor::convertCase('agent_mode_id')		=> intval($agentModeId),
#                        DBConvertor::convertCase('zone_id')         	=> $_SESSION["ZONE_ID"],
#                        DBConvertor::convertCase('context_id')			=> $_SESSION["CONTEXT_ID"]
#                );
#                $request->insert($data);

				$messagequeue = new MessageQueues($db);
				$data = null;
				$data = array(
                        DBConvertor::convertCase('msg_id')     			=> $messageId,
						DBConvertor::convertCase('msg_type')     		=> 1,
						DBConvertor::convertCase('status_id')     		=> 1,
                        DBConvertor::convertCase('insert_timestamp')	=> new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
                        DBConvertor::convertCase('agt_id_in')			=> intval($agent->agentId),
                        DBConvertor::convertCase('agt_id_out')			=> intval($providerId),
                        DBConvertor::convertCase('data')				=> $eventXML,
                        DBConvertor::convertCase('maxbuffersize')		=> intval($bufferSize),
                        DBConvertor::convertCase('version')            	=> $version,
                        DBConvertor::convertCase('agt_mode_id')      	=> intval($agentModeId),
                        DBConvertor::convertCase('zone_id')         	=> $_SESSION["ZONE_ID"],
                        DBConvertor::convertCase('context_id')      	=> $_SESSION["CONTEXT_ID"]
                );
                $messagequeue->insert($data);
				
            }
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
        }//invalid provider check
    }//end setupRequest function
}//end Request class

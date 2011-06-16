<?php

/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OPENZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OPENZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/



class Response {
    var $dom;
	var $xmlStr;
    var $originalSourceId;
    var $originalMsgId;
	var $xslt;

    public function Response($m) {
        $this->dom = $m->dom;
		$this->xmlStr = $m->xmlStr;

        $this->processResponse($m);
    }

    private function processResponse($m) {

        $dom = $m->dom;

        $headerNode          = $m->headerNode;
        $originalMsgId       = $m->msgId;
		$this->originalMsgId = $m->msgId;
        $originalSourceId    = $m->sourceId;
        $version             = $m->version;

        $this->originalSourceId = $originalSourceId;
        $this->originalMsgId = $originalMsgId;

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
                $msgId = $dom->getElementsByTagName('SIF_RequestMsgId')->item(0)->nodeValue;
                if(!RequestObject::validRequestMsgId($msgId)) {
                    RequestError::invalidMsgId($originalSourceId, $originalMsgId);
                }
                else {
                    $requestObject = new RequestObject($msgId);
                    $sifResponseNode = $dom->getElementsByTagName('SIF_Response')->item(0);
                    $size = strlen($dom->saveXML($sifResponseNode));
                    if(!$requestObject->compareBufferSize($size)) {
                        $categoryCode = "8";
                        $sifCode      = "11";
                        $shortDesc    = "Response Error";
                        $longDesc     = "SIF_Response is larger than requested SIF_MaxBufferSize";
                        $this->createResponseError($agent, $msgId, $categoryCode, $sifCode, $shortDesc, $longDesc);
                        SifLogEntry::CreateSifLogEvents($dom->saveXml($headerNode),
                                4,
                                2,
                                'Invalid buffersize for response to agent '.$agent->sourceId.' request'
                        );
                        RequestError::invalidBufferSize($originalSourceId, $originalMsgId);
                    }else {
                        $destinationId = $dom->getElementsByTagName('SIF_DestinationId')->item(0)->nodeValue;
                        if(!$requestObject->compareDestinationId($destinationId)) {
                            $categoryCode = "8";
                            $sifCode      = "14";
                            $shortDesc    = "Response Error";
                            $longDesc     = "SIF_DestinationId does not match SIF_SourceId from SIF_Request";
                            $this->createResponseError($agent, $msgId, $categoryCode, $sifCode, $shortDesc, $longDesc);
                            SifLogEntry::CreateSifLogEvents($dom->saveXml($headerNode),
                                    4,
                                    1,
                                    'Invalid destinationId for response to agent '.$agent->sourceId.' request'
                            );
                            RequestError::invalidDestination($originalSourceId, $originalMsgId);
                        }
                        else {
                            if(RequestObject::validMessageVersion($msgId, $version)) {
                                if(ResponseObject::responseExist($msgId)) {
                                    $responseObject = new ResponseObject($msgId);
                                    $packetNum = $dom->getElementsByTagName('SIF_PacketNumber')->item(0)->nodeValue;
                                    if($packetNum != $responseObject->nextPacketNum) {
                                        $categoryCode = "8";
                                        $sifCode      = "12";
                                        $shortDesc    = "SIF_PacketNumber is invalid in SIF_Response";
                                        $longDesc     = 'Invalid packet number of '.$packetNum.' was expecting '.$responseObject->nextPacketNum.'for response to agent '.$agent->sourceId.' request';
                                        //$this->createResponseError($agent, $msgId, $categoryCode, $sifCode, $shortDesc, $longDesc);
                                        RequestError::invalidPacketNum($originalSourceId, $originalMsgId, $longDesc);
                                    }
                                    else {
                                        $this->setupResponseOld($agent, $msgId, $responseObject);
                                    }
                                }
                                else {
                                    $packetNum = $dom->getElementsByTagName('SIF_PacketNumber')->item(0)->nodeValue;
                                    if($packetNum != 1) {
                                        $categoryCode = "8";
                                        $sifCode      = "12";
                                        $shortDesc    = "Response Error";
                                        $longDesc     = 'Invalid packet number of '.$packetNum.' was expecting 1 for response to agent '.$agent->sourceId.' request';
                                        RequestError::invalidPacketNum($originalSourceId, $originalMsgId, $longDesc);
                                    }
                                    else {
                                        $this->setupResponseNew($agent, $msgId, $m);
                                    }//check if packetnum is 1
                                }//check if response with more packets
                            }
                            else {								

                                $categoryCode = "8";
                                $sifCode      = "13";
                                $shortDesc    = "Response Error";
                                $longDesc     = "SIF_Response does not match any SIF_Version from SIF_Request.";
                                $this->createResponseError($agent, $msgId, $categoryCode, $sifCode, $shortDesc, $longDesc);
                                RequestError::invalidVersion($originalSourceId, $originalMsgId);
                            }//check if version is valid
                        }// check if valid destination
                    }//check if valid buffer
                }//check if valid request message id
            }//check if registered
        }//check sourceId
    }// end processRequest



    private function createResponseError($agent, $msgId, $errorCategory, $errorCode, $shortDesc, $SIF_ExtendedDesc) {
        $dom = $this->dom;
        $db = Zend_Registry::get('my_db');
        $responseObject = new ResponseObject($msgId);
        $agentModeId = RequestObject::getRequesterAgentMode($msgId);
        $requesterId = RequestObject::getRequesterId($msgId);
        $packetnum = 1;

        if($responseObject->nextPacketNum != null && $responseObject->nextPacketNum != '') {
            $packetnum == $responseObject->nextPacketNum;
        }

        $responseXml = XmlHelper::buildSifReponse(Utility::createMessageId(),
                Utility::createTimestamp(),
                Agent::getAgentSourceId($requesterId),
                $msgId,
                $packetnum,
                $errorCategory,
                $errorCode,
                $shortDesc,
                $SIF_ExtendedDesc);

        $responseXml = str_replace('xmlns="http://www.sifinfo.org/infrastructure/2.x"','',$responseXml);
        $responseXml = str_replace('xmlns="http://www.sifinfo.org/infrastructure/1.x"','',$responseXml);
        $responseXml = str_replace('xmlns="http://www.sifinfo.org/uk/infrastructure/2.x"','',$responseXml);
    }



    private function setupResponseOld($agent, $msgId, $responseObject) {
		$empty = null;
        $dom = $this->dom;
        $db = Zend_Registry::get('my_db');
        $requesterId = RequestObject::getRequesterId($msgId);
        $agentModeId = RequestObject::getRequesterAgentMode($msgId);

        try {
            $sifObjectDataNode = $dom->getElementsByTagName('SIF_ObjectData')->item(0);
            $children          = $sifObjectDataNode->childNodes;

			if(is_object($children)){
				$objectName = isset($children->item(0)->nodeName) ? $children->item(0)->nodeName : null;
			} else {
				$objectName = null;
				ZitLog::writeToErrorLog('[SIF_ObjectData Error]', 'No SIF_ObjectData found from agent = '. $agent->sourceId, $this->xmlStr );
				GeneralError::systemError($this->xmlStr);
			}
            $dataObject        = new DataObject($objectName);
        }
        catch(Exception $e) {
			ZitLog::writeToErrorLog('[Error processing message]', "DataObject Name: ".$objectName."\nError Message\n".$e->getMessage()."\n\nStack Trace\n".$e->getTraceAsString().' '. $this->xmlStr, 'Process Message', $_SESSION['ZONE_ID']);
			GeneralError::systemError($this->xmlStr);
        }

        $sifMessageNode = $dom->getElementsByTagName('SIF_Message')->item(0);
        $responseXml = $dom->saveXML($sifMessageNode);
        $responseXml = str_replace('xmlns="http://www.sifinfo.org/infrastructure/1.x"','',$responseXml);
		$responseXml = str_replace('xmlns:sif="http://www.sifinfo.org/infrastructure/1.x"','',$responseXml);
        $responseXml = str_replace('xmlns="http://www.sifinfo.org/infrastructure/2.x" ','',$responseXml);
        $responseXml = str_replace('xmlns="http://www.sifinfo.org/uk/infrastructure/2.x" ','',$responseXml);
		$responseXml = str_replace('xmlns="http://www.sifinfo.org/au/infrastructure/2.x" ','',$responseXml);
		$responseXml = str_replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ','',$responseXml);
        $nextPacketNum = $responseObject->nextPacketNum;
        $nextPacketNum = $nextPacketNum + 1;

		try {
			
			$permissions = new AgentPermissions($db);
			$where = "object_id = $dataObject->objectId and agent_id = $requesterId and zone_id = ".$_SESSION['ZONE_ID']." and context_id = ".$_SESSION['CONTEXT_ID']." ";
			$result = $permissions->fetchAll($where);
			
			switch(DB_TYPE) {
				case 'mysql':
					$this->xslt        = intval($result[0]->xslt);
				break;
				case 'oci8':
					$this->xslt        = intval($result[0]->xslt);
				break;
			}
					
			if ($this->xslt != null) {
				$xsltpro 	 	= new XSLTProcessor();
				$XSL		= new DOMDocument();
				$XSL->loadXML($this->xslt);
				$xsltpro->importStylesheet( $XSL );
				$XML		= new DOMDocument();
				$XML->loadXML($responseXml);
				$responseXml = $xsltpro->transformToXML( $XML );
			}
			
			$responseXml = str_replace('<?xml version="1.0"?>'."\n",'',$responseXml);
			
		} catch (Zend_Exception $e) {
			ZitLog::writeToErrorLog('[Error filtering message]', "DataObject Name: ".$objectName."\nError Message\n".$e->getMessage()."\n\nStack Trace\n".$e->getTraceAsString().' '. $this->xmlStr, 'Process Message', $_SESSION['ZONE_ID']);
			GeneralError::systemError($this->xmlStr);			
		}

		$messagequeue = new MessageQueues($db);
		$data = null;
		$data = array(
                DBConvertor::convertCase('msg_id')	     		=> $this->originalMsgId,
				DBConvertor::convertCase('ref_msg_id')     		=> $msgId,
				DBConvertor::convertCase('msg_type')     		=> 2,
                DBConvertor::convertCase('insert_timestamp')	=> new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
                DBConvertor::convertCase('agt_id_in')			=> $agent->agentId,
                DBConvertor::convertCase('agt_id_out')			=> intval($requesterId),
                DBConvertor::convertCase('data')				=> $responseXml,
				DBConvertor::convertCase('next_packet_num')     => $nextPacketNum,
				DBConvertor::convertCase('status_id')           => '1',
				DBConvertor::convertCase('version')          	=> $_SESSION['ZONE_VERSION'],
                DBConvertor::convertCase('agt_mode_id')      	=> intval($agentModeId),
                DBConvertor::convertCase('zone_id')         	=> $_SESSION["ZONE_ID"],
                DBConvertor::convertCase('context_id')      	=> $_SESSION["CONTEXT_ID"]
        );
        $messagequeue->insert($data);

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



    private function setupResponseNew($agent, $msgId, $m) {
        $dom = $m->dom;
		$dataObject = null;
        $db = Zend_Registry::get('my_db');

        $requesterId = RequestObject::getRequesterId($msgId);
        $agentModeId = RequestObject::getRequesterAgentMode($msgId);

        try {
            $sifObjectDataNode = $dom->getElementsByTagName('SIF_ObjectData')->item(0);
            $children          = isset($sifObjectDataNode->childNodes) ? $sifObjectDataNode->childNodes : '';

			if(is_object($children)){
				$objectName = isset($children->item(0)->nodeName) ? $children->item(0)->nodeName : null;
			} else {
				$objectName = null;
				ZitLog::writeToErrorLog('[SIF_ObjectData Error]', 'No SIF_ObjectData found from agent = '. $agent->sourceId, $this->xmlStr );
				GeneralError::systemError($this->xmlStr);
			}
			
            $dataObject        = new DataObject($objectName);

        }
        catch(Exception $e) {
			ZitLog::writeToErrorLog('[Error processing message]', "DataObject Name: ".$objectName."\nError Message\n".$e->getMessage()."\n\nStack Trace\n".$e->getTraceAsString().' '. $this->xmlStr, 'Process Message', $_SESSION['ZONE_ID']);
			GeneralError::systemError($this->xmlStr);
        }

        $sifMessageNode = $dom->getElementsByTagName('SIF_Message')->item(0);
        $responseXml = $dom->saveXML($sifMessageNode);
        $responseXml = str_replace('xmlns="http://www.sifinfo.org/infrastructure/1.x"','',$responseXml);
		$responseXml = str_replace('xmlns:sif="http://www.sifinfo.org/infrastructure/1.x"','',$responseXml);
        $responseXml = str_replace('xmlns="http://www.sifinfo.org/infrastructure/2.x" ','',$responseXml);
        $responseXml = str_replace('xmlns="http://www.sifinfo.org/uk/infrastructure/2.x" ','',$responseXml);
		$responseXml = str_replace('xmlns="http://www.sifinfo.org/au/infrastructure/2.x" ','',$responseXml);
		$responseXml = str_replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ','',$responseXml);

#        $data = array(
#                DBConvertor::convertCase('request_msg_id')      => $msgId,
#                DBConvertor::convertCase('response_data')       => $responseXml,
#                DBConvertor::convertCase('next_packet_num')     => '2',
#                DBConvertor::convertCase('status_id')           => '1',
#                DBConvertor::convertCase('agent_id_requester')  => intval($requesterId),
#                DBConvertor::convertCase('agent_id_responder')  => $agent->agentId,
#                DBConvertor::convertCase('agent_mode_id')       => intval($agentModeId),
#                DBConvertor::convertCase('zone_id')         	=> $_SESSION["ZONE_ID"],
#                DBConvertor::convertCase('context_id')     	 	=> $_SESSION["CONTEXT_ID"]
#        );

#        $db->insert(DBConvertor::convertCase('response'), $data);

/*          Removing the filterUtility as it should be reworked.	
			$filterUtility = new FilterUtility();
            $filterUtility->FilterCommonElements($dataObject->objectId, $dom, intval($requesterId));
*/

		try {
			
			$this->xslt = null;
			$permissions = new AgentPermissions($db);
			$where = "object_id = ".$dataObject->objectId." and agent_id = ".$requesterId." and zone_id = ".$_SESSION['ZONE_ID']." and context_id = ".$_SESSION['CONTEXT_ID'];
			$result = $permissions->fetchAll($where);
	
			switch(DB_TYPE) {
				case 'mysql':
					$this->xslt        = isset($result[0]->xslt) ? $result[0]->xslt : null;
				break;
				case 'oci8':
					$this->xslt        = isset($result[0]->XSLT) ? $result[0]->XSLT : null;
				break;
			}
	
			if ($this->xslt != null) {
				$xsltpro 	 	= new XSLTProcessor();
				$XSL		= new DOMDocument();
				$XSL->loadXML($this->xslt);
				$xsltpro->importStylesheet( $XSL );
				$XML		= new DOMDocument();
				$XML->loadXML($responseXml);
				$responseXml = $xsltpro->transformToXML( $XML );
			}
			$responseXml = str_replace('<?xml version="1.0"?>'."\n",'',$responseXml);
				
		} catch (Zend_Exception $e) {
			ZitLog::writeToErrorLog('[Error filtering message]', "DataObject Name: ".$objectName."\nError Message\n".$e->getMessage()."\n\nStack Trace\n".$e->getTraceAsString().' '. $this->xmlStr, 'Process Message', $_SESSION['ZONE_ID']);
			GeneralError::systemError($this->xmlStr);			
		}

		$messagequeue = new MessageQueues($db);
		$data = null;
		$data = array(
                DBConvertor::convertCase('msg_id')	     		=> $this->originalMsgId,
				DBConvertor::convertCase('ref_msg_id')     		=> $msgId,
				DBConvertor::convertCase('msg_type')     		=> 2,
				DBConvertor::convertCase('status_id')           => '1',
				DBConvertor::convertCase('version')          	=> $_SESSION['ZONE_VERSION'],
                DBConvertor::convertCase('insert_timestamp')	=> new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
                DBConvertor::convertCase('agt_id_in')			=> $agent->agentId,
                DBConvertor::convertCase('agt_id_out')			=> intval($requesterId),
                DBConvertor::convertCase('data')				=> $responseXml,
				DBConvertor::convertCase('next_packet_num')     => 2,
                DBConvertor::convertCase('agt_mode_id')      	=> intval($agentModeId),
                DBConvertor::convertCase('zone_id')         	=> $_SESSION["ZONE_ID"],
                DBConvertor::convertCase('context_id')      	=> $_SESSION["CONTEXT_ID"]
        );
        $messagequeue->insert($data);

        $timestamp    = Utility::createTimestamp();
        $msgId        = Utility::createMessageId();
        XmlHelper::buildSuccessMessage(
				$msgId,
                $timestamp,
                $this->originalSourceId,
                $this->originalMsgId,
                0,
                $originalMsg = null,
                $desc = null);
    }
}
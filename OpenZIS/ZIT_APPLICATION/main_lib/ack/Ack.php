<?php

/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Ack {

    var $dom;
    var $xml;

    public function Ack($m) {
        $this->dom = $m->dom;
		$this->xml = $m->xmlStr;
        $this->processAck($m);
    }

    public function processAck($m) {
        $dom = $this->dom;
		$headerNode 	    = $m->headerNode;
		$msgId	      		= $m->msgId;
		$agentSourceId   	= $m->sourceId;
		
#        $msgId           = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
#        $agentSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
		
        $originalMsgId   = $dom->getElementsByTagName('SIF_OriginalMsgId')->item(0)->nodeValue;
		
        $timestamp       = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;
        $status          = $dom->getElementsByTagName('SIF_Status');
        $status          = $status->item(0)->nodeValue;
        $validSourceId = Agent::checkSourceId($agentSourceId);
        if(!$validSourceId) {
            RegisterError::invalidSourceId($agentSourceId, $msgId);
        }
        else {
            $agent = new Agent($agentSourceId);
            if($agent->isRegistered()) {
                if($status == 1) {
                    $agent->unFreezeAgent();
                    $this->updateMessageQueue($agent, $originalMsgId, $msgId);
                }
                else
                if($status == 3) {
                    //done processing
                    if($originalMsgId == $agent->frozenMsgId) {
                        $agent->unFreezeAgent();
                        $this->updateMessageQueue($agent, $originalMsgId, $msgId);
                    }
                    else {
                        GeneralError::EventACKError($this->xml);
                    }
                }
                else
                if($status == 2) {
                    //Intermediate wait for final
                    $agent->freezeAgent();
                    $agent->setFrozenMsgId($originalMsgId);
                    $timestamp  = Utility::createTimestamp();
                    $msgId_u    = Utility::createMessageId();
                    XmlHelper::buildSuccessMessage($msgId_u,
                            $timestamp,
                            $agent->sourceId,
                            $msgId,
                            0,
                            $originalMsg = null,
                            $desc = null);
                }
            }
            else {
                RegisterError::notRegisteredError($agentSourceId, $msgId);
            }
        }
    }

    private function updateMessageQueue($agent, $originalMsgId, $msgId) {
        $res = DataObject::updateEvent($agent->agentId, $originalMsgId, $msgId);
        if($res == 1) {
            $timestamp = Utility::createTimestamp();
            $msgId_u   = Utility::createMessageId();
            XmlHelper::buildSuccessMessage($msgId_u,
                    $timestamp,
                    $agent->sourceId,
                    $msgId,
                    0,
                    $originalMsg = null,
                    $desc = null);
        }
        else {
            GeneralError::EventACKError($this->xml);
        }
    }
}
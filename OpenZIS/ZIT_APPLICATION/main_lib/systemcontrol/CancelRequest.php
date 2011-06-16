<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class CancelRequest {

    var $xmlDom;
    var $agent;

    public function CancelRequest($dom, $xml=false, $zoneStatus=false) {
        $this->xmlDom = $dom;
        $this->processCancelRequest();
    }

    private function processCancelRequest() {
        $dom = $this->xmlDom;
		
		$timestamp    = Utility::createTimestamp();
        $msgId        = Utility::createMessageId();
        XmlHelper::buildSuccessMessage($msgId,
                        $timestamp,
                        $originalSourceId,
                        $originalMsgId,
                        0,
                        $originalMsg = null,
                        $desc = null);
				
		$objects = $dom->getElementsByTagName('SIF_RequestMsgId');
		foreach($objects as $object){
					$msgId = $object->itme(0)->nodeValue;
				  // Check SIF_RequestMsgId 
				  // Check SIF_SourceId to the SIF_SourceID of the Request Message
				  // Close out the SIF_Request
				  // if Agent is running in PUSH mode send a SIF_CancelRequests to Agent
				  // Check SIF_Notiication Type
				  // Perpare SIF_Response Message
				  // Set SIF_PacketNumber + 1 Set SIF_MorePackets to No
				  // Place the SIF_Response in Requester's queue
				}
				
			 }// isRegistered
		} // ValidSourceId
    }
}//end class

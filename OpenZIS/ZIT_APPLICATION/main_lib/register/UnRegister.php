<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class UnRegister{

	var $xmlDom;
	
	public function UnRegister($dom){
		$this->xmlDom = $dom;
		$this->processUnregistration();
	}
	
	public function processUnregistration(){
		$dom = $this->xmlDom;
		
		$headerNode   = $dom->getElementsByTagName('SIF_Header')->item(0);
		
		$originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
		$originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
		$originalTimestamp  = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;
		
		
		$validSourceId = Agent::checkSourceId($originalSourceId);
		if(!$validSourceId){
			RegisterError::invalidSourceId($agent->sourceId, $originalMsgId);
		}
		else{
			$agent = new Agent($originalSourceId);
			if($agent->isRegistered()){
				if($agent->unRegister()){
					XmlHelper::buildSuccessMessage(Utility::createMessageId(), 
											       Utility::createTimestamp(),
											   	   $agent->sourceId, 
											       $originalMsgId, 
											       0);
				}
				else{
					RegisterError::genericError($agent->sourceId, $originalMsgId);
				}
			}
			else{
				RegisterError::notRegisteredError($originalSourceId, $originalMsgId);
			}
		}
	}

}

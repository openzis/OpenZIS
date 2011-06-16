<?php
/*
this file is part of OpenZIS (Open Source Zone Integration Server) http://www.openzis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Register{

	var $xmlDom;

	public function Register($dom){
		$this->xmlDom = $dom;
		$this->processRegistration();
	}

	private function processRegistration(){
		$dom = $this->xmlDom;
		
		$headerNode   = $dom->getElementsByTagName('SIF_Header')->item(0);
		
		$authenticationLevel = 
		$originalMsgId       = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
		$originalSourceId    = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
		if ($headerNode->getElementsByTagName('SIF_Timestamp')->item(0)){
			$originalTimestamp   = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;
		} else {
			$originalTimestamp   = Utility::createTimestamp();
		}
		$version 			 = $_SESSION['ZONE_VERSION'];
		$buffer              = $dom->getElementsByTagName('SIF_MaxBufferSize')->item(0)->nodeValue;
		$mode                = $dom->getElementsByTagName('SIF_Mode')->item(0)->nodeValue;

		if($dom->getElementsByTagName('SIF_Protocol')->item(0)){
			$protocolType   = $dom->getElementsByTagName('SIF_Protocol')->item(0)->attributes->item(0)->nodeValue;
			$protocolSecure = $dom->getElementsByTagName('SIF_Protocol')->item(0)->attributes->item(1)->nodeValue;
		}

		if($dom->getElementsByTagName('SIF_URL')->item(0)){
			$callBackUrl    = $dom->getElementsByTagName('SIF_URL')->item(0)->nodeValue;
		}

		$validSourceId = Agent::checkSourceId($originalSourceId);
		if(!$validSourceId){
			RegisterError::invalidSourceId($agent->sourceId, $originalMsgId);
		}
		else{
			
			//create agent object
			$agent = new Agent($originalSourceId);
			if(!Agent::allowedToRegister($agent->agentId)){
				RegisterError::genericError($agent->sourceId, $originalMsgId);
			}
			else{
/*				if($version != ZIT::checkVersion($version)){
					RegisterError::invalidVersion($agent->sourceId, $originalMsgId);
				}
				else{
*/					if($buffer < ZIT::getMinBuffer()){
						RegisterError::invalidMaxBuffer($agent->sourceId, $originalMsgId);
					}
					else{
						$empty = null;
						$agent->authenticationLevel = $this->getSecurityLevel();
						$agent->maxBuffersize = $buffer;
						$agent->protocol      = isset($protocolType) ? $protocolType : $empty;
						$agent->callBackUrl   = isset($callBackUrl) ? urlencode($callBackUrl) : $empty;
						$agent->agentMode     = Agent::convertAgentMode(isset($mode) ? $mode: $empty);
						$agent->secure        = Agent::convertSecure(isset($protocolSecure) ? $protocolSecure : $empty);
						$agent->version       = $version;
						
						if($agent->isRegistered()){
							if($agent->updateRegistration()){
								$this->createAclAckMessage($agent->sourceId, $originalMsgId);
							}
							else{
								RegisterError::genericError($agent->sourceId, $originalMsgId, 'Error Registering Agent at agent->updateRegistration()');
							}//try to update registration
						}
						else{
							if($agent->register()){
								$this->createAclAckMessage($agent->sourceId, $originalMsgId);
							}
							else{
								RegisterError::genericError($agent->sourceId, $originalMsgId, 'Error Registering Agent at agent->register()');
							}//try to register
						}//check if agent is registered
					}//check if buffer size is valid
//				}//check if version is valid
			}//check if agent allowed to register
		}//check if sourceId is valid
	}//end function	processRegistration
	
	private function createAclAckMessage($sourceId, $originalMsgId){
		$acl = new ACL($this->xmlDom);
		
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		XmlHelper::buildSuccessMessage($msgId,
								       $timestamp,
								       $sourceId,
								       $originalMsgId,
								       0,
								       $originalMsg = $acl->BuildACL(),
								       $desc = null);
	}
	
	private function getSecurityLevel()
	{
		$dom = $this->xmlDom;
		$authenticationLevel = 0;
		
		$securityNode = $dom->getElementsByTagName('SIF_Security')->item(0);
		if($securityNode != null)
		{
			$secureChannelNode = $securityNode->getElementsByTagName('SIF_SecureChannel')->item(0);
			if($secureChannelNode != null)
			{
				$authenticationLevel =  $secureChannelNode->getElementsByTagName('SIF_AuthenticationLevel')->item(0)->nodeValue;
			}
		}
		
		return $authenticationLevel;
	}
	
}//end class

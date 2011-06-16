<?php 
/*
This file is part of OpenZIS (Open Source Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free 
Software Foundation; either version 3. OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the 
implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 
59 Temple Place, Suite 330, Boston, MA  02111-1307  USA Refer to documents/COPYING for the full licence agreement 

*/

class SifProcessRequest{

	public static function retrieveVersion($msgObj){
		$dom = new DomDocument();
		
/*
		Moved to MessageObject Class:  CHW (April 11th 2011)
		
		if($xmlStr == '' || $xmlStr == null){
			ZitLog::writeToErrorLog('[Xml missing in request]', 'Xml is missing in request can not process message','Process Message', $_SESSION['ZONE_ID']);
			#echo '<FATAL_ERROR>XML FORMAT</FATAL_ERROR>';
			GeneralError::systemError($xmlStr);
			exit;
		}
*/	
		$dom->loadXML($msgObj->$xmlStr);

		if($dom->documentElement->hasAttribute('Version')){
			$version = $dom->documentElement->getAttribute('Version');
		}
		else
		if($dom->documentElement->hasAttribute('version')){
			$version = $dom->documentElement->getAttribute('version');
		}
		else{
		    if($_SESSION['ZONE_VERSION'] == '1.5r1'){
				$version = $_SESSION['ZONE_VERSION'];
			}
			else{
				ZitLog::writeToErrorLog('[Invalid Version] Zone does not support version '.substr($version,0,10), 'Zone does not support sif message version : '.$xmlStr, 'Retrieve Message Version', $_SESSION['ZONE_ID']);
				GeneralError::versionError($xmlStr);
				exit;
			}
		}
		if($version != $_SESSION['ZONE_VERSION']){
			ZitLog::writeToErrorLog('[Invalid Version] Zone does not support version '.substr($version,0,10), 'Zone does not support sif message version '. $xmlStr, 'Retrieve Message Version', $_SESSION['ZONE_ID']);
			GeneralError::versionError($xmlStr);
			exit;
		}
		return $version;
	}
	
	public static function authenticateAgent($sourceId){
		$agent = new Agent($sourceId);
		$xmlStr = REC_XML;
		$db = Zend_Registry::get('my_db');
		
		//if authentication type is username and password
		if(Zone::getZoneAuthenticationType() == 1)
		{
			if (!isset($_SERVER['PHP_AUTH_USER'])){
				GeneralError::invalidUserError($xmlStr);
				exit;
			}
			else{
				
				$username = $_SERVER['PHP_AUTH_USER'];
				$password = $_SERVER['PHP_AUTH_PW'];
				
				if($agent->username == $username && $agent->password == $password)
				{
					$_SESSION['username'] = $username;
					return;
				}
				else
				{
					ZitLog::writeToErrorLog('[Invalid User] User does not exist', 'User in agent request does not exist in the system', 'Authenticate Agent', $_SESSION['ZONE_ID'], $agent->agentId);
					GeneralError::invalidUserError($xmlStr);
					exit;
				}
			}
		}
		//if authentication type is certificate
		else
		if(Zone::getZoneAuthenticationType() == 2)
		{
			if (!isset($_SERVER['SSL_CLIENT_CERT'])){
				ZitLog::writeToErrorLog('[Missing Certificate] Certificate is missing for agent', 'Agent request does not contain a certificate.  Zone is set to certificate authentication', 'Verify Certificate', $_SESSION['ZONE_ID'], $agent->agentId);
				GeneralError::missingSenderCertificate($xmlStr);
				exit;
			}
			else{
				$cert = $_SERVER['SSL_CLIENT_CERT'];
				$dn = "C: "||$_SERVER['SSL_CLIENT_S_DN_S']||'<br/>'
					||"O: "||$_SERVER['SSL_CLIENT_S_DN_O']||'<br/>'
					||"OU: "||$_SERVER['SSL_CLIENT_S_DN_OU']||'<br/>'
					||"CN: "||$_SERVER['SSL_CLIENT_S_DN_CN']||'<br/>'
					||"End Date: "||$_SERVER['SSL_CLIENT_V_END']||'<br/>';
			}
			
			if ($agent->certCommonName == null || $agent->certCommonName == '' || $agent->certCommonName == ' ' ){
				$agents = new Agents($db);
				$row    = $agents->fetchRow("agent_id = ".$agent->agentId);
				$row->cert_common_name = $cert;
				$row->cert_common_dn   = $dn;
				$row->save();
				$agent->certCommonName = $cert;
			}
			
			if($agent->certCommonName != $cert){
				ZitLog::writeToErrorLog('[CN is not valid] CN of certificate is invalid', 'CN of request certificate does not match the CN setup with the agent', 'Verify Certificate', $_SESSION['ZONE_ID'], $agent->agentId);
				GeneralError::agentCommonNameDoesNoMatch($xmlStr);
				exit;
			} 
		}
			
/*			if($agent->isRegistered())
			{
				//$remoteAddress = SifProcessRequest::getIpAddress();
				$cn = $_SERVER['SSL_CLIENT_S_DN_CN'];
				$result  = $_SERVER['SSL_CLIENT_VERIFY'];
				switch($agent->getAgentRegistrationSifAuthenticationLevel())
				{
					case 1:
						if($cn == null || $cn == '' || $cn == ' '){
							ZitLog::writeToErrorLog('[Missing Certificate] Certificate is missing for agent', 'Agent request does not contain a certificate.  Zone is set to certificate authentication', 'Verify Certificate', $_SESSION['ZONE_ID'], $agent->agentId);
							GeneralError::missingSenderCertificate(REC_XML);
							exit;
						}
					break;
					case 3:
						if($agent->certCommonName != $cn){
							ZitLog::writeToErrorLog('[CN is not valid] CN of certificate is invalid', 'CN of request certificate does not match the CN setup with the agent', 'Verify Certificate', $_SESSION['ZONE_ID'], $agent->agentId);
							GeneralError::agentCommonNameDoesNoMatch(REC_XML);
							exit;
						}
					break;
				}
			}
*/
//		}//end authentication type check
	}//end authenticateAgent
	
	public static function validateXML($xmlStr){
		
		if ((SIF_VALIDATE == 'Y') or (SIF_VALIDATE == 'W')) {
			
		libxml_use_internal_errors(true);
		$xml = explode("\n", $xmlStr);

		$objDom = new DomDocument();
		if($xmlStr == '' || $xmlStr == null){
			ZitLog::writeToErrorLog('[Xml missing in request]', 'Xml is missing in request can not process message','Process Message', $_SESSION['ZONE_ID']);
			echo '<FATAL_ERROR>XML FORMAT</FATAL_ERROR>';
			exit;
		}

		if(!$objDom->loadXML($xmlStr)){
			ZitLog::writeToErrorLog('[Error loading xml to parse]', $xmlStr, 'Process Message', $_SESSION['ZONE_ID']);
			echo '<FATAL_ERROR>XML FORMAT</FATAL_ERROR>';
			exit;
		}

		$schema = $_SESSION['ZONE_VERSION_SCHEMA_DIR'];
		
		if(!$objDom->schemaValidate($schema)){

			$errorString = '';
			$allErrors = libxml_get_errors();
			foreach ($allErrors as $error) 
			{
				$errorString .= SifProcessRequest::Display_xml_error($error, $xml);
			}
			
			ZitLog::writeToErrorLog("[Error Validating Xml]", "Request Xml:\n$xmlStr \n\nSchema Errors:$errorString", "Process Message", $_SESSION['ZONE_ID']);
			if (SIF_VALIDATE == 'Y') {
				return false;
			}
			else {
				return true;
			}
		}
		else{
			return true;
		}
	} else {
		return true;
	}
	}
	
	public static function Display_xml_error($error, $xml)
	{
		$return  = $xml[$error->line - 1] . "\n";
		$return .= str_repeat('-', $error->column) . "^\n";
	
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code: ";
				break;
			 case LIBXML_ERR_ERROR:
				$return .= "Error $error->code: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code: ";
				break;
		}
	
		$return .= trim($error->message) .
				   "\n  Line: $error->line" .
				   "\n  Column: $error->column";
	
		return "$return\n\n--------------------------------------------\n\n";
	}

	public static function checkIfZoneIsSleeping($dom){
		if(Zone::zoneSleeping()){
			
			/* todo: Should create a SIF_MESSAGE_TYPE for errors */
			$_SESSION['SIF_MESSAGE_TYPE'] = 1;

			$headerNode         = $dom->getElementsByTagName('SIF_Header')->item(0);
			$originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
			$originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;

			$timestamp    = Utility::createTimestamp();
			$msgId        = Utility::createMessageId();
			$sifMessageNode = $dom->getElementsByTagName('SIF_Message')->item(0);
			XmlHelper::buildSuccessMessage($msgId,
										   $timestamp,
									//	   ZIT::getSourceId(),
										   $originalSourceId,
										   $originalMsgId,
										   8);
			exit;
		}
	}

	public static function checkIfZitIsSleeping($dom){
		if(Zit::isAsleep()){
			
			/* todo: Should create a SIF_MESSAGE_TYPE for errors */
			$_SESSION['SIF_MESSAGE_TYPE'] = 1;

			$headerNode         = $dom->getElementsByTagName('SIF_Header')->item(0);
			$originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
			$originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;

			$timestamp    = Utility::createTimestamp();
			$msgId        = Utility::createMessageId();
			$sifMessageNode = $dom->getElementsByTagName('SIF_Message')->item(0);
			XmlHelper::buildSuccessMessage( $msgId,
					                        $timestamp,
					                        $originalSourceId,
					                        $originalMsgId,
					                        8,
					                        $originalMsg = null,
					                        $desc = 'Receiver is sleeping');
			exit;
		}
	}

	public static function processMessage($xmlStr)
	{
		try
		{
			$dom = new DomDocument();
			
			/***
			Make sure that preserve white space is off for the the DomDocument object.
			WhiteSpaces interfers with parsing the xml
			***/
			$dom->preserveWhiteSpace = false;
			$res = $dom->loadXML($xmlStr);
	
			$headerNode    = $dom->getElementsByTagName('SIF_Header')->item(0);
			$sourceId      = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
			$originalMsgId = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
			
			$validSourceId = Agent::checkSourceId($sourceId);
			if(!$validSourceId)
			{
				RegisterError::invalidSourceId($sourceId, $originalMsgId);
			}
			SifProcessRequest::authenticateAgent($sourceId);
			SifProcessRequest::checkIfZitIsSleeping($dom);
			SifProcessRequest::checkIfZoneIsSleeping($dom);
			
			$messageNode = $dom->getElementsByTagName('SIF_Message');
			$messageType = isset($messageNode->item(0)->childNodes->item(0)->nodeName) ? $messageNode->item(0)->childNodes->item(0)->nodeName : 'default';
			
			switch($messageType){
				case 'SIF_Register':
						$_SESSION['SIF_MESSAGE_TYPE'] = 5;
						$register = new Register($dom);
				break;
	
				case 'SIF_Unregister':
						$_SESSION['SIF_MESSAGE_TYPE'] = 18;
						$unregister = new UnRegister($dom);
				break;
				
				case 'SIF_Ack':
						$_SESSION['SIF_MESSAGE_TYPE'] = 1;
						$ack = new Ack($dom, $xmlStr);
				break;
	
				case 'SIF_SystemControl':
						$systemControl = new SystemControl($dom, $xmlStr);
				break;
	
				case 'SIF_Provision':
						$_SESSION['SIF_MESSAGE_TYPE'] = 4;
						$provision = new Provision($dom);
				break;
	
				case 'SIF_Event':
						$_SESSION['SIF_MESSAGE_TYPE'] = 2;
						$event = new Event($dom);
				break;
	
				case 'SIF_Provide':
						$_SESSION['SIF_MESSAGE_TYPE'] = 3;
						$provide = new Provide($dom);
				break;
	
				case 'SIF_Unprovide':
						$_SESSION['SIF_MESSAGE_TYPE'] = 17;
						$unProvide = new UnProvide($dom);
				break;
	
				case 'SIF_Subscribe':
						$_SESSION['SIF_MESSAGE_TYPE'] = 8;
						$subscribe = new Subscribe($dom);
				break;
	
				case 'SIF_Unsubscribe':
						$_SESSION['SIF_MESSAGE_TYPE'] = 19;
						$unSubscribe = new UnSubscribe($dom);
				break;
	
				case 'SIF_Request':
						$_SESSION['SIF_MESSAGE_TYPE'] = 6;
							  $request = new Request($dom);
				break;
	
				case 'SIF_Response':
						$_SESSION['SIF_MESSAGE_TYPE'] = 7;
						$response = new Response($dom, $xmlStr);
				break;
	
				default:
						echo '<FATAL_ERROR>INVALID_SIF_REQUEST</FATAL_ERROR>';
						exit;
				break;
				
			}//end switch
		}
		catch (Exception $e) 
		{
   			ZitLog::writeToErrorLog('[Error processing message]', "Error Message\n".$e->getMessage()."\n\nStack Trace\n".$e->getTraceAsString(), 'Process Message', $_SESSION['ZONE_ID']);
		}//end exception block
	}//end processMessage
	
	public static function getIpAddress()
    {
      $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
  	  if($userIP == "")
   	  {
   		$userIP = $_SERVER['REMOTE_ADDR'];
  	  }
 	  return $userIP;
    }//end getIpAddress
}// end SifProcessRequest


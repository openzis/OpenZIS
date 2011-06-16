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

		$dom = $msgObj->dom;
		
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
				ZitLog::writeToErrorLog('[Invalid Version] Zone does not support version '.substr($version,0,10), 'Zone does not support sif message version : '.$msgObj->xmlStr, 'Retrieve Message Version', $_SESSION['ZONE_ID']);
				GeneralError::versionError($msgObj->xmlStr);
				exit;
			}
		}
		if($version != $_SESSION['ZONE_VERSION']){
			ZitLog::writeToErrorLog('[Invalid Version] Zone does not support version '.substr($version,0,10), 'Zone does not support sif message version '. $msgObj->xmlStr, 'Retrieve Message Version', $_SESSION['ZONE_ID']);
			GeneralError::versionError($msgObj->xmlStr);
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
				GeneralError::invalidUserError($msgObj->xmlStr);
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
					GeneralError::invalidUserError($msgObj->xmlStr);
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
				GeneralError::missingSenderCertificate($msgObj->xmlStr);
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
				GeneralError::agentCommonNameDoesNoMatch($msgObj->xmlStr);
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

	public static function checkIfZoneIsSleeping($msgObj){
		if(Zone::zoneSleeping()){
			
			/* todo: Should create a SIF_MESSAGE_TYPE for errors */
			$_SESSION['SIF_MESSAGE_TYPE'] = 1;

/*			$headerNode         = $dom->getElementsByTagName('SIF_Header')->item(0);
			$originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
			$originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
*/

			$originalMsgId      = $msgObj->msgId;
			$originalSourceId   = $msgObj->sourceId;

			
			$timestamp    = Utility::createTimestamp();
			$msgId        = Utility::createMessageId();
			XmlHelper::buildSuccessMessage($msgId,
										   $timestamp,
										   $originalSourceId,
										   $originalMsgId,
										   8);
			exit;
		}
	}

	public static function checkIfZitIsSleeping($msgObj){
		if(Zit::isAsleep()){
			
			/* todo: Should create a SIF_MESSAGE_TYPE for errors */
			$_SESSION['SIF_MESSAGE_TYPE'] = 1;

/*			$headerNode         = $dom->getElementsByTagName('SIF_Header')->item(0);
			$originalMsgId      = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
			$originalSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
*/

			$originalMsgId      = $msgObj->msgId;
			$originalSourceId   = $msgObj->sourceId;

			$timestamp    = Utility::createTimestamp();
			$msgId        = Utility::createMessageId();
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

	public static function processMessage($m)
	{
		try
		{
			$msgObj = $m;
			$dom = $m->dom;
			
			/***
			Make sure that preserve white space is off for the the DomDocument object.
			WhiteSpaces interfers with parsing the xml
			***/
#			$dom->preserveWhiteSpace = false;
#			$res = $dom->loadXML($xmlStr);
	
			$headerNode    = $msgObj->headerNode;
			$sourceId      = $msgObj->sourceId;
			$originalMsgId = $msgObj->msgId;
			
			$validSourceId = Agent::checkSourceId($msgObj->sourceId);
			if(!$validSourceId)
			{
				RegisterError::invalidSourceId($msgObj->sourceId, $msgObj->msgId);
			}
			SifProcessRequest::authenticateAgent($msgObj->sourceId);
			SifProcessRequest::checkIfZitIsSleeping($msgObj);
			SifProcessRequest::checkIfZoneIsSleeping($msgObj);

#			MOVED into MessageObject			
#			$messageNode = $dom->getElementsByTagName('SIF_Message');
#			$msgObj->messageType = isset($messageNode->item(0)->childNodes->item(0)->nodeName) ? $messageNode->item(0)->childNodes->item(0)->nodeName : 'default';
			
			switch($msgObj->messageType){
				case 'SIF_Register':
						require_once 'main_lib/register/Register.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 5;
						$register = new Register($msgObj->dom);
				break;
	
				case 'SIF_Unregister':
						require_once 'main_lib/register/RegisterError.php';
						require_once 'main_lib/register/UnRegister.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 18;
						$unregister = new UnRegister($msgObj->dom);
				break;
				
				case 'SIF_Ack':
						require_once 'main_lib/register/RegisterError.php';
						require_once 'main_lib/ack/Ack.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 1;
						$ack = new Ack($msgObj);
				break;
	
				case 'SIF_SystemControl':
						require_once 'main_lib/systemcontrol/SystemControl.php';
						$systemControl = new SystemControl($msgObj);
				break;
	
				case 'SIF_Provision':
						require_once 'main_lib/provision/ProvisionError.php';
						require_once 'main_lib/provision/Provision.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 4;
						$provision = new Provision($msgObj->dom);
				break;
	
				case 'SIF_Event':
#						require_once 'main_lib/FilterUtility.php';
						require_once 'main_lib/event/Event.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 2;
						$event = new Event($msgObj);
				break;
	
				case 'SIF_Provide':
						require_once 'main_lib/provide/Provide.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 3;
						$provide = new Provide($msgObj->dom);
				break;
	
				case 'SIF_Unprovide':
						require_once 'main_lib/provide/UnProvide.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 17;
						$unProvide = new UnProvide($msgObj->dom);
				break;
	
				case 'SIF_Subscribe':
						require_once 'main_lib/subscribe/Subscribe.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 8;
						$subscribe = new Subscribe($msgObj->dom);
				break;
	
				case 'SIF_Unsubscribe':
						require_once 'main_lib/subscribe/UnSubscribe.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 19;
						$unSubscribe = new UnSubscribe($msgObj->dom);
				break;
	
				case 'SIF_Request':
#						require_once 'main_lib/FilterUtility.php';
						require_once 'main_lib/RequestObject.php';
						require_once 'main_lib/request/Request.php';
						require_once 'main_lib/request/RequestError.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 6;
						$request = new Request($msgObj);
				break;
	
				case 'SIF_Response':
#						require_once 'main_lib/FilterUtility.php';
						require_once '../CLASSES/'.DB_TYPE.'/AgentPermissions.php';
						require_once 'main_lib/ResponseObject.php';
						require_once 'main_lib/RequestObject.php';
						require_once 'main_lib/request/RequestError.php';
						require_once 'main_lib/response/Response.php';
						$_SESSION['SIF_MESSAGE_TYPE'] = 7;
						$response = new Response($msgObj);
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

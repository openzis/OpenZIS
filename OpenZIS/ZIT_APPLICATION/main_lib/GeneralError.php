<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class GeneralError{
	
	static function genericError($m, $categoryCode = "11", $sifCode = "1", $shortDesc = "Generic Error", $longDesc = "Generic Error"){

		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp, 
									 $m->sourceId, 
									 $m->msgId,
									 false);		
	}

	static function missingSenderCertificate($xml){
		
		$dom = new DomDocument();
		$dom->loadXML($xml);
		
		$header       = $dom->getElementsByTagName('SIF_Header')->item(0);
		$msgIdNode    = $header->getElementsByTagName('SIF_MsgId')->item(0);
		$sourceIdNode = $header->getElementsByTagName('SIF_SourceId')->item(0);
		
		$originalMsgId 	  = $msgIdNode->nodeValue;
		$originalSourceId = $sourceIdNode->nodeValue;

		
		$categoryCode = "3";
	    $sifCode      = "3";
	    $shortDesc    = "Authentication";
	    $longDesc     = "Missing sender's certificate.";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp, 
									 $originalSourceId, 
									 $originalMsgId,
									 false);
	}
	
	static function agentCommonNameDoesNoMatch($xml){
		
		$dom = new DomDocument();
		$dom->loadXML($xml);
		
		$header       = $dom->getElementsByTagName('SIF_Header')->item(0);
		$msgIdNode    = $header->getElementsByTagName('SIF_MsgId')->item(0);
		$sourceIdNode = $header->getElementsByTagName('SIF_SourceId')->item(0);
		
		$originalMsgId 	  = isset($msgIdNode->nodeValue) ? $msgIdNode->nodeValue : null;
		$originalSourceId = isset($sourceIdNode->nodeValue) ? $sourceIdNode->nodeValue : null;
		
		
		$categoryCode = "3";
	    $sifCode      = "1";
	    $shortDesc    = "Generic error";
	    $longDesc     = "CN of certificate does not match agent.";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp, 
									 $originalSourceId, 
									 $originalMsgId,
									 false);
	}
	
	static function invalidUserError($xml){
		
		$dom = new DomDocument();
		$dom->loadXML($xml);
		
		$header       = $dom->getElementsByTagName('SIF_Header')->item(0);
		$msgIdNode    = $header->getElementsByTagName('SIF_MsgId')->item(0);
		$sourceIdNode = $header->getElementsByTagName('SIF_SourceId')->item(0);
		
		$originalMsgId 	  = isset($msgIdNode->nodeValue) ? $msgIdNode->nodeValue : '';
		$originalSourceId = isset($sourceIdNode->nodeValue) ? $sourceIdNode->nodeValue : '';
		

		
		$categoryCode = "3";
	    $sifCode      = "1";
	    $shortDesc    = "Authentication";
	    $longDesc     = "Invalid User.";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp, 
									 $originalSourceId, 
									 $originalMsgId,
									 false);
	}
	
	static function versionError($xml){
		
		$dom = new DomDocument();
		$dom->loadXML($xml);
		
		$header       = $dom->getElementsByTagName('SIF_Header')->item(0);
		$msgIdNode    = $header->getElementsByTagName('SIF_MsgId')->item(0);
		$sourceIdNode = $header->getElementsByTagName('SIF_SourceId')->item(0);
		
		$originalMsgId 	  = isset($msgIdNode->nodeValue) ? $msgIdNode->nodeValue : '';
		$originalSourceId = isset($sourceIdNode->nodeValue) ? $sourceIdNode->nodeValue : '';
		
		$categoryCode = "12";
	    $sifCode      = "3";
	    $shortDesc    = "Generic Message Handling";
	    $longDesc     = "Version not supported.";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp,
									 $originalSourceId, 
									 $originalMsgId,
									 false);
	}
	
	static function xmlValidationError($xml){
		
		$dom = new DomDocument();
		$dom->loadXML($xml);
		
		$header       = $dom->getElementsByTagName('SIF_Header')->item(0);
		$msgIdNode    = $header->getElementsByTagName('SIF_MsgId')->item(0);
		$sourceIdNode = $header->getElementsByTagName('SIF_SourceId')->item(0);
		
		$originalMsgId 	  = isset($msgIdNode->nodeValue) ? $msgIdNode->nodeValue : '';
		$originalSourceId = isset($sourceIdNode->nodeValue) ? $sourceIdNode->nodeValue : '';
		
		$categoryCode = "1";
	    $sifCode      = "2";
	    $shortDesc    = "XML Validation";
	    $longDesc     = "Message is not well-formed.";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp,
									 $originalSourceId, 
									 $originalMsgId,
									 false);
	}
	
	static function EventACKError($xml){
		
		$dom = new DomDocument();
		$dom->loadXML($xml);
		
		$header       = $dom->getElementsByTagName('SIF_Header')->item(0);
		$msgIdNode    = $header->getElementsByTagName('SIF_MsgId')->item(0);
		$sourceIdNode = $header->getElementsByTagName('SIF_SourceId')->item(0);
		
		$originalMsgId 	  = isset($msgIdNode->nodeValue) ? $msgIdNode->nodeValue : '';
		$originalSourceId = isset($sourceIdNode->nodeValue) ? $sourceIdNode->nodeValue : '';
		
		$categoryCode = "9";
	    $sifCode      = "1";
	    $shortDesc    = "Event Reporting and Processing";
	    $longDesc     = "Event was not updated as recieved.";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp,
									 $originalSourceId, 
									 $originalMsgId,
									 false);
	}


	static function systemError($xml){
		
		try {
			if ($xml != '' or $xml != null) {
				$dom = new DomDocument();
				$dom->loadXML($xml);
				$header       = $dom->getElementsByTagName('SIF_Header')->item(0);
				$msgIdNode    = $header->getElementsByTagName('SIF_MsgId')->item(0);
				$sourceIdNode = $header->getElementsByTagName('SIF_SourceId')->item(0);
			
				$originalMsgId 	  = isset($msgIdNode->nodeValue) ? $msgIdNode->nodeValue : '';
				$originalSourceId = isset($sourceIdNode->nodeValue) ? $sourceIdNode->nodeValue : '';
				$longDesc     = "Event was not updated as recieved.";
			} else {
				$originalMsgId 	  = '';
				$originalSourceId = '';
				$longDesc     = "Event was not updated as recieved. XML not found.";
			}
		} catch (Exception $e){
			$originalMsgId 	  = '';
			$originalSourceId = '';
			$longDesc     = "Event was not updated as recieved. XML not found.";
		}
		
		$categoryCode = "11";
	    $sifCode      = "1";
	    $shortDesc    = "Generic Error";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp,
									 $originalSourceId, 
									 $originalMsgId,
									 false);
	}


}
<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class XmlHelper{
	
	public static function buildSifMessageBase(){
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput       = false;
		$root = $dom->createElement('SIF_Message');
		
		$ver      = $dom->createAttribute('Version');
		$verValue = $dom->createTextNode(isset($_SESSION['ZONE_VERSION'])? $_SESSION['ZONE_VERSION'] : '');
		
		$ver->appendChild($verValue);
		$root->appendChild($ver);
		
		$xmlns      = $dom->createAttribute('xmlns');
		$xmlnsValue = $dom->createTextNode(isset($_SESSION['VERSION_NAMESPACE'])? $_SESSION['VERSION_NAMESPACE'] : '');
		
		$xmlns->appendChild($xmlnsValue);
		$root->appendChild($xmlns);
		
		$dom->appendChild($root);
		
		return $dom;
	}
	
	public static function buildSifReponse($msgId,
										   $timestamp,
										   $destnationId,
										   $requestMsgId, 
										   $packetNumber, 
										   $errorCategory,
										   $errorCode,
										   $shortDesc,
										   $SIF_ExtendedDesc ){
		
		$root = XmlHelper::buildSifMessageBase();
		
		$sifMessage = $root->getElementsByTagName('SIF_Message')->item(0);
		$sifResponse = $root->createElement("SIF_Response");
		
		$sifHeader  = $root->createElement('SIF_Header');
		$sifHeader->appendChild($root->createElement('SIF_MsgId'))->appendChild($root->createTextNode($msgId));
		$sifHeader->appendChild($root->createElement('SIF_Timestamp'))->appendChild($root->createTextNode($timestamp));
		$sifHeader->appendChild($root->createElement('SIF_SourceId'))->appendChild($root->createTextNode($_SESSION['ZONE_NAME']));
		$sifHeader->appendChild($root->createElement('SIF_DestinationId'))->appendChild($root->createTextNode($destnationId));
		$sifResponse->appendChild($sifHeader);
		
		$sifRequestMsgId = $root->createElement('SIF_RequestMsgId');
		$sifRequestMsgId->appendChild($root->createTextNode($requestMsgId));
		$sifResponse->appendChild($sifRequestMsgId);
		
		$sifPacketNumber = $root->createElement('SIF_PacketNumber');
		$sifPacketNumber->appendChild($root->createTextNode($packetNumber));
		$sifResponse->appendChild($sifPacketNumber);
		
		$sifMorePackets = $root->createElement('SIF_MorePackets');
		$sifMorePackets->appendChild($root->createTextNode("No"));
		$sifResponse->appendChild($sifMorePackets);
		
		$sifErrorNode = $root->createElement('SIF_Error');
		$sifErrorNode->appendChild($root->createElement('SIF_Category'))->appendChild($root->createTextNode($errorCategory));
		$sifErrorNode->appendChild($root->createElement('SIF_Code'))->appendChild($root->createTextNode($errorCode));
		$sifErrorNode->appendChild($root->createElement('SIF_Desc'))->appendChild($root->createTextNode($shortDesc));
		$sifErrorNode->appendChild($root->createElement('SIF_ExtendedDesc'))->appendChild($root->createTextNode($SIF_ExtendedDesc));
		$sifResponse->appendChild($sifErrorNode);
		
		$sifMessage->appendChild($sifResponse);
	
		$root->preserveWhiteSpace = false;
		$root->formatOutput       = false;
		$xmlStr = $root->saveXML();
		ZitLog::writeToLog(REC_XML, $xmlStr);

		return $xmlStr;
	}
	
	public static function buildSifLogEvent($msgId, 
											$timestamp,  
											$originalHeader,
											$category,
											$code,
											$desc
											){
		$root = XmlHelper::buildSifMessageBase();
		
		$sifMessage = $root->getElementsByTagName('SIF_Message');
		$sifEvent   = $root->createElement('SIF_Event');
		
		$sifHeader  = $root->createElement('SIF_Header');
		$sifHeader->appendChild($root->createElement('SIF_MsgId'))->appendChild($root->createTextNode($msgId));
		$sifHeader->appendChild($root->createElement('SIF_Timestamp'))->appendChild($root->createTextNode($timestamp));
		$sifHeader->appendChild($root->createElement('SIF_SourceId'))->appendChild($root->createTextNode($_SESSION['ZONE_NAME']));
		
		$sifObjectData = $root->createElement('SIF_ObjectData');
		
		$sifEventObject = $root->createElement('SIF_EventObject');
		$sifEventObject->appendChild($root->createAttribute("ObjectName"))->appendChild($root->createTextNode("SIF_LogEntry"));
		$sifEventObject->appendChild($root->createAttribute("Action"))->appendChild($root->createTextNode("Add"));
		$sifObjectData->appendChild($sifEventObject);
		
		$sifLogEntry = $root->createElement('SIF_LogEntry');
		$sifLogEntry->appendChild($root->createAttribute("Source"))->appendChild($root->createTextNode("ZIS"));
		$sifLogEntry->appendChild($root->createAttribute("LogLevel"))->appendChild($root->createTextNode("Error"));
		
		$sifLogEntryHeader = $root->createElement('SIF_LogEntryHeader');
		$sifLogEntryHeader_sifHeader = $root->createElement('SIF_Header');
		$sifLogEntryHeader_sifHeader->appendChild($root->createElement('SIF_MsgId'))->appendChild($root->createTextNode($msgId));
		$sifLogEntryHeader_sifHeader->appendChild($root->createElement('SIF_Timestamp'))->appendChild($root->createTextNode($timestamp));
		$sifLogEntryHeader_sifHeader->appendChild($root->createElement('SIF_SourceId'))->appendChild($root->createTextNode($_SESSION['ZONE_NAME']));
		$sifLogEntryHeader->appendChild($sifLogEntryHeader_sifHeader);
		$sifLogEntry->appendChild($sifLogEntryHeader);
		
		$sifOriginalHeader = $root->createElement('SIF_OriginalHeader');
		
		$f = $root->createDocumentFragment();
		$f->appendXML($originalHeader);
		$domNode = $root->importNode($f, true);
		
		$sifOriginalHeader->appendChild($domNode);
		$sifLogEntry->appendChild($sifOriginalHeader);
		
		$sifLogEntry->appendChild($root->createElement('SIF_Category'))->appendChild($root->createTextNode($category));
		$sifLogEntry->appendChild($root->createElement('SIF_Code'))->appendChild($root->createTextNode($code));
		$sifLogEntry->appendChild($root->createElement('SIF_Desc'))->appendChild($root->createTextNode($desc));
		
		$sifEventObject->appendChild($sifLogEntry);
		$sifEvent->appendChild($sifHeader);
		$sifEvent->appendChild($sifObjectData);
		$sifMessage->item(0)->appendChild($sifEvent);
		
		$root->preserveWhiteSpace = false;
		$root->formatOutput       = false;
		$xmlStr = $root->saveXML();
		ZitLog::writeToLog(REC_XML, $xmlStr);

		return $xmlStr;
		
	}
	
	public static function buildSifAckMessage($msgId, $timestamp, $originalSourceId, $originalMsgId){
		$root = XmlHelper::buildSifMessageBase();
		
		$sifMessage = $root->getElementsByTagName('SIF_Message');
		
		$sifAck 	    	 = $root->createElement('SIF_Ack');
		$sifHeader       	 = $root->createElement('SIF_Header');
		
		$sifHeader->appendChild($root->createElement('SIF_MsgId'))->appendChild($root->createTextNode($msgId));
		
		if (isset($_SESSION['ZONE_VERSION'])){
			if($_SESSION['ZONE_VERSION'] == '1.5r1')
			{
				$dateTime = explode("T", $timestamp);
				$dateTimestamp = $dateTime[0];
				$dateTimestamp = str_replace("-", "", $dateTimestamp);
				$timeTimestamp = $dateTime[1];
				$sifHeader->appendChild($root->createElement('SIF_Date'))->appendChild($root->createTextNode($dateTimestamp));;
			
				$sifTimeNode         = $root->createElement('SIF_Time');
				$timeZone            = $root->createAttribute("Zone");
				$timeZoneValue       = $root->createTextNode("UTC".date('P'));
				$timeZone->appendChild($timeZoneValue);
				$sifTimeNode->appendChild($timeZone);
				$text                = $root->createTextNode($timeTimestamp);
				$sifTimeNode->appendChild($text);
				$sifHeader->appendChild($sifTimeNode);
			}
			else
			{
				$sifHeader->appendChild($root->createElement('SIF_Timestamp'))->appendChild($root->createTextNode($timestamp));
			}
		}
		else
		{
			$sifHeader->appendChild($root->createElement('SIF_Timestamp'))->appendChild($root->createTextNode($timestamp));
		}
		
		$sifHeader->appendChild($root->createElement('SIF_SourceId'))->appendChild($root->createTextNode(isset($_SESSION['ZONE_NAME'])? $_SESSION['ZONE_NAME'] : ''));
		$sifAck   ->appendChild($sifHeader);
		$sifAck   ->appendChild($root->createElement('SIF_OriginalSourceId'))->appendChild($root->createTextNode($originalSourceId));
		$sifAck   ->appendChild($root->createElement('SIF_OriginalMsgId'))->appendChild($root->createTextNode($originalMsgId));
		
		$sifMessage->item(0)->appendChild($sifAck);
		
		return $root;
	}
	
	public static function buildSuccessMessage($msgId,
											   $timestamp,
											   $originalSourceId,
											   $originalMsgId,
											   $sifCodeNum,
											   $originalMsg = null,
											   $desc = null){

		$root = XmlHelper::buildSifAckMessage($msgId, $timestamp, $originalSourceId, $originalMsgId);

		$sifAck = $root->getElementsByTagName('SIF_Ack')->item(0);

		$sifStatusNode = $root->createElement('SIF_Status');

		$sifCodeNode   = $root->createElement('SIF_Code');
		$text          = $root->createTextNode($sifCodeNum);
		$sifCodeNode->appendChild($text);
		$sifStatusNode->appendChild($sifCodeNode);

		if($desc != null){
			$sifDesc   = $root->createElement('SIF_Desc');
			$text      = $root->createTextNode($desc);
			$sifDesc->appendChild($text);
			$sifStatusNode->appendChild($sifDesc);
		}
		
		if($originalMsg !=  null){
			$f_dom = new DOMDocument('1.0', 'UTF-8');
			$f_dom->preserveWhiteSpace = false;
			$f_dom->formatOutput       = false;
			$f_dom->loadXML($originalMsg);
			$messageNode = $f_dom->getElementsByTagName('SIF_Message')->item(0);
			if($messageNode == null){
				$messageNode = $f_dom->getElementsByTagName('SIF_AgentACL')->item(0);
				if($messageNode == null){
					$messageNode = $f_dom->getElementsByTagName('SIF_ZoneStatus')->item(0);
				}
			}
			$xmlStr_ = $f_dom->saveXML($messageNode);
			
			$sifDataNode =  $root->createElement('SIF_Data');
			$f = $root->createDocumentFragment();
			$f->appendXML($xmlStr_);
			$domNode = $root->importNode($f, true);
			$sifDataNode->appendChild($domNode);
			$sifStatusNode->appendChild($sifDataNode);
		}

		$sifAck->appendChild($sifStatusNode);
		$root->preserveWhiteSpace = false;
		$root->formatOutput       = false;
		$xmlStr = $root->saveXML();
		ZitLog::writeToLog(REC_XML, $xmlStr);

		$length = strlen($xmlStr);
		header('Content-Length: '.$length);
		echo $xmlStr;
		
		session_destroy();
		exit;
	}
	
	public static function buildErrorMessage($categoryCode, 
											 $errorCode, 
											 $shortDesc, 
											 $longDesc, 
											 $msgId, 
											 $timestamp,
											 $originalSourceId, 
											 $originalMsgId,
											 $writeToLog=true){
											 
		$root = XmlHelper::buildSifAckMessage($msgId, $timestamp, $originalSourceId, $originalMsgId);
		
		$sifAck = $root->getElementsByTagName('SIF_Ack');
		
		$sifErrorNode = $root->createElement('SIF_Error');
		
		$sifCategoryNode      = $root->createElement('SIF_Category');
		$text                 = $root->createTextNode($categoryCode);
		$sifCategoryNode->appendChild($text);
		
		$sifCodeNode          = $root->createElement('SIF_Code');
		$text                 = $root->createTextNode($errorCode);
		$sifCodeNode->appendChild($text);
		
		$sifShortDescNode     = $root->createElement('SIF_Desc');
		$text                 = $root->createTextNode($shortDesc);
		$sifShortDescNode->appendChild($text);
		
		$sifLongDescNode      = $root->createElement('SIF_ExtendedDesc');
		$text                 = $root->createTextNode($longDesc);
		$sifLongDescNode->appendChild($text);
		
		$sifErrorNode->appendChild($sifCategoryNode);
		$sifErrorNode->appendChild($sifCodeNode);
		$sifErrorNode->appendChild($sifShortDescNode);
		$sifErrorNode->appendChild($sifLongDescNode);
		$sifAck->item(0)->appendChild($sifErrorNode);
		
		$xmlStr = $root->saveXML();
		if($writeToLog)
		{
			$_SESSION['SIF_MESSAGE_TYPE'] = 1;
			ZitLog::writeToLog(REC_XML, $xmlStr);
		}
		
		$length = strlen($xmlStr);
		header('Content-Length: '.$length);
		echo $xmlStr;
		
		session_destroy();
		exit;
	}
}

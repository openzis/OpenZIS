<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class XmlHelper{
	
	public static function buildSifMessageBase($zoneVersion, $versionNamespace){
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput       = false;
		$root = $dom->createElement('SIF_Message');
		
		$ver      = $dom->createAttribute('Version');
		$verValue = $dom->createTextNode($zoneVersion);
		
		$ver->appendChild($verValue);
		$root->appendChild($ver);
		
		$xmlns      = $dom->createAttribute('xmlns');
		$xmlnsValue = $dom->createTextNode($versionNamespace);
		
		$xmlns->appendChild($xmlnsValue);
		$root->appendChild($xmlns);
		
		$dom->appendChild($root);
		
		return $dom;
	}

	public static function buildSifAckMessage($msgId, $timestamp, $originalSourceId, $originalMsgId, $zoneVersion, $versionNamespace, $zoneName){
		$root = XmlHelper::buildSifMessageBase($zoneVersion, $versionNamespace);
		
		$sifMessage = $root->getElementsByTagName('SIF_Message');
		
		$sifAck 	    	 = $root->createElement('SIF_Ack');
		$sifHeader       	 = $root->createElement('SIF_Header');
		
		$sifHeader->appendChild($root->createElement('SIF_MsgId'))->appendChild($root->createTextNode($msgId));
		
		if($zoneVersion == '1.5r1')
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
		
		$sifHeader->appendChild($root->createElement('SIF_SourceId'))->appendChild($root->createTextNode($zoneName));
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
											   $zoneVersion, 
											   $versionNamespace, 
											   $zoneName,
											   $originalMsg = null,
											   $desc = null){

		$root = XmlHelper::buildSifAckMessage($msgId, $timestamp, $originalSourceId, $originalMsgId, $zoneVersion, $versionNamespace, $zoneName);

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
		
		return $xmlStr;
	}
}

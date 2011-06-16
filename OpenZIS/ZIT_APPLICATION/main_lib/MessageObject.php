<?php 

/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class MessageObject
{
	var $dom;
	var $res;
	var $xmlStr;				# The XML Message as a String
	var $msgId;					# SIF_MsgId
	var $headerNode;			# SIF_Header
	var $sourceId;  			# SIF_SourceId
	var $destinationId;			# SIF_DestinationId
	var $destinationIdFound;	# true or false
	var $version;
	var $messageType;			#

	public function MessageObject($xmlStr){
		
		if($xmlStr == '' || $xmlStr == null){
			ZitLog::writeToErrorLog('[Xml missing in request]', 'Xml is missing in request can not process message','Process Message', $_SESSION['ZONE_ID']);
			GeneralError::systemError($xmlStr);
			exit;
		}
		
		$this->xmlStr = $xmlStr;
		
		$this->dom    = new DomDocument();
		
		/***
		Make sure that preserve white space is off for the the DomDocument object.
		WhiteSpaces interfers with parsing the xml
		***/
		$this->dom->preserveWhiteSpace = false;
		$this->res    = $this->dom->loadXML($xmlStr);
		
		$dom = $this->dom;
		$this->headerNode     = $dom->getElementsByTagName('SIF_Header')->item(0);
		$this->sourceId       = $this->headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
		$this->msgId 		  = $this->headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
		
		$messageNode = $dom->getElementsByTagName('SIF_Message');
		$this->messageType = isset($messageNode->item(0)->childNodes->item(0)->nodeName) ? $messageNode->item(0)->childNodes->item(0)->nodeName : 'default';
		
		$sifDestinationId 	= $this->headerNode->getElementsByTagName('SIF_DestinationId');
        if($sifDestinationId->length != 0) {
            $this->destinationId = $sifDestinationId->item(0)->nodeValue;
			$this->destinationIdFound = true;
        }
		else {
			$this->destinationId = null;
			$this->destinationIdFound = false;
		}


	}


}
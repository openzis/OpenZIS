<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class RegisterError{
	
	public static function invalidPacketNumber($originalSourceId, $originalMsgId, $agentId, $packetNum, $packetNumDB){
		$categoryCode = "8";
		$sifCode      = "2";
		$shortDesc    = "SIF_PacketNumber is invalid in SIF_Response";
		$longDesc     = 'Invalid packet number of '.$packetNum.' was expecting '.$packetNumDB.'for response to agent '.$agentId.' request';
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp,
									 $originalSourceId, 
									 $originalMsgId);
	}

	public static function invalidSourceId($originalSourceId, $originalMsgId){
		$categoryCode = "8";
		$sifCode      = "2";
		$shortDesc    = "Registration Error";
		$longDesc     = "The SIF_SourceId is invalid. ".$originalSourceId;
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp,
									 $originalSourceId, 
									 $originalMsgId);
	}
	
	public static function invalidVersion($originalSourceId, $originalMsgId){
		$categoryCode = "8";
		$sifCode 	  = "4";
		$shortDesc    = "Registration Error";
		$longDesc 	  = "Requested SIF_Version(s) not supported.";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp, 
									 $originalSourceId, 
									 $originalMsgId);
	}
	
	public static function invalidMaxBuffer($originalSourceId, $originalMsgId){
		$categoryCode = "5";
		$sifCode      = "6";
		$shortDesc    = "Registration Error";
		$longDesc     = "Requested SIF_MaxBufferSize is too small.";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp,
									 $originalSourceId, 
									 $originalMsgId);
	}
	
	public static function genericError($originalSourceId, $originalMsgId, $longDescVal = 'Error Registering Agent.'){
		$categoryCode = "5";
		$sifCode      = "1";
		$shortDesc    = "Registration Error";
		$longDesc     = $longDescVal;
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp,
									 $originalSourceId, 
									 $originalMsgId);
	}
	
	public static function notRegisteredError($originalSourceId, $originalMsgId){
		$categoryCode = "5";
		$sifCode      = "1";
		$shortDesc    = "Registration Error";
		$longDesc     = "Agent Not Registerd";
		$timestamp    = Utility::createTimestamp();
		$msgId        = Utility::createMessageId();
		
		XmlHelper::buildErrorMessage($categoryCode, 
									 $sifCode, 
									 $shortDesc, 
									 $longDesc, 
									 $msgId, 
									 $timestamp, 
									 $originalSourceId, 
									 $originalMsgId);
	}

}

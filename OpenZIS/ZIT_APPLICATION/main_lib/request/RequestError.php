<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openzis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class RequestError{

	public static function invalidPacketNum($originalSourceId, $originalMsgId, $longDesc){
		$categoryCode = "8";
		$sifCode      = "12";
		$shortDesc    = "Response Error";
//		$longDesc     = "SIF_PacketNumber is invalid in SIF_Response: ";
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
	
	public static function invalidDestination($originalSourceId, $originalMsgId){
		$categoryCode = "8";
		$sifCode      = "14";
		$shortDesc    = "Response Error";
		$longDesc     = "SIF_DestinationId does not match SIF_SourceId from SIF_Request";
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
	
	public static function invalidBufferSize($originalSourceId, $originalMsgId){
		$categoryCode = "8";
		$sifCode      = "11";
		$shortDesc    = "Response Error";
		$longDesc     = "SIF_Response is larger than requested SIF_MaxBufferSize";
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
	
	public static function invalidMsgId($originalSourceId, $originalMsgId){
		$categoryCode = "8";
		$sifCode      = "10";
		$shortDesc    = "Response Error";
		$longDesc     = "Invalid SIF_RequestMsgId specified in SIF_Response";
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
	
	public static function genericError($originalSourceId, $originalMsgId){
		$categoryCode = "8";
		$sifCode      = "1";
		$shortDesc    = "Request Error";
		$longDesc     = "Buffer Size Invalid.";
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
	
	public static function invalidProvider($originalSourceId, $originalMsgId){
		$categoryCode = "8";
		$sifCode      = "4";
		$shortDesc    = "Request and Response";
		$longDesc     = "No Provider.";
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
		$sifCode      = "13";
		$shortDesc    = "Response Error";
		$longDesc     = "SIF_Response does not match any SIF_Version from SIF_Request.";
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

	
	public static function invalidObject($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "8";
		$sifCode      = "3";
		$shortDesc    = "Request Error";
		$longDesc     = "Invalid object (".objectName.").";
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

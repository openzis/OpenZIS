<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class ProvisionError{

	static function contextNotSupportedError($originalSourceId, $originalMsgId){
		$categoryCode = "12";
	    $sifCode      = "4";
	    $shortDesc    = "Generic Message Handling";
	    $longDesc     = "Context not supported";
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
		$categoryCode = "6";
	    $sifCode      = "1";
	    $shortDesc    = "Provision Error";
	    $longDesc     = "The given sourceId does not exist.";
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
		$categoryCode = "6";
		$sifCode      = "3";
		$shortDesc    = "Provision Error";
		$longDesc     = "Invalid object (".$objectName.").";
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
	
	public static function alreadyProvided($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "6";
		$sifCode      = "4";
		$shortDesc    = "Provision Error";
		$longDesc     = "Object already has a provider (".$objectName.").";
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
	
	public static function invalidPermissionToProvide($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "4";
		$sifCode      = "3";
		$shortDesc    = "Access and Permissions";
		$longDesc     = "No permission to provide given object (".$objectName.").";
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
	
	public static function invalidPermissionToSubscribe($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "4";
		$sifCode      = "4";
		$shortDesc    = "Access and Permissions";
		$longDesc     = "No permission to subscribe to given object (".$objectName.").";
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
	
	public static function invalidPermissionToPublishAdd($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "4";
	    $sifCode      = "10";
	    $shortDesc    = "Access and Permissions";
	    $longDesc     = "No permission to publish SIF_Event Add (".$objectName.").";
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
	
	public static function invalidPermissionToPublishChange($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "4";
	    $sifCode      = "12";
	    $shortDesc    = "Access and Permissions";
	    $longDesc     = "No permission to publish SIF_Event Change (".$objectName.").";
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
	
	public static function invalidPermissionToPublishDelete($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "4";
	    $sifCode      = "11";
	    $shortDesc    = "Access and Permissions";
	    $longDesc     = "No permission to publish SIF_Event Delete (".$objectName.").";
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
	
	public static function invalidPermissionToRequest($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "4";
	    $sifCode      = "5";
	    $shortDesc    = "Access and Permissions";
	    $longDesc     = "No permission to request this object (".$objectName.").";
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
	
	public static function invalidPermissionToRespond($originalSourceId, $originalMsgId, $objectName){
		$categoryCode = "4";
	    $sifCode      = "6";
	    $shortDesc    = "Access and Permissions";
	    $longDesc     = "No permission to respond to this object (".$objectName.").";
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
	
	public static function notProviderError($originalSourceId, $originalMsgId){
		$categoryCode = "6";
		$sifCode      = "5";
		$shortDesc    = "Not Provider";
		$longDesc     = "Not the provider of the object.";
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

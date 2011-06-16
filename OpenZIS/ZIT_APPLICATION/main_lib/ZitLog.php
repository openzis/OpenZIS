<?php 

/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2010  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement.
 
*/

class ZitLog{

	public static function writeToLog($xmlRec, $xmlSent)
	{
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('create_timestamp')      => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
						DBConvertor::convertCase('rec_xml') 		      => $xmlRec,
						DBConvertor::convertCase('sent_xml') 		   	  => $xmlSent,
						DBConvertor::convertCase('zone_id')          	  => $_SESSION['ZONE_ID'],
						DBConvertor::convertCase('agent_id')              => isset($_SESSION['AGENT_ID']) ? $_SESSION['AGENT_ID'] : 0,
						DBConvertor::convertCase('sif_message_type_id')   => $_SESSION['SIF_MESSAGE_TYPE'],
						DBConvertor::convertCase('log_message_type_id')   => 2
					 );
		if($db->insert(DBConvertor::convertCase('zit_log'), $data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function writeToErrorLog($shortErrorDesc, $longErrorDesc, $errorLocation, $zoneId=null, $agentId=null, $contextId=null)
	{
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('error_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
						DBConvertor::convertCase('short_error_desc')=> $shortErrorDesc,
						DBConvertor::convertCase('long_error_desc') => $longErrorDesc,
						DBConvertor::convertCase('error_location')  => $errorLocation,
						DBConvertor::convertCase('zone_id')         => $zoneId,
						DBConvertor::convertCase('agent_id')        => $agentId,
						DBConvertor::convertCase('context_id')      => $contextId
					 );
		if($db->insert(DBConvertor::convertCase('error_log'), $data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}





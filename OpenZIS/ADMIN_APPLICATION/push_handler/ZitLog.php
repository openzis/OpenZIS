<?php /*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class ZitLog{

	public static function writeToLog($xmlRec, $xmlSent, $zoneId, $agentId, $sifMessageTypeId)
	{
		$db = ZitDBAdapter::getDBAdapter();
		$data = array(
						'create_timestamp'    => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
						'rec_xml' 		      => $xmlRec,
						'sent_xml' 		   	  => $xmlSent,
						'zone_id'          	  => $zoneId,
						'agent_id'            => $agentId,
						'sif_message_type_id' => $sifMessageTypeId,
						'log_message_type_id' => 1
					 );
		if($db->insert('zit_log', $data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function writeToErrorLog($shortErrorDesc, $longErrorDesc, $errorLocation, $zoneId=null, $contextId=null, $agentId=null)
	{
		$db = ZitDBAdapter::getDBAdapter();
		$data = array(
						'error_timestamp' => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
						'short_error_desc'=> $shortErrorDesc,
						'long_error_desc' => $longErrorDesc,
						'error_location'  => $errorLocation,
						'zone_id'         => $zoneId,
						'agent_id'        => $agentId,
						'context_id'      => $contextId
					 );
		if($db->insert('error_log', $data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
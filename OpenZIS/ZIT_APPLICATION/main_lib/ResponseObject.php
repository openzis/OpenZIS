<?php 
/*
this file is part of OPENZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OPENZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OPENZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OPENZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class ResponseObject{

	var $responseId;
	var $nextPacketNum;
	var $requestMsgId;
	var $requesterId;

	public function ResponseObject($msgId){
		$db = Zend_Registry::get('my_db');
		
#		$query = "SELECT ".DBConvertor::convertCase('response_id').", ".DBConvertor::convertCase('next_packet_num').", ".DBConvertor::convertCase('agent_id_requester').", ".DBConvertor::convertCase('msg_id')." FROM ".DBConvertor::convertCase('response')." where request_msg_id = '".$msgId."' order by next_packet_num desc";		
#		$query = "SELECT ".DBConvertor::convertCase('id').", ".DBConvertor::convertCase('next_packet_num').", ".DBConvertor::convertCase('agt_id_out').", ".DBConvertor::convertCase('ref_msg_id')." FROM ".DBConvertor::convertCase('messagequeue')." where msg_type = 2 and ref_msg_id = '".$msgId."' order by next_packet_num desc";
#		$result = $db->fetchAll($query);
		
		switch(DB_TYPE) {
			case 'mysql':
				$query = "SELECT ".DBConvertor::convertCase('id').", ".DBConvertor::convertCase('next_packet_num').", ".DBConvertor::convertCase('agt_id_out').", ".DBConvertor::convertCase('ref_msg_id')." FROM ".DBConvertor::convertCase('messagequeue')." where msg_type = 2 and ref_msg_id = '".$msgId."' order by next_packet_num desc";
				$result = $db->fetchAll($query);
				$this->responseId     = isset($result[0]->id) ? $result[0]->id : null;
				$this->nextPacketNum  = isset($result[0]->next_packet_num) ? $result[0]->next_packet_num : null;
				$this->requestMsgId   = isset($result[0]->msg_Id) ? $result[0]->msg_Id : null;
				$this->requesterId    = isset($result[0]->agt_id_out) ? $result[0]->agt_id_out : null;
			break;
			case 'oci8':
				$query = "SELECT ".DBConvertor::convertCase('response_id').", ".DBConvertor::convertCase('next_packet_num').", ".DBConvertor::convertCase('agent_id_requester').", ".DBConvertor::convertCase('msg_id')." FROM ".DBConvertor::convertCase('response')." where request_msg_id = '".$msgId."' order by next_packet_num desc";
				$result = $db->fetchAll($query);
				$this->responseId     = $result[0]->ID;
				$this->nextPacketNum  = $result[0]->NEXT_PACKET_NUM;
				$this->requestMsgId   = isset($result[0]->MSG_ID) ? $result[0]->MSG_ID : null;
				$this->requesterId    = isset($result[0]->AGT_ID_OUT) ? $result[0]->AGT_ID_OUT : null;
			break;
		}
	}

	public static function responseExist($msgId){
		$db = Zend_Registry::get('my_db');
		
		switch(DB_TYPE) {
			case 'mysql':
				$query = "SELECT COUNT(*) AS num_rows FROM ".DBConvertor::convertCase('messagequeue')." WHERE ref_msg_id = '".$msgId."' and msg_type = 2";
				$result = $db->fetchAll($query);
				$rows = isset($result[0]->num_rows) ? $result[0]->num_rows : 0;
			break;
			case 'oci8':
				## This should be corrected:: ##
				$query = "SELECT COUNT(*) AS num_rows FROM ".DBConvertor::convertCase('response')." WHERE ref_msg_id = '".$msgId."'";
				$result = $db->fetchAll($query);
				$rows = isset($result[0]->NUM_ROWS) ? $result[0]->NUM_ROWS : 0;
			break;
		}

		if($rows != 0){
			return true;
		}
		else{
			return false;
		}
	}
}
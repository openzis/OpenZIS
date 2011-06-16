<?php /*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class RequestObject {

    var $maxBufferSize;
    var $requestId;
    var $msgId;
    var $version;
    var $requestSourceId;

    public function RequestObject($msgId) {
        $db = Zend_Registry::get('my_db');
		
		switch(DB_TYPE) {
			case 'mysql':
			    #MYSQL is using a single table to hold messages: 
				$query = "select m.id, m.version, m.maxbuffersize, a.source_id from messagequeue m, agent a "
						."where m.msg_type = 1 and m.agt_id_in = a.agent_id and m.msg_id = '".$msgId."'";

		        $result = $db->fetchAll($query);
        		$this->maxBufferSize   = $result[0]->maxbuffersize;
		        $this->requestId       = $result[0]->id;
		        $this->msgId           = $msgId;
		        $this->version         = $result[0]->version;
		        $this->requestSourceId = $result[0]->source_id;
			break;
			case 'oci8':
				$ra  = new RequestAgentVW($db);
				$where = "request_msg_id = '".$msgId."'";
				$result = $ra->fetchAll($where);
        		$this->maxBufferSize   = $result[0]->MAX_BUFFER_SIZE;
		        $this->requestId       = $result[0]->REQUEST_ID;
		        $this->msgId           = $msgId;
		        $this->version         = $result[0]->VERSION;
		        $this->requestSourceId = $result[0]->SOURCE_ID;
			break;
		}
    }

    public static function getRequesterId($msgId) {
        $db = Zend_Registry::get('my_db');

		switch(DB_TYPE) {
			case 'mysql':
				#MYSQL is using a single table to hold messages: 
				$request = new MessageQueues($db);
				$where = "msg_id = '".$msgId."' and msg_type = 1";
				$result = $request->fetchAll($where);
				return $result[0]->agt_id_in;
			break;
			case 'oci8':
				$request = new Requests($db);
				$where = "request_msg_id = '".$msgId."'";
				$result = $request->fetchAll($where);
				return $result[0]->AGENT_ID_REQUESTER;
			break;
		}
    }

    public static function getRequesterAgentMode($msgId) {
        $db = Zend_Registry::get('my_db');

		switch(DB_TYPE) {
			case 'mysql':
   		 	$query = "select agent_registered.agent_mode_id
					  FROM  agent_registered
					  inner join
					  	messagequeue on messagequeue.agt_id_in = agent_registered.agent_id and messagequeue.msg_id = '".$msgId." and msg_type = 1'
					  WHERE 
					  	agent_registered.unregister_timestamp is null
						and agent_registered.context_id = ".$_SESSION["CONTEXT_ID"]." 
						and agent_registered.zone_id = ".$_SESSION["ZONE_ID"];
			break;
			case 'oci8':
       		 	$query = "select agent_registered.agent_mode_id
						  FROM  agent_registered
						  inner join
						  	request on request.agent_id_requester = agent_registered.agent_id and request.request_msg_id = '".$msgId."'
						  WHERE 
						  	agent_registered.unregister_timestamp is null
							and agent_registered.context_id = ".$_SESSION["CONTEXT_ID"]." 
							and agent_registered.zone_id = ".$_SESSION["ZONE_ID"];
			break;
		}

        $result = $db->fetchAll($query);
        
		switch(DB_TYPE) {
			case 'mysql':
				return isset($result[0]->agent_mode_id) ? $result[0]->agent_mode_id : null;
			break;
			case 'oci8':
				return isset($result[0]->AGENT_MODE_ID) ? $result[0]->AGENT_MODE_ID : null;
			break;
		}	
    }

    public function compareDestinationId($sourceId) {
        if(strtolower($sourceId) != strtolower($this->requestSourceId)) {
            return false;
        }
        else {
            return true;
        }
    }

    public function compareBufferSize($passedBuffer) {
        if($passedBuffer <= $this->maxBufferSize) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function validRequestMsgId($msgId) {
        $db = Zend_Registry::get('my_db');
		
		switch(DB_TYPE) {
			case 'mysql':
				$request = new MessageQueues($db);
				$where = "msg_id = '".$msgId."' and msg_type = 1";
			break;
			case 'oci8':
				$request = new Requests($db);
				$where = "request_msg_id = '".$msgId."'";
			break;
		}

	 	$result = $request->fetchAll($where);
       	$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function validMessageVersion($msgId, $version) {
        $db = Zend_Registry::get('my_db');

		switch(DB_TYPE) {
			case 'mysql':
				$message = new MessageQueues($db);
				$where = "msg_id = '".$msgId."' and msg_type = 1";
				$result = $message->fetchAll($where);
        		$request_version = $result[0]->version;
			break;
			case 'oci8':
				$request = new Requests($db);
				$where = "request_msg_id = '".$msgId."'";
				$result = $request->fetchAll($where);
        		$request_version = $result[0]->VERSION;
			break;
		}
			
        $pos = strpos($request_version, '*');
        if($pos === false) {
            if($version == $request_version) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            if(substr($version, 0, $pos) == substr($request_version, 0, $pos)) {
                return true;
            }
            else {
                return false;
            }
        }
    }
}

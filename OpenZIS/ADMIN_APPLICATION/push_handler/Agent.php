<?php /*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Agent{

	var $agentId;
	var $sourceId;
	var $name;
	var $password;
	var $frozen;
	var $frozenMsgId;
	var $zoneId;
	var $contextId;

	public function Agent($sourceId, $zoneId, $contextId){
		$db = ZitDBAdapter::getDBAdapter();

		$query = "SELECT
				  agent_id,
				  agent_name,
				  password
				  from agent where lower(source_id) = lower('".$sourceId."')";

		$result = $db->fetchAll($query);

		$this->agentId        = intval($result[0]->agent_id);
		$this->sourceId       = $sourceId;
		$this->name           = $result[0]->agent_name;
		$this->password       = $result[0]->password;
		$this->zoneId         = $zoneId;
		$this->contextId      = $contextId;
		$this->getFrozenStatus();
		$this->getFrozenMsgId();
	}
	
	public function isRegistered(){
		$db = ZitDBAdapter::getDBAdapter();

		$query = "select count(*) as num_rows from agent_registered where
				  unregister_timestamp is null
				  and agent_id = $this->agentId
				  and context_id = ".$this->contextId."
				  and zone_id = ".$this->zoneId ;
		$result = $db->fetchAll($query);

		$rows = $result[0]->num_rows;
		if($rows == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	private function getFrozenStatus(){
		$db = ZitDBAdapter::getDBAdapter();
		
		$query = 'select frozen 
				  from agent_registered
				  where 
				  agent_id = '.$this->agentId.'
				  and unregister_timestamp is null 
				  and context_id = '.$this->contextId.' 
				  and zone_id = '.$this->zoneId;
		
		$result = $db->fetchAll($query);
		$this->frozen = $result[0]->frozen;
		
	}
	
	private function getFrozenMsgId(){
		$db = ZitDBAdapter::getDBAdapter();
		
		$query = 'select frozen_msg_id 
				  from agent_registered
				  where 
				  agent_id = '.$this->agentId.'
				  and unregister_timestamp is null 
				  and context_id = '.$this->contextId.' 
				  and zone_id = '.$this->zoneId;
		
		$result = $db->fetchAll($query);
		$this->frozenMsgId = $result[0]->frozen_msg_id;
	}
	
	public static function checkSourceId($sourceId){
		$db = ZitDBAdapter::getDBAdapter();

		$query = "select count(*) as num_rows from agent where lower(source_id) = lower('".$sourceId."')";
		$result = $db->fetchAll($query);
		$rows = $result[0]->num_rows;
		if($rows == 1){
			return true;
		}
		else{
			return false;
		}
	}

	public function freezeAgent(){
		$db = ZitDBAdapter::getDBAdapter();
		$data = array(
						'frozen' => 1
					 );
		$n = $db->update('agent_registered', $data, 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$this->contextId.' and zone_id = '.$this->zoneId);
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function unFreezeAgent(){
		$db = ZitDBAdapter::getDBAdapter();
		$data = array(
						'frozen' => 0
					 );
		$n = $db->update('agent_registered', $data, 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$this->contextId.' and zone_id = '.$this->zoneId);
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function setFrozenMsgId($id){
		$db = ZitDBAdapter::getDBAdapter();
		$data = array(
						'frozen_msg_id' => $id
					 );
		$n = $db->update('agent_registered', $data, 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$this->contextId.' and zone_id = '.$this->zoneId);
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}
}

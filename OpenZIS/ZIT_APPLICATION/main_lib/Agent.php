<?php 
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Agent{

	var $agentId;
	var $sourceId;
	var $name;
	var $password;
	var $username;
	var $maxBuffersize;
	var $protocol;
	var $callBackUrl;
	var $agentMode;
	var $secure;
	var $version;
	var $asleep = 0;
	var $frozen;
	var $sifSpeed = 0;
	var $certCommonName;
	var $certCommonDn;
	var $authenticationLevel;
	var $frozenMsgId;

	public function Agent($sourceId){
		$db = Zend_Registry::get('my_db');
		
		$agents = new Agents($db);
		$result = $agents->fetchAll("lower(source_id) = lower('". $sourceId."')");
		
		switch(DB_TYPE) {
			case 'mysql':
				$this->agentId        = intval($result[0]->agent_id);
				$this->name           = $result[0]->agent_name;
				$this->password       = $result[0]->password;
				$this->username       = $result[0]->username;
				$this->certCommonName = $result[0]->cert_common_name;
				$this->certCommonDn = $result[0]->cert_common_dn;
			break;
			case 'oci8':
				$this->agentId        = intval($result[0]->AGENT_ID);
				$this->name           = $result[0]->AGENT_NAME;
				$this->password       = $result[0]->PASSWORD;
				$this->username       = $result[0]->USERNAME;
				$this->certCommonName = $result[0]->CERT_COMMON_NAME;
				$this->certCommonDn = $result[0]->CERT_COMMON_DN;
			break;
		}

		$this->sourceId       = $sourceId;
		$_SESSION['AGENT_ID'] = $this->agentId;
		$_SESSION['AGENT_SOURCE_ID'] = $sourceId;
		$this->getFrozenStatus();
		$this->getFrozenMsgId();
	}
	
	private function getFrozenStatus(){
		$db = Zend_Registry::get('my_db');
		
		$agentsR = new AgentRegistered($db);
		$result = $agentsR->fetchAll('agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					$this->frozen = $row->frozen;
				break;
				case 'oci8':
					$this->frozen = $row->FROZEN;
				break;
			}
		}
	}
	
	private function getFrozenMsgId(){
		$db = Zend_Registry::get('my_db');
		
		$agentsR = new AgentRegistered($db);
		$result = $agentsR->fetchAll('agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					$this->frozenMsgId = $row->frozen_msg_id;
				break;
				case 'oci8':
					$this->frozenMsgId = $row->FROZEN_MSG_ID;
				break;
			}
		}
	}
	
	public static function getAgentSourceId($agentId){
		$db = Zend_Registry::get('my_db');
		
		$query = "select source_id from agent where agent_id = $agentId";
		$result = $db->fetchAll($query);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					return $row->source_id;
				break;
				case 'oci8':
					return $row->SOURCE_ID;
				break;
			}
		}
	}
	
	public static function allowedToRegister($agentId){
		$db = Zend_Registry::get('my_db');
		$agentZC = new AgentZoneContext($db);
		$result = $agentZC->fetchAll('agent_id = '.$agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		
		if($result->count() != 1){
			return false;
		}
		else{
			return true;
		}
	}

	public static function bufferSizeAllowed($bufferSize, $agentId){
		$db = Zend_Registry::get('my_db');
		
		$agentsR = new AgentRegistered($db);
		$result = $agentsR->fetchAll('agent_id = '.$agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					$agentBufferSize = $result[0]->maxbuffersize;
				break;
				case 'oci8':
					$agentBufferSize = $result[0]->MAXBUFFERSIZE;
				break;
			}
		}
		
		if($bufferSize <= $agentBufferSize){
			return true;
		}
		else{
			return false;
		}
	}

	public static function checkSourceId($sourceId){
		$db = Zend_Registry::get('my_db');
		
		$agent = new Agents($db);
		$quote_sourceId = $db->quote($sourceId);
		$result = $agent->fetchAll("source_id = '".$sourceId."'");
		
		$count = $result->count();

		if($count != 0){
			return true;
		}
		else{
			return false;
		}
	}

	public function isRegistered(){
		$db = Zend_Registry::get('my_db');
		$agentsR = new AgentRegistered($db);
		$result = $agentsR->fetchAll('agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);

		$rows = $result->count();
		if($rows == 1){
			return true;
		}
		else{
			return false;
		}
	}

	public static function convertAgentMode($value){
		if(strtolower($value) == 'push'){
			return 1;
		}
		else{
			return 2;
		}
	}

	public static function convertSecure($value){
		if(strtolower($value) == 'yes'){
			return 1;
		}
		else{
			return 2;
		}
	}

	public function unRegister(){
		$db = Zend_Registry::get('my_db');
		
		$agentsR = new AgentRegistered($db);
		$where = 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID'];
		$data = array(DBConvertor::convertCase('unregister_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime()));
		$n = $agentsR->update($data, $where);
		
		if($n == 1){
			$where = 'agent_id = '.$this->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID'];
			
			$agentPro = new AgentProvisions($db);
			$agentPro->delete($where);

			$agentSub = new AgentSubscriptions($db);
			$agentSub->delete($where);
						
			return true;
		}
		else{
			return false;
		}
	}

	public function getAgentRegistrationVersion(){
		$db = Zend_Registry::get('my_db');
		$agentsR = new AgentRegistered($db);
		$result = $agentsR->fetchAll('agent_id = '.$this->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					return $row->sif_version;
				break;
				case 'oci8':
					return $row->SIF_VERSION;
				break;
			}
		}
	}
	
	public function getAgentRegistrationAgentMode(){
		$db = Zend_Registry::get('my_db');
		$agentsR = new AgentRegistered($db);
		$result = $agentsR->fetchAll('agent_id = '.$this->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					return $row->agent_mode_id;
				break;
				case 'oci8':
					return $row->AGENT_MODE_ID;
				break;
			}
		}
	}
	
	public function getAgentRegistrationSifAuthenticationLevel(){
		$db = Zend_Registry::get('my_db');
		$agentsR = new AgentRegistered($db);
		$result = $agentsR->fetchAll('agent_id = '.$this->agentId.' and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					return $row->authentication_level_id;
				break;
				case 'oci8':
					return $row->AUTHENTICATION_LEVEL_ID;
				break;
			}
		}
		
	}

	public function updateRegistration(){
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('callback_url')       	  	=> $this->callBackUrl,
						DBConvertor::convertCase('agent_mode_id')      	  	=> $this->agentMode,
						DBConvertor::convertCase('register_timestamp') 	  	=> new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
						DBConvertor::convertCase('protocol_type')  		  	=> $this->protocol,
						DBConvertor::convertCase('secure')  			 	=> $this->secure,
						DBConvertor::convertCase('sif_version') 		 	=> $this->version,
						DBConvertor::convertCase('asleep') 		    	  	=> $this->asleep,
						DBConvertor::convertCase('maxbuffersize')  	 	  	=> $this->maxBuffersize,
						DBConvertor::convertCase('zone_id')           	 	=> $_SESSION['ZONE_ID'],
						DBConvertor::convertCase('context_id')              => $_SESSION['CONTEXT_ID'],
						DBConvertor::convertCase('authentication_level_id') => $this->authenticationLevel,
						DBConvertor::convertCase('frozen')             	  => 0
					 );
		$agentsR = new AgentRegistered($db);
		$n = $agentsR->update($data, ' agent_id = '.$this->agentId.' and unregister_timestamp is NULL and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}

	public function register(){
		$db = Zend_Registry::get('my_db');
		
		$agentR = new AgentRegistered($db);
		
		$data = array(  DBConvertor::convertCase('agent_id')           		=> $this->agentId,
						DBConvertor::convertCase('callback_url')       		=> $this->callBackUrl,
						DBConvertor::convertCase('agent_mode_id')      		=> $this->agentMode,
						DBConvertor::convertCase('register_timestamp') 		=> new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
						DBConvertor::convertCase('protocol_type')  	 		=> $this->protocol,
						DBConvertor::convertCase('secure')  			 	=> $this->secure,
						DBConvertor::convertCase('sif_version') 		 	=> $this->version,
						DBConvertor::convertCase('asleep') 		     		=> $this->asleep,
						DBConvertor::convertCase('maxbuffersize')  	 		=> $this->maxBuffersize,
						DBConvertor::convertCase('zone_id')            		=> $_SESSION['ZONE_ID'],
						DBConvertor::convertCase('context_id')         		=> $_SESSION['CONTEXT_ID'],
						DBConvertor::convertCase('authentication_level_id') => $this->authenticationLevel,
						DBConvertor::convertCase('frozen')             	  	=> 0 );
						
		try {
			$agentR->insert($data);
			return true;
		} catch (Zend_Db_Exception $e) {
			echo $e->getMessage();
			return false;
		}
		
/*		$n = $agentR->insert($data);
		
		echo 'value of $n = '.$n.'.';
		
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
*/
	}
	
	public function freezeAgent(){
		$db = Zend_Registry::get('my_db');
		$ra = new AgentRegistered($db);
		$data = array(
						DBConvertor::convertCase('frozen') => 1
					 );
		$n = $ra->update($data, 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function unFreezeAgent(){
		$db = Zend_Registry::get('my_db');
		$ra = new AgentRegistered($db);
		$data = array(
						DBConvertor::convertCase('frozen') => 0
					 );
		$n = $ra->update($data, 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function setFrozenMsgId($id){
		$db = Zend_Registry::get('my_db');
		$ra = new AgentRegistered($db);
		$data = array(
						DBConvertor::convertCase('frozen_msg_id') => $id
					 );
		$n = $ra->update( $data, 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}

	public function putToSleep(){
		$db = Zend_Registry::get('my_db');
		$ra = new AgentRegistered($db);
		$data = array(DBConvertor::convertCase('asleep') => new Zend_Db_Expr('1'));
		$n = $ra->update($data, 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}

	public function wakeup(){
		$db = Zend_Registry::get('my_db');
		$ra = new AgentRegistered($db);
		$data = array(
						DBConvertor::convertCase('asleep') => new Zend_Db_Expr('0'),
						DBConvertor::convertCase('frozen') => 0
					 );
		$n = $ra->update($data, 'agent_id = '.$this->agentId.' and unregister_timestamp is null and context_id = '.$_SESSION['CONTEXT_ID'].' and zone_id = '.$_SESSION['ZONE_ID']);
		if($n == 1){
			return true;
		}
		else{
			return false;
		}
	}

}

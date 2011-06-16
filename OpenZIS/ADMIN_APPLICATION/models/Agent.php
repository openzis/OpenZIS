<?php

/*
this file is part of OpenZIS (Zone Integration Server) http://www.openzis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/

class Agent {
    /*
  Agent Mode Id
  1 = push
  2 = pusll
    */

    var $agentId;
    var $agentDesc;
    var $adminId;
    var $active;
    var $agentCallbackUrl = 'N/A';
    var $authenticationLevel = '';
    var $sourceId;
    var $password = '';
    var $username = '';
    var $status = 'Not Registered';
    var $sleeping = '';
    var $numPushMessages;
    var $numReceivedMessages;
    var $providing = array();
    var $subscribing = array();
    var $pushMessages = array();
    var $receivedMessages = array();
    var $createdUsername;
    var $certCommonName;
	var $certCommonDn;
  var $ipaddress;
  var $maxbuffersize;

    public function Agent($agentId, $zoneId = null) {

    	$db2 = Zend_Registry::get('my_db2');
    	$agt = new Agents($db2);
    	$result = $agt->fetchAll("agent_id = ".$agentId);

    	$this->agentId = $agentId;
    
    switch(DB_TYPE) {
            case 'mysql':
            $this->agentDesc        = $result[0]->agent_name;
            $this->sourceId         = $result[0]->source_id;
        	$this->username         = $result[0]->username;
            $this->password         = $result[0]->password;
            $this->certCommonName   = $result[0]->cert_common_name;
			$this->certCommonDn    	= $result[0]->cert_common_dn;
        	$this->ipaddress      	= isset($result[0]->ipaddress) ? $result[0]->ipaddress : '';
        	$this->maxbuffersize  	= isset($result[0]->maxbuffersize) ? $result[0]->maxbuffersize : '';
            $this->active           = $result[0]->active;
            $this->adminId          = $result[0]->admin_id;
        	$this->pushMessages   	= Agent::getMessages($this->agentId, 1, $zoneId);
            $this->receivedMessages = Agent::getMessages($this->agentId, 2, $zoneId);
      break;
      case 'oci8':
            $this->agentDesc        = $result[0]->AGENT_NAME;
            $this->sourceId         = $result[0]->SOURCE_ID;
        	$this->username         = $result[0]->USERNAME;
            $this->password         = $result[0]->PASSWORD;
            $this->certCommonName   = $result[0]->CERT_COMMON_NAME;
			$this->certCommonDn     = $result[0]->CERT_COMMON_DN;
        	$this->ipaddress    	= $result[0]->IPADDRESS;
        	$this->maxbuffersize  	= $result[0]->MAXBUFFERSIZE;
            $this->active           = $result[0]->ACTIVE;
            $this->adminId          = $result[0]->ADMIN_ID;
        	$this->pushMessages   	= Agent::getMessages($this->agentId, 1, $zoneId);
            $this->receivedMessages = Agent::getMessages($this->agentId, 2, $zoneId);
      break;
    }
    
    $this->getCreatorUsername();
    $this->numPushMessages  = 0; #$this->getNumMessages($zoneId, 1);
    $this->numReceivedMessages = 0;
    $this->numReceivedMessages = $this->getNumMessages($zoneId, 2);
    
    }

    public function getCreatorUsername() {
        $admin = new ZitAdmin($this->adminId);
        $this->createdUsername = $admin->username;
    }

    public static function getMessages($agentId, $logMessageType, $zoneId = null) {
    $limit = NUMMESSAGES;
    $db = Zend_Registry::get('my_db2');

        $messages = array();

        if($zoneId != null) {
            $select = $db->select()
                    ->from(array(DBConvertor::convertCase('l') => DBConvertor::convertCase('zit_log')),
                    array(DBConvertor::convertDateFormat(DBConvertor::convertCase('l.create_timestamp'), 'm-dd-yyyy-t', DBConvertor::convertCase('create_timestamp')), DBConvertor::convertCase('l.log_id')));
                    $select->join(array(DBConvertor::convertCase('mt') => DBConvertor::convertCase('sif_message_type')),
                    DBConvertor::convertCase('l.sif_message_type_id').' = '.DBConvertor::convertCase('mt.sif_message_type_id'),
                    array(DBConvertor::convertCase('sif_message_type_desc')))
                    ->where('l.zone_id = ?', $zoneId)
          			->where('l.agent_id = ?', $agentId)
                    ->where('l.log_message_type_id = ?', $logMessageType)
                    ->where('l.archived = 0')
                    ->order(array(DBConvertor::convertCase('create_timestamp DESC')))
                    ->limit($limit,0);
        }
        else {
            $select = $db->select()
                    ->from(array(DBConvertor::convertCase('l') => DBConvertor::convertCase('zit_log')),
                    array(DBConvertor::convertDateFormat(DBConvertor::convertCase('l.create_timestamp'), 'm-dd-yyyy-t', DBConvertor::convertCase('create_timestamp')), DBConvertor::convertCase('l.log_id')));
                    $select->join(array(DBConvertor::convertCase('mt') => DBConvertor::convertCase('sif_message_type')),
                    DBConvertor::convertCase('l.sif_message_type_id').' = '.DBConvertor::convertCase('mt.sif_message_type_id'),
                    array(DBConvertor::convertCase('sif_message_type_desc')))
                    ->where('l.log_message_type_id = ?', $logMessageType)
          			->where('l.agent_id = ?', $agentId)
                    ->where('l.archived = 0')
                    ->order(array(DBConvertor::convertCase('create_timestamp DESC')))
                    ->limit($limit,0);
        }

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        foreach($result as $row) {
      switch(DB_TYPE) {
              case 'mysql':
          $zle = new ZitLogEntry_Agent($row->log_id, $row->create_timestamp, $row->sif_message_type_desc);
        break;
        case 'oci8':
          $empty = null;
          $zle = new ZitLogEntry_Agent($row->log_id, $row->CREATE_TIMESTAMP, $row->SIF_MESSAGE_TYPE_DESC);
        break;
      }
            array_push($messages, $zle);
        }
        return $messages;
    }

    public function getNumMessages($zoneId, $logMessageType) {
        $num = 0;
    	$db = Zend_Registry::get('my_db2');
        if($zoneId != null) {
      		$query = "select count(*) as num_rows from ".DBConvertor::convertCase('messagequeue')." where agt_id_out = ".$this->agentId." and zone_id = ".$zoneId." and status_id in (1,2)";
        }
        else {
      		$query = "select count(*) as num_rows from ".DBConvertor::convertCase('messagequeue')." where agt_id_out = ".$this->agentId." and status_id in (1,2)";
    	}

        $result = $db->fetchAll($query);
    	foreach($result as $row) {
	      switch(DB_TYPE) {
	              case 'mysql':
	          $num = $num + $row->num_rows;
	        break;
	        case 'oci8':
	          $num = $num + $row->NUM_ROWS;
	        break;
	      }
	    }
	    return $num;
    }

    public function getObjectsProviding($zoneId) {
        //$db = ZitDBAdapter::getDBAdapter();
    $db = Zend_Registry::get('my_db');

        $query = "SELECT
          o.object_name,
          p.publish_add as add_,
          p.publish_delete as delete_,
          p.publish_change as update_,
          ".DBConvertor::convertDateFormat('p.provision_timestamp', 'm-dd-yyyy-t', 'provision_timestamp')."
          from
          ".DBConvertor::convertCase('agent_provisions')." p,
          ".DBConvertor::convertCase('data_object')." o
          where
          o.object_id = p.object_type_id
          and agent_id = $this->agentId
          and zone_id = $zoneId";

        $result = $db->fetchAll($query);
        foreach($result as $row) {
            switch(DB_TYPE) {
              case 'mysql':
          $provideObject = new ProvideObject($row->object_name, $row->add_, $row->delete_, $row->update_, $row->provision_timestamp);
        break;
        case 'oci8':
          $provideObject = new ProvideObject($row->OBJECT_NAME, $row->ADD_, $row->DELETE_, $row->UPDATE_, $row->PROVISION_TIMESTAMP);
        break;
      }
            array_push($this->providing, $provideObject);
        }
    }

    public function getObjectsSubscribing($zoneId) {
        //$db = ZitDBAdapter::getDBAdapter();
    $db = Zend_Registry::get('my_db');

        $query = "select
          o.object_name,
          ".DBConvertor::convertDateFormat('s.subscribe_timestamp', 'm-dd-yyyy-t', 'subscribe_timestamp')."
          from
          ".DBConvertor::convertCase('agent_subscriptions')." s,
          ".DBConvertor::convertCase('data_object')." o
          where
          o.object_id = s.object_type_id
          and agent_id = $this->agentId
          and zone_id = $zoneId";

        $result = $db->fetchAll($query);
        foreach($result as $row) {
            switch(DB_TYPE) {
              case 'mysql':
          $subscribeObject = new SubscribeObject($row->object_name, $row->subscribe_timestamp);
        break;
        case 'oci8':
          $subscribeObject = new SubscribeObject($row->OBJECT_NAME, $row->SUBSCRIBE_TIMESTAMP);
        break;
      }
            array_push($this->subscribing, $subscribeObject);
        }
    }

    public static function getAgents() {
        //$db = ZitDBAdapter::getDBAdapter();
    $db = Zend_Registry::get('my_db');
    $agt = new Agents($db);

        $agents = array();
        $adminLevel = $_SESSION['ADMIN_LEVEL'];
        $adminId    = $_SESSION['ADMIN_ID'];
        $query = null;
        if($adminLevel == Utility::$SUPER_ADMIN) {
            //$query = "select agent_name, agent_id from agent where active = 1";
      $result = $agt->fetchAll("active = 1");
        }
        else {
            //$query = "select agent_name, agent_id from agent where admin_id = $adminId and active = 1";
      $result = $agt->fetchAll("admin_id = $adminId and active = 1");
        }

//        $result = $agt->fetchAll($query);
        foreach($result as $row) {
      switch(DB_TYPE) {
              case 'mysql':
          array_push($agents, array('name' => $row->agent_name, 'id' => $row->agent_id));
        break;
        case 'oci8':
          array_push($agents, array('name' => $row->AGENT_NAME, 'id' => $row->AGENT_ID));
        break;
      }
    }
        return $agents;
    }

    public static function hasAgents($zoneId) {
        //$db = ZitDBAdapter::getDBAdapter();
    $db = Zend_Registry::get('my_db');

//        $query = "select agent_id as num_rows from agent_zone_context where zone_id = ".$zoneId;
//      $result = $db->fetchAll($query);

    $azc = new AgentZoneContext($db);
    $where = "zone_id = $zoneId";
    $result = $azc->fetchAll($where);

        if($result->count() != 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public static  function getAllAgentsInZone($zoneId) {

    $db = Zend_Registry::get('my_db');
    $agents = array();

    $query = "select azc.agent_id from ".DBConvertor::convertCase('agent_zone_context')." azc, " 
			.DBConvertor::convertCase('agent')." a where a.agent_id = azc.agent_id "
			." and azc.zone_id = $zoneId order by a.agent_name";
			
	$result = $db->fetchAll($query);


    foreach($result as $row) {
      switch(DB_TYPE) {
        case 'mysql':
          $agent_id = $row->agent_id;
        break;
        case 'oci8':
          $agent_id = $row->AGENT_ID;
        break;
      }
            $agent = new Agent($agent_id, $zoneId);
            $agent = agent::getRegisteredAgentsInformation($agent, $zoneId);
            array_push($agents, $agent);
        }
        return $agents;
    }

    public static function getAgent($agentId, $zoneId) {
        $agent = new Agent($agentId, $zoneId);
        $agent->getObjectsProviding($zoneId);
        $agent->getObjectsSubscribing($zoneId);
        $agent = Agent::getRegisteredAgentsInformation($agent, $zoneId);
        return $agent;
    }

    public static function getRegisteredAgentsInformation($agent, $zoneId) {
        
    $status_id = -1;
    /* 
    TODO: Push this info into a Query and Cache It
    Data Based on agent_modes tables 
    */
    $agentModesLKUP = array(1 => 'Push', 2 => 'Pull');

    $db = Zend_Registry::get('my_db');
    
    $ar = new agentRegistered($db);
    $where = "agent_id = $agent->agentId and zone_id = $zoneId and unregister_timestamp is null";
    $result = $ar->fetchAll($where);

    foreach($result as $row) {
      switch(DB_TYPE) {
              case 'mysql':
          if($row->agent_mode_id != null) {
            	$status_id = $row->agent_mode_id;
                $agent->sleeping = $row->asleep;
                $agent->authenticationLevel = isset($row->authentication_level_id) ? $row->authentication_level_id : '0';
              }
          if($row->agent_mode_id != null && $row->callback_url != null) {
            $agent->agentCallbackUrl = $row->callback_url;
          } else { $agent->agentCallbackUrl = 'N/A'; }
        break;
        case 'oci8':
          if($row->AGENT_MODE_ID != null) {
                  $status_id = $row->AGENT_MODE_ID;
            $agent->sleeping = $row->ASLEEP;
                  $agent->authenticationLevel = isset($row->AUTHENTICATION_LEVEL_ID) ? $row->AUTHENTICATION_LEVEL_ID : '0';
              }
          if($row->AGENT_MODE_ID != null  && $row->CALLBACK_URL != null) {
            $agent->agentCallbackUrl = $row->CALLBACK_URL;
          } else { $agent->agentCallbackUrl = 'N/A';}
        break;
      }
    }
    
    if ($status_id != -1 ){
      $agent->status = $agentModesLKUP[$status_id];
    }
    
    return $agent;
    }

    public static function deleteAgent($agentId) {
        //$db = ZitDBAdapter::getDBAdapter();
    $db = Zend_Registry::get('my_db');

        $db->delete(DBConvertor::convertCase('agent'),               DBConvertor::convertCase('agent_id').' = '.$agentId);
        $db->delete(DBConvertor::convertCase('agent_zone_context'),  DBConvertor::convertCase('agent_id').' = '.$agentId);
        $db->delete(DBConvertor::convertCase('agent_provisions'),    DBConvertor::convertCase('agent_id').' = '.$agentId);
        $db->delete(DBConvertor::convertCase('agent_subscriptions'), DBConvertor::convertCase('agent_id').' = '.$agentId);
        $db->delete(DBConvertor::convertCase('agent_requester'),     DBConvertor::convertCase('agent_id').' = '.$agentId);
        $db->delete(DBConvertor::convertCase('agent_responder'),     DBConvertor::convertCase('agent_id').' = '.$agentId);
        $db->delete(DBConvertor::convertCase('agent_permissions'),   DBConvertor::convertCase('agent_id').' = '.$agentId);
        $data = array(
                DBConvertor::convertCase('unregister_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime())
        );
        $db->update(DBConvertor::convertCase('agent_registered'), $data, 'agent_id = '.$agentId.' and unregister_timestamp is null');
    }

    public static function agentExists($name, $sourceId) {
        $db = ZitDBAdapter::getDBAdapter();
    $num_rows = 0;

        $select = $db->select()
                ->from(array(DBConvertor::convertCase('a') => DBConvertor::convertCase('agent')),array(DBConvertor::convertCase('num_rows') => 'count(*)'))
                ->where('lower(a.source_id) = lower(?)', $sourceId)
                ->orWhere('lower(a.agent_name) = lower(?)', $name);

        $stmt = $select->query();
        $result = $stmt->fetchAll();
    

    foreach($result as $row) {
      switch(DB_TYPE) {
              case 'mysql':
          $num_rows = $row->num_rows;
        break;
        case 'oci8':
          $num_rows = $row->NUM_ROWS;
        break;
      }
    }

        if($num_rows == 0) {
            return false;
        }
        else {
            return true;
        }
    }

    public static function addAgent( 
      $name,
            $sourceId,
            $password,
            $username,
            $ipaddress,
      $maxbuffersize) {
        //$db = ZitDBAdapter::getDBAdapter();
    $db = Zend_Registry::get('my_db');
    $empty = null;
    
    $data = array(
        DBConvertor::convertCase('admin_id')         => $_SESSION['ADMIN_ID'],
                DBConvertor::convertCase('agent_name')       => $name,
                DBConvertor::convertCase('source_id')        => $sourceId
        );

    if ($username != $empty and $password != $empty){
      $data[DBConvertor::convertCase('username')] = $username;
      $data[DBConvertor::convertCase('password')] = $password;
    }

    if ($ipaddress != $empty){
      $data[DBConvertor::convertCase('ipaddress')] = $ipaddress;
    }
    
    if ($maxbuffersize != $empty){
      $data[DBConvertor::convertCase('maxbuffersize')] = $maxbuffersize;
    }

/*
  DBConvertor::convertCase('username')         => $username,
    DBConvertor::convertCase('password')         => $password,
    DBConvertor::convertCase('ipaddress')        => $ipaddress
*/


  $frontendOptions = array( 'lifetime' => Null );
  $backendOptions = array( 'cache_dir' => CACHE );

  $cache = Zend_Cache::factory('Output',
                               'File',
                               $frontendOptions,
                               $backendOptions);
  $cacheID='Zone1BuildNavigation'.$_SESSION['ADMIN_ID'];
  $cache->remove($cacheID);
  $cache->remove('Zone1BuildNavigation1');
  

        if($db->insert(DBConvertor::convertCase('agent'), $data)) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function updateAgent(
            $name,
            $sourceId,
      		$username,
            $password,
            $agentId,
            $ipaddress,
      		$maxbuffersize,
            $active) {
        
    $empty =null;
    $db = Zend_Registry::get('my_db');
        $data = array(
                DBConvertor::convertCase('agent_name')       => $name,
                DBConvertor::convertCase('source_id')        => $sourceId,
                DBConvertor::convertCase('active')           => $active
        );
  		
	      $data[DBConvertor::convertCase('username')] = $username;
	      $data[DBConvertor::convertCase('password')] = $password;

	      $data[DBConvertor::convertCase('ipaddress')] = $ipaddress;
    
	      $data[DBConvertor::convertCase('maxbuffersize')] = $maxbuffersize;

        $n = $db->update(DBConvertor::convertCase('agent'), $data, 'agent_id = '.$agentId);

    $frontendOptions = array( 'lifetime' => Null );
    $backendOptions = array( 'cache_dir' => CACHE );

    $cache = Zend_Cache::factory('Output',
                                 'File',
                                 $frontendOptions,
                                 $backendOptions);
    $cacheID='Zone1BuildNavigation'.$_SESSION['ADMIN_ID'];
    $cache->remove($cacheID);
    $cache->remove('Zone1BuildNavigation1');
    
    }

    public static function getAllAgents() {
        $agents = array();

        //$db = ZitDBAdapter::getDBAdapter();
    	$db = Zend_Registry::get('my_db2');
    	$agt = new Agents($db);
        $adminLevel = $_SESSION['ADMIN_LEVEL'];
        $adminId    = $_SESSION['ADMIN_ID'];

        if($adminLevel == Utility::$SUPER_ADMIN) {
      		$select = $agt->select()->order('source_id');
        }
        else {
      		$select = $agt->select()->where('admin_id = '.$adminId)->order('source_id');
        }
		$result = $agt->fetchAll($select);

        foreach($result as $row) {
      		switch(DB_TYPE) {
		              case 'mysql':
		                $agent = new Agent($row->agent_id);
		        break;
		        case 'oci8':
		                $agent = new Agent($row->AGENT_ID);
		        break;
		      }
            array_push($agents, $agent);
        }
        return $agents;
    }
}
?>
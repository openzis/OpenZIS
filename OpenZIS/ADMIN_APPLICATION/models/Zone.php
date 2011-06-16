<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/

class Zone {
    var $zoneId;
    var $zoneDesc;
    var $sourceId;
    var $numAgents;
    var $update;
    var $create;
    var $version;
    var $versionId;
    var $pushedMessages = array();
    var $receivedMessages = array();
    var $sleeping;
    var $zoneUrl;
    var $creator;
    var $zoneAuthenticationType;
    var $numMessages;

    public function Zone($zoneId, $loadLog = true) {
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');

        $query = "select
				  z.zone_desc,
				  za.admin_username,
				  z.sleeping,
				  z.source_id,
				  z.zone_authentication_type_id,
				  ".DBConvertor::convertDateFormat(DBConvertor::convertCase('z.create_timestamp'), 'm-dd-yyyy-t', DBConvertor::convertCase('create_timestamp')).",
				  ".DBConvertor::convertDateFormat(DBConvertor::convertCase('z.update_timestamp'), 'm-dd-yyyy-t', DBConvertor::convertCase('update_timestamp')).",
				  z.version_id,
				  v.version_desc as version
				  from ". DBConvertor::convertCase('zones z') 
						." left outer join ". DBConvertor::convertCase('versions')." v on (v.version_id = z.version_id)
				  		   inner join ". DBConvertor::convertCase('zit_admin')." za on (za.admin_id = z.admin_id) where z.zone_id = $zoneId";
				
        $result = $db->fetchAll($query);

		switch(DB_TYPE) {
            case 'mysql':
        		$this->zoneId      			  = $zoneId;
		        $this->creator     			  = $result[0]->admin_username;
		        $this->zoneAuthenticationType = $result[0]->zone_authentication_type_id;
		        $this->zoneDesc    		  	  = $result[0]->zone_desc;
		        $this->sourceId    			  = $result[0]->source_id;
		        $this->update      			  = $result[0]->update_timestamp;
		        $this->create      			  = $result[0]->create_timestamp;
		        $this->version    			  = $result[0]->version;
		        $this->versionId   			  = $result[0]->version_id;
		        $this->sleeping    			  = $result[0]->sleeping;
			break;
			case 'oci8':
        		$this->zoneId      			  = $zoneId;
		        $this->creator     			  = $result[0]->ADMIN_USERNAME;
		        $this->zoneAuthenticationType = $result[0]->ZONE_AUTHENTICATION_TYPE_ID;
		        $this->zoneDesc    		  	  = $result[0]->ZONE_DESC;
		        $this->sourceId    			  = $result[0]->SOURCE_ID;
		        $this->update      			  = $result[0]->UPDATE_TIMESTAMP;
		        $this->create      			  = $result[0]->CREATE_TIMESTAMP;
		        $this->version    			  = $result[0]->VERSION;
		        $this->versionId   			  = $result[0]->VERSION_ID;
		        $this->sleeping    			  = $result[0]->SLEEPING;
			break;
		}

        $this->numAgents = Zone::getNumAgents($zoneId);
        if($loadLog) {
            $this->pushedMessages = $this->getLogEntries(1);
            $this->receivedMessages = $this->getLogEntries(2);
        }
        $this->getZoneUrl();
        $this->getNumberLogEntries();
    }

    public function getZoneUrl() {
        $zit = new Zit(1);
        $this->zoneUrl = $zit->zitUrl.'/'.$this->sourceId;
    }

    public function getNumberLogEntries() {
		$db = Zend_Registry::get('my_db2');
		
        $select = $db->select()
                ->from(array(DBConvertor::convertCase('l') => DBConvertor::convertCase('zit_log')),array(DBConvertor::convertCase('num_rows') => 'count(*)'))
                ->where("l.zone_id = $this->zoneId")
                ->where('l.archived = 0');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
		foreach($result as $row) {	
			switch(DB_TYPE) {
	            case 'mysql':
			        $this->numMessages = $result[0]->num_rows;
				break;
				case 'oci8':
			        $this->numMessages = $result[0]->NUM_ROWS;
				break;
			}
		}

    }

    public function delete(){

		$db = Zend_Registry::get('my_db');

        $db->delete(DBConvertor::convertCase('error_log'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('agent_filters'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('agent_registered'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('agent_filters'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('zit_log'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('agent_subscriptions'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('event'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('agent_provisions'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('agent_permissions'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('response'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('agent_zone_context'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('push_handler'), 'zone_id = '.$this->zoneId);
        $db->delete(DBConvertor::convertCase('zones'), 'zone_id = '.$this->zoneId);

		$frontendOptions = array( 'lifetime' => Null );
		$backendOptions = array( 'cache_dir' => CACHE );

		$cache = Zend_Cache::factory('Output',
		                             'File',
		                             $frontendOptions,
		                             $backendOptions);
		$cacheID='DataObject1Navigation';
		$cache->clean(Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG, array($cacheID));

    }

    public static function getLogEntries_zoneId($zoneId, $zitLogType) {
	
		$db = Zend_Registry::get('my_db2');
        $entries = array();

	        $select = $db->select()
	                ->from(array(DBConvertor::convertCase('l') => DBConvertor::convertCase('zit_log')),
	                array(DBConvertor::convertDateFormat(DBConvertor::convertCase('l.create_timestamp'), 'm-dd-yyyy-t', DBConvertor::convertCase('create_timestamp')), DBConvertor::convertCase('l.log_id')))
	                ->join(array(DBConvertor::convertCase('a') => DBConvertor::convertCase('agent')),
	                DBConvertor::convertCase('a.agent_id').' = '.DBConvertor::convertCase('l.agent_id').' and '.DBConvertor::convertCase('l.zone_id').' = '.$zoneId,
	                array(DBConvertor::convertCase('agent_name')))
	                ->join(array(DBConvertor::convertCase('mt') => DBConvertor::convertCase('sif_message_type')),
	                DBConvertor::convertCase('l.sif_message_type_id').' = '.DBConvertor::convertCase('mt.sif_message_type_id'),
	                array(DBConvertor::convertCase('sif_message_type_desc')))
	                ->where('l.log_message_type_id = ?', $zitLogType)
	                ->where('l.archived = 0')
	                ->order(array(DBConvertor::convertCase('create_timestamp DESC')))
	                ->limit( 100, 0);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

		$counter = 0;
		$counter = count($result);

        foreach($result as $row) {
			switch(DB_TYPE) {
	            case 'mysql':
            		$zle = new ZitLogEntry($row->log_id, $row->create_timestamp, $row->agent_name, $row->sif_message_type_desc);
				break;
				case 'oci8':
            		$zle = new ZitLogEntry($row->$LOG_ID, isset($row->CREATE_TIMESTAMP) ? $row->CREATE_TIMESTAMP : 'N/A', $row->AGENT_NAME, $row->SIF_MESSAGE_TYPE_DESC);
				break;
			}
            array_push($entries, $zle);
        }


        return array($counter, $entries);
    }

    public function getLogEntries($zitLogType) {

		$db = Zend_Registry::get('my_db2');

        $messages = array();

        $select = $db->select()
                ->from(array(DBConvertor::convertCase('l') => DBConvertor::convertCase('zit_log')),
                array(DBConvertor::convertCase('create_timestamp') => DBConvertor::convertDateFormat(DBConvertor::convertCase('l.create_timestamp'), 'm-dd-yyyy-t', DBConvertor::convertCase('create_timestamp')), DBConvertor::convertCase('l.log_id')))
                ->join(array(DBConvertor::convertCase('a') => DBConvertor::convertCase('agent')),
                DBConvertor::convertCase('a.agent_id').' = '.DBConvertor::convertCase('l.agent_id').' and '.DBConvertor::convertCase('l.zone_id').' = '.$this->zoneId,
                array(DBConvertor::convertCase('agent_name')))
                ->join(array(DBConvertor::convertCase('mt') => DBConvertor::convertCase('sif_message_type')),
                DBConvertor::convertCase('l.sif_message_type_id').' = '.DBConvertor::convertCase('mt.sif_message_type_id'),
                array(DBConvertor::convertCase('sif_message_type_desc')))
                ->where('l.log_message_type_id = ?', $zitLogType)
                ->where('l.archived = 0')
                ->order(array(DBConvertor::convertCase('create_timestamp DESC')))
                ->limit(100,0);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
        			$zle = new ZitLogEntry($row->log_id, isset($row->create_timestamp) ? $row->create_timestamp : null, $row->agent_name, $row->sif_message_type_desc);
					// $zle = new ZitLogEntry($row->log_id, null, null, $row->rec_xml, $row->sent_xml, isset($row->create_timestamp) ? $row->create_timestamp : null, $row->agent_name, $row->sif_message_type_desc);
				break;
				case 'oci8':
					//$rec_xml = $row->REC_XML;
					//$sent_xml = $row->SENT_XML;
	        		$zle = new ZitLogEntry($row->LOG_ID, isset($row->CREATE_TIMESTAMP) ? $row->CREATE_TIMESTAMP : 'N/A', $row->AGENT_NAME, $row->SIF_MESSAGE_TYPE_DESC);
					// $zle = new ZitLogEntry($rec_xml->read($rec_xml->size()), $sent_xml->read($sent_xml->size()), isset($row->CREATE_TIMESTAMP) ? $row->CREATE_TIMESTAMP : 'N/A', $row->AGENT_NAME, $row->SIF_MESSAGE_TYPE_DESC);
				break;
			}
            array_push($messages, $zle);
        }

        return $messages;
    }

    public static function ArchiveLogEntries($zoneId) {
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		$data = array(DBConvertor::convertCase('archived') => 1);
        $db->update(DBConvertor::convertCase('zit_log'), $data, 'zone_id = '.$zoneId);
		
    }

    public static function putZoneToSleep($zoneId, $val) {
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
        $data = array(DBConvertor::convertCase('sleeping') => $val);
        $db->update(DBConvertor::convertCase('zones'), $data, DBConvertor::convertCase('zone_id').' = '.$zoneId);
    }

    public static function getNumAgents($zoneId) {
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
        $query = "select count(*) as num_rows from ".DBConvertor::convertCase('agent_zone_context')." where zone_id = $zoneId";
        $result = $db->fetchAll($query);
		switch(DB_TYPE) {
            case 'mysql':
				return $result[0]->num_rows;
			break;
			case 'oci8':
				return $result[0]->NUM_ROWS;
			break;
		}
    }

    public static function removeAgent($agentId, $zoneId, $contextId) {
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
        $db->delete(DBConvertor::convertCase('agent_zone_context'),  DBConvertor::convertCase('agent_id').' = '.$agentId.' and '.DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);
        $db->delete(DBConvertor::convertCase('agent_provisions'),    DBConvertor::convertCase('agent_id').' = '.$agentId.' and '.DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);
        $db->delete(DBConvertor::convertCase('agent_subscriptions'), DBConvertor::convertCase('agent_id').' = '.$agentId.' and '.DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);
        $db->delete(DBConvertor::convertCase('agent_permissions'),   DBConvertor::convertCase('agent_id').' = '.$agentId.' and '.DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);
        $data = array(
                DBConvertor::convertCase('unregister_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime())
        );
        $db->update(DBConvertor::convertCase('agent_registered'), $data, DBConvertor::convertCase('agent_id').' = '.$agentId.' and '.DBConvertor::convertCase('unregister_timestamp').' is null and '.DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);

		$frontendOptions = array( 'lifetime' => Null );
		$backendOptions = array( 'cache_dir' => CACHE );

		$cache = Zend_Cache::factory('Output',
		                             'File',
		                             $frontendOptions,
		                             $backendOptions);
		$cacheID='DataObject1Navigation';
		$cache->clean(Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG, array($cacheID));
		
    }

    public static function addAgent($agentId, $zoneId) {
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
        if($agentId == null) {
            return 3;
        }
        $query = "select count(*) as num_rows from ".DBConvertor::convertCase('agent_zone_context')." where agent_id = $agentId and zone_id = $zoneId";
        $result = $db->fetchAll($query);
		switch(DB_TYPE) {
            case 'mysql':
				$rows = $result[0]->num_rows;
			break;
			case 'oci8':
				$rows = $result[0]->NUM_ROWS;
			break;
		}
        if($rows != 0) {
            return 2;
        }
        else {
            $data = array(
                    DBConvertor::convertCase('agent_id')   => $agentId,
                    DBConvertor::convertCase('zone_id')    => $zoneId,
                    DBConvertor::convertCase('context_id') => 1
            );
            if($db->insert(DBConvertor::convertCase('agent_zone_context'), $data)) {
				$frontendOptions = array( 'lifetime' => Null );
				$backendOptions = array( 'cache_dir' => CACHE );

				$cache = Zend_Cache::factory('Output',
				                             'File',
				                             $frontendOptions,
				                             $backendOptions);
				$cacheID='DataObject1Navigation';
				$cache->clean(Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG, array($cacheID));
				
                return 1;
            }
            else {
                return 0;
            }
        }
    }

    public static function zoneExists($desc, $sourceId) {

		$num_of_rows = 0;
		$db = Zend_Registry::get('my_db2');
        $select = $db->select()
                ->from(array(DBConvertor::convertCase('z') => DBConvertor::convertCase('zones')),array(DBConvertor::convertCase('num_rows') => 'count(*)'))
                ->where('lower(z.source_id) = lower(?)', $sourceId)
                ->orWhere('lower(z.zone_desc) = lower(?)', $desc);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

		switch(DB_TYPE) {
            case 'mysql':
				$num_of_rows = isset($result[0]->num_rows) ? $result[0]->num_rows : 0;
			break;
			case 'oci8':
				$num_of_rows = isset($result[0]->NUM_ROWS) ? $result[0]->NUM_ROWS : 0;
			break;
		}
		
        if($num_of_rows == 0) {
            return false;
        }
        else {
            return true;
        }
    }

    public static function addZone($description, $sourceId, $versionId, $zoneAuthenticationType) {
		$db = Zend_Registry::get('my_db');
        $data = array(
                DBConvertor::convertCase('zone_desc')           		  => $description,
                DBConvertor::convertCase('source_id')           		  => $sourceId,
                DBConvertor::convertCase('ZONE_AUTHENTICATION_TYPE_ID')   => $zoneAuthenticationType,
                DBConvertor::convertCase('admin_id')            		  => $_SESSION['ADMIN_ID'],
                DBConvertor::convertCase('create_timestamp')    		  => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
                DBConvertor::convertCase('version_id')         		  	  => $versionId
        );

        if($db->insert(DBConvertor::convertCase('zones'), $data)) {
	
			$frontendOptions = array( 'lifetime' => Null );
			$backendOptions = array( 'cache_dir' => CACHE );

			$cache = Zend_Cache::factory('Output',
			                             'File',
			                             $frontendOptions,
			                             $backendOptions);
			$cacheID='DataObject1Navigation';
			$cache->clean(Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG, array($cacheID));

            return true;			
        }
        else {
            return false;
        }
    }

    public static function updatingVersion($zoneId, $versionId) {

		$db = Zend_Registry::get('my_db');

        $select = $db->select()
                ->from(array(DBConvertor::convertCase('z') => DBConvertor::convertCase('zones')),
                array('num_rows' => DBConvertor::convertCase('count(*)')))
                ->where('zone_id = ?', $zoneId)
                ->where('version_id = ?', $versionId);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

		switch(DB_TYPE) {
            case 'mysql':
				$num_of_rows = isset($result[0]->num_rows) ? $result[0]->num_rows : 0;
			break;
			case 'oci8':
				$num_of_rows = isset($result[0]->NUM_ROWS) ? $result[0]->NUM_ROWS : 0;
			break;
		}

        if($num_of_rows == 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function deleteAllPermissions_subscriptions_provisions_requests_responds($zoneId, $contextId = 1) {
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');

        $db->delete(DBConvertor::convertCase('agent_provisions'),    DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);
        $db->delete(DBConvertor::convertCase('agent_subscriptions'), DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);
        $db->delete(DBConvertor::convertCase('agent_permissions'),   DBConvertor::convertCase('zone_id').' = '.$zoneId.' and '.DBConvertor::convertCase('context_id').' = '.$contextId);
    }

    public static function unregisterAllAgents($zoneId, $contextId = 1) {
	
		$db = Zend_Registry::get('my_db');

        $select = $db->select()
                ->from(array(DBConvertor::convertCase('r') => DBConvertor::convertCase('agent_registered')),
                array(DBConvertor::convertCase('registration_id')))
                ->where('zone_id = ?', $zoneId)
                ->where('context_id = ?', $contextId)
                ->where('unregister_timestamp is null');

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        foreach($result as $row) {
			$registration_id = 0;
            $data = array(
                    DBConvertor::convertCase('unregister_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime())
            );

		switch(DB_TYPE) {
            case 'mysql':
				$registration_id = $row->registration_id;
			break;
			case 'oci8':
				$registration_id = $row->REGISTRATION_ID;
			break;
		}
		
            $db->update(DBConvertor::convertCase('agent_registered'), $data, DBConvertor::convertCase('registration_id').' = '. $registration_id);
        }
    }

    public static function updateZone($description, $sourceId, $zoneId, $versionId, $zoneAuthenticationType) {
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');

        if(Zone::updatingVersion($zoneId, $versionId)) {
            Zone::deleteAllPermissions_subscriptions_provisions_requests_responds($zoneId);
            Zone::unregisterAllAgents($zoneId);
        }

        $data = array(DBConvertor::convertCase('zone_desc')		              => $description,
                	  DBConvertor::convertCase('source_id') 	   			  => $sourceId,
                	  DBConvertor::convertCase('ZONE_AUTHENTICATION_TYPE_ID') => $zoneAuthenticationType,
                	  DBConvertor::convertCase('update_timestamp') 			  => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
                	  DBConvertor::convertCase('version_id')       			  => $versionId
        );

        $n = $db->update(DBConvertor::convertCase('zones'), $data, 'zone_id = '.$zoneId);

		$frontendOptions = array( 'lifetime' => Null );
		$backendOptions = array( 'cache_dir' => CACHE );

		$cache = Zend_Cache::factory('Output',
		                             'File',
		                             $frontendOptions,
		                             $backendOptions);
		
		$cacheID='DataObject1Navigation';
		$cache->clean(Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG, array($cacheID));
		
    }

    public static function getAllZones() {
        $zones = array();

		$db = Zend_Registry::get('my_db2');

        $adminLevel = $_SESSION['ADMIN_LEVEL'];
        $adminId    = $_SESSION['ADMIN_ID'];
		
		$z = new Zones($db);
		
        if($adminLevel == Utility::$SUPER_ADMIN) {
			$select = $z->select()->order('zone_desc ASC');
			$rows = $z->fetchAll($select);
        }
        else {
			$select = $z->select()->where('admin_id = '.$adminId)->order('zone_desc ASC');
        }
		
		$rows = $z->fetchAll($select);

        foreach($rows as $row) {
            switch(DB_TYPE) {
	            case 'mysql':
					$zone = new Zone($row->zone_id, false);
				break;
				case 'oci8':
					$zone = new Zone($row->ZONE_ID, false);
				break;
			}
            array_push($zones, $zone);
        }
        return $zones;
    }

    public static function getXMLMessage($id, $type) {
		$db = Zend_Registry::get('my_db2');
		$xml = null;
		
		$zisLog = new ZisLog($db);
		$where  = "log_id = $id";
		$rows = $zisLog->fetchAll($where);
		
        foreach($rows as $row) {
            switch(DB_TYPE) {
	            case 'mysql':
					if ($type == 1){
						$xml = $row->rec_xml;
					} else {
						$xml = $row->sent_xml;
					}
				break;
				case 'oci8':
					if ($type == 1){
						$xml = $row->REC_XML;
					} else {
						$xml = $row->SENT_XML;
					}
				break;
			}
        }
        return $xml;
    }
}

<?php /*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2010  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class DataObject {

    var $objectId;
    var $objectName;
    var $contextId = 1;

    public function DataObject($objectName) {
        $db = Zend_Registry::get('my_db');
		$do = new DataObjects($db);
		$quoted = $db->quote($objectName);
		$where = "lower(object_name) = lower($quoted) "
			   . " and version_id = ".$_SESSION['ZONE_VERSION_ID'];
		
		$result = $do->fetchAll($where);
		foreach($result as $row) {
			switch(DB_TYPE) {
				case 'mysql':
					$this->objectId   = intval($row->object_id);
				break;
				case 'oci8':
					$this->objectId   = intval($row->OBJECT_ID);
				break;
			}
		}

        $this->objectName = $objectName;
    }

    public static function alreadyProviding($objectId, $contextId, $agentId) {
        $db = Zend_Registry::get('my_db');
		$agt_pro = new AgentProvisions($db);
		$where = 'object_type_id = '.$objectId.' and agent_id = '. $agentId. ' and zone_id = '.$_SESSION['ZONE_ID'].' and context_id = ' .$contextId;
		
		$result = $agt_pro->fetchAll($where);

        if($result->count() != 0) {
            return true;
        }
        else {
            return false;
        }

    }

    public static function saveProvides($dataObjects, $agentId) {
        $db = Zend_Registry::get('my_db');
        foreach($dataObjects as $object) {
            $alreadyProviding = DataObject::alreadyProviding($object->objectId, $object->contextId, $agentId);
            if(!$alreadyProviding) {
				$agt_pro = null;
				$agt_pro = new AgentProvisions($db);
                $data = array(
                        DBConvertor::convertCase('agent_id')            => $agentId,
                        DBConvertor::convertCase('object_type_id')      => $object->objectId,
                        DBConvertor::convertCase('provision_timestamp') => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
                        DBConvertor::convertCase('context_id')          => $object->contextId,
                        DBConvertor::convertCase('publish_add')		  => '1',
                        DBConvertor::convertCase('publish_delete')	  => '1',
                        DBConvertor::convertCase('publish_change')	  => '1',
                        DBConvertor::convertCase('zone_id')             => $_SESSION['ZONE_ID']
                );
                $agt_pro->insert($data);
            }
        }
    }

    public static function isProvider($objectName, $agentId) {
        $db = Zend_Registry::get('my_db');
		$pdo = new ProvisionDataObjectVW($db);
		$where = " lower(object_name) = lower('".$objectName."')"
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and agent_id = ". $agentId
				." and context_id = ".$_SESSION['CONTEXT_ID']." and zone_id = ".$_SESSION['ZONE_ID'];

		$result = $pdo->fetchAll($where);
        if($result->count() == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function getProviderId($objectName) {
		$empty = null;
		$id = $empty;
		
        $db = Zend_Registry::get('my_db');
		$pdo = new ProvisionDataObjectVW($db);
		$where = " lower(object_name) = lower('".$objectName."')"
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and context_id = ".$_SESSION['CONTEXT_ID']." and zone_id = ".$_SESSION['ZONE_ID'];

        $result = $pdo->fetchAll($where);
		$row = $result->count();
		if ($row != 0){
			switch(DB_TYPE) {
				case 'mysql':
					$id = $result[0]->agent_id;
				break;
				case 'oci8':
					$id = $result[0]->AGENT_ID;
				break;
			}
		}

		if($id == $empty) {
            return 0;
        }
        else {
            return $id;
        }
    }

    public static function validResponder($objectName, $sourceId) {
        $db = Zend_Registry::get('my_db');
		$ardoa = new AgentResponderDataObjectAgentVW($db);
		$where = " lower(object_name) = lower('".$objectName."') "
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and lower(source_id) = lower('".$sourceId."') "
				." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID'];

		$result = $ardoa->fetchAll($where);
        $rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function validProvider($objectName, $sourceId) {
        $db = Zend_Registry::get('my_db');
		$pdoa = new ProvisionDataObjectAgentVW($db);
		$where = " lower(object_name) = lower('".$objectName."')"
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and lower(source_id) = lower('".$sourceId."') "
				." and context_id = ".$_SESSION['CONTEXT_ID']." and zone_id = ".$_SESSION['ZONE_ID'];

		$result = $pdoa->fetchAll($where);
        $rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function objectExists($objectName) {
        $db = Zend_Registry::get('my_db');
		$do = new DataObjects($db);
		$quoted = $db->quote($objectName);
		$where = "lower(object_name) = lower($quoted) and version_id = ".$_SESSION['ZONE_VERSION_ID'];

		$result = $do->fetchAll($where);
		$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function alreadyProvided($agentId, $objectName) {
        $db = Zend_Registry::get('my_db');
		$pdo = new ProvisionDataObjectVW($db);
		$where = " agent_id != ".$agentId." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID']." and lower(object_name) = lower('".$objectName."')"
		  		." and version_id = ".$_SESSION['ZONE_VERSION_ID'];
		$result = $pdo->fetchAll($where);


        $rows = $result->count();
        if($rows == 0) {
            return false;
        }
        else {
            return true;
        }
    }

    public static function allowedToRespond($agentId, $objectName) {
        $db = Zend_Registry::get('my_db');
		$apdo = new AgentPermisionDataObjectVW($db);
		$where = " agent_id = ".$agentId
				." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID']
				." and lower(object_name) = lower('".$objectName."') "
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and can_respond = 1";

        $result = $apdo->fetchAll($where);
		$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function allowedToRequest($agentId, $objectName) {
        $db = Zend_Registry::get('my_db');
		$apdo = new AgentPermisionDataObjectVW($db);
		$where = " agent_id = ".$agentId
				." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID']
				." and lower(object_name) = lower('".$objectName."') "
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and can_request = 1";

		$result = $apdo->fetchAll($where);
		$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function allowedToPublishDelete($agentId, $objectName) {
        $db = Zend_Registry::get('my_db');
		$apdo = new AgentPermisionDataObjectVW($db);
		$where = " agent_id = ".$agentId
				." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID']
				." and lower(object_name) = lower('".$objectName."') "
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and can_delete = 1";

		$result = $apdo->fetchAll($where);
		$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function allowedToPublishChange($agentId, $objectName) {
        $db = Zend_Registry::get('my_db');
		$apdo = new AgentPermisionDataObjectVW($db);
		$where = " agent_id = ".$agentId
				." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID']
				." and lower(object_name) = lower('".$objectName."') "
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and can_update = 1";

		$result = $apdo->fetchAll($where);
		$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function allowedToPublishAdd($agentId, $objectName) {
        $db = Zend_Registry::get('my_db');
		$apdo = new AgentPermisionDataObjectVW($db);
		$where = " agent_id = ".$agentId
				." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID']
				." and lower(object_name) = lower('".$objectName."') "
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and can_add = 1";

		$result = $apdo->fetchAll($where);
		$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function allowedToSubscribe($agentId, $objectName) {
        $db = Zend_Registry::get('my_db');
		$apdo = new AgentPermisionDataObjectVW($db);
		$where = " agent_id = ".$agentId
				." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID']
				." and lower(object_name) = lower('".$objectName."') "
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and can_subscribe = 1";

		$result = $apdo->fetchAll($where);
		$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function allowedToProvide($agentId, $objectName, $contextId = 1) {
        $db = Zend_Registry::get('my_db');

		$apdo = new AgentPermisionDataObjectVW($db);
		
		$where = " agent_id = ".$agentId
				." and context_id = ".$_SESSION['CONTEXT_ID']
				." and zone_id = ".$_SESSION['ZONE_ID']
				." and lower(object_name) = lower('".$objectName."') "
				." and version_id = ".$_SESSION['ZONE_VERSION_ID']
				." and can_provide = 1";

		$result = $apdo->fetchAll($where);
		$rows = $result->count();
        if($rows == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function updateEvent($agentId, $orginalMsgId, $msgId) {
        $db = Zend_Registry::get('my_db');
#		$event = new Events($db);
#
#        $data = array(
#                DBConvertor::convertCase('status_id') => 3
#        );
#        $where = "msg_id = '".$orginalMsgId."'";
#        $res = $event->update($data, $where);
#        if($res != 1) {
#			$response = new Responses($db);
#            $res2 = $response->update($data, $where);
#            if($res2 != 1) {
#				$request = new Requests($db);
#                return $request->update($data, $where);
#            }
#            else {
#                return $res2;
#            }
#        }
#        else {
#            return $res;
#        }
#    }

		$message = new MessageQueues($db);
		$data = array(
		              DBConvertor::convertCase('status_id') => 3,
					  DBConvertor::convertCase('ack_msg_id') => $msgId
		);
		$where = "msg_id = '".$orginalMsgId."'";
		return $message->update($data, $where);
	}
	
}

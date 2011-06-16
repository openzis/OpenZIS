<?php
/*
this file is part of OPENZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OPENZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OPENZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OPENZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Permission {
    var $permissionId;
    var $zoneId;
    var $agentId;
    var $contextId;
    var $objectId;
    var $provide;
    var $subscribe;
    var $add;
    var $update;
    var $delete_;
    var $request;
    var $respond;
    var $objectName;
    var $dataElements = array();

    public function Permission($id) {
		$db = Zend_Registry::get('my_db');

        $select = "SELECT
				  p.agent_id,
				  p.zone_id,
				  p.context_id,
				  p.object_id,
				  p.can_provide,
				  p.can_subscribe,
				  p.can_add,
				  p.can_update,
				  p.can_delete,
				  p.can_request,
				  p.can_respond,
				  o.object_name
				  FROM ".DBConvertor::convertCase('agent_permissions')." p, ".DBConvertor::convertCase('data_object')." o
				  where o.object_id = p.object_id and p.permission_id = ".$id;
				
        $result = $db->fetchAll($select);
		foreach($result as $row) {
			switch(DB_TYPE) {
	            case 'mysql':
					$this->permissionId = $id;
			        $this->agentId      = $row->agent_id;
			        $this->contextId    = $row->context_id;
			        $this->objectId     = $row->object_id;
			        $this->provide      = $row->can_provide;
			        $this->subscribe    = $row->can_subscribe;
			        $this->add          = $row->can_add;
			        $this->update       = $row->can_update;
			        $this->delete_      = $row->can_delete;
			        $this->request      = $row->can_request;
			        $this->respond      = $row->can_respond;
			        $this->zoneId       = $row->zone_id;
			        $this->objectName   = $row->object_name;
				break;
				case 'oci8':
					$this->permissionId = $id;
			        $this->agentId      = $row->AGENT_ID;
			        $this->contextId    = $row->CONTEXT_ID;
			        $this->objectId     = $row->OBJECT_ID;
			        $this->provide      = $row->CAN_PROVIDE;
			        $this->subscribe    = $row->CAN_SUBSCRIBE;
			        $this->add          = $row->CAN_ADD;
			        $this->update       = $row->CAN_UPDATE;
			        $this->delete_      = $row->CAN_DELETE;
			        $this->request      = $row->CAN_REQUEST;
			        $this->respond      = $row->CAN_RESPOND;
			        $this->zoneId       = $row->ZONE_ID;
			        $this->objectName   = $row->OBJECT_NAME;
				break;
			}
		}
    }

    public static function checkIfPermissionExist($zoneId, $agentId, $contextId, $objectId) {
		
		$db = Zend_Registry::get('my_db2');
		$num_rows = 0;
        $query = "SELECT COUNT(*) as num_rows from  ".DBConvertor::convertCase('agent_permissions')." where
				  zone_id = $zoneId and agent_id = $agentId and context_id = $contextId and object_id = $objectId";

        $result = $db->fetchAll($query);
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
            return 0;
        }
        else {
            return 1;
        }
    }

    public static function addPermission(
            $zoneId,
            $agentId,
            $contextId,
            $objectId,
            $provide,
            $subscribe,
            $add,
            $update,
            $delete,
            $request,
            $respond ) {
	
		$db = Zend_Registry::get('my_db');
        $data = array(
                DBConvertor::convertCase('zone_id')        =>$zoneId,
                DBConvertor::convertCase('agent_id')       =>$agentId,
                DBConvertor::convertCase('context_id')     =>$contextId,
                DBConvertor::convertCase('object_id')      =>$objectId,
                DBConvertor::convertCase('can_provide')    =>Utility::convertCheckBoxValue($provide),
                DBConvertor::convertCase('can_subscribe')  =>Utility::convertCheckBoxValue($subscribe),
                DBConvertor::convertCase('can_add')        =>Utility::convertCheckBoxValue($add),
                DBConvertor::convertCase('can_update')     =>Utility::convertCheckBoxValue($update),
                DBConvertor::convertCase('can_delete')     =>Utility::convertCheckBoxValue($delete),
                DBConvertor::convertCase('can_request')    =>Utility::convertCheckBoxValue($request),
                DBConvertor::convertCase('can_respond')    =>Utility::convertCheckBoxValue($respond)
        );

        if($db->insert(DBConvertor::convertCase('agent_permissions'), $data)) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function updatePermission(
            $permissionId,
            $provide,
            $subscribe,
            $add,
            $update,
            $delete,
            $request,
            $respond
    ) {

		$db = Zend_Registry::get('my_db');
        $data = array(
                DBConvertor::convertCase('can_provide')    =>Utility::convertCheckBoxValue($provide),
                DBConvertor::convertCase('can_subscribe')  =>Utility::convertCheckBoxValue($subscribe),
                DBConvertor::convertCase('can_add')        =>Utility::convertCheckBoxValue($add),
                DBConvertor::convertCase('can_update')     =>Utility::convertCheckBoxValue($update),
                DBConvertor::convertCase('can_delete')     =>Utility::convertCheckBoxValue($delete),
                DBConvertor::convertCase('can_request')    =>Utility::convertCheckBoxValue($request),
                DBConvertor::convertCase('can_respond')    =>Utility::convertCheckBoxValue($respond)
        );

        $n = $db->update(DBConvertor::convertCase('agent_permissions'), $data, 'permission_id = '.$permissionId);
    }

    public static function getAllAgentPermissions($agentId, $zoneId, $contextId) {
        //$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');

        $permissions = array();

        $select = null;
		$select = 	"SELECT p.permission_id "
				.	"  FROM ".DBConvertor::convertCase('AGENT_PERMISSIONS')." p "
				.	" INNER JOIN ".DBConvertor::convertCase('DATA_OBJECT') ." d ON p.object_id = d.object_id "
				.	" WHERE (p.agent_id = $agentId ) AND (p.context_id =  $contextId ) AND (p.zone_id = $zoneId) "
				.   " ORDER BY d.object_name ASC";
				
        $result = $db->fetchAll($select);
        foreach($result as $row) {
			switch(DB_TYPE) {
	            case 'mysql':
					$permission_id = $row->permission_id;
				break;
				case 'oci8':
					$permission_id = $row->PERMISSION_ID;
				break;
			}
            $permission = new Permission($permission_id);
            array_push($permissions, $permission);
        }
        return $permissions;
    }

    public static function getAllSubscribedPermissions($agentId, $zoneId, $contextId) {
        //$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');

        $permissions = array();

        $select = null;
		$select = 	"SELECT p.permission_id "
				.	"  FROM ".DBConvertor::convertCase('AGENT_PERMISSIONS')." p "
				.	" INNER JOIN ".DBConvertor::convertCase('DATA_OBJECT') ." d ON p.object_id = d.object_id "
				.	" WHERE (p.agent_id = $agentId ) and (p.context_id =  $contextId ) and (p.zone_id = $zoneId) and p.can_subscribe = 1"
				.   " ORDER BY d.object_name ASC";

/*        $select = $db->select()
                    ->from(array('p' => 'agent_permissions'),array('p.permission_id'))
                    ->join(array('d' => 'data_object'), 'p.object_id = d.object_id')
                    ->where('p.agent_id = ?', $agentId)
                    ->where('p.context_id = ?', $contextId)
                    ->where('p.zone_id = ?', $zoneId)
                    ->where('p.can_subscribe = 1')
                    ->order('d.object_name');

        $stmt = $select->query();
*/
        $result = $db->fetchAll($select);
        foreach($result as $row) {
			switch(DB_TYPE) {
	            case 'mysql':
					$permission_id = $row->permission_id;
				break;
				case 'oci8':
					$permission_id = $row->PERMISSION_ID;
				break;
			}
            $permission = new Permission($permission_id);
            array_push($permissions, $permission);
        }
        return $permissions;
    }
}


<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class DataObject {
    var $dataObjectId;
    var $dataObjectName;
    var $dataElements;

    public function DataObject($id, $name) {
        $this->dataObjectName = $name;
        $this->dataObjectId   = $id;
    }

    public static function getAllDataObjects($groupId=null) {
        $dataObjects = array();

		$db = Zend_Registry::get('my_db2');
		$select = "select object_id, object_name from ".DBConvertor::convertCase('data_object');

        if($groupId != null)
        {
            $select = $select . " where group_id = ".$groupId;
        }
        
        $result = $db->fetchAll($select);
        foreach($result as $row) {
			switch(DB_TYPE) {
	            case 'mysql':
					$object = new DataObject($row->object_id, $row->object_name);
				break;
				case 'oci8':
					$object = new DataObject($row->OBJECT_ID, $row->OBJECT_NAME);
				break;
			}
            array_push($dataObjects, $object);
        }
        return $dataObjects;
    }

    public static function getDataObjectElementId($objectId, $elementId){
		$db = Zend_Registry::get('my_db2');
		$doe = new DataObjectElement($db);
		$where = "object_id = $objectId and element_id = $elementId";

        $result = $doe->fetchAll($where);
        foreach($result as $row) {
			switch(DB_TYPE) {
	            case 'mysql':
					return $row->data_object_element_id;
				break;
				case 'oci8':
					return $row->DATA_OBJECT_ELEMENT_ID;
				break;
			}
        }
    }

}
?>
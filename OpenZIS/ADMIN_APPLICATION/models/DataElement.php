<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/

class DataElement {
    var $elementId;
    var $elementName;
    var $filtered;
    var $childElements = array();
    var $canFilter;

    public function DataElement($id, $name, $filtered, $canFilter=true) {

        $this->elementName    = $name;
        $this->elementId      = $id;
        $this->filtered       = $filtered;
        $this->canFilter      = $canFilter;
    }

    public static function saveFilteredElement($objectId, $elementId, $agentId, $zoneId, $contextId) {

		$db = Zend_Registry::get('my_db');

        $data = array(
                'zone_id'                => $zoneId,
                'agent_id'               => $agentId,
                'context_id'             => $contextId,
                'data_object_element_id' => DataObject::getDataObjectElementId($objectId, $elementId),
                'DATA_ELEMENT_CHILD_ID'  => 0,
        );

        if($db->insert(DBConvertor::convertCase('agent_filters'), $data)) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function saveFilteredChildElement($objectId, $parentElementId, $childElementId, $agentId, $zoneId, $contextId) {

		$db = Zend_Registry::get('my_db');

        $data = array(
                'zone_id'                => $zoneId,
                'agent_id'               => $agentId,
                'context_id'             => $contextId,
                'data_object_element_id' => DataObject::getDataObjectElementId($objectId, $parentElementId),
                'DATA_ELEMENT_CHILD_ID'  => $childElementId,
        );

        if($db->insert(DBConvertor::convertCase('agent_filters'), $data)) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function clearFilters($agentId, $zoneId, $contextId) {

		$db = Zend_Registry::get('my_db');
        $db->delete('agent_filters',"agent_id = '$agentId' and zone_id = '$zoneId' and context_id = '$contextId'");
    }

    public static function getAllDataElements($permission, $agentId, $zoneId, $contextId) {

		$db = Zend_Registry::get('my_db2');

        $query = "select distinct
                    de.element_id,
                    de.element_name,
                    f.data_object_element_id,
                    doe.can_filter
		  from ".DBConvertor::convertCase('data_element')." de
		  inner join ".DBConvertor::convertCase('data_object_element')." doe on doe.element_id = de.element_id
		  left outer join ".DBConvertor::convertCase('agent_filters')." f on f.data_object_element_id = doe.data_object_element_id
                  and f.data_element_child_id = 0 
                  and f.agent_id = ".$agentId." and f.zone_id = ".$zoneId." and f.context_id = ".$contextId."
		  where doe.object_id = ".$permission->objectId;

        $result = $db->fetchAll($query);
        foreach($result as $row) {
            $filtered = false;
            if($row->filter_id != null) {
                $filtered = true;
            }

            $element = new DataElement($row->element_id . "_" . $permission->objectId, $row->element_name, Utility::nullBooleanConvertor($row->data_object_element_id), Utility::intBooleanConvertor($row->can_filter));

            $query2 = "select distinct
                        child.data_element_child_id as id,
                        doe.object_id,
                        de.element_name,
                        f.data_element_child_id
                       from
                        ".DBConvertor::convertCase('data_element_child')." child
                       inner join
                        ".DBConvertor::convertCase('data_element')." de on de.element_id = child.child_element_id
                       inner join
                        ".DBConvertor::convertCase('data_object_element')." doe on doe.element_id = ".$row->element_id."
                       left outer join
                        ".DBConvertor::convertCase('agent_filters')." as f on f.data_element_child_id = child.data_element_child_id
                            and f.data_object_element_id = doe.data_object_element_id
                           and f.agent_id = ".$agentId." and f.zone_id = ".$zoneId." and f.context_id = ".$contextId."
                       where
                        child.parent_element_id =  ".$row->element_id."
                       and
                        doe.object_id = ".$permission->objectId."
                       order by
                        child.data_element_child_id";



            $result2 = $db->fetchAll($query2);
            foreach($result2 as $row2) {
                $child_element = new DataElement($row2->id. "_" .$row->element_id. "_" . $permission->objectId, $row2->element_name, Utility::nullBooleanConvertor($row2->data_element_child_id));
                array_push($element->childElements, $child_element);
            }

            array_push($permission->dataElements, $element);
        }
    }
}
?>
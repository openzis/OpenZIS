<?php

/*

this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 

*/

class FilterUtility {
    public function FilterUtility() {}

    public function FilterCommonElements($objectId, $dom, $agentId) {
        $db = Zend_Registry::get('my_db');

        $query = "select
                    de.xml_tag_name
		  from
                    data_element as de
                  inner join
                    data_object_element as doe on doe.element_id = de.element_id
		  inner join
                    agent_filters as af on af.data_object_element_id = doe.data_object_element_id
                  and
                    af.data_element_child_id = 0
                  and
                    af.agent_id = ".$agentId."
		  and
                    af.zone_id = ".$_SESSION['ZONE_ID']."
		  and
                    af.context_id = ".$_SESSION['CONTEXT_ID']."
                  where
                    doe.object_id = ".$objectId;

        $result = $db->fetchAll($query);
        foreach($result as $row){
            $this->removeParentElement($dom, $row->xml_tag_name);
        }

        $query = "select
                    de.xml_tag_name
		  from
                    data_element as de
                  inner join
                    data_element_child as child on child.child_element_id = de.element_id
                  inner join
                    data_object_element as doe on doe.element_id = child.parent_element_id
                  inner join
                    agent_filters as af on af.data_element_child_id = child.data_element_child_id
                  and
                    af.data_object_element_id = doe.data_object_element_id
		  and
                    af.agent_id = ".$agentId."
		  and
                    af.zone_id = ".$_SESSION['ZONE_ID']."
		  and
                    af.context_id = ".$_SESSION['CONTEXT_ID']."
                  and
                    af.data_element_child_id is not null
                  where
                    doe.object_id = ".$objectId;

        $result = $db->fetchAll($query);
        foreach($result as $row){
            $this->removeChildElement($dom, $row->xml_tag_name);
        }


    }

    private function removeChildElement($node, $tagName){

        $childNodes = $node->childNodes;
        if($childNodes != null && $childNodes->length > 0){
            foreach($childNodes as $child){
                if($child->nodeName == $tagName){
                    $this->deleteChildren($child);
                    $node->removeChild($child);
                }
                else{
                    $this->removeChildElement($child, $tagName);
                }
            }
        }
    }

    private function removeParentElement($dom, $tagNames) {
        $node = $dom;
        $found = false;
        $lastTagName = $tagNames[count($tagNames) - 1];

        if($tagNames != null && $tagNames[0] != null) {
            foreach($tagNames as $tagName) {
                if($dom->getElementsByTagName($tagName) != null) {
                    $node = $node->getElementsByTagName($tagName)->item(0);
                }
                else {
                    break;
                }
                if($tagName == $lastTagName) {
                    $found = true;
                }
            }
        }

        if($found) {
            $this->deleteChildren($node);
            $parent = $node->parentNode;
            $parent->removeChild($node);
        }
    }

    private function deleteChildren($node) {
        while (isset($node->firstChild)) {
            $this->deleteChildren($node->firstChild);
            $node->removeChild($node->firstChild);
        }
    }
}
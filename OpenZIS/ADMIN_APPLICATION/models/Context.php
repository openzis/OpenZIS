<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Context
{
	var $contextId;
	var $contextDesc;

	public function Context($contextId){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		
		$query = 'SELECT context_desc from context where context_id = '.$contextId;
		$result = $db->fetchAll($query);

		$this->contextDesc = $result[0]->context_desc;
		$this->contextId = $contextId;
	}

	public static function addContext($description){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		$data = array(
						DBConvertor::convertCase('context_desc') => $description
					 );

		if($db->insert(DBConvertor::convertCase('context'), $data)){
			return true;
		}
		else{
			return false;
		}
	}

	public static function updateContext($description, $contextId){
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		$data = array('CONTEXT_DESC' => $description);

		$n = $db->update(DBConvertor::convertCase('context'), $data, DBConvertor::convertCase('context_id').' = '.$contextId);
	}

	public static function getAllContexts(){
		$contexts = array();

		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		
		$query = "select context_id from context";
		$result = $db->fetchAll($query);
		foreach($result as $row){
				$context = new Context($row->context_id);
				array_push($contexts, $context);
		}
		return $contexts;
	}
}
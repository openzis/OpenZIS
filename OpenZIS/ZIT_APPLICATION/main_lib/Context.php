<?php /*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2010  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Context{

	public static function getContextId($contextDesc){
		$db = Zend_Registry::get('my_db');
		$context = new Contexts($db);
		$where = "lower(context_desc) = lower('$contextDesc')";
		

		$result = $context->fetchAll($where);
		switch(DB_TYPE) {
			case 'mysql':
				return $result[0]->context_id;
			break;
			case 'oci8':
				return $result[0]->CONTEXT_ID;
			break;
		}
		
	}
	
	public static function isValidContext($contextDesc){
		$db = Zend_Registry::get('my_db');
		
		$context = new Contexts($db);
		$where = "lower(context_desc) = lower('$contextDesc')";
						
		$result = $context->fetchAll($where);
		if($result->count() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

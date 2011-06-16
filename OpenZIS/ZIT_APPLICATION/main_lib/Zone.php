<?php /*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Zone{

	public static function retrieveZoneSourceId(){
		$zone = null;
		$context = null;

		$vars    = explode ('/', $_SERVER['REQUEST_URI']);
		$zone    = $vars[ZONE_URL_INDEX];
		if (isset($vars[ZONE_URL_INDEX + 1])){
			$context = $vars[ZONE_URL_INDEX + 1];
		} else {
			$context = 'SIF_Default';
		}

		Zone::setupZone($zone);
		Zone::setupContext($context);
		Zone::getZoneVersion();
	}

	public static function setupZone($zone){
		if($zone == '' || $zone == null){
			ZitLog::writeToErrorLog('[Zone is missing in request]', 'Zone is missing in request', 'Retreive Zone Information');
			echo '<FATAL_ERROR>INVALID ZONE</FATAL_ERROR>';
			exit;
		}

		if(!Zone::checkZoneExist($zone)){
			ZitLog::writeToErrorLog('[Zone does not exist]', 'Zone does not exist in the system => '. substr($zone, 0, 30), 'Retreive Zone Information');
			echo '<FATAL_ERROR>INVALID ZONE</FATAL_ERROR>';
			exit;
		}
		else{
			$_SESSION['ZONE_NAME'] = $zone;
			Zone::getZoneId($zone);
		}
	}


	public static function setupContext($context){
		if($context == null && $context == ''){
			$_SESSION['CONTEXT_ID'] = 1;
		}
		else{
			if(!Zone::checkContextExist($context)){
				echo '<FATAL_ERROR>INVALID CONTEXT</FATAL_ERROR>';
				exit;
			}
			else{
				$_SESSION['CONTEXT_ID']  = Zone::getContextId($context);
			}
		}
	}

	public static function checkContextExist($contextName){		
		
		$db = Zend_Registry::get('my_db');
		$c = new Contexts($db);
		$quotedName = $db->quote($contextName);
		$result = $c->fetchAll("upper(context_desc) = upper($quotedName)");
		$rows = $result->count();
		if($rows == 0){
			return false;
		}
		else{
			switch(DB_TYPE) {
				case 'mysql':
					$_SESSION['CONTEXT_ID'] = $result[0]->context_id;
				break;
				case 'oci8':
					$_SESSION['CONTEXT_ID'] = $result[0]->CONTEXT_ID;
				break;
			}
			return true;
		}
		
	}

	public static function getContextId($contextName){
		return $_SESSION['CONTEXT_ID'];
	}

	public static function checkZoneExist($zoneName){
		$db = Zend_Registry::get('my_db');
		$quoteZoneName = $db->quote($zoneName);
		$z = new Zones($db);
		$result = $z->fetchAll("source_id = $quoteZoneName");
		$rows = $result->count();
		if($rows == 0){
			return false;
		}
		else{
			switch(DB_TYPE) {
				case 'mysql':
					$source_id = $result[0]->source_id;
				break;
				case 'oci8':
					$source_id = $result[0]->SOURCE_ID;
				break;
			}
			
			if($source_id == $zoneName){
				return true;
			}
			else{
				return false;
			}
		}
	}
	public static function getZoneId($zoneName){
		$db = Zend_Registry::get('my_db');
		
		$z = new Zones($db);
		$cleanZoneName = $db->quote($zoneName);
		$result = $z->fetchAll("upper(source_id) = upper($cleanZoneName)");
		
		switch(DB_TYPE) {
			case 'mysql':
				$_SESSION['ZONE_ID']   = intval($result[0]->zone_id);
				$_SESSION['ZONE_DESC'] = $result[0]->zone_desc;
				$_SESSION['ZONE_VERSION_ID'] = $result[0]->version_id;
				$_SESSION['ZONE_SLEEPING'] = $result[0]->sleeping;
				$_SESSION['ZONE_AUTH_TYPE'] = $result[0]->zone_authentication_type_id;
			break;
			case 'oci8':
				$_SESSION['ZONE_ID']   = intval($result[0]->ZONE_ID);
				$_SESSION['ZONE_DESC'] = $result[0]->ZONE_DESC;
				$_SESSION['ZONE_VERSION_ID'] = $result[0]->VERSION_ID;
				$_SESSION['ZONE_SLEEPING'] = $result[0]->SLEEPING;
				$_SESSION['ZONE_AUTH_TYPE'] = $result[0]->ZONE_AUTHENTICATION_TYPE_ID;
			break;
		}

	}

	public static function getZoneVersion(){
		$db = Zend_Registry::get('my_db');
		
		$v = new Versions($db);
		$version_id = $_SESSION['ZONE_VERSION_ID'];
		$result = $v->fetchAll("version_id = $version_id");
		
		switch(DB_TYPE) {
			case 'mysql':
				$_SESSION['VERSION_NAMESPACE']       = $result[0]->version_namespace;
				$_SESSION['ZONE_VERSION']            = $result[0]->version_num;
				$_SESSION['ZONE_VERSION_DIRECTORY']  = $result[0]->version_directory;
				$_SESSION['ZONE_VERSION_SCHEMA_DIR'] = $result[0]->schema_directory;
			break;
			case 'oci8':
				$_SESSION['VERSION_NAMESPACE']       = $result[0]->VERSION_NAMESPACE;
				$_SESSION['ZONE_VERSION']            = $result[0]->VERSION_NUM;
				$_SESSION['ZONE_VERSION_DIRECTORY']  = $result[0]->VERSION_DIRECTORY;
				$_SESSION['ZONE_VERSION_SCHEMA_DIR'] = $result[0]->SCHEMA_DIRECTORY;
			break;
		}
	}

	public static function zoneSleeping(){
		if($_SESSION['ZONE_SLEEPING'] == 1){
			return true;
		}
		else{
			return false;
		}
	}
	
	public static function getZoneAuthenticationType(){
		return $_SESSION['ZONE_AUTH_TYPE'];
	}
}
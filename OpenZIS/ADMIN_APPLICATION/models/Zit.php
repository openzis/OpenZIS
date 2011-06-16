<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Zit
{
	var $id;
	var $sourceId;
	var $asleep;
	var $adminUrl;
	var $zitUrl;
	var $zitName;
	var $minBuffer;
	var $maxBuffer;

	public function Zit($zitId)
	{
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db2');
		$z = new ZisServer($db);
		$result = $z->fetchAll("zit_id = $zitId");
		
//		$sql = "select source_id, asleep, admin_url, zit_name, min_buffer, max_buffer, zit_url from zit_server where zit_id = $zitId";
//		$result = $db->fetchAll($sql);

		$this->id 		 = $zitId;
		
		switch(DB_TYPE) {
            case 'mysql':
				$this->sourceId  = $result[0]->source_id;
				$this->asleep    = $result[0]->asleep;
				$this->adminUrl  = $result[0]->admin_url;
				$this->zitName   = $result[0]->zit_name;
				$this->minBuffer = $result[0]->min_buffer;
				$this->maxBuffer = $result[0]->max_buffer;
				$this->zitUrl    = $result[0]->zit_url;
			break;
			case 'oci8':
				$this->sourceId  = $result[0]->SOURCE_ID;
				$this->asleep    = $result[0]->ASLEEP;
				$this->adminUrl  = $result[0]->ADMIN_URL;
				$this->zitName   = $result[0]->ZIT_NAME;
				$this->minBuffer = $result[0]->MIN_BUFFER;
				$this->maxBuffer = $result[0]->MAX_BUFFER;
				$this->zitUrl    = $result[0]->ZIT_URL;
			break;
		}
	}

	public static function updateZit($zitId,
									 $zitName,
									 $sourceId,
									 $adminUrl,
									 $minBuffer,
									 $maxBuffer,
									 $zitUrl){
	
		$db = Zend_Registry::get('my_db');
		$z = new ZisServer($db);

		$data = array(DBConvertor::convertCase('zit_name')   => $zitName,
					  DBConvertor::convertCase('source_id')  => $sourceId,
					  DBConvertor::convertCase('admin_url')  => $adminUrl,
					  DBConvertor::convertCase('min_buffer') => $minBuffer,
					  DBConvertor::convertCase('max_buffer') => $maxBuffer,
					  DBConvertor::convertCase('zit_url')    => $zitUrl);

		$n = $z->update($data, DBConvertor::convertCase('zit_id').' = '.$zitId);
	}

	public static function putZitToSleep($zitId, $val)
	{
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		
		$data = array(DBConvertor::convertCase('asleep') => $val);
		$n = $db->update(DBConvertor::convertCase('zit_server'), $data, DBConvertor::convertCase('zit_id').' = '.$zitId);
	}
	
	public static function getErrorMessages()
	{
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		
		$errorMessages = array();
		
		$select = $db->select()
					  ->from(array(DBConvertor::convertCase('l') => DBConvertor::convertCase('error_log')),
						array(DBConvertor::convertCase('error_timestamp') => DBConvertor::convertDateFormat(DBConvertor::convertCase('l.error_timestamp'), 'm-dd-yyyy-t', DBConvertor::convertCase('error_timestamp')), DBConvertor::convertCase('l.error_location'), DBConvertor::convertCase('l.short_error_desc'), DBConvertor::convertCase('l.long_error_desc'), DBConvertor::convertCase('l.error_id')))
					  ->joinLeft(array(DBConvertor::convertCase('a') => DBConvertor::convertCase('agent')),
						DBConvertor::convertCase('a.agent_id').' = '.DBConvertor::convertCase('l.agent_id'),
						array(DBConvertor::convertCase('agent_name')))
					  ->joinLeft(array(DBConvertor::convertCase('z') => DBConvertor::convertCase('zones')),
						DBConvertor::convertCase('z.zone_id').' = '.DBConvertor::convertCase('l.zone_id'),
						array(DBConvertor::convertCase('zone_desc')))
					  ->order(array(DBConvertor::convertCase(DBConvertor::convertCase('error_timestamp DESC'))))
					  ->where('l.archived = 0')
					  ->limit(100,0);
		
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		foreach($result as $row){
			switch(DB_TYPE) {
	            case 'mysql':
				  $zel = new ZitErrorLog($row->error_id, isset($row->agent_name) ? $row->agent_name : 'N/A', $row->zone_desc, $row->error_timestamp, $row->error_location, $row->short_error_desc, isset($row->long_error_desc) ? $row->long_error_desc : '');
				break;
				case 'oci8':
				  $longErrorXML = $row->LONG_ERROR_DESC;
				  $longErrorDesc = $longErrorXML->read($longErrorXML->size());
				  $zel = new ZitErrorLog($row->ERROR_ID,  isset($row->AGENT_NAME) ? $row->AGENT_NAME : '', $row->ZONE_DESC, isset($row->ERROR_TIMESTAMP) ? $row->ERROR_TIMESTAMP : 'N/A', $row->ERROR_LOCATION, $row->SHORT_ERROR_DESC, isset($longErrorDesc) ? $longErrorDesc : '');
				break;
			}
			array_push($errorMessages, $zel);
		}
		
		return $errorMessages;
	}
	
	public static function ArchiveMessages()
	{
		//$db = ZitDBAdapter::getDBAdapter();
		$db = Zend_Registry::get('my_db');
		$data = array(DBConvertor::convertCase('archived') => 1);
		$n = $db->update(DBConvertor::convertCase('error_log'), $data);
	}
}
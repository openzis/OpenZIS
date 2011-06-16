Starting....
<?php
ini_set('error_reporting', E_ALL & ~E_STRICT);
ini_set('memory_limit', -1);

/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/

set_include_path('../../ZendFramework/library' . PATH_SEPARATOR . get_include_path());
set_include_path('../UTIL/' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Exception');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Mysql');
Zend_Loader::loadClass('Zend_Db_Adapter_Oracle');

//Main Library
require_once 'zit_db_adapter.php';
require_once 'db_convertor.php';

$db = ZitDBAdapter::getDBAdapter();

switch(DB_TYPE) {
	case 'mysql':
  	    $dbversion = 'mysql';
    break;

	case 'oci8':
		$dbversion = 'oracle';
		$db->setLobAsString(true);
    break;	
}


require_once '../CLASSES/'.$dbversion.'/ZitLog.php';
require_once '../CLASSES/'.$dbversion.'/ZitLogArch.php';
require_once '../CLASSES/'.$dbversion.'/Events.php';
require_once '../CLASSES/'.$dbversion.'/Responses.php';
require_once '../CLASSES/'.$dbversion.'/Requests.php';

$oldLog = new ZisLogArchieve($db);

try {
	$sql = "UPDATE ".DBConvertor::convertCase('zit_log')." SET archived = 1";
	$stmt = $db->prepare($sql);
	$result = $stmt->execute();
} catch (Zend_Exception $e) {
	echo "Caught exception: " . get_class($e) . "\n";
	echo "Message: " . $e->getMessage() . "\n";
}

try {
	$select = $db->select()
				 ->from(DBConvertor::convertCase('zit_log'))
				 ->where('archived = 1');
	$stmt = $db->query($select);
	$result2 = $stmt->fetchAll();
} catch (Zend_Exception $e) {
	echo "Caught exception: " . get_class($e) . "\n";
	echo "Message: " . $e->getMessage() . "\n";
}


foreach($result2 as $row) {
	switch(DB_TYPE) {
        case 'mysql':
		 $id = $row->log_id;
		 $data = array('log_id'	=> $row->log_id,
					   'log_message_type_id' => $row->log_message_type_id,
					   'agent_id' => $row->agent_id,
					   'archived' => $row->archived,
					   'create_timestamp' => $row->create_timestamp,
					   'rec_xml' => $row->rec_xml,
					   'sent_xml' => $row->sent_xml,
					   'sif_message_type_id' => $row->sif_message_type_id,
					   'zone_id' => $row->zone_id);
		break;
		
		case 'oci8':
		 $id = $row->LOG_ID;
		 $rec_xml = $row->REC_XML;
		 $sent_xml = $row->SENT_XML;
		 $data = array('LOG_ID'					=> $row->LOG_ID,
					   'LOG_MESSAGE_TYPE_ID' 	=> $row->LOG_MESSAGE_TYPE_ID,
					   'AGENT_ID' 				=> $row->AGENT_ID,
					   'ARCHIVED' 				=> $row->ARCHIVED,
					   'CREATE_TIMESTAMP' 		=> $row->CREATE_TIMESTAMP,
					   'REC_XML' 				=> $rec_xml->read($rec_xml->size()),
					   'SENT_XML' 				=> $sent_xml->read($sent_xml->size()),
					   'SIF_MESSAGE_TYPE_ID' 	=> $row->SIF_MESSAGE_TYPE_ID,
					   'ZONE_ID' 				=> $row->ZONE_ID);
		break;
	}
	try {
		$db->insert(DBConvertor::convertCase('zit_log_archive'), $data);
	} catch (Zend_Exception $e) {
		echo "Caught exception: " . get_class($e) . "\n";
		echo "Message: " . $e->getMessage() . "\n";
	}
}

?>
Finished!
<?php
/*

This file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 

*/


ini_set('memory_limit', -1);
define('DBSCHEMA', 'openzis');

set_include_path('../../ZendFramework/library' . PATH_SEPARATOR . get_include_path());
set_include_path('../ADMIN_APPLICATION' . PATH_SEPARATOR . get_include_path());
set_include_path('../UTIL' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Controller_Front');
Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Db_Table_Abstract');
Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Mysql');
Zend_Loader::loadClass('Zend_Db_Adapter_Oracle');
Zend_Loader::loadClass('Zend_Form');
Zend_Loader::loadClass('Zend_Form_Element_Text');
Zend_Loader::loadClass('Zend_Form_Element_Password');
Zend_Loader::loadClass('Zend_Session');
Zend_Loader::loadClass('Zend_Auth_Storage_Session');
Zend_Loader::loadClass('Zend_Auth');
Zend_Loader::loadClass('Zend_Filter');
Zend_Loader::loadClass('Zend_Filter_StringToLower');
Zend_Loader::loadClass('Zend_Filter_StripTags');
Zend_Loader::loadClass('Zend_Filter_Digits');
Zend_Loader::loadClass('Zend_Filter_Alnum');
Zend_Loader::loadClass('Zend_Filter_Alpha');
Zend_Loader::loadClass('Zend_Validate');
Zend_Loader::loadClass('Zend_Validate_EmailAddress');
Zend_Loader::loadClass('Zend_Validate_NotEmpty');
Zend_Loader::loadClass('Zend_Validate_Alnum');
Zend_Loader::loadClass('Zend_Validate_Alpha');
Zend_Loader::loadClass('Zend_Validate_Digits');
Zend_Loader::loadClass('Zend_Validate_Between');
Zend_Loader::loadClass('Zend_Validate_Db_RecordExists');
Zend_Loader::loadClass('Zend_Filter_Input');
Zend_Loader::loadClass('Zend_Validate_NotEmpty');
Zend_Loader::loadClass('Zend_Json');

Zend_Loader::loadClass('Zend_Cache');
Zend_Loader::loadClass('Zend_Cache_Core');

Zend_Loader::loadClass('Zend_Log');
Zend_Loader::loadClass('Zend_Log_Writer_Stream');
Zend_Loader::loadClass('Zend_Log_Formatter_Xml');


require_once 'zit_db_adapter.php';
require_once 'zit_db_adapter2.php';
require_once 'db_convertor.php';

//General Error
require_once '../ZIT_APPLICATION/main_lib/GeneralError.php';


try {
	$db = ZitDBAdapter::getDBAdapter();
	Zend_Registry::set('my_db', $db);
} catch (Zend_Exception $e) {
	GeneralError::systemError($xml);
}

try {
	$db2 = ZitDBAdapter2::getDBAdapter();
	Zend_Registry::set('my_db2', $db2);

} catch (Zend_Exception $e) {
	GeneralError::systemError($xml);
}

switch(DB_TYPE) {
	case 'mysql':
  	    $dbversion = 'mysql';
    break;

	case 'oci8':
		$dbversion = 'oracle';
    break;	
}

require_once '../CLASSES/'.$dbversion.'/ZitAdminDB.php';
require_once '../CLASSES/'.$dbversion.'/ZitLog.php';
require_once '../CLASSES/'.$dbversion.'/ZitLogArch.php';
require_once '../CLASSES/'.$dbversion.'/Zones.php';
require_once '../CLASSES/'.$dbversion.'/Contexts.php';
require_once '../CLASSES/'.$dbversion.'/Versions.php';
require_once '../CLASSES/'.$dbversion.'/DataObjectGroups.php';
require_once '../CLASSES/'.$dbversion.'/DataObjects.php';
require_once '../CLASSES/'.$dbversion.'/ZisServer.php';
require_once '../CLASSES/'.$dbversion.'/Agents.php';
require_once '../CLASSES/'.$dbversion.'/AgentRegistered.php';
require_once '../CLASSES/'.$dbversion.'/AgentZoneContext.php';
require_once '../CLASSES/'.$dbversion.'/AgentProvisions.php';
require_once '../CLASSES/'.$dbversion.'/AgentSubscriptions.php';
require_once '../CLASSES/'.$dbversion.'/AgentRequester.php';
require_once '../CLASSES/'.$dbversion.'/AgentResponder.php';
require_once '../CLASSES/'.$dbversion.'/AgentModes.php';

require_once '../CLASSES/'.$dbversion.'/ProvisionDataObjectAgentVW.php';
require_once '../CLASSES/'.$dbversion.'/ProvisionDataObjectVW.php'; 
require_once '../CLASSES/'.$dbversion.'/AgentPermisionDataObjectVW.php';
require_once '../CLASSES/'.$dbversion.'/AgentResponderDataObjectAgentVW.php';

require_once '../CLASSES/'.$dbversion.'/Events.php';
require_once '../CLASSES/'.$dbversion.'/Responses.php';
require_once '../CLASSES/'.$dbversion.'/Requests.php';
require_once '../CLASSES/'.$dbversion.'/RequestAgentVW.php';


require_once 'models/Utility.php';
require_once 'models/Zit.php';
require_once 'models/ZitAdmin.php';
require_once 'models/Zone.php';
require_once 'models/Context.php';
require_once 'models/Agent.php';
require_once 'models/DataObject.php';
require_once 'models/DataObjectGroup.php';
require_once 'models/DataElement.php';
require_once 'models/Permission.php';
require_once 'models/Version.php';
require_once 'models/SubscribeObject.php';
require_once 'models/ProvideObject.php';
require_once 'models/ZitLogEntry.php';
require_once 'models/ZitLogEntry_Agent.php';
require_once 'models/Zit.php';
require_once 'models/GroupPermission.php';
require_once 'models/GroupPermissionItem.php';
require_once 'models/ZitErrorLog.php';
require_once 'push_handler/OpenZisPushHandler.php';


$ZSN = new Zend_Session_Namespace('openzisadmin');
$config = new Zend_Config_Ini('../config.ini', 'zit_config');

$tz = isset($config->application->root->date_timezone) ? $config->application->root->date_timezone : 'America/New_York';

date_default_timezone_set($tz);

define('CACHE', $config->application->root->directory."/tmp");
define('NUMMESSAGES', $config->interface->message->display);

$frontendOptions = array('automatic_serialization' => true);
$backendOptions = array( 'cache_dir' => CACHE );
$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
	
$logger = new Zend_Log;	
$writer = new Zend_Log_Writer_Stream($config->application->root->directory."/logs/recordshelf-log.xml");
$formater = new Zend_Log_Formatter_Xml();
$writer->setFormatter($formater);
$logger->addWriter($writer);
$logger->setEventItem('timestamp', date('D, j M Y H:i:s', time()));

Zend_Registry::set('logger', $logger);
Zend_Registry::set('log', $config->application->root->directory.'/logs/recordshelf-log.xml');

try {
	$frontController = Zend_Controller_Front::getInstance();
	$frontController->setControllerDirectory('../ADMIN_APPLICATION/controllers');
	
	if ($config->code->level == 1){
		$frontController->throwExceptions(false);
		ini_set('error_reporting', 0);
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
	} elseif ($config->code->level == 2) {
		$frontController->throwExceptions(true);
		ini_set('error_reporting', E_ERROR);
	} elseif ($config->code->level == 3) {
		$frontController->throwExceptions(true);
		ini_set('error_reporting', E_ERROR & ~E_STRICT);
	}else {
		$frontController->throwExceptions(true);
		ini_set('error_reporting', 0);
	}
	
    if (@strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
        ob_start();
        $frontController->dispatch();
        $output = gzencode(ob_get_contents(), 9);
        ob_end_clean();
        header('Content-Encoding: gzip');
        echo $output;
    } else {
        $frontController->dispatch();
    }
} catch (Exception $e) {
	$message = $e->getMessage() . "\n\n" . $e->getTraceAsString();
	if (Zend_Registry::isRegistered('logger')) {
	    Zend_Registry::get('logger')->err($message);
	}
	?>{"success":false, errors:{reason:"<?php echo $message ?>"}}<?php 

}
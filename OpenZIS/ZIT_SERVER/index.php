<?php
session_start();
ini_set('error_reporting', E_ALL & ~E_STRICT);
define('DBSCHEMA', 'openzis');

/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/

set_include_path('../../ZendFramework/library' . PATH_SEPARATOR . get_include_path());
set_include_path('../ZIT_APPLICATION/' . PATH_SEPARATOR . get_include_path());
set_include_path('../UTIL/' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Mysql');
Zend_Loader::loadClass('Zend_Db_Adapter_Oracle');

//Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Pgsql');
//Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Mssql');

//Main Library
require_once 'zit_db_adapter.php';
require_once 'db_convertor.php';
require_once 'main_lib/ZitLog.php';
require_once 'main_lib/SifProcessRequest.php';
require_once 'main_lib/Zit.php';
require_once 'main_lib/Zone.php';
require_once 'main_lib/Agent.php';
require_once 'main_lib/Context.php';
require_once 'main_lib/DataObject.php';
require_once 'main_lib/Version.php';
require_once 'main_lib/Utility.php';
require_once 'main_lib/XmlHelper.php';
require_once 'main_lib/SifLogEntry.php';
require_once 'main_lib/register/RegisterError.php';


#require_once 'main_lib/RequestObject.php';
#require_once 'main_lib/ResponseObject.php';
#require_once 'main_lib/FilterUtility.php';


//MessageObject
require_once 'main_lib/MessageObject.php';


//Register
#require_once 'main_lib/register/Register.php';
#require_once 'main_lib/register/UnRegister.php';

//Provision
#require_once 'main_lib/provision/ProvisionError.php';
#require_once 'main_lib/provision/Provision.php';

//Provide
# require_once 'main_lib/provide/Provide.php';
# require_once 'main_lib/provide/UnProvide.php';

//Subscribe
#require_once 'main_lib/subscribe/Subscribe.php';
#require_once 'main_lib/subscribe/UnSubscribe.php';

//Event
#require_once 'main_lib/event/Event.php';

//Request
#require_once 'main_lib/request/Request.php';
#require_once 'main_lib/request/RequestError.php';

//Response
#require_once 'main_lib/response/Response.php';

//Ack
#require_once 'main_lib/ack/Ack.php';

//General Error
require_once 'main_lib/GeneralError.php';

//ACL
require_once 'main_lib/acl/ACL.php';

//SystemControl
#require_once 'main_lib/systemcontrol/SystemControl.php';

try {
	$xml = trim(file_get_contents('php://input'));
} catch (Zend_Exception $e) {
	ZitLog::writeToErrorLog("[php://input]", "Errors:$e->getTraceAsString()", "php://input", 0);
	GeneralError::systemError($xml);
}

try{
	$db = ZitDBAdapter::getDBAdapter();
	Zend_Registry::set('my_db', $db);
} catch (Zend_Exception $e) {
	GeneralError::systemError($xml);
}


switch(DB_TYPE) {
	case 'mysql':
  	    $dbversion = 'mysql';
    break;

	case 'oci8':
		$dbversion = 'oracle';
		
		require_once '../CLASSES/'.$dbversion.'/Events.php';
		require_once '../CLASSES/'.$dbversion.'/Responses.php';
		require_once '../CLASSES/'.$dbversion.'/Requests.php';
    break;	
}

require_once '../CLASSES/'.$dbversion.'/ZitAdminDB.php';
require_once '../CLASSES/'.$dbversion.'/ZitLog.php';
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

require_once '../CLASSES/'.$dbversion.'/ProvisionDataObjectAgentVW.php';
require_once '../CLASSES/'.$dbversion.'/ProvisionDataObjectVW.php'; 
require_once '../CLASSES/'.$dbversion.'/AgentPermisionDataObjectVW.php';
require_once '../CLASSES/'.$dbversion.'/AgentResponderDataObjectAgentVW.php';

require_once '../CLASSES/'.$dbversion.'/MessageQueues.php';
require_once '../CLASSES/'.$dbversion.'/RequestAgentVW.php';
require_once '../CLASSES/'.$dbversion.'/GetFirstMessageVW.php';

require_once '../CLASSES/'.$dbversion.'/AgentProvisions.php';

try {
	$zisServer = new ZisServer($db);
	$result = $zisServer->fetchAll('zit_id = 1');
} catch (Zend_Exception $e) {
	ZitLog::writeToErrorLog("[ZISServer]", "Errors:$e->getTraceAsString()", "ZISServer", 0);
	GeneralError::systemError($xml);
}

switch(DB_TYPE) {
	case 'mysql':
		$_SESSION['ZIS_SOURCEID']   = $result[0]->source_id;
		$_SESSION['ASLEEP']   		= $result[0]->asleep;
		$_SESSION['ADMIN_URL']   	= $result[0]->admin_url;
		$_SESSION['ZIS_NAME']   	= $result[0]->zit_name;
		$_SESSION['MIN_BUFFER']   	= $result[0]->min_buffer;
		$_SESSION['MAX_BUFFER']   	= $result[0]->max_buffer;
		$_SESSION['ZIT_URL']   		= $result[0]->zit_url;
	break;
	
	case 'oci8':
		$_SESSION['ZIS_SOURCEID']   = $result[0]->SOURCE_ID;
		$_SESSION['ASLEEP']   		= $result[0]->ASLEEP;
		$_SESSION['ADMIN_URL']   	= $result[0]->ADMIN_URL;
		$_SESSION['ZIS_NAME']   	= $result[0]->ZIT_NAME;
		$_SESSION['MIN_BUFFER']   	= $result[0]->MIN_BUFFER;
		$_SESSION['MAX_BUFFER']   	= $result[0]->MAX_BUFFER;
		$_SESSION['ZIT_URL']   		= $result[0]->ZIT_URL;
	break;
}


header('Content-Type: application/xml;charset="utf-8"');
header('Server: '.Zit::getSourceId());

$config = new Zend_Config_Ini('../config.ini', 'zit_config');

$codeLevel      	= $config->code->level;
$vendorName     	= $config->vendor->name;
$vendorVersion  	= $config->vendor->version;
$vendorProduct  	= $config->vendor->product;
$zoneUrlIndex       = $config->zone->url->location;

define('CODELEVEL',  $codeLevel);
define('VENDOR_NAME',$vendorName);
define('VENDOR_VERSION',$vendorVersion);
define('VENDOR_PRODUCT',$vendorProduct);
define('ZONE_URL_INDEX',$zoneUrlIndex);
define('SERVER_SOURCE_ID', Zit::getSourceId());

/* Proper Values for SIF_VALIDATE
** 	Y : Validate and Send Error and Log Error
**  N : No Validate and Pass Message
**  W : Validate and Pass Message and Log Error
*/  
define('SIF_VALIDATE', 'W');


//global $USERNAME;

if($config->code->level == 2)
{
	ini_set('error_reporting', E_ALL & ~E_STRICT);
}
elseif ($config->code->level == 3)
{
	ini_set('error_reporting', E_ALL & ~E_STRICT);
}
else{
	ini_set('error_reporting', 0);
}


Zone::retrieveZoneSourceId();

define('NODE_NUM', 0);

try {
	if(SifProcessRequest::validateXML($xml)){
		define('REC_XML',  $xml);
		$messageObject = new MessageObject($xml);
		$messageObject->version = SifProcessRequest::retrieveVersion($messageObject);

		SifProcessRequest::processMessage($messageObject);
	}
	else{
		GeneralError::xmlValidationError($messageObject->xmlStr);
	}
} catch (Zend_Exception $e) {
	ZitLog::writeToErrorLog("[Main Loop]", "Request Xml:\n$messageObject->xmlStr \n\n Errors:$e->getTraceAsString()", "Main Loop", $_SESSION['ZONE_ID']);
	GeneralError::systemError($messageObject->xmlStr);
}








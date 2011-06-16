<?php

set_include_path('../../ZendFramework/library' . PATH_SEPARATOR . get_include_path());
set_include_path('../UTIL/' . PATH_SEPARATOR . get_include_path());
set_include_path('../ADMIN_APPLICATION/push_handler' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Http_Client');
Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Mysql');
Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Pgsql');
Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Mssql');

require_once 'zit_db_adapter.php';
require_once 'db_convertor.php';
require_once 'Agent.php';
require_once 'XmlHelper.php';
require_once 'Utility.php';
require_once 'PushMessageHandler.php';
require_once 'ZitLog.php';

$zoneId = $argv[1];
$contextId = $argv[2];

try
{
	ini_set('error_reporting', 0);
	$db = ZitDBAdapter::getDBAdapter();
	$query = "select 
				push_running,
				sleep_time_seconds
			  from 
				push_handler
			  where
				zone_id = ".$zoneId."  
			  and 
				context_id = ".$contextId;
							
	$result = $db->fetchAll($query);
	$running = $result[0]->push_running;
	$sleep_time_seconds = $result[0]->sleep_time_seconds;
	while($running == 1)
	{
		sleep((int)$sleep_time_seconds);
		
		$pushMessageHandler = new PushMessageHandler($zoneId, $contextId);
		$pushMessageHandler->processEventMessages();
		$pushMessageHandler->processResponseMessages();
		$pushMessageHandler->processRequestMessages();
		
		$query = "select 
					push_running
				  from 
					push_handler 
				  where
					zone_id = ".$zoneId."  
				  and 
					context_id = ".$contextId;
							
		$result = $db->fetchAll($query);
		$running = $result[0]->push_running;
	}
}
catch (Exception $e) 
{
	ZitLog::writeToErrorLog("[Error Pushing Messages] Check Agents", "Error Message\n".$e->getMessage()."\n\nStack Trace\n".$e->getTraceAsString(),'Push Thread', $zoneId, $contextId);
	
	$data = array('push_running' => 0, 'last_stop' => new Zend_Db_Expr(DBConvertor::convertCurrentTime()));
	$db->update('push_handler', $data, 'zone_id = '.$zoneId.' and context_id = '.$contextId);
}

return true;

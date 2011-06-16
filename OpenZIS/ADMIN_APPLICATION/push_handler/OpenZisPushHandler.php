<?php

class OpenZisPushHandler
{

	var $lastStart;
	var $lastStop;
	var $pushRunning;
	var $sleepTimeSeconds;
	
	public function OpenZisPushHandler(){}
		
	public function GetPushStatus($zoneId, $contextId=1)
	{
		$this->lastStart = null;
		$this->lastStop = null;
		$this->pushRunning = null;
		$this->sleepTimeSeconds = null;
		
		try {
			$db = ZitDBAdapter::getDBAdapter();
		} catch (Zend_Exception $e) {
			echo $e;
		}
		
		$query = "select 
					".DBConvertor::convertDateFormat('last_start', 'm-dd-yyyy-t', 'last_start').",
					".DBConvertor::convertDateFormat('last_stop', 'm-dd-yyyy-t', 'last_stop').",
					 sleep_time_seconds, push_running
				  from ".DBConvertor::convertCase('push_handler')." where zone_id = $zoneId and context_id = $contextId";
						
		$result = $db->fetchAll($query);
		
		foreach($result as $row){
			switch(DB_TYPE) {
	            case 'mysql':
					$this->lastStart = $row->last_start;
					$this->lastStop = $row->last_stop;
					$this->pushRunning = $row->push_running;
					$this->sleepTimeSeconds = $row->sleep_time_seconds;
				break;
	            case 'oci8':
					$this->lastStart = $row->LAST_START;
					$this->lastStop = $row->LAST_STOP;
					$this->pushRunning = $row->PUSH_RUNNING;
					$this->sleepTimeSeconds = $row->SLEEP_TIME_SECONDS;
				break;
			}
		} 
	}
	
	public function Start($sleep, $zoneId, $contextId=1)
	{
		$db = ZitDBAdapter::getDBAdapter();

		$query = "select 
					count(*) as num_rows
				  from 
					 ".DBConvertor::convertCase('push_handler')." 
				  where
					zone_id = $zoneId  and context_id = $contextId";
						
		$result = $db->fetchAll($query);
		$numRows = $result[0]->num_rows;
		if($numRows == 0)
		{
			
			$data = array('sleep_time_seconds' => $sleep, 'zone_id' => $zoneId, 'context_id' => $contextId, 'push_running' => 1, 'last_start' => new Zend_Db_Expr(DBConvertor::convertCurrentTime()));
			$db->insert('push_handler', $data);
		}
		else
		{
			$data = array('sleep_time_seconds' => $sleep, 'push_running' => 1, 'last_start' => new Zend_Db_Expr(DBConvertor::convertCurrentTime()));
			$db->update('push_handler', $data, 'zone_id = '.$zoneId.' and context_id = '.$contextId);
		}
		
		$pid = $this->psExecute($zoneId, $contextId);
		
		$data = array('php_pid' => $pid);
		$db->update('push_handler', $data, 'zone_id = '.$zoneId.' and context_id = '.$contextId);
	}
	
	public function Stop($zoneId, $contextId=1)
	{
		$db = ZitDBAdapter::getDBAdapter();
		
		$data = array('push_running' => 0, 'last_stop' => new Zend_Db_Expr(DBConvertor::convertCurrentTime()));
		$db->update('push_handler', $data, 'zone_id = '.$zoneId.' and context_id = '.$contextId);
		
		$query = "select 
					php_pid from 
					 ".DBConvertor::convertCase('push_handler')." 
				  where zone_id = $zoneId and context_id = $contextId";
						
		$result = $db->fetchAll($query);
		$pid = $result[0]->pid;
		
		if($this->psExists($pid))
		{
			$this->psKill($pid);
		}
	}
	
	private function psExecute($zoneId, $contextId) 
	{
		$config   = new Zend_Config_Ini('../config.ini', zit_config);
		$appLocation = $config->application->root->directory;
		$scriptLocation = $appLocation.'ADMIN_APPLICATION/push_handler/PushThread.php';
		
		if (substr(php_uname(), 0, 7) == "Windows")
			{
				$exec = "start /b php ".$scriptLocation."  ".$zoneId."  ".$contextId;
				pclose(popen($exec, 'r')); 
			}
			else 
			{
				$exec = " php ".$scriptLocation." ".$zoneId." ".$contextId." > /dev/null &";
				exec($exec, $arrOutput);  
			} 
		
		$pid = (int)$op[0];
		
		return $pid;
    }

    private function psExists($pid) {

        exec("ps ax | grep $pid 2>&1", $output);

        while( list(,$row) = each($output) ) {

                $row_array = explode(" ", $row);
                $check_pid = $row_array[0];

                if($pid == $check_pid) {
                        return true;
                }

        }

        return false;
    }

    private function psKill($pid) {
        exec("kill -9 $pid", $output);
    } 
}

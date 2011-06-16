<?php

class PushMessageHandler{

	var $zoneVersion;
	var $versionNamespace;
	var $zoneName;
	var $zoneId;
	var $contextId;
	
	public function PushMessageHandler($zoneId, $contextId)
	{
		$db = ZitDBAdapter::getDBAdapter();
		
		$query = "select
				  	zones.source_id,
					versions.version_num,
					versions.version_namespace
				  from
				  	zones
				  inner join 
				  	versions on versions.version_id = zones.version_id
				  where
				  	zone_id = ".$zoneId;
		$result = $db->fetchAll($query);
		
		$this->zoneVersion = $result[0]->version_num;
		$this->versionNamespace = $result[0]->version_namespace;
		$this->zoneName = $result[0]->source_id;
		$this->zoneId = $zoneId;
		$this->contextId = $contextId;
	}
	
	public function processResponseMessages()
	{
		$db = ZitDBAdapter::getDBAdapter();
		
		$query = "select 
			      	response_data,
					agent_registered.callback_url,
					agent_registered.frozen,
					response_id
				  from
				  	response
				  inner join
				  	agent_registered on response.agent_id_requester = agent_registered.agent_id
				   where
				  	request.status_id in (1,2)
				  and
				  	agent_registered.asleep = 0
				  and
				  	request.agent_mode_id = 1
				  and
				  	event.zone_id = ".$this->zoneId."
				  and
					agent_registered.zone_id = ".$this->zoneId."
				  and 
				  	event.context_id = ".$this->contextId."
				  and 
					agent_registered.context_id = ".$this->contextId;
					
				  
		$result = $db->fetchAll($query);
		foreach($result as $row)
		{
			if($row->frozen != 1)
			{
				$timestamp = Utility::createTimestamp();
				$msgId     = Utility::createMessageId();
				$xml = XmlHelper::buildSuccessMessage($msgId,
											   		  $timestamp,
											   		  $this->zoneName,
											          $msgId,
													  0,
													  $this->zoneVersion, 
													  $this->versionNamespace, 
													  $this->zoneName,
													  $originalMsg = $row->response_data,
													  $desc = null);
				
				$sendMessageResult = $this->sendMessage($xml, $row->callback_url, $this->zoneId, $this->contextId, $row->agent_id);
				if(!$sendMessageResult['Error'])
				{
					$data = array('status_id' => 2, 'msg_id' => $msgId);
					$db->update('response', $data, 'response_id = '.$row->response_id);
					
					$processResponseResult = $this->processResponseMessage($sendMessageResult['Xml'], $this->zoneId, $this->contextId, $row->agent_id);
					if(!$processResponseResult['Error'])
					{
						if($processResponseResult['ImmediateUpdate'])
						{
							$data = array('status_id' => 3, 'msg_id' => $msgId);
							$db->update('response', $data, 'response_id = '.$row->response_id);
						}
						ZitLog::writeToLog($sendMessageResult['Xml'], $xml, $this->zoneId, $row->agent_id, 7);
					}//check for errors in response
				}//check for errors in request
				else
				{
					$data = array('status_id' => 4, 'msg_id' => $msgId);
					$db->update('response', $data, 'response_id = '.$row->response_id);
				}//errors in request	
				
			}//make sure agent is not frozen
		}//loop through results
	}// end processResponseMessages
	
	public function processEventMessages()
	{
		$db = ZitDBAdapter::getDBAdapter();
		
		$query = "select 
			      	event_data,
					event_id,
					agent_registered.callback_url,
					agent_registered.frozen,
					agent_registered.agent_id
				  from
				  	event
				  inner join
				  	agent_registered on event.agent_id_rec = agent_registered.agent_id
				  where
				  	event.status_id in (1,2)
				  and
				  	agent_registered.asleep = 0
				  and
					event.agent_mode_id = 1
				  and
				  	event.zone_id = ".$this->zoneId."
				  and
					agent_registered.zone_id = ".$this->zoneId."
				  and 
				  	event.context_id = ".$this->contextId."
				  and 
					agent_registered.context_id = ".$this->contextId;
				  
		$result = $db->fetchAll($query);
		foreach($result as $row)
		{
			if($row->frozen != 1)
			{
				$timestamp = Utility::createTimestamp();
				$msgId     = Utility::createMessageId();
				$xml = XmlHelper::buildSuccessMessage($msgId,
											   		  $timestamp,
											   		  $this->zoneName,
											          $msgId,
													  0,
													  $this->zoneVersion, 
													  $this->versionNamespace, 
													  $this->zoneName,
													  $originalMsg = $row->event_data,
													  $desc = null);
				
				$sendMessageResult = $this->sendMessage($xml, $row->callback_url, $this->zoneId, $this->contextId, $row->agent_id);
				if(!$sendMessageResult['Error'])
				{
					$data = array('status_id' => 2, 'msg_id' => $msgId);
					$db->update('event', $data, 'event_id = '.$row->event_id);
					
					$processResponseResult = $this->processResponseMessage($sendMessageResult['Xml'], $this->zoneId, $this->contextId, $row->agent_id);
					if(!$processResponseResult['Error'])
					{
						if($processResponseResult['ImmediateUpdate'])
						{
							$data = array('status_id' => 3, 'msg_id' => $msgId);
							$db->update('event', $data, 'event_id = '.$row->event_id);
						}
						ZitLog::writeToLog($sendMessageResult['Xml'], $xml, $this->zoneId, $row->agent_id, 2);
					}//check for errors in response
				}//check for errors in request
				else
				{
					$data = array('status_id' => 4, 'msg_id' => $msgId);
					$db->update('event', $data, 'event_id = '.$row->event_id);
				}//errors in request	
			}//make sure agent is not frozen
		}//loop through results
	}//end processEventMessages
	
	public function processRequestMessages()
	{
		$db = ZitDBAdapter::getDBAdapter();
		
		$query = "select 
			      	request_data,
					agent_registered.callback_url,
					agent_registered.frozen,
					request_id
				  from
				  	request
				  inner join
				  	agent_registered on request.agent_id_responder = agent_registered.agent_id
				   where
				  	request.status_id in (1,2)
				  and
				  	agent_registered.asleep = 0
				  and
				  	request.agent_mode_id = 1
				  and
				  	event.zone_id = ".$this->zoneId."
				  and
					agent_registered.zone_id = ".$this->zoneId."
				  and 
				  	event.context_id = ".$this->contextId."
				  and 
					agent_registered.context_id = ".$this->contextId;				;
				  
		$result = $db->fetchAll($query);
		foreach($result as $row)
		{
			if($row->frozen != 1)
			{
				$timestamp = Utility::createTimestamp();
				$msgId     = Utility::createMessageId();
				$xml = XmlHelper::buildSuccessMessage($msgId,
											   		  $timestamp,
											   		  $this->zoneName,
											          $msgId,
													  0,
													  $this->zoneVersion, 
													  $this->versionNamespace, 
													  $this->zoneName,
													  $originalMsg = $row->request_data,
													  $desc = null);
				
				$sendMessageResult = $this->sendMessage($xml, $row->callback_url, $this->zoneId, $this->contextId, $row->agent_id);
				echo 'here after';
				if(!$sendMessageResult['Error'])
				{
					$data = array('status_id' => 2, 'msg_id' => $msgId);
					$db->update('request', $data, 'request_id = '.$row->request_id);
					
					$processResponseResult = $this->processResponseMessage($sendMessageResult['Xml'], $this->zoneId, $this->contextId, $row->agent_id);
					if(!$processResponseResult['Error'])
					{
						if($processResponseResult['ImmediateUpdate'])
						{
							$data = array('status_id' => 3, 'msg_id' => $msgId);
							$db->update('request', $data, 'request_id = '.$row->request_id);
						}
						ZitLog::writeToLog($sendMessageResult['Xml'], $xml, $this->zoneId, $row->agent_id, 6);
					}//check for errors in response
				}//check for errors in request
				else
				{
					$data = array('status_id' => 4, 'msg_id' => $msgId);
					$db->update('request', $data, 'request_id = '.$row->request_id);
				}//errors in request
			}//make sure agent is not frozen
		}//loop through results
	}//end processRequestMessages
	
	private function sendMessage($xml, $url, $zoneId, $contextId, $agentId)
	{
		$results = array('Xml' => '', 'Error' => false);
		
		try
		{
			$client = new Zend_Http_Client(urldecode($url), array('adapter' => 'Zend_Http_Client_Adapter_Socket'));
			$results['Xml'] = $client->setRawData($xml, 'text/xml')->setEncType('text/xml')->request('POST')->getBody();
		}
		catch (Exception $e) 
		{
			ZitLog::writeToErrorLog("[Error Pushing Messages] Check Agents", "Error Message\n".$e->getMessage()."\n\nStack Trace\n".$e->getTraceAsString(),'Push Thread', $zoneId, $contextId, $agentId);
			$results['Error'] = true;
		}
		
		return $results;
	}
	
	private function processResponseMessage($xml, $zoneId, $contextId, $agentId){
		$results = array('ImmediateUpdate' => true, 'Error' => false);
		
		$dom = new DomDocument();
		$dom->preserveWhiteSpace = false;
		$res = $dom->loadXML($xml);
		if($res){
		
			$headerNode   = $dom->getElementsByTagName('SIF_Header')->item(0);
			
			$msgId           = $headerNode->getElementsByTagName('SIF_MsgId')->item(0)->nodeValue;
			$agentSourceId   = $headerNode->getElementsByTagName('SIF_SourceId')->item(0)->nodeValue;
			$timestamp       = $headerNode->getElementsByTagName('SIF_Timestamp')->item(0)->nodeValue;
			
			$originalMsgId   = $dom->getElementsByTagName('SIF_OriginalMsgId')->item(0)->nodeValue;
			$status          = $dom->getElementsByTagName('SIF_Status');
			$status          = $status->item(0)->nodeValue;
			$validSourceId = Agent::checkSourceId($agentSourceId);
			if(!$validSourceId){
				ZitLog::writeToErrorLog('[Error Pushing Messages] Invalid Source Id In Response', "Reponse Xml:\n$xml", 'Push Message Response', $zoneId, $contextId, $agentId);
				$results['Error'] = true;
			}
			else{
				$agent = new Agent($agentSourceId, $zoneId, $contextId);
				if($agent->isRegistered()){
					if($status == 2){
						//Intermediate wait for final
						$results['ImmediateUpdate'] = false;
						$agent->freezeAgent();
						$agent->setFrozenMsgId($originalMsgId);
					}
				}
				else{
					ZitLog::writeToErrorLog('[Error Pushing Messages] Response Agent Not Registered', "Response agent is not registered in the system",  'Push Message Response', $zoneId, $contextId, $agentId);
					$results['Error'] = true;
				}
			}
		}
		else
		{
			ZitLog::writeToErrorLog('[Error Pushing Messages] Reponse Xml Invalid', $xml, 'Push Message Response', $zoneId, $contextId, $agentId);
			$results['Error'] = true;
		}
		return $results;
	}
}

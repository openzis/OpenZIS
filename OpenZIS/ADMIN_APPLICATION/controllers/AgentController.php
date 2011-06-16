<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

require_once 'Zend/Controller/Action.php';

class AgentController extends Zend_Controller_Action {
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            $identity = $auth->getIdentity();
            $zitAdmin = new ZitAdmin($identity);

            $this->view->zitName = $zitAdmin->zit->zitName;

            $agents = Agent::getAllAgents();
            $this->view->agents = $agents;
        }
        else {
            return $this->_forward('index', 'index', null);
        }
    }

    public function getagentmessagesAction() {
  
    $lic = $_REQUEST['lic'];
    if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');      
    } else {
          $auth = Zend_Auth::getInstance();
          if ($auth->hasIdentity()) {

              if (!$this->getRequest()->isXmlHttpRequest()) {
                  $this->view->msg = 'Not Ajax Request';
                  $this->_forward('error', 'error');
              }
              else {
          $empty = null;
                  $filterChain = new Zend_Filter();
                  $filterChain->addFilter(new Zend_Filter_Digits());
          $agentId = isset($_POST['AGENT_ID']) ? $filterChain->filter($_POST['AGENT_ID']) : $empty;
          $zoneId = isset($_POST['ZONE_ID']) ? $filterChain->filter($_POST['ZONE_ID']) : $empty;
          
          if ($agentId != $empty){
            $this->view->pushMessages = Agent::getMessages($agentId, 1, $zoneId);
                    $this->view->receivedMessages = Agent::getMessages($agentId, 2, $zoneId);
                    $this->render('agentmessages');
          }
          else {
            $this->view->msg = 'Not Ajax Request';
                    $this->_forward('error', 'error');
          }
              }
          }
          else {
        if (!$this->getRequest()->isXmlHttpRequest()) {
          $this->view->msg = 'Not Ajax Request';
          $this->_forward('error', 'error');
        }
        else{
          $this->view->msg = 'Invalid User';
                $this->_forward('error', 'error');
        }
          }
    }
    }

    public function getagentlistAction() {
        $auth = Zend_Auth::getInstance();
    $lic = $_REQUEST['lic'];
    if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');      
    } else {
        if ($auth->hasIdentity()) {
      Zend_Session::regenerateId();
            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->view->msg = 'Not Ajax Request';
                $this->_forward('error', 'error');
            }
            else {
                $agents = Agent::getAllAgents();
                $this->view->agents = $agents;
                $this->render('agentlist');
            }
        }
        else {
      if (!$this->getRequest()->isXmlHttpRequest()) {
        $this->view->msg = 'Not Ajax Request';
        $this->_forward('error', 'error');
      }
      else{
        $this->view->msg = 'Invalid User';
              $this->_forward('error', 'error');
      }
        }
    }
    }

    public function getagentsAction() {
        $auth = Zend_Auth::getInstance();
    $lic = $_REQUEST['lic'];
    if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');      
    } else {
        if ($auth->hasIdentity()) {

            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->view->msg = 'Not Ajax Request';
                $this->_forward('error', 'error');
            }
            else {
                $agents = Agent::getAgents();
                if(count($agents) > 0) {
                    $json = Zend_Json::encode($agents);
                    $this->view->json = $json;
                }
                $this->render('ajaxsuccessjson');
            }
        }
        else {
      if (!$this->getRequest()->isXmlHttpRequest()) {
        $this->view->msg = 'Not Ajax Request';
        $this->_forward('error', 'error');
      }
      else{
        $this->view->msg = 'Invalid User';
              $this->_forward('error', 'error');
      }
        }
    } 
    }

    public function getagentAction() {
    $lic = $_REQUEST['lic'];
    if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');      
    } else {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->view->msg = 'Not Ajax Request';
                $this->_forward('error', 'error');
            }
            else {
                $filterChain = new Zend_Filter();
                $filterChain->addFilter(new Zend_Filter_Digits());

                $agentId = $filterChain->filter($_POST['AGENT_ID']);
                $zoneId = $filterChain->filter($_POST['ZONE_ID']);

                $agent = Agent::getAgent($agentId, $zoneId);
                $permissions = Permission::getAllAgentPermissions($agentId, $zoneId, 1);

                $agentJson = Zend_Json::encode($agent);
                $agentJson = ',"agents":'.$agentJson;

                $permissionJson = Zend_Json::encode($permissions);
                $permissionJson  = ',"permissions":'.$permissionJson;

                $this->view->agentJson      = $agentJson;
                $this->view->permissionJson = $permissionJson;

                $this->render('ajaxsuccessjson');
            }
        }
        else {
      if (!$this->getRequest()->isXmlHttpRequest()) {
        $this->view->msg = 'Not Ajax Request';
        $this->_forward('error', 'error');
      }
      else{
        $this->view->msg = 'Invalid User';
              $this->_forward('error', 'error');
      }
        }
}
    }

    public function getzoneagentsAction() {
        $auth = Zend_Auth::getInstance();
    $lic = $_REQUEST['lic'];
    if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');      
    } else {
        if ($auth->hasIdentity()) {

            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->view->msg = 'Not Ajax Request';
                $this->_forward('error', 'error');
            }
            else {
        $empty = null;
                $filterChain = new Zend_Filter();
                $filterChain->addFilter(new Zend_Filter_Digits());
        $zoneId = isset($_POST['ID']) ? $filterChain->filter($_POST['ID']) : $empty;
        
        if ($zondId != $empty){
          $agents = Agent::getAllAgentsInZone($zoneId);
                  if(count($agents) > 0) {
                      $json = Zend_Json::encode($agents);
                      $this->view->json = $json;
                  }
                  $this->render('ajaxsuccessjson');
        }
        else{
          $this->view->msg = 'Zone Id Missing';
                  $this->_forward('error', 'error');
        }
            }
        }
        else {
      if (!$this->getRequest()->isXmlHttpRequest()) {
        $this->view->msg = 'Not Ajax Request';
        $this->_forward('error', 'error');
      }
      else{
        $this->view->msg = 'Invalid User';
              $this->_forward('error', 'error');
      }
    }
        }
    }

    public function addagentAction() {
    $lic = $_REQUEST['lic'];
    if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');      
    } else {
      
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
      		Zend_Session::regenerateId();

            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->view->msg = 'Not Ajax Request';
                $this->_forward('error', 'error');
            }
            else {
                $empty = '';
        		$filterChain = new Zend_Filter();
                $filterChain->addFilter(new Zend_Filter_StripTags());

        		$filterChain2 = new Zend_Filter();
                $filterChain2->addFilter(new Zend_Filter_Digits());

        		$desc     = isset($_POST['DESCRIPTION']) ? $filterChain->filter($_POST['DESCRIPTION']) : $empty;
		        $sourceId   = isset($_POST['SOURCE_ID']) ? $filterChain->filter($_POST['SOURCE_ID']) : $empty;
		        $password = isset($_POST['PASSWORD']) ? $filterChain->filter($_POST['PASSWORD']) : $empty;
		        $username   = isset($_POST['USERNAME']) ? $filterChain->filter($_POST['USERNAME']) : $empty;
		        $ipaddress  = $empty;
			  	$ip 		  = isset($_POST['IPADDRESS']) ? $filterChain->filter($_POST['IPADDRESS']) : $empty;
			  	$ip_str	  = substr($ip, 0, 3);
			  	if ($ip_str != 'ex:'){
					$ipaddress  = $ip;
			  	}
 		  
	  			$maxbuffer  = $empty;
			  	$max 		  = isset($_POST['MAXBUFFERSIZE']) ? $filterChain->filter($_POST['MAXBUFFERSIZE']) : $empty;
			  	$max_str	  = substr($max, 0, 3);
			  	if ($max_str != 'ex:'){
					$maxbuffer  = isset($_POST['MAXBUFFERSIZE']) ? $filterChain2->filter($_POST['MAXBUFFERSIZE']) : $empty;
			  	}

                if(!Agent::agentExists($desc, $sourceId)) {
                    if(Agent::addAgent($desc, $sourceId, $password, $username, $ipaddress, $maxbuffer)) {
                        $this->render('ajaxsuccessjson');
                    }
                    else {
                        $this->view->msg = "Error Creating Agent";
                        $this->_forward('index', 'error');
                    }
                }
                else {
                    $this->view->msg = "Agent name or Source ID already being used";
                    $this->_forward('index', 'error');
                }
            }
        }
        else {
      if (!$this->getRequest()->isXmlHttpRequest()) {
        $this->view->msg = 'Not Ajax Request';
        $this->_forward('error', 'error');
      }
      else{
        $this->view->msg = 'Invalid User';
              $this->_forward('error', 'error');
      }
    }}
    }

    public function updateagentAction() {
    $lic = $_REQUEST['lic'];
    if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');      
    } else {
      
          $auth = Zend_Auth::getInstance();
          if ($auth->hasIdentity()) {
        Zend_Session::regenerateId();
        
              if (!$this->getRequest()->isXmlHttpRequest()) {
                  $this->view->msg = 'Not Ajax Request';
                  $this->_forward('error', 'error');
              }
              else {
                  
          		  $empty = null;
                  $filterChain = new Zend_Filter();
                  $filterChain->addFilter(new Zend_Filter_StripTags());

				  $filterChain2 = new Zend_Filter();
                  $filterChain2->addFilter(new Zend_Filter_Digits());

                  $desc       = $filterChain->filter($_POST['DESCRIPTION']);
                  $agentId    = $filterChain->filter($_POST['ID']);
                  $sourceId   = $filterChain->filter($_POST['SOURCE_ID']);
                  $password   = $filterChain->filter($_POST['PASSWORD']);
                  $username   = $filterChain->filter($_POST['USERNAME']);
				  $ipaddress  = $empty;
				  $ip 		  = isset($_POST['IPADDRESS']) ? $filterChain->filter($_POST['IPADDRESS']) : $empty;
				  $ip_str	  = substr($ip, 0, 3);
				  if ($ip_str != "ex:"){
					$ipaddress  = $ip;
				  }
          		  
				  $maxbuffer  = $empty;
				  $max 		  = isset($_POST['MAXBUFFERSIZE']) ? $filterChain->filter($_POST['MAXBUFFERSIZE']) : $empty;
				  $max_str	  = substr($max, 0, 3);
				  if ($max_str != "ex:"){
					$maxbuffer  = $filterChain2->filter($max);
				  }

                  $active     = $filterChain->filter($_POST['ACTIVE']);

                  Agent::updateAgent(
              				$desc,
                          	$sourceId,
              				$username,
                          	$password,
                          	$agentId,
                          	$ipaddress,
              				$maxbuffer,
                          	$active);
                  $this->render('ajaxsuccessjson');
              }
          }
          else {
        if (!$this->getRequest()->isXmlHttpRequest()) {
          $this->view->msg = 'Not Ajax Request';
          $this->_forward('error', 'error');
        }
        else{
          $this->view->msg = 'Invalid User';
                $this->_forward('error', 'error');
        }
          }
    }
    }

    public function deleteagentAction() {
    $lic = $_REQUEST['lic'];
    if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');      
    } else {
      
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
      Zend_Session::regenerateId();

            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->view->msg = 'Not Ajax Request';
                $this->_forward('error', 'error');
            }
            else {
                $filterChain = new Zend_Filter();
                $filterChain->addFilter(new Zend_Filter_Digits())->addFilter(new Zend_Filter_StripTags());

                $agentId = $filterChain->filter($_POST['AGENT_ID']);

                Agent::deleteAgent($agentId);
                $this->render('ajaxsuccessjson');
            }
        }
        else {
      if (!$this->getRequest()->isXmlHttpRequest()) {
        $this->view->msg = 'Not Ajax Request';
        $this->_forward('error', 'error');
      }
      else{
        $this->view->msg = 'Invalid User';
              $this->_forward('error', 'error');
      }
        }
    }
}
}
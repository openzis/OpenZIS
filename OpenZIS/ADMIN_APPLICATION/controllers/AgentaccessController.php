<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/
require_once 'Zend/Controller/Action.php';

class AgentAccessController extends Zend_Controller_Action
{
    public function indexAction() 
	{ 
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			
			$identity = $auth->getIdentity();
			$zitAdmin = new ZitAdmin($identity);
			
			$this->view->zitName = $zitAdmin->zit->zitName;
			
			$zones = Zone::getAllZones();
			$contexts = Context::getAllContexts();
			$agents = Agent::getAllAgents();
			
			$this->view->zones = $zones;
			$this->view->agents = $agents;
			$this->view->contexts = $contexts;
		}
		else{
			return $this->_forward('index', 'index', null);
		}
	} 	

	public function getcomboboxesAction(){
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			
			if (!$this->getRequest()->isXmlHttpRequest()) {
				$this->view->msg = 'Not Ajax Request';
				$this->_forward('error', 'error');
			}
			else{
				$zones = Zone::getAllZones();
				$contexts = Context::getAllContexts();
				$agents = Agent::getAllAgents();
				
				$this->view->zones = $zones;
				$this->view->agents = $agents;
				$this->view->contexts = $contexts;
				$this->render('aclcomboboxes');
			}
		}
		else{
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
	
	public function getagentpermissionsAction(){
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			
			if (!$this->getRequest()->isXmlHttpRequest()) {
				$this->view->msg = 'Not Ajax Request';
				$this->_forward('error', 'error');
			}
			else{
				$filterChain = new Zend_Filter();
                $filterChain->addFilter(new Zend_Filter_Digits())
							->addFilter(new Zend_Filter_StripTags());
				
				$zoneId = $filterChain->filter($_POST['ZONE_ID']);
				$contextId = $filterChain->filter($_POST['CONTEXT_ID']);
				$agentId = $filterChain->filter($_POST['AGENT_ID']);
				
				$permissions = Permission::getAllAgentPermissions($agentId, $zoneId, $contextId);
				if(count($permissions) > 0){
					$json = Zend_Json::encode($permissions);
					$this->view->json = $json;
				}
				
				$this->render('ajaxsuccessjson');
			}
		}
		else{
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
	
	public function updatepermissionAction(){
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			Zend_Session::regenerateId();
			if (!$this->getRequest()->isXmlHttpRequest()) {
				$this->view->msg = 'Not Ajax Request';
				$this->_forward('error', 'error');
			}
			else{
				$filterChain = new Zend_Filter();
				$filterChain->addFilter(new Zend_Filter_Digits());
				
				$permissionId = $filterChain->filter($_POST['PERMISSION_ID']);

				$provide	  =	$filterChain->filter(Utility::convertCheckBoxValue($_POST['PROVIDE']));	      
				$subscribe	  =	$filterChain->filter(Utility::convertCheckBoxValue($_POST['SUBSCRIBE']));      
				$request	  =	$filterChain->filter(Utility::convertCheckBoxValue($_POST['REQUEST']));     
				$respond	  =	$filterChain->filter(Utility::convertCheckBoxValue($_POST['RESPOND']));       
				$add		  =	$filterChain->filter(Utility::convertCheckBoxValue($_POST['ADD']));           
				$change		  =	$filterChain->filter(Utility::convertCheckBoxValue($_POST['CHANGE']));         
				$delete		  =	$filterChain->filter(Utility::convertCheckBoxValue($_POST['DELETE']));

				
						Permission::updatePermission(
											$permissionId,
											$provide,
											$subscribe,
											$add,
											$change,
											$delete,
											$request,
											$respond
											);
						$this->render('ajaxsuccessjson');
				/*	} else {
						$this->view->msg = 'Data Validation Error';
						$this->_forward('error', 'error');						
					}
				*/
				
			}
		}
		else{
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
	
	public function savenewpermissionAction(){
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			Zend_Session::regenerateId();
			if (!$this->getRequest()->isXmlHttpRequest()) {
				$this->view->msg = 'Not Ajax Request';
				$this->_forward('error', 'error');
			}
			else{
				$filterChain = new Zend_Filter();
				$filterChain->addFilter(new Zend_Filter_Digits());
				
				$default = null;
				
				$zoneId       = $filterChain->filter($_POST['ZONE_ID']);
				$contextId    = $filterChain->filter($_POST['CONTEXT_ID']);
				$agentId      = $filterChain->filter($_POST['AGENT_ID']);
				$dataObjectId = $filterChain->filter($_POST['DATA_OBJECT_ID']); 
				$provide	  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['PROVIDE']) ? $_POST['PROVIDE'] : $default));	      
				$subscribe	  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['SUBSCRIBE']) ? $_POST['SUBSCRIBE'] : $default));     
				$request	  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['REQUEST']) ? $_POST['REQUEST'] : $default));  
				$respond	  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['RESPOND']) ? $_POST['RESPOND'] : $default));    
				$add		  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['ADD']) ? $_POST['ADD'] : $default));          
				$change		  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['CHANGE']) ? $_POST['CHANGE'] : $default));        
				$delete		  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['DELETE']) ? $_POST['DELETE'] : $default));
				
				$validatorChain = new Zend_Validate();
				$validatorChain->addValidator(new Zend_Validate_Digits())
							   ->addValidator( new Zend_Validate_Between(array('min' => 0, 'max' => 1)));
							
				/*
				if (!$validatorChain->isValid($provide) &&
					!$validatorChain->isValid($subscribe) &&
					!$validatorChain->isValid($request) &&
					!$validatorChain->isValid($respond) &&
					!$validatorChain->isValid($add) &&
					!$validatorChain->isValid($change) &&
					!$validatorChain->isValid($delete)
				) {
					$this->view->msg = 'Data Validation Error';
					$this->_forward('error', 'error');			
				}
				*/
				
				$exist = Permission::checkIfPermissionExist($zoneId,
														    $agentId,
														    $contextId,
														    $dataObjectId);
				if($exist){
					$this->view->msg = 'Permission Already Exists';
					$this->_forward('error', 'error');
				}
				else{
					Permission::addPermission(
												$zoneId,
												$agentId,
												$contextId,
												$dataObjectId,
												$provide,
												$subscribe,
												$add,
												$change,
												$delete,
												$request,
												$respond
											);
											
					$this->render('ajaxsuccessjson');
					
				}
			}
		}
		else{
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

<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/
require_once 'Zend/Controller/Action.php';

class GroupPermissionController extends Zend_Controller_Action
{
    public function indexAction() 
	{} 	
	
	public function agentusegroupAction(){
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
				else{
					Zend_Session::regenerateId();
					$filterChain = new Zend_Filter();
					$filterChain->addFilter(new Zend_Filter_StripTags());
				
					$filterChain2 = new Zend_Filter();
	                $filterChain2->addFilter(new Zend_Filter_Digits());
				
					$groupId    = $filterChain2->filter($_POST['GROUP_ID']);
					$agentId    = $filterChain2->filter($_POST['AGENT_ID']);
					$zoneId     = $filterChain2->filter($_POST['ZONE_ID']);
					$override   = $filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['OVERRIDE']) ? $_POST['OVERRIDE'] : null));
					$contextId  = 1;
				
					if($override != null){
						$override = 1;
					}
					else{
						$override = 0;
					}
				
					GroupPermission::useGroupOnAgent($groupId, $agentId, $zoneId, $contextId, $override);
					$this->render('ajaxsuccessjson');
				}
			}
			else{
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$this->view->msg = 'Not Ajax Request';
					$this->_forward('error', 'error');
				}
				else{
					$this->view->msg = 'errors:{reason:"Invalid User"}';
	            	$this->_forward('error', 'error');
				}
			}
		}
	}
	
	public function addgroupitemAction(){
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
				else{
					$filterChain = new Zend_Filter();
					$filterChain->addFilter(new Zend_Filter_StripTags());
				
					$filterChain2 = new Zend_Filter();
	                $filterChain2->addFilter(new Zend_Filter_Digits());
				
					$groupId      = $filterChain2->filter($_POST['GROUP_ID']);
					$objectId     = $filterChain2->filter($_POST['DATA_OBJECT_ID']); 
					$provide	  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['PROVIDE'])   ? $_POST['PROVIDE'] 	: null));	      
					$subscribe	  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['SUBSCRIBE']) ? $_POST['SUBSCRIBE'] : null));     
					$request	  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['REQUEST'])   ? $_POST['REQUEST'] 	: null));  
					$respond	  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['RESPOND'])   ? $_POST['RESPOND'] 	: null));    
					$add		  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['ADD']) 	  ? $_POST['ADD'] 		: null));          
					$change		  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['CHANGE']) 	  ? $_POST['CHANGE'] 	: null));        
					$delete		  =	$filterChain->filter(Utility::convertCheckBoxValue(isset($_POST['DELETE']) 	  ? $_POST['DELETE'] 	: null));
				
					if(GroupPermissionItem::dataObjectGroupExist($groupId, $objectId)){
						$this->view->msg = 'errors:{reason:"Permission Already Exists"}';
						$this->_forward('error', 'error');
					}
					else{
						if(GroupPermissionItem::addGroupItem($groupId,$objectId,$provide,$subscribe, $add, $change, $delete, $request,$respond)){
							$this->render('ajaxsuccessjson');
						}
						else{
							$this->view->msg = 'errors:{reason:"Error Adding Permission"}';
							$this->_forward('error', 'error');
						}
					}
				}
			}
			else{
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$this->view->msg = 'Not Ajax Request';
					$this->_forward('error', 'error');
				}
				else {
					$this->view->msg = 'errors:{reason:"Invalid User"}';
				    $this->_forward('error', 'error');
				}
			}
		}
	}
	
	public function getgroupsitemsAction(){
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
				else{
					$filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_Digits());
				
					$id = $filterChain->filter($_POST['GROUP_ID']);
				
					$items = GroupPermissionItem::getGroupItems($id);
					$json = Zend_Json::encode($items);
					$this->view->json = $json;
					$this->render('ajaxsuccessjson');
				}
			}
			else{
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$this->view->msg = 'Not Ajax Request';
					$this->_forward('error', 'error');
				}
				else {
					$this->view->msg = 'errors:{reason:"Invalid User"}';
				    $this->_forward('error', 'error');
				}
			}
		}
	}
	
	public function getgroupsAction(){
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
				else{
					$groups = GroupPermission::getGroups();
					$json = Zend_Json::encode($groups);
					$this->view->json = $json;
					$this->render('ajaxsuccessjson');
				}
			}
			else{
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$this->view->msg = 'Not Ajax Request';
					$this->_forward('error', 'error');
				}
				else {
					$this->view->msg = 'errors:{reason:"Invalid User"}';
				    $this->_forward('error', 'error');
				}
			}
		}
	}
	
	public function getgroupszoneAction(){
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
				else{
					$filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_StripTags())
								->addFilter(new Zend_Filter_Digits());
				
					$zoneId = $filterChain->filter($_REQUEST['ZONE_ID']);
				
					$zone    = new Zone($zoneId);
					$version = $zone->versionId;
					$groups = GroupPermission::getGroupsByVersion($version);
					$json = Zend_Json::encode($groups);
					$this->view->json = $json;
					$this->render('ajaxsuccessjson');
				}
			}
			else{
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$this->view->msg = 'Not Ajax Request';
					$this->_forward('error', 'error');
				}
				else {
					$this->view->msg = 'errors:{reason:"Invalid User"}';
				    $this->_forward('error', 'error');
				}
			}
		}
	}
	
	public function deletegroupAction(){
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
				else{
					$filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_StripTags())
								->addFilter(new Zend_Filter_Digits());
				
					$id = $filterChain->filter($_POST['GROUP_ID']);
					GroupPermission::deleteGroup($id);
					$this->render('ajaxsuccessjson');
				}
			}
			else{
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$this->view->msg = 'Not Ajax Request';
					$this->_forward('error', 'error');
				}
				else {
					$this->view->msg = 'errors:{reason:"Invalid User"}';
				    $this->_forward('error', 'error');
				}
			}
		}
	}
	
	public function addgroupAction(){
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
				else{
					Zend_Session::regenerateId();
					$filterChain = new Zend_Filter();
					$filterChain->addFilter(new Zend_Filter_StripTags());
				
					$name    = $filterChain->filter($_POST['NAME']);
					$version = $filterChain->filter($_POST['VERSION']);
				
					if($groups = GroupPermission::addGroup($name, $version)){
						$this->render('ajaxsuccessjson');
					}
					else{
						$this->view->msg = 'errors:{reason:"Error Adding Group"}';
						$this->_forward('error', 'error');
					}
				}
			}
			else
			{
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$this->view->msg = 'Not Ajax Request';
					$this->_forward('error', 'error');
				}
				else {
					$this->view->msg = 'errors:{reason:"Invalid User"}';
				    $this->_forward('error', 'error');
				}
			}
		}
	}
	
	public function updategroupitemAction(){
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
				else{
					Zend_Session::regenerateId();
					$filterChain = new Zend_Filter();
					$filterChain->addFilter(new Zend_Filter_StripTags());
				
					$filterChain2 = new Zend_Filter();
	                $filterChain2->addFilter(new Zend_Filter_Digits());
				
					$itemId       = $filterChain2->filter($_POST['ITEM_ID']);
					$provide	  =	$filterChain2->filter(Utility::convertCheckBoxValue($_POST['PROVIDE']));	      
					$subscribe	  =	$filterChain2->filter(Utility::convertCheckBoxValue($_POST['SUBSCRIBE']));      
					$request	  =	$filterChain2->filter(Utility::convertCheckBoxValue($_POST['REQUEST']));     
					$respond	  =	$filterChain2->filter(Utility::convertCheckBoxValue($_POST['RESPOND']));       
					$add		  =	$filterChain2->filter(Utility::convertCheckBoxValue($_POST['ADD']));           
					$change		  =	$filterChain2->filter(Utility::convertCheckBoxValue($_POST['CHANGE']));         
					$delete		  =	$filterChain2->filter(Utility::convertCheckBoxValue($_POST['DELETE']));
				
					GroupPermissionItem::updateGroupItem(
												$itemId,
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
			else{
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$this->view->msg = 'Not Ajax Request';
					$this->_forward('error', 'error');
				}
				else {
					$this->view->msg = 'errors:{reason:"Invalid User"}';
				    $this->_forward('error', 'error');
				}
			}
		}
	}
}
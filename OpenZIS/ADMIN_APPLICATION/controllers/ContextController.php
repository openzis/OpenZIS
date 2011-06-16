<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

require_once 'Zend/Controller/Action.php';

class ContextController extends Zend_Controller_Action
{
    public function indexAction(){ 
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			
			$identity = $auth->getIdentity();
			$zitAdmin = new ZitAdmin($identity);
			
			$this->view->zitName = $zitAdmin->zit->zitName;
			
			$contexts = Context::getAllContexts();
			$this->view->contexts = $contexts;
		}
		else
		{
			return $this->_forward('index', 'index', null);
		}
	} 	
	
	public function getcontextlistAction(){
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
				
					$contexts = Context::getAllContexts();
					$this->view->contexts = $contexts;
					$this->render('contextlist');
				}
			}
			else
			{
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
	
	public function addcontextAction()
	{
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
					$filterChain->addFilter(new Zend_Filter_StripTags());
				
					$desc = $filterChain->filter($_POST['DESCRIPTION']);
					Context::addContext($desc);
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
	}
	
	public function updatecontextAction()
	{
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
					$filterChain->addFilter(new Zend_Filter_StripTags());
				
					$filterChain2 = new Zend_Filter();
					$filterChain2->addFilter(new Zend_Filter_StripTags())
								->addFilter(new Zend_Filter_Digits());
				
					$desc = $filterChain->filter($_POST['DESCRIPTION']);
					$contextId = $filterChains->filter($_POST['ID']);
					Context::updateContext($desc, $contextId);
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
	}
}
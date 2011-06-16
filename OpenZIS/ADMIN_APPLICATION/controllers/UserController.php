<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/
require_once 'Zend/Controller/Action.php';

class UserController extends Zend_Controller_Action
{
    public function createuserAction()
	{
		$auth = Zend_Auth::getInstance();
		$lic = $_REQUEST['lic'];
		if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');			
		} 
		else
		{
		
			if ($auth->hasIdentity()){
				Zend_Session::regenerateId();
				
				if($_SESSION['ADMIN_LEVEL'] == Utility::$SUPER_ADMIN)
				{
					if (!$this->getRequest()->isXmlHttpRequest()) 
					{
						$this->view->msg = 'Not Ajax Request';
						$this->_forward('error', 'error');
					}
					else
					{
						$filterChain = new Zend_Filter();
						$filterChain->addFilter(new Zend_Filter_StripTags());
						$filterChain1 = new Zend_Filter();
						$filterChain1->addFilter(new Zend_Filter_Digits());
						$filterChain2 = new Zend_Filter();
						$filterChain2->addFilter(new Zend_Filter_Alnum());
						
						
						$valid = new Zend_Validate_NotEmpty();
					
						$level    = $filterChain1->filter(isset($_POST['ADMIN_LEVEL']) 	  ? $_POST['ADMIN_LEVEL'] 	: 0 );
						if (!$valid->isValid($level)){
							$level = 0;
						}
						
						$email    = $filterChain->filter($_POST['EMAIL']);
						if (!$valid->isValid($email)){
							$this->view->msg = 'Error Creating User  -> bad email';
							$this->_forward('error', 'error');
						}
						
						$fName    = $filterChain2->filter($_POST['FNAME']);
						if (!$valid->isValid($fName)){
							$this->view->msg = 'Error Creating User  -> bad first name';
							$this->_forward('error', 'error');
						}
						
						$lName    = $filterChain2->filter($_POST['LNAME']);
						if (!$valid->isValid($lName)){
							$this->view->msg = 'Error Creating User  -> bad last name';
							$this->_forward('error', 'error');
						}
						
						$password = $filterChain->filter($_POST['PASSWORD']);
						if (!$valid->isValid($password)){
							$this->view->msg = 'Error Creating User -> bad password';
							$this->_forward('error', 'error');
						}
						
						$username = $filterChain->filter($_POST['USERNAME']);
						if (!$valid->isValid($username)){
							$this->view->msg = 'Error Creating User -> bad username';
							$this->_forward('error', 'error');
						}
						
					
						if(ZitAdmin::createAdmin($level,$email,$fName,$lName,$password,$username))
						{
							$this->render('ajaxsuccess');
						}
						else
						{
							$this->view->msg = 'Error Creating User';
							$this->_forward('error', 'error');
						}
					}
				}
				else
				{
					$this->view->msg = 'Permission Denied';
					$this->_forward('error', 'error');
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

	public function updateuserAction()
	{
		$auth = Zend_Auth::getInstance();
		$lic = $_REQUEST['lic'];
		if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');			
		} else {
			if ($auth->hasIdentity()) {
				Zend_Session::regenerateId();

				if($_SESSION['ADMIN_LEVEL'] == Utility::$SUPER_ADMIN)
				{
					if (!$this->getRequest()->isXmlHttpRequest()) {
						$this->view->msg = 'Not Ajax Request';
						$this->_forward('error', 'error');
					}
					else{
	
						 $filterChain = new Zend_Filter();
						 $filterChain->addFilter(new Zend_Filter_StripTags());
						 $userId   = $filterChain->filter($_POST['USER_ID']);
						 $active   = $filterChain->filter($_POST['ACTIVE']);
						 $level    = $filterChain->filter($_POST['ADMIN_LEVEL']);
						 $email    = $filterChain->filter($_POST['EMAIL']);
						 $fName    = $filterChain->filter($_POST['FNAME']);
						 $lName    = $filterChain->filter($_POST['LNAME']);
						 $password = $filterChain->filter($_POST['PASSWORD']);
						 $username = $filterChain->filter($_POST['USERNAME']);
	
						 ZitAdmin::updateAdmin($userId,
											  $active,
											  $level,
											  $email,
											  $fName,
											  $lName,
											  $password,
											  $username
											  );
						$this->render('ajaxsuccess');
					}
				}
				else
				{
					$this->view->msg = 'Permission Denied';
					$this->_forward('error', 'error');
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

	public function userlistAction()
	{
		$auth = Zend_Auth::getInstance();
		$lic = $_REQUEST['lic'];
		if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');			
		} else {
			
			if ($auth->hasIdentity()) 
			{
				Zend_Session::regenerateId();
				if($_SESSION['ADMIN_LEVEL'] == Utility::$SUPER_ADMIN)
				{
					if (!$this->getRequest()->isXmlHttpRequest()) 
					{
						$this->view->msg = 'Not Ajax Request';
						$this->_forward('error', 'error');
					}
					else
					{
	
						 $admins = ZitAdmin::getAllAdmins();
						 $this->view->admins = $admins;
						 $this->render('ajaxsuccess');
					}
				}
				else
				{
					$this->view->msg = 'Permission Denied';
					$this->_forward('error', 'error');
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
}
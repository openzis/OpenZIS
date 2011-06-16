<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/
require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
	{
			
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			Zend_Session::regenerateId();
			$this->view->validUser = true;
		}
		else{
			$this->view->validUser = false;
			$zit = isset($_REQUEST['homepage']) ? $_REQUEST['homepage'] : null;

			if($zit){
				$this->_forward(login);
			}
		}
	}

	public function logoutAction()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
//		session_destroy();
		Zend_Session::destroy(true);
		$this->render('ajaxsuccessjson');
	}

	public function loginAction()
    {	
		$lic = isset($_REQUEST['lic']) ? $_REQUEST['lic'] : null;
		
		$error_msg = 'Invalid Username or Password!';

		if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = $error_msg.'!';
			$this->_forward('error', 'error');			
		} else {
		
		$zit = isset($_REQUEST['homepage']) ? $_REQUEST['homepage'] : null;
		$db = ZitDBAdapter::getDBAdapter();

		$f = new Zend_Filter_StripTags();
		
		$request_username = $_REQUEST['loginUsername'];
		$username = $f->filter($request_username);
		$username2 = $f->filter($request_username);
		$password = $f->filter($_REQUEST['loginPassword']);
		
		if (empty($username) || empty($password)||($username=='')||($password=='')){
			session_destroy();
			Zend_Session::regenerateId();
			$this->view->msg = $error_msg.'!!';
			$this->_forward('error', 'error');
		}			
		
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		switch(DB_TYPE) {
			case 'mysql':
			$authAdapter->setTableName('authenticate');
			$authAdapter->setIdentityColumn('admin_username');
			$authAdapter->setCredentialColumn('admin_password');
			break;		
			case 'oci8':
			$authAdapter->setTableName('AUTHENTICATE');
			$authAdapter->setIdentityColumn('ADMIN_USERNAME');
			$authAdapter->setCredentialColumn('ADMIN_PASSWORD');
			break;
		}		
		
		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($password);
		
		$auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);

		Zend_Session::regenerateId();
		$username = $db->quote($username);
		if (!$result->isValid()){		
			$this->view->msg = $error_msg."!"."!";
			$this->_forward('error', 'error');
		} else {

			session_regenerate_id();
			$_SESSION['SERVER_GENERATED_SID'] = true;
			
			$za = new ZitAdminDB($db);
			$resultset = $za->fetchAll("admin_username = $username");
			
			
			switch(DB_TYPE) {
				case 'mysql':
//				$query = "SELECT admin_level_id, admin_id, active FROM zit_admin WHERE admin_username = $username";
//				$resultset = $db->fetchAll($query);
				$ZSN->admin_level = $resultset[0]->admin_level_id;
				$ZSN->admin_id 	  = $resultset[0]->admin_id;
				$_SESSION['ADMIN_LEVEL'] = $resultset[0]->admin_level_id;
				$_SESSION['ADMIN_ID']    = $resultset[0]->admin_id;					
				$this->view->adminLevel  = $resultset[0]->admin_level_id;
				break;

				case 'oci8':
//				$query = "SELECT ADMIN_LEVEL_ID, ADMIN_ID, ACTIVE FROM ZIT_ADMIN WHERE admin_username = $username";
//				$resultset = $db->fetchAll($query);
				$ZSN->admin_level = $resultset[0]->ADMIN_LEVEL_ID;
				$ZSN->admin_id 	  = $resultset[0]->ADMIN_ID;
				$_SESSION['ADMIN_LEVEL'] = $resultset[0]->ADMIN_LEVEL_ID;
				$_SESSION['ADMIN_ID']    = $resultset[0]->ADMIN_ID;					
				$this->view->adminLevel  = $resultset[0]->ADMIN_LEVEL_ID;
				break;
			}
			
			$token = md5(uniqid());
			$better_token = md5(uniqid(rand(), true));
			$key = strtoupper($better_token);
			$_SESSION['OPENZISKEYHOLE'] = $key;
			$ZSN->key	  	  = $key;

			$data  =  array('LAST_LOGIN' => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
							'ATTEMPTS' => 0);
			$where = 'admin_id = '.$_SESSION['ADMIN_ID'];
			$za->update($data, $where);
			
			
//			$admin = new TB_ZitAdmin();
//			$who = $admin->FetchRow->( "LOWER(ADMIN_USERNAME) = LOWER('$username')");
//			print_r($who);

			if($zit == 1){
				$this->view->validUser = true;
				$this->render('index');
			}
			else{
				$this->render('ajaxsuccessjson');
		    }
		}	
		}
	}
}
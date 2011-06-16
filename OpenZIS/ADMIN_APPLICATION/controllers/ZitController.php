<?php
/*
this file is part of OpenZIS (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/
require_once 'Zend/Controller/Action.php';

class ZitController extends Zend_Controller_Action {
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            if (!$this->getRequest()->isXmlHttpRequest()) {
                $this->view->msg = 'Not Ajax Request';
                $this->_forward('error', 'error');
            }
            else {
                $zit = new Zit(1);
                $errorMessages = Zit::getErrorMessages();
                $this->view->zit = $zit;
                $this->view->errorMessages = $errorMessages;
                $this->render('index');
            }
        }
        else {
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

    public function updatezitAction() {
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
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_StripTags());

					$filterChain2 = new Zend_Filter();
	                $filterChain2->addFilter(new Zend_Filter_Digits());

	                $zitName   = $filterChain->filter($_POST['ZIT_NAME']);
					$sourceId  = $filterChain->filter($_POST['SOURCE_ID']);
					//$zitName   = $_POST['ZIT_NAME'];
	                //$sourceId  = $_POST['SOURCE_ID'];
	                $minBuffer = $filterChain2->filter($_POST['MIN_BUFFER']);
	                $maxBuffer = $filterChain2->filter($_POST['MAX_BUFFER']);
					$adminUrl  = $filterChain->filter($_POST['ADMIN_URL']);
	                $zitUrl    = $filterChain->filter($_POST['ZIT_URL']);
					//$adminUrl  = $_POST['ADMIN_URL'];
	                //$zitUrl    = $_POST['ZIT_URL'];

	                Zit::updateZit(1,$zitName,$sourceId,$adminUrl,$minBuffer,$maxBuffer,$zitUrl);
	                $this->render('ajaxsuccessjson');
	            }
	        }
	        else {
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

    public function putzittosleepAction() {
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
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_StripTags());

	                $sleepVal   = $filterChain->filter($_POST['SLEEP_VAL']);

	                Zit::putZitToSleep(1, $sleepVal);
	                $this->render('ajaxsuccessjson');
	            }
	        }
	        else {
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

    public function geterrormessagesAction() {
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


	                $errorMessages = Zit::getErrorMessages();
	                $this->view->errorMessages = $errorMessages;
	                $this->render('index');
	            }
	        }
	        else {
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

    public function archivemessagesAction() {
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
	                Zit::ArchiveMessages();
	                $this->render('ajaxsuccessjson');
	            }
	        }
	        else {
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

<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/
require_once 'Zend/Controller/Action.php';

class DataObjectController extends Zend_Controller_Action {
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            $identity = $auth->getIdentity();
            $zitAdmin = new ZitAdmin($identity);

            $this->view->zitName = $zitAdmin->zit->zitName;

            $dataObjectGroups = DataObjectGroup::getAllDataObjectGroups();

            $this->view->dataObjectGroups = $dataObjectGroups;
        }
        else {
            return $this->_forward('index', 'index', null);
        }
    }

    public function buildnavigationAction() {
	
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
                $this->render('navigation');
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

    public function getdataobjectgrouplistAction() {
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
	                $filterChain->addFilter(new Zend_Filter_Digits());

	                $versionId = $filterChain->filter($_POST['VERSION']);

	                $this->view->dataObjectGroups = DataObjectGroup::getAllDataObjectGroups_version($versionId);
	                $this->render('dataobjectgrouplist');
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

    public function getdataobjectgroupsnozoneAction() {
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
	                $filterChain->addFilter(new Zend_Filter_Digits());

	                $version = $filterChain->filter($_REQUEST['VERSION']);

	                $dataObjectGroups = DataObjectGroup::getAllDataObjectGroups_version($version);
	                if(count($dataObjectGroups) > 0) {
	                    $json = Zend_Json::encode($dataObjectGroups);
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

    public function getdataobjectgroupsAction() {
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
	                $filterChain->addFilter(new Zend_Filter_StripTags())
								->addFilter(new Zend_Filter_Digits());

	                $zoneId = $filterChain->filter($_REQUEST['ZONE']);
	                $zone = new Zone($zoneId);

	                $dataObjectGroups = DataObjectGroup::getAllDataObjectGroups_version($zone->versionId);
	                if(count($dataObjectGroups) > 0) {
	                    $json = Zend_Json::encode($dataObjectGroups);
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

    public function getdataobjectsAction() {
        $auth = Zend_Auth::getInstance();
		$lic = $_REQUEST['lic'];
		if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');			
		} else {
	        if ($auth->hasIdentity()) {

	            if (!$this->getRequest()->isXmlHttpRequest()) {
	                $this->view->msg = 'Not Ajax Request';
	                $this->_forward('index', 'error');
	            }
	            else {
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_StripTags())
								->addFilter(new Zend_Filter_Digits());

	                $id = $filterChain->filter($_REQUEST['ID']);

	                $dataObjects = DataObject::getAllDataObjects($id);
	                if(count($dataObjects) > 0) {
	                    $json = Zend_Json::encode($dataObjects);
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

    public function getfilterabledataobjectsAction() {
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
                $allDataElements = array();
                $allDateObjects  = array();

                $filterChain = new Zend_Filter();
                $filterChain->addFilter(new Zend_Filter_StripTags())
							->addFilter(new Zend_Filter_Digits());

                $agentId   = $filterChain->filter($_REQUEST['AGENT_ID']);
                $zoneId    = $filterChain->filter($_REQUEST['ZONE_ID']);
                $contextId = $filterChain->filter($_REQUEST['CONTEXT_ID']);

                $allAgentPermissions = Permission::getAllSubscribedPermissions($agentId, $zoneId, $contextId);

                foreach($allAgentPermissions as $permission) {
                    DataElement::getAllDataElements($permission, $agentId, $zoneId, $contextId);
                }

                $this->view->permissions = $allAgentPermissions;
                $this->render('filtersuccessjson');
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

    public function savefiltersAction() {
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
	                $filterChain->addFilter(new Zend_Filter_StripTags())
								->addFilter(new Zend_Filter_Digits());
							
					$filterChain2 = new Zend_Filter();
			        $filterChain2->addFilter(new Zend_Filter_StripTags());


	                $agentId                = $filterChain->filter($_REQUEST['agent_id']);
	                $zoneId                 = $filterChain->filter($_REQUEST['zone_id']);
	                $contextId              = $filterChain->filter($_REQUEST['context_id']);
	                $filteredElementsString = $filterChain2->filter($_REQUEST['filtered_elements']);

	                DataElement::ClearFilters($agentId, $zoneId, $contextId);

	                $elementIdHash = explode("|", $filteredElementsString);
	                foreach($elementIdHash as $idHash){
	                    $idArray = explode("_", $idHash);
	                    $element_id = $idArray[0];
	                    if(count($idArray) == 3){
	                        $object_id = $idArray[2];
	                        $parentElementId = $idArray[1];
	                        DataElement::saveFilteredChildElement($object_id, $parentElementId, $element_id, $agentId, $zoneId, $contextId);
	                    }
	                    else{
	                        $object_id = $idArray[1];
	                        if($object_id != null && $element_id != null)
	                        DataElement::saveFilteredElement($object_id, $element_id, $agentId, $zoneId, $contextId);
	                    }

	                }

	                $this->render('ajaxsuccessjson');
	            }
	        }
		}
    }
}
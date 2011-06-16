<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement
*/
require_once 'Zend/Controller/Action.php';

class ZoneController extends Zend_Controller_Action {
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $zitAdmin = new ZitAdmin($identity);
        }
        else {
            return $this->_forward('index', 'index', null);
        }
    }

    public function removeagentAction() {
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
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_Digits());

	                $agentId   = $filterChain->filter($_POST['AGENT_ID']);
	                $zoneId    = $filterChain->filter($_POST['ZONE_ID']);
	                $contextId = $filterChain->filter($_POST['CONTEXT_ID']);

	                Zone::removeAgent($agentId, $zoneId, $contextId);
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

    public function getzonesAction() {
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
	                $zones = Zone::getAllZones();
	                $zit = new Zit(1);
	                if(count($zones) > 0) {
	                    $json = Zend_Json::encode($zones);
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
				else {
					$this->view->msg = 'errors:{reason:"Invalid User"}';
				    $this->_forward('error', 'error');
				}
	        }
		}
    }

    public function buildnavigationAction() {
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
                $zones = Zone::getAllZones();
                $this->view->zones = $zones;
                $this->render('navigation');
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

    public function getzonemessagesAction() {
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

	                $zoneId     = $filterChain2->filter($_POST['ZONE_ID']);
	                $zitLogType = $filterChain->filter($_POST['ZIT_LOG_MESSAGE_TYPE']);

	                $messages = Zone::getLogEntries_zoneId($zoneId, $zitLogType);
					$this->view->messages = $messages;

	                $this->render('zonemessages');
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
	                $empty = null;
					$filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_Digits());
					$zoneId = isset($_POST['ZONE_ID']) ? $filterChain->filter($_POST['ZONE_ID']) : $empty;
					
					if ($zoneId != $empty){
						$messages = Zone::ArchiveLogEntries($zoneId);
		                $this->render('ajaxsuccessjson');
					}
					else {
						$this->view->msg = 'Zone Id Empty';
		                $this->_forward('error', 'error');
					}
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

    public function getzonestatusAction() {
		$auth = Zend_Auth::getInstance();
		$lic = $_REQUEST['lic'];
		if ($lic != $_SESSION['OPENZISKEYHOLE']){
            $this->view->msg = 'Not Ajax Request';
            $this->_forward('error', 'error');			
		}
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

				if ($zoneId == $empty) {
						$this->view->msg = 'Not Ajax Request';
		                $this->_forward('error', 'error');
						break;
				}

                $openZisPushHandler = new OpenZisPushHandler();
                $openZisPushHandler->GetPushStatus($zoneId);
                $agents = Agent::getAllAgentsInZone($zoneId);
                $zone   = new Zone($zoneId);
                $this->view->agents = $agents;
                $this->view->zone   = $zone;
                $this->view->openZisPushHandler = $openZisPushHandler;

                $this->view->admin  = new ZitAdmin($_SESSION['ADMIN_ID']);

                $this->render('zoneinfo');
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

    public function addagentAction() {
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
					Zend_Session::regenerateId();
					
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_Digits());

	                $agentId = $filterChain->filter($_POST['AGENT_ID']);
	                $zoneId  = $filterChain->filter($_POST['ZONE_ID']);

	                $result = Zone::addAgent($agentId, $zoneId);
	                if($result == 1) {
	                    $this->render('ajaxsuccessjson');
	                }
	                else {
	                    switch($result) {
	                        case 0:
	                            $this->view->msg = 'Error Adding Agent';
	                            $this->_forward('error', 'error');
	                            break;
	                        case 2:
	                            $this->view->msg = 'Agent Already Added';
	                            $this->_forward('error', 'error');
	                            break;
	                        case 3:
	                            $this->view->msg = 'Agent Doesn\'t Exist';
	                            $this->_forward('error', 'error');
	                            break;
	                    }
	                }
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

    public function addzoneAction() {
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
					Zend_Session::regenerateId();
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_StripTags());
							
					$filterChain2 = new Zend_Filter();
	                $filterChain2->addFilter(new Zend_Filter_Digits());


	                $desc     			    = $filterChain->filter($_POST['DESCRIPTION']);
	                $sourceId  				= $filterChain->filter($_POST['SOURCE_ID']);
	                $versionId 				= $filterChain2->filter($_POST['VERSION_ID']);
	                $zoneAuthenticationType = $filterChain2->filter($_POST['ZONEAUTHENTICATIONTYPE']);

	                if(!Zone::zoneExists($desc, $sourceId)) {
	                    if(Zone::addZone($desc, $sourceId, $versionId, $zoneAuthenticationType)) {
	                        $this->render('ajaxsuccessjson');
	                    }
	                    else {
	                        $this->view->msg = "Error Creating Zone";
	                        $this->render('error');
	                    }
	                }
	                else {
	                    $this->view->msg = "Zone Description or Source ID already exists";
	                    $this->render('error','error');
	                }
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

    public function updatezoneAction() {
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
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_StripTags());
						
					$filterChain2 = new Zend_Filter();
			        $filterChain2->addFilter(new Zend_Filter_Digits());

	                $desc     = $filterChain->filter($_POST['DESCRIPTION']);
	                $zoneId   = $filterChain2->filter($_POST['ID']);
	                $sourceId = $filterChain->filter($_POST['SOURCE_ID']);
	                $versionId = $filterChain2->filter($_POST['VERSION_ID']);
	                $zoneAuthenticationType = $filterChain2->filter($_POST['ZONEAUTHENTICATIONTYPE']);

	                Zone::updateZone($desc, $sourceId, $zoneId, $versionId, $zoneAuthenticationType);
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

    public function deletezoneAction(){
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
					Zend_Session::regenerateId();
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_Digits());

	                $zoneId   = $filterChain->filter($_POST['zone_id']);

	                $zone = new Zone($zoneId);
	                $zone->delete();

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

    public function putzonetosleepAction() {
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
					Zend_Session::regenerateId();
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_Digits());

	                $zoneId   = $filterChain->filter($_POST['ID']);
	                $sleepVal   = $filterChain->filter($_POST['SLEEP_VAL']);

	                if($sleepVal == 2) {
	                    $sleepValu = 0;
	                }

	                Zone::putZoneToSleep($zoneId, $sleepVal);
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

    public function startpushAction() {
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
					Zend_Session::regenerateId();
					$filterChain = new Zend_Filter();
	       			$filterChain->addFilter(new Zend_Filter_Digits());

					$zoneId_d        = $_POST['ZONE_ID'];
	                $contextId_d	 = $_POST['CONTEXT_ID'];
	                $timeInterval_d  = $_POST['TIME_INTERVAL'];
	                $timeFrame_d     = $_POST['TIME_FRAME'];

	                $zoneId        = $filterChain->filter($zoneId_d);
	                $contextId 	   = $filterChain->filter($contextId_d);
	                $timeInterval  = $filterChain->filter($timeInterval_d);
	                $timeFrame     = $filterChain->filter($timeFrame_d);

					$timeInterval  = $timeInterval + 0;

	                //timeFrame
	                //0 = seconds
	                //1 = minutes
	                //2 = hours

	                if($timeFrame == 1) {
	                    $timeInterval = $timeInterval * 60;
	                }
	                else
	                if($timeFrame == 2) {
	                    $timeInterval = $timeInterval * 60 * 60;
	                }

	                $openZisPushHandler = new OpenZisPushHandler();

	                $openZisPushHandler->Start($timeInterval, $zoneId);
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


	public function getxmlmessageAction() {
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
					$filterChain = new Zend_Filter();
					$filterChain->addFilter(new Zend_Filter_Digits());

					$type_d          = $_POST['type'];
	                $id_d	 		 = $_POST['id'];

	                $Id        		= $filterChain->filter($id_d);
	                $Type	 	   	= $filterChain->filter($type_d);
	
					$xml = Zone::getXMLMessage($Id, $Type);
					$this->view->messages = $xml;

	                $this->render('xmlmessage');
				}
			}
		}
	}

    public function stoppushAction() {
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
					Zend_Session::regenerateId();
	                $filterChain = new Zend_Filter();
	                $filterChain->addFilter(new Zend_Filter_Digits());

	                $zoneId    = $filterChain->filter($_POST['ZONE_ID']);
	                $contextId = $filterChain->filter($_POST['CONTEXT_ID']);

	                $openZisPushHandler = new OpenZisPushHandler();

	                $openZisPushHandler->Stop($zoneId);
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

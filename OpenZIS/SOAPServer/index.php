<?php

ini_set('error_reporting', E_ALL & ~E_STRICT);
ini_set("soap.wsdl_cache_enabled", "0");

set_include_path('../../ZendFramework/library' . PATH_SEPARATOR . get_include_path());
set_include_path('../UTIL/' . PATH_SEPARATOR . get_include_path());

include_once 'Zend/Loader/Autoloader.php';

$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);
$loader->suppressNotFoundWarnings(false);

require_once "Zend/Soap/Server.php";


ini_set("soap.wsdl_cache_enabled", "1"); // disabling WSDL cache

$wsdl = 'Administrate_Provision-1.wsdl';

try {	
	$soap = new Zend_Soap_Server($wsdl,array( "trace" => 1, "exceptions" => 1));
	$soap->setSoapVersion = SOAP_1_1;
} catch (Zend_Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

$soap->handle();

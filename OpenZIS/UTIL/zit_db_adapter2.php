<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

#require_once('Zend/Db.php');

class ZitDBAdapter2 {
    private static $instance;
    var $adapter;
	var $oracle_php_adapter;

    public static function getDBAdapter() {
        if (!isset(self::$instance)) {
			self::$instance = new ZitDbAdapter2();
		}
     
		return self::$instance->adapter;	
    }

    private function __construct() {
        $config   = new Zend_Config_Ini('../config.ini', 'zit_config');
        $type     = $config->database;
        $host     = $config->host;
        $username = $config->username2;
        $password = $config->password2;
        $dbname   = $config->dbname;
        $sleep    = $config->dbsleep;

        $dbAdapter = null;
        if($sleep == null) {
            $_SESSION['SLEEP'] = 0;
        }
        else {
            $_SESSION['SLEEP'] = $sleep;
        }

        switch(DB_TYPE) {
            case 'mysql':
                $pdoParams = array(PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true);
                $options = array(Zend_Db::CASE_FOLDING => Zend_Db::CASE_LOWER);
                
                $params = array(
                   'host'           => $host,
                    'username'       => $username,
                    'password'       => $password,
                    'dbname'         => $dbname,
                    'options'        => $options,
                    'driver_options' => $pdoParams
                );
                
                $this->adapter = Zend_Db::factory('Pdo_Mysql', $params);
				$this->adapter->setFetchMode(Zend_Db::FETCH_OBJ);
                break;

            case 'oci8':
              
              $params = array(
			  'username' => $username, 
              'password' => $password, 
              'dbname'   => $dbname, 
              'charset'  => 'utf8'
              );

			  $this->adapter = Zend_Db::factory('Oracle', $params);
			  $this->adapter->setFetchMode(Zend_Db::FETCH_OBJ);
			  $this->adapter->setLobAsString(true);

              break;
        }
    }
}
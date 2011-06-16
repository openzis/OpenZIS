<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/
//date_default_timezone_set('UTC');

class Utility{

	public static function createMessageId(){
		$token = md5(uniqid());
		$better_token = md5(uniqid(rand(), true));
		$key = strtoupper($better_token);

		return $key;	
	}
	
	public static function createTimestamp(){
		if (isset($_SESSION['ZONE_VERSION'])){
			if($_SESSION['ZONE_VERSION'] == '1.5r1'){
				$timestamp = date('Ymd').'T'.date('h:i:s');
			}
			else{
				$timestamp = date('Y-m-d').'T'.date('h:i:s');
			}
		}
		else{
			$timestamp = date('Y-m-d').'T'.date('h:i:s');
		}
		return $timestamp;
	}
}

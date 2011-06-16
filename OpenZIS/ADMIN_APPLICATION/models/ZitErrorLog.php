<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class ZitErrorLog{

	var $id;
	var $agentName;
	var $zoneName;
	var $timestamp;
	var $location;
	var $shortDescription;
	var $longDescription;
	
	public function ZitErrorLog($id, $agentName, $zoneName, $timestamp, $location, $shortDescription, $longDescription){
		$this->id          = $id;
		$this->agentName   = $agentName;
		$this->zoneName    = $zoneName;
		$this->timestamp   = $timestamp;
		$this->location    = $location;
		$this->shortDescription = $shortDescription;
		$this->longDescription = $longDescription;
	}
}

?>
<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class ProvideObject{

	var $name;
	var $add;
	var $update;
	var $delete_;
	var $timestamp;
	
	public function ProvideObject($objectName, $pubAdd, $pubUpdate, $pubDelete, $timestamp){
		$this->name      = $objectName;
		$this->add       = $pubAdd;
		$this->update    = $pubUpdate;
		$this->delete_   = $pubDelete;
		$this->timestamp = $timestamp;
	}
}
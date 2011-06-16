<?php
/*
this file is part of ZIT (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2008  BillBoard.Net Consulting Team
ZIT is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
ZIT is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class Utility {
    public static  $SUPER_ADMIN = 1;
    public static  $MEMBER = 2;

    public static function intBooleanConvertor($val) {
        if($val == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function nullBooleanConvertor($val) {
        if($val == null) {
            return false;
        }
        else {
            return true;
        }
    }
    
    public static function convertCheckBoxValue($value) {
        if($value == 'on') {
            return 1;
        }
        else if($value == null || $value == 0 || $value == '0' || $value == 'off') {
            return 0;
        }
        else {
            return 1;
        }
    }

    public static function convertBooleanToString($value) {
        if($value) {
            return 'true';
        }
        else {
            return 'false';
        }
    }
}

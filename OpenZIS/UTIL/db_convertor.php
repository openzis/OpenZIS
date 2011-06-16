<?php
/*
this file is part of OpenZIS (Zone Integration Server) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenZIS; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/

class DBConvertor {


    public static function convertCurrentTime() {
//        $type = $_SESSION['DB_TYPE'];
        $expression = null;
        switch(DB_TYPE) {
            case 'postgres':
                $expression = 'CURRENT_TIMESTAMP';
                break;

            case 'mysql':
                $expression = 'NOW()';
                break;

            case 'oci8':
                $expression = 'SYSDATE';
                break;

            case 'mssql':
                $expression = 'GetDate()';
                break;

            default:
                $expression = 'NOW()';
                break;
        }

        return $expression;
    }

    public static function convertCase($var) {
//        $type = $_SESSION['DB_TYPE'];
        switch(DB_TYPE) {
            case 'mysql':
                $var = strtolower($var);
                break;

            case 'oracle':
                $var = strtoupper($var);
                break;

			case 'oci8':
                $var = strtoupper($var);
                break;
	            
			case 'postgres':
	             $var = strtolower($var);
	             break;

            case 'mssql':
                $var = strtolower($var);
                break;

            default:
                $var = strtolower($var);
                break;
        }
        return $var;
    }

    public static function convertDateFormat($col, $format, $name) {
//        $type = $_SESSION['DB_TYPE'];
        $expression = null;

        $vars = explode("-", $format);
        $month = $vars[0];
        $day   = $vars[1];
        $year  = $vars[2];
        $time  = $vars[3];

        switch(DB_TYPE) {
            case 'postgres':
                $loc_format = 'yyyy-';
                if($month == 'm') {
                    $loc_format .= 'mm-';
                }
                else
                if($month == 'M') {
                    $loc_format .= 'Mon';
                }
                $loc_format .= 'dd-';
                if($time != null) {
                    $loc_format .= ' [hh24:mi:ss]';
                }

                $expression = " to_char(".$col.", '".$loc_format."') as ".$name;
                break;

            case 'oracle':
                $loc_format ='YYYY-';
                if($month == 'm') {
                    $loc_format .= 'mm-';
                }
                else
                if($month == 'M') {
                    $loc_format .= 'MM-';
                }
                $loc_format .= 'dd';
                if($time != null) {
                    $loc_format .= ' [HH24:MI:SS]';
                }

                $expression = " TO_CHAR(".$col.", '".$loc_format."') as ".$name;
                break;

	            case 'oci8':
	                $loc_format ='YYYY-';
	                if($month == 'm') {
	                    $loc_format .= 'mm-';
	                }
	                else
	                if($month == 'M') {
	                    $loc_format .= 'MM-';
	                }
	                $loc_format .= 'dd';
	                if($time != null) {
	                    $loc_format .= ' [HH24:MI:SS]';
	                }
					$expression = " TO_CHAR(".$col.", '".$loc_format."') ";
					if ($name != null){
						$expression = $expression ." as ".$name;
					}
					break;

            case 'mysql':
                $loc_format = '%Y-';
                if($month == 'm') {
                    $loc_format .= '%c-';
                }
                else
                if($month == 'M') {
                    $loc_format .= '%b-';
                }
                $loc_format .= '%d';
                if($time != null) {
                    $loc_format .= ' [%T]';
                }

                $expression = " DATE_FORMAT(".$col.", '".$loc_format."') ";
				if ($name != null){
					$expression = $expression ." as ".$name;
				}
				break;
                break;

            case 'mssql':
                $loc_format = '%Y-';
                if($month == 'm-') {
                    $loc_format .= '%c';
                }
                else
                if($month == 'M-') {
                    $loc_format .= '%b';
                }
                $loc_format .= '%d';
                if($time != null) {
                    $loc_format .= ' [%T]';
                }

                $expression = " CONVERT(CHAR(19),".$col.") as ".$name;
                break;


            default:
                $loc_format = '%Y-';
                if($month == 'm') {
                    $loc_format .= '%c-';
                }
                else
                if($month == 'M') {
                    $loc_format .= '%b-';
                }
                $loc_format .= '%d';
                if($time != null) {
                    $loc_format .= ' [%T]';
                }

                $expression = " DATE_FORMAT(".$col.", '".$loc_format."') as ".$name;
                break;
        }

        return $expression;
    }
}
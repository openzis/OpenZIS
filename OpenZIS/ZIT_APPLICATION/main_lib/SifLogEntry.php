<?php
/*
this file is part of OpenZIS (Zone Integration Terminal) http://www.openszis.org/ Copyright (C) 2011  BillBoard.Net Consulting Team
OpenZIS is free software; you can redistribute it and/or modify it under the terms of the GNU General Public Licence as published by the Free Software Foundation; either version 3.
OpenZIS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with ZIT; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
Refer to documents/COPYING for the full licence agreement 
*/
class SifLogEntry {

    public static function CreateSifLogEvents($header, $category, $code, $desc) {
        $db = Zend_Registry::get('my_db');

        $xml = XmlHelper::buildSifLogEvent(Utility::createMessageId(),
                Utility::createTimestamp(),
                $header,
                $category,
                $code,
                $desc
        );

        $xml = str_replace('xmlns="http://www.sifinfo.org/uk/infrastructure/2.x"','',$xml);
        $xml = str_replace('xmlns="http://www.sifinfo.org/infrastructure/2.x"','',$xml);
        $xml = str_replace('xmlns="http://www.sifinfo.org/infrastructure/1.x"','',$xml);

        $dataObject = new DataObject('sif_logentry');
        $query = "select
				  	agent_registered.agent_id,
					agent_registered.agent_mode_id,
                    agent_registered.context_id,
                    agent_registered.zone_id
				  from 
				  	".DBConvertor::convertCase('agent_registered')." 
				  where 
				  	agent_registered.context_id = ".$_SESSION["CONTEXT_ID"]." 
					and agent_registered.zone_id = ".$_SESSION["ZONE_ID"]."
					and agent_registered.unregister_timestamp is null";
        $result = $db->fetchAll($query);
        foreach($result as $row) {
	
			switch(DB_TYPE) {
				case 'mysql':
					$agentId        = intval($row->agent_id);
					$agentModeId  	= intval($row->agent_mode_id);
					$zoneId			= intval($row->zone_id);
					$contextId		= intval($row->context_id);
				break;
				case 'oci8':
					$agentId        = intval($row->AGENT_ID);
					$agentModeId  	= intval($row->AGENT_MODE_ID);
					$zoneId			= intval($row->ZONE_ID);
					$contextId		= intval($row->CONTEXT_ID);
				break;
			}
			
            $data = array(
                    'event_timestamp' => new Zend_Db_Expr(DBConvertor::convertCurrentTime()),
                    'agent_id_sender' => intval(0),
                    'agent_id_rec'    => $agentId,
                    'event_data'      => $xml,
                    'object_id'       => $dataObject->objectId,
                    'action_id'       => intval(1),
                    'zone_id'         => $zoneId,
                    'context_id'      => $contextId,
                    'agent_mode_id'   => $agentModeId
            );

/*
*            $db->insert('event', $data);
*/        }
    }
}

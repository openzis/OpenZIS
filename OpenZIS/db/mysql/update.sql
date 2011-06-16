
ALTER TABLE `request` ADD COLUMN `CANCEL_TIMESTAMP` DATETIME DEFAULT NULL AFTER `ZONE_ID`;

ALTER TABLE `zit_admin` ADD COLUMN `ATTEMPTS` INT DEFAULT 0 AFTER `LAST_LOGIN`;

ALTER TABLE `zit_admin` ADD COLUMN `LOCKOUT` TIMESTAMP DEFAULT  '2000-01-01 00:00:00' AFTER `ATTEMPTS`;


DROP TABLE IF EXISTS `zit_log_archive`;

CREATE TABLE  `zit_log_archive` (
  `LOG_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CREATE_TIMESTAMP` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `REC_XML` meiumtext NOT NULL,
  `SENT_XML` mediumtext NOT NULL,
  `ZONE_ID` int(10) unsigned NOT NULL DEFAULT '0',
  `AGENT_ID` int(10) unsigned DEFAULT NULL,
  `SIF_MESSAGE_TYPE_ID` int(10) unsigned NOT NULL,
  `LOG_MESSAGE_TYPE_ID` int(10) unsigned NOT NULL,
  `ARCHIVED` tinyint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`LOG_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8;


CREATE OR REPLACE VIEW `authenticate` AS
Select * from zit_admin 
where active = 1;

create or replace view `data_object_group_count` as
 select version_id, count(*) counter from `data_object_group`
  group by version_id;

ALTER TABLE `agent_zone_context` ADD INDEX ZoneOnly(`ZONE_ID`);

ALTER TABLE `agent_registered` ADD INDEX byAgent(`AGENT_ID`, `ZONE_ID`, `CONTEXT_ID`);


CREATE OR REPLACE VIEW provision_dataobject_agent_vw AS
SELECT  ap.provision_id, ap.zone_id, ap.agent_id, a.source_id, ap.context_id, do.version_id, do.group_id, do.object_id, ap.publish_add, ap.publish_delete, ap.publish_change
  FROM  agent_provisions  ap,
        agent             a,
        data_object       do
 WHERE ap.agent_id = a.agent_id
   AND ap.object_type_id = do.object_id;

CREATE OR REPLACE VIEW provision_dataobject_vw AS
SELECT ap.provision_id, ap.zone_id, ap.agent_id, ap.context_id, do.version_id, do.group_id, do.object_id, do.object_name, ap.publish_add, ap.publish_delete, ap.publish_change
  FROM  agent_provisions  ap,
        data_object       do
 WHERE ap.object_type_id = do.object_id;


CREATE OR REPLACE VIEW agentpermisiondataobject_vw AS
SELECT ap.*, do.object_name, do.group_id, do.version_id
  FROM agent_permissions ap,
       data_object do
 WHERE ap.object_id = do.object_id;


create or replace view agentresponderdataobjectagent_vw as
select ar.*, a.source_id, do.version_id, do.object_name
  from agent_responder ar,
       agent a,
       data_object do
 where ar.agent_id = a.agent_id
    and ar.object_type_id = do.object_id;

create or replace view requestagent_vw as
select request_id, version, max_buffer_size, source_id, request_msg_id, zone_id, context_id
  from  request r,
        agent a
  where a.agent_id = r.agent_id_requester;


create or replace view nummessage_vw as
select zone_id, context_id, agent_id_rec agent_id from event where (status_id = 1 or status_id = 2)
union all
select zone_id, context_id, agent_id_responder agent_id from request where (status_id = 1 or status_id = 2)
union all
select zone_id, context_id, agent_id_requester agent_id from response where (status_id = 1 or status_id = 2); 


ALTER TABLE `zit_log` ADD INDEX zit_log_archived(`archived`);

ALTER TABLE `agent` DROP COLUMN `CERT_COMMON` , 
			      ADD COLUMN `cert_common_name` LONGTEXT NULL  AFTER `ACTIVE` ,
			      ADD COLUMN `cert_common_dn` TEXT NULL DEFAULT ''   AFTER `cert_common_name` ;

ALTER TABLE `agent` CHANGE COLUMN `cert_common_dn` `cert_common_dn` TEXT NULL DEFAULT ''  ;


ALTER TABLE `response` ADD INDEX response_msd_id USING BTREE(`MSG_ID`);

ALTER TABLE `event` ADD INDEX event_msg_id USING BTREE(`MSG_ID`);




DROP TABLE IF EXISTS `messagequeue`;

CREATE  TABLE `messagequeue` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `msg_id` VARCHAR(50) NOT NULL ,
  `ack_msg_id` VARCHAR(50) CHARACTER SET 'utf8' NULL ,
  `msg_type` INT NOT NULL DEFAULT 0 ,
  `zone_id` INT NOT NULL ,
  `context_id` INT NOT NULL ,
  `status_id` INT NOT NULL DEFAULT 3 ,
  `data` MEDIUMTEXT NOT NULL ,
  `agt_id_in` INT NOT NULL DEFAULT 0 ,
  `agt_id_out` INT NOT NULL DEFAULT 0 ,
  `maxbuffersize` INT NOT NULL DEFAULT 64000 ,
  `version` VARCHAR(6) NOT NULL DEFAULT 0 ,
  `next_packet_num` INT NOT NULL DEFAULT 0 ,
  `agt_mode_id` INT NOT NULL DEFAULT 0 ,
  `object_id` INT NULL ,
  `action_id` INT NULL ,
  `insert_timestamp` DATETIME NULL DEFAULT '0000-00-00 00:00:00' ,
  `cancel_timestamp` DATETIME NULL ,
  `ref_msg_id` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


ALTER TABLE `agent` 
ADD INDEX `SOURCEID_IDX` USING BTREE (`SOURCE_ID` ASC) ;

ALTER TABLE `agent_registered` 
ADD INDEX `AGENT_TIMESTAMP` (`AGENT_ID` ASC, `UNREGISTER_TIMESTAMP` ASC, `ZONE_ID` ASC, `CONTEXT_ID` ASC) ;

ALTER TABLE `agent_provisions` 
ADD INDEX `fnAlreadyProviding` (`AGENT_ID` ASC, `OBJECT_TYPE_ID` ASC, `ZONE_ID` ASC, `CONTEXT_ID` ASC) ;

ALTER TABLE `data_object` 
ADD INDEX `fnObjectExists` (`VERSION_ID` ASC, `OBJECT_NAME` ASC) ;

ALTER TABLE `zones` 
ADD INDEX `fnCheckZoneExist` (`ZONE_DESC` ASC) ;


ALTER TABLE `zit_log` CHANGE COLUMN `REC_XML` `REC_XML` MEDIUMTEXT NOT NULL  , CHANGE COLUMN `SENT_XML` `SENT_XML` MEDIUMTEXT NOT NULL  ;




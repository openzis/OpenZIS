insert into data_object_group values (2121, 'Infrastructure', 212);
insert into data_object values( 'SIF_LogEntry', 711, 2121, 212);

DROP TABLE IF EXISTS `zit_log_archive `;

CREATE TABLE  `zit_log_archive` (
  `LOG_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CREATE_TIMESTAMP` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `REC_XML` mediumtext NOT NULL,
  `SENT_XML` mediumtext NOT NULL,
  `ZONE_ID` int(10) unsigned NOT NULL DEFAULT '0',
  `AGENT_ID` int(10) unsigned DEFAULT NULL,
  `SIF_MESSAGE_TYPE_ID` int(10) unsigned NOT NULL,
  `LOG_MESSAGE_TYPE_ID` int(10) unsigned NOT NULL,
  `ARCHIVED` tinyint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`LOG_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8;
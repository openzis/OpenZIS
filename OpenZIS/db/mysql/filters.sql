
DROP TABLE IF EXISTS `agent_filters`;



ALTER TABLE `agent_permissions` ADD COLUMN `xslt` TEXT NULL  AFTER `ZONE_ID` ;
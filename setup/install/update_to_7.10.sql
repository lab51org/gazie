UPDATE `gaz_config` SET `cvalue` = '109' WHERE `id` =2;
ALTER TABLE `gaz_module` CHANGE COLUMN `link` `link` VARCHAR(100) NOT NULL DEFAULT '' AFTER `name`;
ALTER TABLE `gaz_menu_module` CHANGE COLUMN `link` `link` VARCHAR(100) NOT NULL DEFAULT '' AFTER `id_module`;
ALTER TABLE `gaz_menu_script` CHANGE COLUMN `link` `link` VARCHAR(100) NOT NULL DEFAULT '' AFTER `id_menu`;
INSERT INTO `gaz_module` (`name`, `link`, `icon`, `class`, `access`, `weight`) VALUES ('wiki', 'docume_wiki.php', 'wiki.png', '', '0', 15);
DELETE FROM `gaz_menu_script` WHERE  `link` LIKE 'select_an%';
DELETE FROM `gaz_menu_script` WHERE  `link` LIKE 'select_esportazione%';
DELETE FROM `gaz_menu_module` WHERE  `link` LIKE '%report_statis.php%';
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
INSERT INTO `gaz_admin_module` (`adminid`, `company_id`, `moduleid`, `access`) SELECT user_name,(CONVERT('XXX',UNSIGNED INTEGER)),(SELECT MAX(id) FROM `gaz_module`),3 FROM `gaz_admin` WHERE 1;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)
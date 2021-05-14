UPDATE `gaz_config` SET `cvalue` = '138' WHERE `id` =2;
UPDATE `gaz_country` SET `istat_area`='12' WHERE  `iso`='GB';
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_movcon.php'), 'acquire_bank_accbal.php', '', '', 15, '', 5  FROM `gaz_menu_script`;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXfiles` ADD INDEX (`item_ref`);
ALTER TABLE `gaz_XXXfiles`	CHANGE COLUMN `item_ref` `item_ref` VARCHAR(100) NOT NULL COMMENT 'a secondo dell\'utilizzo che se ne fà può contenere il codice articolo, il filename o altre referenze' AFTER `id_ref`;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione )
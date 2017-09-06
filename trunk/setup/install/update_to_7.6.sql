UPDATE `gaz_config` SET `cvalue` = '103' WHERE `id` =2;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT id FROM `gaz_menu_module` WHERE `link`='select_liqiva.php'), 'comunicazione_dati_fatture.php', '', '', 10, '', 15  FROM `gaz_menu_script`;
UPDATE `gaz_menu_module` SET `link`='comunicazioni_doc.php' WHERE `link`='select_liqiva.php';
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT id FROM `gaz_menu_module` WHERE `link`='select_regiva.php'), 'select_liqiva.php', '', '', 11, '', 15  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT id FROM `gaz_menu_module` WHERE `link`='comunicazioni_doc.php'), 'report_comunicazioni_dati_fatture.php', '', '', 12, '', 20  FROM `gaz_menu_script`;
ALTER TABLE `gaz_anagra` CHANGE COLUMN `legrap` `legrap_pf_nome` VARCHAR(60) NOT NULL DEFAULT '' AFTER `sedleg`;
ALTER TABLE `gaz_anagra` ADD COLUMN `legrap_pf_cognome` VARCHAR(60) NOT NULL DEFAULT '' AFTER `legrap_pf_nome`;
ALTER TABLE `gaz_aziend` CHANGE COLUMN `legrap` `legrap_pf_nome` VARCHAR(60) NOT NULL DEFAULT '' AFTER `sedleg`;
ALTER TABLE `gaz_aziend` ADD COLUMN `legrap_pf_cognome` VARCHAR(60) NOT NULL DEFAULT '' AFTER `legrap_pf_nome`;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
CREATE TABLE IF NOT EXISTS `gaz_XXXcomunicazioni_dati_fatture` ( `id` int(9) NOT NULL AUTO_INCREMENT, `anno` int(4) DEFAULT NULL, `periodicita` varchar(1) NOT NULL DEFAULT 'M', `trimestre_semestre` int(2) DEFAULT NULL, `nome_file_DTE` varchar(100) DEFAULT '', `nome_file_DTR` varchar(100) DEFAULT '', `nome_file_ZIP` varchar(100) DEFAULT '', `IdFile` varchar(18) DEFAULT '', `nome_file_ANN` varchar(100) DEFAULT '', PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='tabella contenente i dati delle Comunicazioni dati fatture (spesometro) secondo le specifiche tecniche dell''Agenzia delle Entrate';
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)
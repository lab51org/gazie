UPDATE `gaz_config` SET `cvalue` = '129' WHERE `id` =2; 
SET @id_mod := 0;
SELECT @id_mod := `id` FROM  `gaz_module` WHERE `name` = 'camp' LIMIT 1;
UPDATE `gaz_menu_module` SET `link` = 'camp_report_artico.php' WHERE `id_module` = @id_mod AND `link`= 'report_artico.php';
UPDATE `gaz_menu_module` SET `link` = 'camp_report_catmer.php' WHERE `id_module` = @id_mod AND `link`= 'report_catmer.php';
UPDATE `gaz_menu_module` SET `link` = 'camp_report_movmag.php' WHERE `id_module` = @id_mod AND `link`= 'report_movmag.php';
UPDATE `gaz_menu_module` SET `link` = 'camp_report_caumag.php' WHERE `id_module` = @id_mod AND `link`= 'report_caumag.php';
SET @id_menu_mod := 0;
SELECT  @id__menu_mod := `id` FROM  `gaz_menu_module` WHERE `link` = 'camp_report_artico.php' LIMIT 1;
UPDATE `gaz_menu_script` SET `link` = 'camp_admin_artico.php?Insert' WHERE `id_menu` = @id_menu_mod AND `link`= 'admin_artico.php?Insert';
UPDATE `gaz_menu_script` SET `link` = 'camp_inventory_stock.php' WHERE `id_menu` = @id_menu_mod AND `link`= 'inventory_stock.php';
UPDATE `gaz_menu_script` SET `link` = 'camp_stampa_invmag.php' WHERE `id_menu` = @id_menu_mod AND `link`= 'stampa_invmag.php';
UPDATE `gaz_menu_script` SET `link` = 'camp_browse_document.php' WHERE `id_menu` = @id_menu_mod AND `link`= 'browse_document.php';
SELECT  @id__menu_mod := `id` FROM  `gaz_menu_module` WHERE `link` = 'camp_report_catmer.php' LIMIT 1;
UPDATE `gaz_menu_script` SET `link` = 'camp_admin_catmer.php?Insert' WHERE `id_menu` = @id_menu_mod AND `link`= 'admin_catmer.php?Insert';
SELECT  @id__menu_mod := `id` FROM  `gaz_menu_module` WHERE `link` = 'camp_report_movmag.php' LIMIT 1;
UPDATE `gaz_menu_script` SET `link` = 'camp_admin_movmag.php?Insert' WHERE `id_menu` = @id_menu_mod AND `link`= 'admin_movmag.php?Insert';
UPDATE `gaz_menu_script` SET `link` = 'camp_select_schart.php' WHERE `id_menu` = @id_menu_mod AND `link`= 'select_schart.php';
UPDATE `gaz_menu_script` SET `link` = 'camp_select_giomag.php' WHERE `id_menu` = @id_menu_mod AND `link`= 'select_giomag.php';
SELECT  @id__menu_mod := `id` FROM  `gaz_menu_module` WHERE `link` = 'camp_report_caumag.php' LIMIT 1;
UPDATE `gaz_menu_script` SET `link` = 'camp_admin_caumag.php?Insert' WHERE `id_menu` = @id_menu_mod AND `link`= 'admin_caumag.php?Insert';
ALTER TABLE `gaz_config` CHANGE COLUMN `cvalue` `cvalue` VARCHAR(2000) NULL DEFAULT '' AFTER `variable`;
INSERT INTO `gaz_config` (`description`, `variable`, `cvalue`, `weight`, `show`) VALUES	( 'Referenze per movimenti di magazzino per link al documento che lo ha generato ', 'report_movmag_ref_doc', '{\r\n"ADT":"acquis",\r\n"AFA":"acquis",\r\n"AFC":"acquis",\r\n"DDR":"acquis",\r\n"ADT":"acquis",\r\n"AFT":"acquis",\r\n"DDL":"acquis", \r\n"RDL":"acquis",\r\n"DDR":"acquis",\r\n"VCO":"vendit", \r\n"VRI":"vendit", \r\n"DDT":"vendit", \r\n"FAD":"vendit", \r\n"FAI":"vendit", \r\n"FAA":"vendit", \r\n"FAQ":"vendit", \r\n"FAP":"vendit", \r\n"FNC":"vendit", \r\n"FND":"vendit", \r\n"DDV":"vendit", \r\n"RDV":"vendit", \r\n"DDY":"vendit", \r\n"DDS":"vendit",\r\n"VPR":"vendit", \r\n"VOR":"vendit", \r\n"VOW":"vendit", \r\n"VOG":"vendit", \r\n"CMR":"vendit", \r\n"CAM":"camp",\r\n"PRO":"orderman",\r\n"MAG":"magazz"\r\n}', 0, 0);
ALTER TABLE `gaz_camp_fitofarmaci` ADD COLUMN `CONTENUTO_PER_100G` VARCHAR(30) NOT NULL COMMENT 'Contenuto per 100 grammi di prodotto' AFTER `SOSTANZE_ATTIVE`;
ALTER TABLE `gaz_camp_fitofarmaci` ADD COLUMN `ATTIVITA` VARCHAR(50) NOT NULL COMMENT 'Attivita d\'uso' AFTER `CONTENUTO_PER_100G`;
ALTER TABLE `gaz_camp_fitofarmaci` ADD COLUMN `IP` VARCHAR(2) NOT NULL COMMENT 'Importazioni parallele' AFTER `ATTIVITA`;
ALTER TABLE `gaz_camp_fitofarmaci` ADD COLUMN `PPO` VARCHAR(2) NOT NULL COMMENT 'Prodotti per piante ornamentali' AFTER `IP`;
ALTER TABLE `gaz_aziend` CHANGE COLUMN `sync_ecom_mod` `gazSynchro` VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Indico i moduli dove trovare i file contenenti la classe "gazSynchro" con le funzioni  necessarie per eseguire aggiornamenti e/o sincronizzazioni; \r\nè indispensabile che la classe stessa contenga le funzioni api_token e get_sync_status.  \r\nAd ogni cambiamento di movimento di magazzino, cliente, articolo, categoria merceologica, aliquota IVA, presenza di nuovi ordini e/o clienti inseriti dal sito\r\nsi provvederà a fare delle chiamate alle funzioni contenute in essa.' COLLATE 'utf8_general_ci' AFTER `web_url`;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
UPDATE `gaz_XXXcaucon_rows` SET `n_order` = '1' WHERE `caucon_cod` = 'AFT' AND `clfoco_ref` = '212000000';
UPDATE `gaz_XXXcaucon_rows` SET `n_order` = '2' WHERE `caucon_cod` = 'AFT' AND `clfoco_ref` = '330000004';
UPDATE `gaz_XXXcaucon_rows` SET `n_order` = '3' WHERE `caucon_cod` = 'AFT' AND `clfoco_ref` = '106000001';
ALTER TABLE `gaz_XXXmovmag`	ADD COLUMN `id_assets` INT(9) NOT NULL COMMENT 'Ref. alla tabella gaz_001assets (riferito a bene ammortizzabile)' AFTER `id_orderman`;
ALTER TABLE `gaz_XXXartico`	ADD COLUMN `maintenance_period` INT(3) NOT NULL DEFAULT 0 COMMENT 'Periodicità in giorni della eventuale manutenzione del bene/servizio' AFTER `classif_amb`;
ALTER TABLE `gaz_XXXcaumag`	ADD COLUMN `type_cau` TINYINT(1) NOT NULL COMMENT '0=solo modulo magazzino, 1=solo quaderno di campagna, 9=tutti' AFTER `descri`;
INSERT IGNORE INTO `gaz_XXXcaumag` (`codice`, `descri`, `type_cau`, `clifor`, `insdoc`, `operat`) VALUES (91, 'SCARICO PER MANUTENZIONE INTERNA',5, 0, 1, -1);
UPDATE `gaz_XXXcaumag` SET `type_cau` = 9 WHERE 1;
ALTER TABLE `gaz_XXXassets`	ADD COLUMN `codice_campi` INT(3) NOT NULL DEFAULT '0' COMMENT 'Riferimento alla tabella gaz_NNNcampi (luogo di produzione), ovvero il luogo ove è installata la macchina' AFTER `install_date`;
ALTER TABLE `gaz_XXXassets`	ADD COLUMN `hostpath` VARCHAR(200) NOT NULL COMMENT 'Percorso di rete della macchina (IoT e/o Industry 4.0)' AFTER `codice_campi`;
ALTER TABLE `gaz_XXXassets`	ADD COLUMN `username` VARCHAR(50) NOT NULL COMMENT 'Nome utente per accesso a dispositivo (IoT e/o Industry 4.0)' AFTER `hostpath`;
ALTER TABLE `gaz_XXXassets`	ADD COLUMN `password` VARCHAR(50) NOT NULL COMMENT 'Password per accesso a dispositivo (IoT e/o Industry 4.0)' AFTER `username`;
ALTER TABLE `gaz_XXXassets`	ADD COLUMN `linkmode` VARCHAR(15) NOT NULL COMMENT 'Modalità-protocollo di comunicazione (IoT e/o Industry 4.0)' AFTER `password`;
ALTER TABLE `gaz_XXXartico` ADD COLUMN `id_reg` INT(6) NOT NULL COMMENT 'Riferimento al numero registrazione della tabella camp_fitofarmaci' AFTER `SIAN`;
ALTER TABLE `gaz_XXXmovmag`	CHANGE COLUMN `desdoc` `desdoc` VARCHAR(100) NOT NULL AFTER `tipdoc`;
INSERT INTO `gaz_XXXcompany_config` (`description`, `var`, `val`) VALUES ('Periodicità in minuti per i controlli di presenza alerts (su menù) ', 'menu_alerts_check', '15');
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)

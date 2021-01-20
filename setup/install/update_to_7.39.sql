UPDATE `gaz_config` SET `cvalue` = '134' WHERE `id` =2;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_scontr.php'), 'report_ecr.php', '', '', 56, '', 9  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_scontr.php'), 'admin_ecr.php', '', '', 57, '', 12  FROM `gaz_menu_script`;
UPDATE `gaz_module` SET `link`='docume_finann.php' WHERE  `link`='docume_finean.php';
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXmovmag`	CHANGE COLUMN `quanti` `quanti` DECIMAL(14,5) NULL DEFAULT NULL AFTER `id_assets`;
ALTER TABLE `gaz_XXXrigdoc`	CHANGE COLUMN `quanti` `quanti` DECIMAL(14,5) NULL DEFAULT NULL AFTER `pezzi`;
ALTER TABLE `gaz_XXXrigbro`	CHANGE COLUMN `quanti` `quanti` DECIMAL(14,5) NULL DEFAULT NULL AFTER `pezzi`;
ALTER TABLE `gaz_XXXcash_register` CHANGE COLUMN `adminid` `adminid` VARCHAR(20) NOT NULL COMMENT 'riferimento all\'ultimo utente della tabella gaz_admin che ha utilizzato il dispositivo' AFTER `seziva`;
ALTER TABLE `gaz_XXXcash_register` CHANGE COLUMN `driver` `driver` VARCHAR(32) NOT NULL COMMENT 'il nome del file .php contenuto nella dir \library\cash_register\ da utilizzare come driver di comunicazione con il registratore telematico' AFTER `serial_port`;
ALTER TABLE `gaz_XXXcash_register` ADD COLUMN `path_data` VARCHAR(1000) NOT NULL COMMENT 'dati da passare al driver di cui sopra per raggiungere il dispositivo (path) ed eventuali dati caratteristici della macchina specifica. Contenuto e/o il formato dipende da come è scritto il driver stesso' AFTER `driver`, ADD COLUMN `lotteria_scontrini` VARCHAR(32) NOT NULL COMMENT 'stringa inviata dal driver al RT per i righi tipo "5 - Lotteria scontrini" ' AFTER `descri`, ADD COLUMN `enabled_users` VARCHAR(1000) NOT NULL COMMENT 'contiene i riferimenti agli utenti abilitati all\'utilizzo del dispositivo separati da un punto e virgola' AFTER `path_data`;
CREATE TABLE `gaz_XXXcash_register_reparto` ( `id_cash_register_reparto` INT(2) NOT NULL AUTO_INCREMENT, `cash_register_id_cash` INT(2) NOT NULL DEFAULT '0', `aliiva_codice` INT(3) NOT NULL DEFAULT '0',`reparto` VARCHAR(8) NOT NULL DEFAULT '' , `descrizione` VARCHAR(50) NOT NULL DEFAULT '' , PRIMARY KEY (`id_cash_register_reparto`), INDEX (`cash_register_id_cash`) ) COMMENT='Tabella contenente le associazioni tra id_cash della tabella gaz_NNNcash_register e codice della tabella gaz_NNNaliiva al fine di passare il valore del reparto IVA ad ogni rigo dello scontrino telematico';
CREATE TABLE `gaz_XXXcash_register_tender` ( `id_cash_register_tender` INT(2) NOT NULL AUTO_INCREMENT, `cash_register_id_cash` INT(2) NOT NULL DEFAULT '0', `pagame_codice` INT(2) NOT NULL DEFAULT '0',`tender` VARCHAR(8) NOT NULL DEFAULT '' , `descrizione` VARCHAR(50) NOT NULL DEFAULT '' , PRIMARY KEY (`id_cash_register_tender`), INDEX (`cash_register_id_cash`) ) COMMENT='Tabella contenente le associazioni tra id_cash della tabella gaz_NNNcash_register e codice della tabella gaz_NNNpagame al fine di valorizzare il tender specifico del registratore telematico in base alla modalità di pagamento';
ALTER TABLE `gaz_XXXaliiva`	CHANGE COLUMN `codice` `codice` INT(3) NOT NULL DEFAULT '0' FIRST;
ALTER TABLE `gaz_XXXcash_register` DROP COLUMN `enterpriseid`;
ALTER TABLE `gaz_XXXcash_register` CHANGE COLUMN `id_cash` `id_cash` TINYINT(2) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id_cash`);
ALTER TABLE `gaz_XXXrigdoc`	DROP PRIMARY KEY, ADD PRIMARY KEY (`id_rig`) USING BTREE;
ALTER TABLE `gaz_XXXrigbro`	DROP PRIMARY KEY, ADD PRIMARY KEY (`id_rig`) USING BTREE, ADD INDEX (`id_tes`);
ALTER TABLE `gaz_XXXrigmoi`	DROP PRIMARY KEY, ADD PRIMARY KEY (`id_rig`) USING BTREE, ADD INDEX (`id_tes`);
ALTER TABLE `gaz_XXXrigmoc`	DROP PRIMARY KEY, ADD PRIMARY KEY (`id_rig`) USING BTREE, ADD INDEX (`id_tes`);
ALTER TABLE `gaz_XXXprovvigioni` ADD INDEX `id_agente` (`id_agente`);
ALTER TABLE `gaz_XXXmovmag`	DROP PRIMARY KEY, ADD PRIMARY KEY (`id_mov`) USING BTREE;
ALTER TABLE `gaz_XXXcontract_row` DROP PRIMARY KEY,	ADD PRIMARY KEY (`id_row`) USING BTREE;
ALTER TABLE `gaz_XXXsconti_articoli` DROP PRIMARY KEY;
ALTER TABLE `gaz_XXXsconti_raggruppamenti` DROP PRIMARY KEY;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione )
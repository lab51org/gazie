UPDATE `gaz_config` SET `cvalue` = '153' WHERE `id` =2;
ALTER TABLE `gaz_breadcrumb` ADD COLUMN `codice_aziend` INT(3) NULL DEFAULT 0 COMMENT 'Riferimento alla colonna codice della tabella gaz_aziend' AFTER `adminid`,	ADD INDEX `codice_aziend` (`codice_aziend`),	ADD INDEX `adminid` (`adminid`);
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione )

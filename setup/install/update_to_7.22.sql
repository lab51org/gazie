UPDATE `gaz_config` SET `cvalue` = '118' WHERE `id` =2;
ALTER TABLE `gaz_aziend` CHANGE COLUMN `vat_susp` `vat_susp` TINYINT(1) NOT NULL COMMENT 'Se a 1 si indica che si sta operando in  regime di “Iva per cassa ex art. 32 bis del D.p.r. 83/2012”' AFTER `ra_cassa`;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
INSERT INTO `gaz_XXXcompany_config` (`description`, `var`, `val`) VALUES ('Stampa Documenti di Trasporto in modalità 2xA5 (affiancati)', 'ddt_A5', '0');
INSERT INTO `gaz_XXXcompany_config` (`description`, `var`, `val`) VALUES ('Stampa Ricevute su moduli prenumerati es.\'buffetti\' per Buffetti art.8205L2000', 'received_template', '');
ALTER TABLE `gaz_XXXtesmov` ADD COLUMN `datliq` DATE DEFAULT '2004-01-27' COMMENT 'Data di riferimento del periodo della liquidazione periodica dell\'IVA, non può precedere la data del documento (datdoc)' AFTER `datreg`;
UPDATE `gaz_XXXtesmov` SET `datliq` = `datreg` WHERE 1;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)

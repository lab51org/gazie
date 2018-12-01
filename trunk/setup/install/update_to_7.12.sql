UPDATE `gaz_config` SET `cvalue` = '111' WHERE `id` =2;
ALTER TABLE `gaz_staff_work_type` ADD INDEX `id_work_type` (`id_work_type`);
ALTER TABLE `gaz_staff_work_type` ADD INDEX `descri` (`descri`);
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_doctra.php'), 'admin_docven.php?Insert&tipdoc=CMR', '', '', 50, '', 10  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_doctra.php'), 'report_doccmr.php', '', '', 51, '', 15  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_doctra.php'), 'emissi_fatdif.php?tipodocumento=CMR', '', '', 53, '', 20  FROM `gaz_menu_script`;
DELETE FROM `gaz_menu_module` WHERE  `link`='stampa_schcar.php';
UPDATE `gaz_menu_module` SET `weight`=`weight`*3 WHERE  `id_module`= (SELECT MIN(id) FROM `gaz_module` WHERE `name`='acquis');
UPDATE `gaz_menu_module` SET `link`='report_broacq.php?flt_tipo=APR' WHERE  `link`='report_broacq.php';;
INSERT INTO `gaz_menu_module` SELECT  MAX(id)+1,(SELECT MIN(id) FROM `gaz_module` WHERE `name`='acquis'), 'report_broacq.php?flt_tipo=AOR','', '', 10, '', 4 FROM `gaz_menu_module`;
UPDATE `gaz_menu_script` SET `id_menu`=(SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_broacq.php?flt_tipo=AOR') WHERE  `link`='admin_broacq.php?tipdoc=AOR';
UPDATE `gaz_menu_script` SET `id_menu`=(SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_broacq.php?flt_tipo=AOR') WHERE  `link`='prop_ordine.php';
UPDATE `gaz_menu_script` SET `weight`='10' WHERE  `link`='admin_docacq.php?Insert&tipdoc=AFC';
ALTER TABLE `gaz_aziend` ADD COLUMN `fae_tipo_cassa` VARCHAR(4) NOT NULL COMMENT 'eventualmente con uno dei valori dell\'elemento <TipoCassa> della fattura elettronica TC01,TC02,ecc' AFTER `amm_min`;
ALTER TABLE `gaz_aziend` ADD COLUMN `ra_cassa` TINYINT(1) NULL COMMENT 'scelta se applicare o meno la ritenuta d\'acconto sulla cassa previdenziale' AFTER `fae_tipo_cassa`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_ddtacq.php'), 'admin_docacq.php?seziva=1&tipdoc=RDL&Insert', '', '', 20, '', 5  FROM `gaz_menu_script`;
ALTER TABLE `gaz_aziend` CHANGE COLUMN `boleff` `boleff` INT(9) NOT NULL DEFAULT '0' COMMENT 'Conto dei ricavi per bolli su vendite (il conto di costo su acquisti è la colonna taxstamp_account)' AFTER `impvar`,	ADD COLUMN `taxstamp_account` INT(9) NOT NULL DEFAULT '0' COMMENT 'Conto di costo su acquisti per bolli ( quello di ricavo su vendite è sulla colonna boleff)' AFTER `taxstamp_vat`;
ALTER TABLE `gaz_aziend` CHANGE COLUMN `c_ritenute` `c_ritenute` INT(9) NOT NULL COMMENT 'conto ritenute subite' AFTER `cocamb`, ADD COLUMN `c_ritenute_autonomi` INT(9) NOT NULL COMMENT 'conto ritenute autonomi da versare ' AFTER `c_ritenute`;
ALTER TABLE `gaz_aziend` ADD COLUMN `order_type` CHAR(3) NOT NULL DEFAULT '' COMMENT 'Tipo produzione: agricola, industriale, professionale, artigianale, ricerca e sviluppo; viene usato nel modulo orderman (produzioni)' AFTER `datnas`;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXtesmov`	ADD COLUMN `notess` TEXT NULL DEFAULT '' COMMENT 'Note che NON vengono stampate sui registri contabili' AFTER `descri`;
ALTER TABLE `gaz_XXXrigbro`	ADD COLUMN `id_orderman` INT(9) NULL COMMENT 'Per avere riferimenti uno a molti, e viceversa, con le produzioni (orderman)' AFTER `id_mag`;
ALTER TABLE `gaz_XXXrigbro`	ADD INDEX `id_orderman` (`id_orderman`);
ALTER TABLE `gaz_XXXrigbro`	CHANGE COLUMN `status` `status` VARCHAR(100) NOT NULL DEFAULT '' AFTER `id_orderman`;
ALTER TABLE `gaz_XXXtesbro`	ADD COLUMN `email` VARCHAR(50) NULL DEFAULT '' COMMENT 'Utilizzato per inviare i documenti ad un indirizzo diverso da quello in anagrafica' AFTER `template`;
ALTER TABLE `gaz_XXXtesdoc`	ADD COLUMN `email` VARCHAR(50) NULL DEFAULT '' COMMENT 'Utilizzato per inviare i documenti ad un indirizzo diverso da quello in anagrafica' AFTER `template`;
ALTER TABLE `gaz_XXXrigdoc`	CHANGE COLUMN `provvigione` `provvigione` DECIMAL(4,2) NOT NULL COMMENT 'Provvigione in caso di agente oppure percentuale cassa previdenziale in caso di tipo rigo = 4' AFTER `codric`;
ALTER TABLE `gaz_XXXartico`	ADD COLUMN `larghezza` DECIMAL(10,3) NULL DEFAULT NULL AFTER `unimis`, ADD COLUMN `lunghezza` DECIMAL(10,3) NULL DEFAULT NULL AFTER `larghezza`, ADD COLUMN `spessore` DECIMAL(10,3) NULL DEFAULT NULL AFTER `lunghezza`;
ALTER TABLE `gaz_XXXartico`	ADD COLUMN `quality` VARCHAR(50) NOT NULL COMMENT 'per indicare la qualità del materiale , normativa, ecc' AFTER `codice_fornitore`;
ALTER TABLE `gaz_XXXrigbro`	ADD COLUMN `larghezza` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'espresso in mm' AFTER `unimis`, ADD COLUMN `lunghezza` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'espresso in mm' AFTER `larghezza`, ADD COLUMN `spessore` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'espresso in mm' AFTER `lunghezza`, ADD COLUMN `peso_specifico` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'non necessariamente in kg/l, potrà moltiplicare anche con i ml, mq, pezzi' AFTER `spessore`,	ADD COLUMN `pezzi` INT(9) NULL DEFAULT NULL AFTER `peso_specifico`, ADD COLUMN `quality` VARCHAR(50) NOT NULL COMMENT 'per indicare la qualità del materiale richiesto, normativa, ecc' AFTER `descri`;
ALTER TABLE `gaz_XXXletter`	ADD COLUMN `email` VARCHAR(50) NULL DEFAULT '' COMMENT 'Utilizzato per inviare i documenti ad un indirizzo diverso da quello in anagrafica' AFTER `clfoco`;
ALTER TABLE `gaz_XXXrigdoc`	ADD COLUMN `codice_fornitore` VARCHAR(50) NOT NULL DEFAULT '' AFTER `codart`;
INSERT INTO `gaz_XXXcaumag` (`codice`, `descri`, `clifor`, `insdoc`, `operat`) VALUES (82, 'CARICO DA PRODUZIONE', 0, 0, 1),(81, 'SCARICO PER PRODUZIONE', 0, 0, -1);
ALTER TABLE `gaz_XXXtesbro`	CHANGE COLUMN `id_pro` `id_parent_doc` INT(9) NOT NULL COMMENT 'riferimento ad id_tes del documento genitore  (es. ordine riferito a preventivo genitore)' AFTER `id_agente`;
ALTER TABLE `gaz_XXXtesdoc`	CHANGE COLUMN `id_pro` `id_parent_doc` INT(9) NOT NULL COMMENT 'riferimento ad id_tes del documento genitore  (es. ordine riferito a preventivo genitore)' AFTER `id_agente`;

-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)
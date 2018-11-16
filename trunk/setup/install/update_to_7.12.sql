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
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXtesmov`	ADD COLUMN `notess` TEXT NULL DEFAULT '' COMMENT 'Note che NON vengono stampate sui registri contabili' AFTER `descri`;
ALTER TABLE `gaz_XXXrigbro`	ADD COLUMN `id_orderman` INT(9) NULL COMMENT 'Per avere riferimenti uno a molti, e viceversa, con le produzioni (orderman)' AFTER `id_mag`;
ALTER TABLE `gaz_XXXrigbro`	ADD INDEX `id_orderman` (`id_orderman`);
ALTER TABLE `gaz_XXXrigbro`	CHANGE COLUMN `status` `status` VARCHAR(100) NOT NULL DEFAULT '' AFTER `id_orderman`;
ALTER TABLE `gaz_XXXtesbro`	ADD COLUMN `email` VARCHAR(50) NULL DEFAULT '' COMMENT 'Utilizzato per inviare i documenti ad un indirizzo diverso da quello in anagrafica' AFTER `template`;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)
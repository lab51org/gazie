UPDATE `gaz_config` SET `cvalue` = '93' WHERE `id` =2;
ALTER TABLE `gaz_menu_usage` ADD `color` VARCHAR(6) NOT NULL DEFAULT '5cb85c' AFTER `click`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, 2, 'select_docforlist.php', '', '', 36, '', 9 FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, 7, 'print_anagrafe.php?clifor=C', '', '', 37, '', 6  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, 14, 'print_anagrafe.php?clifor=F', '', '', 15, '', 3  FROM `gaz_menu_script`;
ALTER TABLE `gaz_aziend` ADD COLUMN `payroll_tax` DECIMAL(3,1) NOT NULL AFTER `ritenuta`, ADD COLUMN `c_payroll_tax` INT(9) NOT NULL DEFAULT '0' AFTER `c_ritenute`;
UPDATE `gaz_menu_script` SET `link` = 'select_suppliers_status.php' WHERE  ( `link`='select_partner_status.php' AND `translate_key` < 30 );
UPDATE `gaz_menu_script` SET `link`='select_schedule_debt.php' WHERE  ( `link`='select_schedule.php' AND `translate_key` < 30 );
UPDATE `gaz_aziend` SET `c_payroll_tax`=215000012 WHERE 1;
UPDATE `gaz_aziend` SET `payroll_tax`=4.0 WHERE 1;
UPDATE `gaz_aziend` SET `ritenuta`=20.0 WHERE 1;
TRUNCATE `gaz_menu_usage`;
INSERT INTO `gaz_config` (`description`, `variable`, `cvalue`) VALUES ('Ghostscript shell exec', 'gs_shell_exec', 'C:\\"Program Files"\\gs\\gs9.18\\bin\\gswin64.exe');
INSERT INTO `gaz_config` (`description`, `variable`, `cvalue`) VALUES ('Ghostscript dpi resolution', 'gs_resolution', '300');
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, 9, 'select_an_acq_clienti.php', '', '', 38, '', 1  FROM `gaz_menu_script`;INSERT INTO `gaz_menu_module` SELECT MAX(id)+1, 13, 'report_period.php', '', '', 2, '', 2  FROM `gaz_menu_module`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MAX(id) FROM `gaz_menu_module`), 'admin_period.php?Insert', '', '', 4, '', 1  FROM `gaz_menu_script`;-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXeffett` ADD COLUMN `cigcup` VARCHAR(40) NOT NULL AFTER `id_con`;
ALTER TABLE `gaz_XXXartico`	ADD COLUMN `good_or_service` INT(1) NULL DEFAULT NULL AFTER `descri`, ADD COLUMN `depli_public` TINYINT(1) NOT NULL NULL DEFAULT '0' AFTER `web_public`, ADD COLUMN `retention_tax` TINYINT NOT NULL DEFAULT '0' AFTER `aliiva`, ADD COLUMN `payroll_tax` TINYINT NOT NULL DEFAULT '0' AFTER `last_cost`;
UPDATE `gaz_XXXartico` SET `depli_public`=1 WHERE 1;
ALTER TABLE `gaz_XXXbody_text`	ADD INDEX `table_name_ref` (`table_name_ref`);
ALTER TABLE `gaz_XXXassist` ADD `soluzione` text COLLATE 'utf8_general_ci' NULL AFTER `descrizione`;
ALTER TABLE `gaz_XXXassist` ADD `ora_inizio` varchar(5) NULL AFTER `soluzione`;
ALTER TABLE `gaz_XXXassist` ADD `ora_fine` varchar(5) NULL AFTER `ora_inizio`;
INSERT INTO `gaz_XXXcompany_config` (`description`, `var`, `val`) VALUES ('Descrizione contributo cassa previdenziale', 'payroll_tax_descri', 'Contributo integrativo cassa previdenziale');
ALTER TABLE `gaz_XXXassist` ADD `note` text NULL AFTER `stato`;
ALTER TABLE `gaz_XXXassist` ADD `tipo` varchar(3) NULL AFTER `id`;
UPDATE gaz_001assist set tipo='ASS' where 1=1;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)

UPDATE `gaz_config` SET `cvalue` = '141' WHERE `id` =2;
INSERT INTO `gaz_menu_module` SELECT MAX(id)+1, '5', 'report_wharehouse.php', '', '', '10', '', '1'  FROM `gaz_menu_module`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_wharehouse.php'), 'admin_wharehouse.php?Insert', '', '', 15, '', 10  FROM `gaz_menu_script`;
ALTER TABLE `gaz_staff_work_type` ADD COLUMN `descri_ext` VARCHAR(255) NULL DEFAULT NULL AFTER `descri`, ADD COLUMN `inps_ref` VARCHAR(3) NULL DEFAULT NULL AFTER `descri_ext`,	ADD COLUMN `causal` VARCHAR(3) NULL DEFAULT NULL AFTER `inps_ref`;
UPDATE `gaz_staff_work_type` SET `descri_ext` = `descri` WHERE 1;
INSERT INTO gaz_staff_work_type (id_work_type, hour_year_limit, hour_month_limit, hour_week_limit, hour_day_limit, increase, inps_ref, causal, descri, descri_ext) SELECT 9, 0, 0, 0, 0, 0, inps_ref, causal, descri, descri_ext FROM `gaz_staff_absence_type` WHERE 1;
DROP TABLE `gaz_staff_absence_type`;
ALTER TABLE `gaz_staff_work_type` CHANGE COLUMN `id_work_type` `id_work_type` INT(3) NOT NULL COMMENT '1=straordinario;  2,8=altri; 9=assenze;' AFTER `id_work`;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione )
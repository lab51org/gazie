UPDATE `gaz_config` SET `cvalue` = '79' WHERE `id` =2;
CREATE TABLE IF NOT EXISTS `gaz_currencies` ( `id` INT NOT NULL AUTO_INCREMENT, `curr_name` VARCHAR(50) DEFAULT NULL, `symbol` VARCHAR(3) NOT NULL, `html_symbol` VARCHAR(10) NOT NULL, `decimal_place` CHAR(4) DEFAULT NULL, `decimal_symbol` CHAR(4) DEFAULT NULL, `thousands_symbol` CHAR(4) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
INSERT INTO `gaz_currencies` (`id`, `curr_name`, `symbol`, `html_symbol`,`decimal_place`,`decimal_symbol`,`thousands_symbol`) VALUES (1, 'euro', '€ ', '&#8364; ',2,',','.');
CREATE TABLE IF NOT EXISTS `gaz_currency_history` ( `id_currency` INT NOT NULL, `change_value` decimal(12,5) NOT NULL, `date_reference` DATE NOT NULL, `id_currency_obj` INT NOT NULL ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `gaz_anagra` ADD `id_currency` INT NOT NULL AFTER `country`, ADD `id_language` INT NOT NULL AFTER `id_currency`; 
ALTER TABLE `gaz_aziend` ADD `mas_staff` INT NOT NULL AFTER `masban`, ADD `id_currency` INT NOT NULL AFTER `country`, ADD `id_language` INT NOT NULL AFTER `id_currency`; 
UPDATE `gaz_aziend` SET `id_currency` = '1', `id_language` = '1';
UPDATE `gaz_anagra` SET `id_currency` = '1', `id_language` = '1';
CREATE TABLE IF NOT EXISTS `gaz_languages` ( `lang_id` INT NOT NULL AUTO_INCREMENT, `lang_code` CHAR(7) NOT NULL, `title` VARCHAR(50) NOT NULL, `title_native` VARCHAR(50) NOT NULL, `sef` VARCHAR(50) NOT NULL, `image` VARCHAR(50) NOT NULL, `description` VARCHAR(512) NOT NULL, `metakey` TEXT NOT NULL, `metadesc` TEXT NOT NULL, `published` INT NOT NULL, `ordering` INT NOT NULL, PRIMARY KEY (`lang_id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
INSERT INTO `gaz_languages` (`lang_id`, `lang_code`, `title`, `title_native`, `sef`, `image`, `description`, `metakey`, `metadesc`, `published`, `ordering`) VALUES (1, 'it-IT', 'Italiano (IT)', 'Italian (IT)', 'it', 'it', '', '', '', 1, 0),(2, 'en-GB', 'English (UK)', 'English (UK)', 'en', 'en', '', '', '', 1, 1),(3, 'es-CL', 'Español (CL)', 'Spanish (CL)', 'es', 'es', '', '', '', 1, 2);
DELETE FROM `gaz_menu_module` WHERE `link` = 'select_bilcee.php';
UPDATE `gaz_menu_module` SET `link` = 'docume_bilanc.php' WHERE `link` = 'select_bilanc.php';
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, '35', 'select_bilanc.php', '', '', '3', '', '3'  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, '35', 'select_bilcee.php', '', '', '4', '', '4'  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, '35', 'extcon.php', '', '', '5', '', '5'  FROM `gaz_menu_script`;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXpaymov` ADD `id_tesdoc_ref` INT NOT NULL AFTER `id` ;
ALTER TABLE `gaz_XXXpaymov` CHANGE `id_paymovcon` `id_rigmoc_pay` INT NOT NULL ;
ALTER TABLE `gaz_XXXpaymov` CHANGE `id_docmovcon` `id_rigmoc_doc` INT NOT NULL ;
ALTER TABLE `gaz_XXXbody_text` CHANGE `iso3_country` `lang_id` INT NOT NULL;
UPDATE `gaz_XXXbody_text` SET `lang_id` = 1;
ALTER TABLE `gaz_XXXcontract_row` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id_row` , `id_contract` );
CREATE TABLE IF NOT EXISTS `gaz_XXXstaff` ( `id_staff` INT NOT NULL AUTO_INCREMENT, `id_clfoco` INT NOT NULL,  `id_contract` INT NOT NULL, `Login_admin` VARCHAR(20) DEFAULT NULL, `job_title` VARCHAR(100) DEFAULT NULL, `employment_status` INT DEFAULT NULL, `joined_date` DATE NOT NULL DEFAULT '0000-00-00', `job_grade` VARCHAR(50) DEFAULT NULL, `title` VARCHAR(100) DEFAULT NULL, `institution` VARCHAR(100) DEFAULT NULL, `graduation_year` DATE NOT NULL DEFAULT '0000-00-00', `start_date` DATE NOT NULL DEFAULT '0000-00-00', `end_date` DATE NOT NULL DEFAULT '0000-00-00', PRIMARY KEY (`id_staff`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `gaz_XXXstaff_skills` ( `id` INT NOT NULL AUTO_INCREMENT, `id_staff` INT NOT NULL, `variable_name` VARCHAR(50) DEFAULT NULL, `skill_value` VARCHAR(100) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER TABLE `gaz_XXXtesbro` ADD `print_total` INT NOT NULL AFTER `template`, ADD `delivery_time` INT NOT NULL AFTER `print_total`,ADD `day_of_validity` INT NOT NULL AFTER `delivery_time`;
CREATE TABLE IF NOT EXISTS `gaz_XXXextcon` ( `year` INT NOT NULL, `cos_serv_ind` decimal(11,2) DEFAULT '0.00', `cos_serv_amm` decimal(11,2) DEFAULT '0.00', `cos_serv_com` decimal(11,2) DEFAULT '0.00', `cos_godb_ind` decimal(11,2) DEFAULT '0.00', `cos_godb_amm` decimal(11,2) DEFAULT '0.00', `cos_godb_com` decimal(11,2) DEFAULT '0.00', `cos_pers_ind` decimal(11,2) DEFAULT '0.00', `cos_pers_amm` decimal(11,2) DEFAULT '0.00', `cos_pers_com` decimal(11,2) DEFAULT '0.00', `cos_amms_ind` decimal(11,2) DEFAULT '0.00', `cos_amms_amm` decimal(11,2) DEFAULT '0.00', `cos_amms_com` decimal(11,2) DEFAULT '0.00', `cos_accr_ind` decimal(11,2) DEFAULT '0.00', `cos_accr_amm` decimal(11,2) DEFAULT '0.00', `cos_accr_com` decimal(11,2) DEFAULT '0.00', `cos_acca_ind` decimal(11,2) DEFAULT '0.00', `cos_acca_amm` decimal(11,2) DEFAULT '0.00', `cos_acca_com` decimal(11,2) DEFAULT '0.00', `cos_divg_ind` decimal(11,2) DEFAULT '0.00', `cos_divg_amm` decimal(11,2) DEFAULT '0.00', `cos_divg_com` decimal(11,2) DEFAULT '0.00', `deb_breve`    decimal(11,2) DEFAULT '0.00', `deb_medio`    decimal(11,2) DEFAULT '0.00', `deb_lungo`    decimal(11,2) DEFAULT '0.00', `num_dip`      INT        DEFAULT '0', PRIMARY KEY  (`year`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- STOP_WHILE( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query seguenti su tutte le aziende dell'installazione)
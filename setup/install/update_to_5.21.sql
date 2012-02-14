UPDATE `gaz_config` SET `cvalue` = '79' WHERE `id` =2;
CREATE TABLE IF NOT EXISTS `gaz_currencies` ( `id` int(3) NOT NULL AUTO_INCREMENT, `curr_name` varchar(50) DEFAULT NULL, `symbol` varchar(3) NOT NULL, `html_symbol` varchar(10) NOT NULL, `decimal_place` char(4) DEFAULT NULL, `decimal_symbol` char(4) DEFAULT NULL, `thousands_symbol` char(4) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
INSERT INTO `gaz_currencies` (`id`, `curr_name`, `symbol`, `html_symbol`,`decimal_place`,`decimal_symbol`,`thousands_symbol`) VALUES (1, 'euro', '€ ', '&#8364; ',2,',','.');
CREATE TABLE IF NOT EXISTS `gaz_currency_history` ( `id_currency` int(9) NOT NULL, `change_value` decimal(12,5) NOT NULL, `date_reference` date NOT NULL, `id_currency_obj` int(9) NOT NULL ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `gaz_anagra` ADD `id_currency` INT( 3 ) NOT NULL AFTER `country`, ADD `id_language` INT( 3 ) NOT NULL AFTER `id_currency`; 
ALTER TABLE `gaz_aziend` ADD `mas_staff` INT( 3 ) NOT NULL AFTER `masban`, ADD `id_currency` INT( 3 ) NOT NULL AFTER `country`, ADD `id_language` INT( 3 ) NOT NULL AFTER `id_currency`; 
UPDATE `gaz_aziend` SET `id_currency` = '1', `id_language` = '1';
UPDATE `gaz_anagra` SET `id_currency` = '1', `id_language` = '1';
CREATE TABLE IF NOT EXISTS `gaz_languages` ( `lang_id` int(3) unsigned NOT NULL AUTO_INCREMENT, `lang_code` char(7) NOT NULL, `title` varchar(50) NOT NULL, `title_native` varchar(50) NOT NULL, `sef` varchar(50) NOT NULL, `image` varchar(50) NOT NULL, `description` varchar(512) NOT NULL, `metakey` text NOT NULL, `metadesc` text NOT NULL, `published` int(11) NOT NULL DEFAULT '0', `ordering` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`lang_id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
INSERT INTO `gaz_languages` (`lang_id`, `lang_code`, `title`, `title_native`, `sef`, `image`, `description`, `metakey`, `metadesc`, `published`, `ordering`) VALUES (1, 'it-IT', 'Italiano (IT)', 'Italian (IT)', 'it', 'it', '', '', '', 1, 0),(2, 'en-GB', 'English (UK)', 'English (UK)', 'en', 'en', '', '', '', 1, 1),(3, 'es-CL', 'Español (CL)', 'Spanish (CL)', 'es', 'es', '', '', '', 1, 2);

-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXpaymov` ADD `id_tesdoc_ref` INT ( 9 ) NOT NULL AFTER `id` ;
ALTER TABLE `gaz_XXXpaymov` CHANGE `id_paymovcon` `id_rigmoc_pay` INT( 9 ) NOT NULL ;
ALTER TABLE `gaz_XXXpaymov` CHANGE `id_docmovcon` `id_rigmoc_doc` INT( 9 ) NOT NULL ;
ALTER TABLE `gaz_XXXbody_text` CHANGE `iso3_country` `lang_id` INT( 3 ) NOT NULL;
UPDATE `gaz_XXXbody_text` SET `lang_id` = 1;
ALTER TABLE `gaz_XXXcontract_row` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id_row` , `id_contract` );
CREATE TABLE IF NOT EXISTS `gaz_XXXstaff` ( `id_staff` int(9) NOT NULL AUTO_INCREMENT, `id_clfoco` int(9) NOT NULL,  `id_contract` int(9) NOT NULL, `Login_admin` varchar(20) DEFAULT NULL, `job_title` varchar(100) DEFAULT NULL, `employment_status` int(2) DEFAULT NULL, `joined_date` date NOT NULL DEFAULT '0000-00-00', `job_grade` varchar(50) DEFAULT NULL, `title` varchar(100) DEFAULT NULL, `institution` varchar(100) DEFAULT NULL, `graduation_year` date NOT NULL DEFAULT '0000-00-00', `start_date` date NOT NULL DEFAULT '0000-00-00', `end_date` date NOT NULL DEFAULT '0000-00-00', PRIMARY KEY (`id_staff`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `gaz_XXXstaff_skills` ( `id` int(9) NOT NULL AUTO_INCREMENT, `id_staff` int(9) NOT NULL, `variable_name` varchar(50) DEFAULT NULL, `skill_value` varchar(100) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- STOP_WHILE( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query seguenti su tutte le aziende dell'installazione)
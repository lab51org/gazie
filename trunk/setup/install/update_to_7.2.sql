UPDATE `gaz_config` SET `cvalue` = '96' WHERE `id` =2;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT id FROM `gaz_menu_module` WHERE `link`='report_client.php'), 'select_sconti_articoli.php', '', '', 41, '', 7 FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT id FROM `gaz_menu_module` WHERE `link`='report_client.php'), 'select_sconti_raggruppamenti.php', '', '', 42, '', 8 FROM `gaz_menu_script`;
UPDATE `gaz_menu_script` SET `weight`=5 WHERE `link`='select_esportazione_articoli_venduti.php';
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT id FROM `gaz_menu_module` WHERE `link`='../magazz/report_statis.php'), 'select_analisi_fatturato_clienti.php', '', '', 43, '', 3  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT id FROM `gaz_menu_module` WHERE `link`='../magazz/report_statis.php'), 'select_analisi_fatturato_cliente_fornitore.php', '', '', 44, '', 4  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_module` SELECT MAX(id)+1, (SELECT id FROM `gaz_module` WHERE `name`='suppor'), 'report_install.php', '', '', 3, '', 3  FROM `gaz_menu_module`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MAX(id) FROM `gaz_menu_module`), 'admin_install.php?Insert', '', '', 3, '', 1  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT id FROM `gaz_menu_module` WHERE `link`='select_liqiva.php'), 'select_spesometro_analitico.php', '', '', 8, '', 2  FROM `gaz_menu_script`;
INSERT INTO `gaz_config` (`id`, `description`, `variable`, `cvalue`, `weight`, `show`, `last_modified`) VALUES (NULL, 'Menu/header personalizzabile', 'menu', 'g7', '0', '0', '2016-11-12 19:00:00');
CREATE TABLE IF NOT EXISTS `gaz_classroom` (  `id` int(6) NOT NULL AUTO_INCREMENT, `classe` varchar(16) NOT NULL, `sezione` varchar(16) NOT NULL, `anno_scolastico` int(4) NOT NULL, `teacher` varchar(50) NOT NULL, `location` varchar(100) NOT NULL, `title_note` varchar(200) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `gaz_students` (
 `student_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing student_id of each student, unique index',
 `student_classroom_id` int(6) NOT NULL COMMENT 'classroom_id of student',
 `student_firstname` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'student''s first name',
 `student_lastname` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'student''s last name',
 `student_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'student''s name, unique',
 `student_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'student''s password in salted and hashed format',
 `student_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'student''s email, unique',
 `student_telephone` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'student''s telephone number',
 `student_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'student''s activation status',
 `student_activation_hash` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'student''s email verification hash string',
 `student_password_reset_hash` char(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'student''s password reset code',
 `student_password_reset_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the password reset request',
 `student_rememberme_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'student''s remember-me cookie token',
 `student_failed_logins` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'student''s failed login attemps',
 `student_last_failed_login` int(10) DEFAULT NULL COMMENT 'unix timestamp of last failed login attempt',
 `student_registration_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `student_registration_ip` varchar(39) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',
 PRIMARY KEY (`student_id`),
 UNIQUE KEY `student_name` (`student_name`),
 UNIQUE KEY `student_email` (`student_email`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='student data';
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
CREATE TABLE `gaz_XXXsconti_articoli` (`clfoco` int(9),`codart` varchar(15),`sconto` decimal(6,3),`prezzo_netto` decimal(14,5), primary key(`clfoco`,`codart`));
CREATE TABLE `gaz_XXXsconti_raggruppamenti` (`clfoco` int(9),`ragstat` char(15),`sconto` decimal(6,3), primary key(`clfoco`,`ragstat`));
ALTER TABLE `gaz_XXXassist` ADD `ripetizione` varchar(10) COLLATE 'utf8_general_ci' NOT NULL AFTER `prezzo`;
ALTER TABLE `gaz_XXXassist` ADD `codart` varchar(15) NOT NULL AFTER `ore`;
ALTER TABLE `gaz_XXXassist` change `ripetizione` `ripetizione` int NULL DEFAULT '1' AFTER `prezzo`, ADD `ogni` int NULL DEFAULT '365' AFTER `ripetizione`;
ALTER TABLE `gaz_XXXassist` ADD `codeart` varchar(10) COLLATE 'utf8_general_ci' NULL AFTER `prezzo`;
ALTER TABLE `gaz_XXXassist` CHANGE `ogni` `ogni` varchar(10) NULL DEFAULT 'Anni' AFTER `ripetizione`;
CREATE TABLE `gaz_XXXinstal` ( `id` int NOT NULL, `clfoco` int NOT NULL, `descrizione` varchar(255) NOT NULL, `seriale` varchar(255) NOT NULL, `datainst` date NOT NULL, `note` text NOT NULL ) COLLATE 'utf8_general_ci';
ALTER TABLE `gaz_XXXinstal` ADD `codice` int(11) NOT NULL AFTER `id`;
ALTER TABLE `gaz_XXXinstal` ADD `oggetto` varchar(100) NOT NULL AFTER `clfoco`;
ALTER TABLE `gaz_XXXassist` ADD `idinstallazione` int(11) NOT NULL AFTER `id`;
ALTER TABLE `gaz_XXXinstal` CHANGE `id` `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
UPDATE `gaz_XXXcompany_config` SET `description`='GAzie school or order mail address' WHERE  `var`='order_mail';
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)
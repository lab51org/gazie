UPDATE `gaz_config` SET `cvalue` = '86' WHERE `id` =2;
ALTER TABLE `gaz_aziend` ADD `virtual_stamp_auth_prot` VARCHAR( 14 ) NOT NULL AFTER `round_bol` ,
ADD `virtual_stamp_auth_date` DATE NOT NULL AFTER `virtual_stamp_auth_prot`, ADD `causale_pagam_770` VARCHAR( 1 ) NOT NULL AFTER `virtual_stamp_auth_date`; 
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
CREATE TABLE IF NOT EXISTS `gaz_XXXfae_flux` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `filename_ori` varchar(30) NOT NULL,  `id_tes_ref` int(9) NOT NULL DEFAULT '0',  `exec_date` datetime NOT NULL,  `filename_son` varchar(30) NOT NULL,  `id_SDI` int(20) NOT NULL DEFAULT '0',  `data` blob NOT NULL,  `status` varchar(10) NOT NULL,  `descri` varchar(255) NOT NULL DEFAULT '',  PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
INSERT INTO `gaz_XXXcompany_config` (`id`, `description`, `var`, `val`) VALUES 
(15, 'Casella di posta', 'cemail', ''), 
(16, 'Password', 'cpassword', ''), 
(17, 'FIltro casella di posta', 'cfiltro', ''), 
(18, 'Configurazione pop imap', 'cpopimap', '');
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query seguenti su tutte le aziende dell'installazione)
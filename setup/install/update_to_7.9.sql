UPDATE `gaz_config` SET `cvalue` = '107' WHERE `id` =2;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
CREATE TABLE `gaz_XXXcaucon_rows` (`caucon_cod` CHAR(3) NOT NULL DEFAULT '', `clfoco_ref` INT(9) NOT NULL DEFAULT '0', `type_imp` CHAR(1) NOT NULL DEFAULT '',	`dare_avere` CHAR(1) NOT NULL DEFAULT '', `n_order` INT(3) NOT NULL DEFAULT '0') ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `gaz_XXXcaucon_rows` (`caucon_cod`,clfoco_ref,type_imp,dare_avere,n_order) SELECT `codice`,`contr1`,`tipim1`,`daav_1`,1 FROM `gaz_001caucon`;
INSERT INTO `gaz_XXXcaucon_rows` (`caucon_cod`,clfoco_ref,type_imp,dare_avere,n_order) SELECT `codice`,`contr2`,`tipim2`,`daav_2`,2 FROM `gaz_001caucon`;
INSERT INTO `gaz_XXXcaucon_rows` (`caucon_cod`,clfoco_ref,type_imp,dare_avere,n_order) SELECT `codice`,`contr3`,`tipim3`,`daav_3`,3 FROM `gaz_001caucon`;
INSERT INTO `gaz_XXXcaucon_rows` (`caucon_cod`,clfoco_ref,type_imp,dare_avere,n_order) SELECT `codice`,`contr4`,`tipim4`,`daav_4`,4 FROM `gaz_001caucon`;
INSERT INTO `gaz_XXXcaucon_rows` (`caucon_cod`,clfoco_ref,type_imp,dare_avere,n_order) SELECT `codice`,`contr5`,`tipim5`,`daav_5`,5 FROM `gaz_001caucon`;
INSERT INTO `gaz_XXXcaucon_rows` (`caucon_cod`,clfoco_ref,type_imp,dare_avere,n_order) SELECT `codice`,`contr6`,`tipim6`,`daav_6`,6 FROM `gaz_001caucon`;
ALTER TABLE `gaz_XXXcaucon` DROP COLUMN `contr1`, DROP COLUMN `tipim1`, DROP COLUMN `daav_1`, DROP COLUMN `contr2`, DROP COLUMN `tipim2`, DROP COLUMN `daav_2`, DROP COLUMN `contr3`, DROP COLUMN `tipim3`, DROP COLUMN `daav_3`, DROP COLUMN `contr4`, DROP COLUMN `tipim4`, DROP COLUMN `daav_4`, DROP COLUMN `contr5`, DROP COLUMN `tipim5`, DROP COLUMN `daav_5`, DROP COLUMN `contr6`, DROP COLUMN `tipim6`, DROP COLUMN `daav_6`;
DELETE FROM `gaz_XXXcaucon_rows` WHERE `clfoco_ref` = 0;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)
UPDATE `gaz_config` SET `cvalue` = '108' WHERE `id` =2;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_anagra.php'), 'report_anagra.php', '', '', 10, '', 5  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_anagra.php'), 'report_municipalities.php', '', '', 11, '', 10  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='report_anagra.php'), 'report_provinces.php', '', '', 12, '', 15  FROM `gaz_menu_script`;
INSERT INTO `gaz_menu_script` SELECT MAX(id)+1, (SELECT MIN(id) FROM `gaz_menu_module` WHERE `link`='staff_report.php'), 'employee_timesheet.php', '', '', 2, '', 5  FROM `gaz_menu_script`;
INSERT INTO `gaz_provinces` (`id`, `id_region`, `name`, `stat_code`, `abbreviation`, `web_url`, `email`) VALUES (108, 3, 'Monza - Brianza', '108', 'MB', '', ''),(109, 11, 'Fermo', '109', 'FM', '', ''),(110, 16, 'Barletta-Andria-Trani', '110', 'BT', '', ''),(111, 20, 'Sud Sardegna', '111', 'SU', '', '');
CREATE TABLE `gaz_temp` ( `id_province` varchar(255) DEFAULT NULL, `codice_alfanumerico` varchar(255) DEFAULT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `gaz_temp` (`id_province`, `codice_alfanumerico`) VALUES ('108', 'A087'),('108', 'A096'),('108', 'A159'),('108', 'A376'),('108', 'A668'),('108', 'A759'),('108', 'A802'),('108', 'A818'),('108', 'A849'),('108', 'B105'),('108', 'B187'),('108', 'B212'),('108', 'B272'),('108', 'B501'),('108', 'B729'),('108', 'B798'),('108', 'C395'),('108', 'C512'),('108', 'C566'),('108', 'C820'),('108', 'C952'),('108', 'D038'),('108', 'D286'),('108', 'E063'),('108', 'E504'),('108', 'E550'),('108', 'E591'),('108', 'E617'),('108', 'E786'),('108', 'F078'),('108', 'F165'),('108', 'F247'),('108', 'F704'),('108', 'F797'),('108', 'F944'),('108', 'G116'),('108', 'H233'),('108', 'H537'),('108', 'I625'),('108', 'I709'),('108', 'I878'),('108', 'I998'),('108', 'L434'),('108', 'L511'),('108', 'L677'),('108', 'L704'),('108', 'L709'),('108', 'L744'),('108', 'M017'),('108', 'M052'),('108', 'B289'),('108', 'B671'),('108', 'D019'),('108', 'E530'),('108', 'H529'),('109', 'A233'),('109', 'A252'),('109', 'A760'),('109', 'B534'),('109', 'D477'),('109', 'D542'),('109', 'D760'),('109', 'E208'),('109', 'E447'),('109', 'E807'),('109', 'F021'),('109', 'F379'),('109', 'F428'),('109', 'F493'),('109', 'F509'),('109', 'F517'),('109', 'F520'),('109', 'F522'),('109', 'F536'),('109', 'F549'),('109', 'F599'),('109', 'F614'),('109', 'F626'),('109', 'F653'),('109', 'F664'),('109', 'F665'),('109', 'F697'),('109', 'F722'),('109', 'G137'),('109', 'G403'),('109', 'G516'),('109', 'G873'),('109', 'G920'),('109', 'G921'),('109', 'H182'),('109', 'I315'),('109', 'I324'),('109', 'C070'),('109', 'I774'),('109', 'L279'),('110', 'A285'),('110', 'A669'),('110', 'A883'),('110', 'B619'),('110', 'E946'),('110', 'F220'),('110', 'H839'),('110', 'I907'),('110', 'L328'),('110', 'B915'),('111', 'A359'),('111', 'A419'),('111', 'A597'),('111', 'A677'),('111', 'A681'),('111', 'B250'),('111', 'B274'),('111', 'B383'),('111', 'B745'),('111', 'B789'),('111', 'M288'),('111', 'C882'),('111', 'D260'),('111', 'D323'),('111', 'D333'),('111', 'D334'),('111', 'D344'),('111', 'D430'),('111', 'D431'),('111', 'D443'),('111', 'D639'),('111', 'D827'),('111', 'D968'),('111', 'D970'),('111', 'D982'),('111', 'D994'),('111', 'D997'),('111', 'E022'),('111', 'E084'),('111', 'E086'),('111', 'E085'),('111', 'E234'),('111', 'E252'),('111', 'E270'),('111', 'E281'),('111', 'E336'),('111', 'E464'),('111', 'E742'),('111', 'E877'),('111', 'M270'),('111', 'F333'),('111', 'F808'),('111', 'F822'),('111', 'F841'),('111', 'F981'),('111', 'F982'),('111', 'F983'),('111', 'F986'),('111', 'F991'),('111', 'G122'),('111', 'G133'),('111', 'G207'),('111', 'G382'),('111', 'G446'),('111', 'G669'),('111', 'M291'),('111', 'G922'),('111', 'H659'),('111', 'H738'),('111', 'H739'),('111', 'H766'),('111', 'H856'),('111', 'G287'),('111', 'G383'),('111', 'I166'),('111', 'I402'),('111', 'H974'),('111', 'I182'),('111', 'I271'),('111', 'M209'),('111', 'I294'),('111', 'I428'),('111', 'I570'),('111', 'I582'),('111', 'I615'),('111', 'I624'),('111', 'I647'),('111', 'I667'),('111', 'I668'),('111', 'I705'),('111', 'I706'),('111', 'I707'),('111', 'I724'),('111', 'I734'),('111', 'I735'),('111', 'I765'),('111', 'I797'),('111', 'I995'),('111', 'L154'),('111', 'L337'),('111', 'L463'),('111', 'L473'),('111', 'L512'),('111', 'L513'),('111', 'L613'),('111', 'L924'),('111', 'L966'),('111', 'L968'),('111', 'L992'),('111', 'L986'),('111', 'L987'),('111', 'M278'),('111', 'L998'),('111', 'M016'),('111', 'B738'),('111', 'M025'),('111', 'M026');
UPDATE `gaz_municipalities` t1 INNER JOIN `gaz_temp` t2 ON t1.code_register = t2.codice_alfanumerico SET t1.id_province = t2.id_province;
DROP TABLE `gaz_temp`;
 
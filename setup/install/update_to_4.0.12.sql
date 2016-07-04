UPDATE `gaz_config` SET `cvalue` = '57' WHERE `id` =2;
ALTER TABLE `gaz_contract` CHANGE `id_costumer` `id_customer` INT( 9 ) NOT NULL DEFAULT '0';
UPDATE `gaz_config` SET `cvalue` = '58' WHERE `id` =2;
ALTER TABLE `gaz_admin_module` CHANGE `enterpriseid` `enterprise_id` INT( 3 ) NOT NULL;
CREATE TABLE `gaz_anagra` ENGINE=MyISAM DEFAULT CHARSET=utf8 AS SELECT codice,ragso1,ragso2,sedleg,legrap,sexper,datnas,luonas,pronas,indspe,capspe,citspe,prospe,country,latitude,longitude,telefo,fax,cell,codfis,pariva,e_mail FROM `gaz_clfoco` WHERE capspe > 0;
ALTER TABLE `gaz_anagra` ADD `id` INT( 9 ) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY ( `id` );
ALTER TABLE `gaz_clfoco` ADD `id_anagra` INT( 9 ) NOT NULL AFTER `codice`; 
UPDATE `gaz_clfoco` LEFT JOIN `gaz_anagra` ON gaz_clfoco.codice=gaz_anagra.codice SET id_anagra=gaz_anagra.id WHERE gaz_clfoco.codice = gaz_anagra.codice;
ALTER TABLE `gaz_anagra` DROP `codice`;
ALTER TABLE `gaz_clfoco` DROP `ragso2`, DROP `sedleg`, DROP `legrap`, DROP `sexper`, DROP `datnas`, DROP `luonas`,  DROP `pronas`, DROP `indspe`, DROP `capspe`, DROP `citspe`, DROP `prospe`, DROP `country`, DROP `latitude`, DROP `longitude`, DROP `telefo`, DROP `fax`, DROP `cell`, DROP `codfis`, DROP `pariva`, DROP `e_mail`;
ALTER TABLE `gaz_clfoco` CHANGE `ragso1` `descri` VARCHAR( 100 );
UPDATE `gaz_config` SET `cvalue` = '59' WHERE `id` =2;
UPDATE `gaz_admin` SET `enterprise_id` = 1 WHERE `enterprise_id` = 0;
UPDATE `gaz_admin_module` SET `enterprise_id` = 1 WHERE `enterprise_id` = 0;
ALTER TABLE `gaz_aziend` ADD `template` VARCHAR( 50 ) NOT NULL AFTER `artsea`;
UPDATE `gaz_aziend` SET template = (SELECT `cvalue` FROM `gaz_config` WHERE `variable` = 'template' LIMIT 1);
UPDATE `gaz_config` SET `variable` = 'last_update_exec' WHERE `variable` = 'template' LIMIT 1;
UPDATE `gaz_config` SET `cvalue` = '412' WHERE `variable` = 'last_update_exec' LIMIT 1;
UPDATE `gaz_config` SET `description` = 'Ultimo script PHP di aggiornamento eseguito' WHERE `variable` = 'last_update_exec' LIMIT 1;
UPDATE `gaz_config` SET `cvalue` = '60' WHERE `id` =2;
RENAME TABLE `gaz_agenti`  TO `gaz_001agenti` ;
RENAME TABLE `gaz_aliiva`  TO `gaz_001aliiva` ;
RENAME TABLE `gaz_artico`  TO `gaz_001artico` ;
RENAME TABLE `gaz_banapp`  TO `gaz_001banapp` ;
RENAME TABLE `gaz_cash_register`  TO `gaz_001cash_register` ;
RENAME TABLE `gaz_catmer` TO `gaz_001catmer`;
RENAME TABLE `gaz_caucon` TO `gaz_001caucon`;
RENAME TABLE `gaz_caumag` TO `gaz_001caumag`;
RENAME TABLE `gaz_clfoco` TO `gaz_001clfoco`;
RENAME TABLE `gaz_effett` TO `gaz_001effett`;
RENAME TABLE `gaz_imball` TO `gaz_001imball`;
RENAME TABLE `gaz_letter` TO `gaz_001letter`;
RENAME TABLE `gaz_movmag` TO `gaz_001movmag`;
RENAME TABLE `gaz_pagame` TO `gaz_001pagame`;
RENAME TABLE `gaz_portos` TO `gaz_001portos`;
RENAME TABLE `gaz_provvigioni` TO `gaz_001provvigioni` ;
RENAME TABLE `gaz_rigbro` TO `gaz_001rigbro`;
RENAME TABLE `gaz_rigdoc` TO `gaz_001rigdoc`;
RENAME TABLE `gaz_rigmoc` TO `gaz_001rigmoc`;
RENAME TABLE `gaz_rigmoi` TO `gaz_001rigmoi`;
RENAME TABLE `gaz_spediz` TO `gaz_001spediz`;
RENAME TABLE `gaz_tesbro` TO `gaz_001tesbro`;
RENAME TABLE `gaz_tesdoc` TO `gaz_001tesdoc`;
RENAME TABLE `gaz_tesmov` TO `gaz_001tesmov`;
RENAME TABLE `gaz_vettor` TO `gaz_001vettor`;
RENAME TABLE `gaz_body_text` TO `gaz_001body_text`;
RENAME TABLE `gaz_contract` TO `gaz_001contract`;
RENAME TABLE `gaz_contract_row` TO `gaz_001contract_row`;
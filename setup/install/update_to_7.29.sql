UPDATE `gaz_config` SET `cvalue` = '124' WHERE `id` =2;
ALTER TABLE `gaz_anagra` ADD `id_SIAN` INT(10) NULL DEFAULT NULL COMMENT 'identificativo stabilimento assegnato dal SIAN' AFTER `fatt_email`;
INSERT INTO `gaz_breadcrumb` (`exec_mode`,`file`,`titolo`,`link`,`position_order`,`icon`,`adminid`) SELECT  `exec_mode`, 'vendit/dash_customer_schedule.php', `titolo`, `link`,`position_order`,`icon`,`adminid` from `gaz_breadcrumb` WHERE `file`='root/dash_customer_schedule.php';
DELETE FROM `gaz_breadcrumb` WHERE `file`='root/dash_customer_schedule.php';
INSERT INTO `gaz_breadcrumb` (`exec_mode`,`file`,`titolo`,`link`,`position_order`,`icon`,`adminid`) SELECT  `exec_mode`, 'acquis/dash_supplier_schedule.php', `titolo`, `link`,`position_order`,`icon`,`adminid` from `gaz_breadcrumb` WHERE `file`='root/dash_supplier_schedule.php';
DELETE FROM `gaz_breadcrumb` WHERE `file`='root/dash_supplier_schedule.php';
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
CREATE TABLE IF NOT EXISTS `gaz_XXXcamp_recip_stocc` (
  `cod_silos` varchar(10) NOT NULL COMMENT 'Codice recipiente di stoccaggio olio. Deve essere identico a quello registrato al SIAN',
  `capacita` decimal(13,3) NOT NULL COMMENT 'La capacità in kg di olio del recipiente',
  `affitto` int(1) NOT NULL COMMENT '0=di proprietà 1=in affitto',
  `dop_igp` int(1) NOT NULL COMMENT '0=non classificato 1=Recipiente destinato a olio DOP o IGP',
  PRIMARY KEY (`cod_silos`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `gaz_XXXcamp_mov_sian` (
  `id_mov_sian` int(9) NOT NULL AUTO_INCREMENT,
  `id_mov_sian_rif` int(9) NOT NULL COMMENT 'Serve in caso di produzione per connettere insieme il prodotto con i suoi componenti',
  `id_movmag` int(9) NOT NULL COMMENT 'Movimento magazzino connesso',
  `recip_stocc_destin` varchar(10) NOT NULL COMMENT 'ID recipiente stoccaggio di destinazione',
  `recip_stocc` varchar(10) NOT NULL COMMENT 'Identificativo recipiente di stoccaggio. Deve essere identico a quello inserito nel SIAN',
  `cod_operazione` varchar(10) NOT NULL COMMENT 'Codice dell''operazione esguita',
  `stabil_dep` int(10) NOT NULL COMMENT 'Identificativo dello stabilimento o deposito assegnato dal SIAN',
  PRIMARY KEY (`id_mov_sian`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `gaz_XXXcampi`
	ADD COLUMN `indirizzo` VARCHAR(50) NULL AFTER `image`,
	ADD COLUMN `provincia` VARCHAR(2) NULL AFTER `indirizzo`,
	ADD COLUMN `comune` VARCHAR(50) NULL AFTER `provincia`,
	ADD COLUMN `id_rif` INT(10) NULL DEFAULT '0' COMMENT 'Identificativo dello stabilimento/deposito assegnato dal SIAN' AFTER `id_mov`;
ALTER TABLE `gaz_XXXartico`
	ADD COLUMN `SIAN` INT(1) NOT NULL DEFAULT '0' COMMENT '0 non movimenta, 1 movimenta il SIAN come olio, 2 movimenta il SIAN come olive' AFTER `tempo_sospensione`;
CREATE TABLE IF NOT EXISTS `gaz_XXXcamp_artico` (
  `id_campartico` int(9) NOT NULL AUTO_INCREMENT,
  `codice` varchar(15) NOT NULL COMMENT 'Codice articolo uguale alla tabella artico',
  `or_macro` int(2) NOT NULL COMMENT 'macroarea di origine',
  `or_spec` varchar(80) NOT NULL COMMENT 'origine specifica',
  `estrazione` int(1) NOT NULL COMMENT 'Flag estrazione: 0=non specificata 1=prima spremitura a freddo 2=estratto a freddo',
  `biologico` int(1) NOT NULL COMMENT 'Flag agricoltura: 0=convenzionale 1=biologica 2=in conversione',
  `etichetta` int(1) NOT NULL COMMENT 'Flag etichettatura: 0=non etichettato 1=etichettato',
  `confezione` decimal(9,3) NOT NULL COMMENT 'CapacitĂ  singola confezione. 0=sfuso',
  `categoria` int(2) NOT NULL COMMENT 'Categoria olio come codificato dal SIAN',
  PRIMARY KEY (`id_campartico`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `gaz_XXXcompany_config` SELECT MAX(id)+1, 'Identificativo dello stabilimento o deposito del SIAN', 'id_sian', '0' FROM `gaz_XXXcompany_config`;
ALTER TABLE `gaz_XXXsyncronize_oc`	CHANGE COLUMN `date_update` `date_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `date_created`;
INSERT INTO `gaz_XXXcompany_config` SELECT MAX(id)+1, 'Allerta se la lunghezza del codice articolo è diverso da', 'codart_len', '0' FROM `gaz_XXXcompany_config`;
ALTER TABLE `gaz_XXXpaymov`	DROP INDEX `id_rigmoc_pay`,	ADD INDEX `id_rigmoc_pay` (`id_rigmoc_pay`) USING HASH;
ALTER TABLE `gaz_XXXtesdoc`	CHANGE COLUMN `id_contract` `id_contract` INT(9) NOT NULL DEFAULT '0' COMMENT 'In caso di corrispettivo, scontrino con causale VCO, viene usato per contenere l\'id del registratore ovvero id_cash della tabella gaz_NNNcash_register, se "0" verrà generato un file XML, altrimenti verranno inviati i dati al RT in base a quanto indicato sul rigo della tabella citata' AFTER `geneff`;
ALTER TABLE `gaz_001clfoco`	ADD COLUMN `status_SIAN` INT(1) NOT NULL COMMENT '0=soggetto non trasmesso al SIAN, 1=soggetto trasmesso al SIAN' AFTER `status`;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)

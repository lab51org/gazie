UPDATE `gaz_config` SET `cvalue` = '117' WHERE `id` =2;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
INSERT INTO `gaz_XXXcompany_config` (`description`, `var`, `val`) VALUES ('Usa articoli composti come Kit o come Standard (KIT o STD)', 'tipo_composti', 'STD');
INSERT INTO `gaz_XXXcompany_config` (`description`, `var`, `val`) VALUES ('Layout posizione logo su documenti (LEFT o DEFAULT)', 'layout_pos_logo_on_doc', 'DEFAULT');
ALTER TABLE `gaz_XXXrigdoc` ADD COLUMN `larghezza` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'espresso in mm' AFTER `unimis`, ADD COLUMN `lunghezza` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'espresso in mm' AFTER `larghezza`, ADD COLUMN `spessore` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'espresso in mm' AFTER `lunghezza`, ADD COLUMN `peso_specifico` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'non necessariamente in kg/l, potrà moltiplicare anche con i ml, mq, pezzi' AFTER `spessore`,	ADD COLUMN `pezzi` INT(9) NULL DEFAULT NULL AFTER `peso_specifico`, ADD COLUMN `quality` VARCHAR(50) NOT NULL COMMENT 'per indicare la qualità del materiale richiesto, normativa, ecc' AFTER `descri`;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)
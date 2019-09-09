UPDATE `gaz_config` SET `cvalue` = '123' WHERE `id` =2;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 1', 'umeco1', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 2', 'umeco2', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 3', 'umeco3', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 4', 'umeco4', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 5', 'umeco5', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 6', 'umeco6', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 7', 'umeco7', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 8', 'umeco8', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro dei Corrispettivi della sezione IVA 9', 'umeco9', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 1', 'umeve1', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 2', 'umeve2', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 3', 'umeve3', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 4', 'umeve4', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 5', 'umeve5', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 6', 'umeve6', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 7', 'umeve7', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 8', 'umeve8', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di vendita della sezione IVA 9', 'umeve9', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 1', 'umeac1', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 2', 'umeac2', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 3', 'umeac3', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 4', 'umeac4', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 5', 'umeac5', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 6', 'umeac6', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 7', 'umeac7', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 8', 'umeac8', '0');
INSERT INTO `gaz_XXXcompany_data` (`description`, `var`, `data`) VALUES ('Ultimo mese stampato del Registro delle Fatture di acquito della sezione IVA 9', 'umeac9', '0');
ALTER TABLE `gaz_XXXcampi` ADD COLUMN `zona_vulnerabile` INT(1) NULL DEFAULT '0' COMMENT 'Nelle regioni che hanno già individuato le Zone Vulnerabili da Nitrati di origine agricola, chiamate ZVN, quando si concima, non si può superare un certo limite di azoto annuo' AFTER `id_colture`;
ALTER TABLE `gaz_XXXartico`	ADD COLUMN `perc_N` DECIMAL(3,1) NULL DEFAULT NULL COMMENT 'Se un concime dev\'essere valorizzato con la percentuale di azoto (N)' AFTER `rame_metallico`, ADD COLUMN `perc_P` DECIMAL(3,1) NULL DEFAULT NULL COMMENT 'Se un concime dev\'essere valorizzato con la percentuale di fosforo (P)' AFTER `perc_N`,	ADD COLUMN `perc_K` DECIMAL(3,1) NULL DEFAULT NULL COMMENT 'Se un concime dev\'essere valorizzato con la percentuale di potassio (K)' AFTER `perc_P`;
ALTER TABLE `gaz_XXXcampi` ADD COLUMN `limite_azoto_zona_vulnerabile` INT(3) NOT NULL DEFAULT '170' COMMENT 'Limite di azoto ad ettaro se ZVN ( valore default = 170)' AFTER `zona_vulnerabile`, ADD COLUMN `limite_azoto_zona_non_vulnerabile` INT(3) NOT NULL DEFAULT '340' COMMENT 'Limite di azoto ad ettaro se zona NON vulnerabile ( valore default = 340)' AFTER `limite_azoto_zona_vulnerabile`;


-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione)

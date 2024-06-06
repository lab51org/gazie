UPDATE `gaz_config` SET `cvalue` = '159' WHERE `id` =2;
DELETE FROM `gaz_config` WHERE  `variable`='last_update_exec';
DELETE FROM gaz_menu_script WHERE link LIKE '%prop_ordine.php%';
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXtesdoc`	ADD COLUMN `tipdoc_buf` CHAR(3) NOT NULL DEFAULT '' COMMENT 'Quando tipdoc sarà valorizzato con "BUF", potrò utilizzare questa colonna per indicare il valore che dovrà assumere la colonna tipdoc alla conferma del contenuto del documento che si sta inserendo, questo eviterà in futuro (o a chi personalizza qualche interfaccia utente) di fare il POST di tutti i righi già immessi superando il limite attuale imposto dalla direttiva  max_input_vars del PHP. In sostanza si potrà mettere sempre e subito tutto sul database man mano che vengono inseriti i righi per poi valorizzare con il giusto tipdoc alla conferma. Il tipdoc=BUF dovrà essere uno solo per ogni utente (colonna adminid) e quindi ripulito ad ogni nuovo documento perché potrebbe essere rimasto in sospeso con un precedente documento mai confermato' AFTER `tipdoc`, ADD INDEX `tipdoc_buf` (`tipdoc_buf`);
ALTER TABLE `gaz_XXXtesbro`	ADD COLUMN `tipdoc_buf` CHAR(3) NOT NULL DEFAULT '' COMMENT 'Quando tipdoc sarà valorizzato con "BUF", potrò utilizzare questa colonna per indicare il valore che dovrà assumere la colonna tipdoc alla conferma del contenuto del documento che si sta inserendo, questo eviterà in futuro (o a chi personalizza qualche interfaccia utente) di fare il POST di tutti i righi già immessi superando il limite attuale imposto dalla direttiva  max_input_vars del PHP. In sostanza si potrà mettere sempre e subito tutto sul database man mano che vengono inseriti i righi per poi valorizzare con il giusto tipdoc alla conferma. Il tipdoc=BUF dovrà essere uno solo per ogni utente (colonna adminid) e quindi ripulito ad ogni nuovo documento perché potrebbe essere rimasto in sospeso con un precedente documento mai confermato' AFTER `tipdoc`, ADD INDEX `tipdoc_buf` (`tipdoc_buf`);
ALTER TABLE `gaz_XXXtesmov`	ADD COLUMN `caucon_buf` CHAR(3) NOT NULL DEFAULT '' COMMENT 'Quando caucon sarà valorizzato con "BUF", potrò utilizzare questa colonna per indicare il valore che dovrà assumere la colonna caucon alla conferma del contenuto del movimento che si sta inserendo, questo eviterà in futuro o a chi personalizza l\'interfaccia utente di fare il POST di tutti i righi già immessi superando il limite attuale imposto dalla direttiva  max_input_vars del PHP. In sostanza si potrà mettere sempre e subito tutto sul database man mano che vengono inseriti i righi per poi valorizzare con la giusta causale alla conferma. Il tipdoc=BUF dovrà essere uno solo per ogni utente (colonna adminid) e quindi ripulito ad ogni nuovo movimento contabile perché potrebbe essere rimasto in sospeso con un precedente  mai confermato' AFTER `caucon`, ADD INDEX `caucon` (`caucon`), ADD INDEX `caucon_buf` (`caucon_buf`);
ALTER TABLE `gaz_XXXrigbro`	ADD COLUMN `nrow` INT NOT NULL DEFAULT '0' COMMENT 'Numero del rigo sul documento' AFTER `id_rig`, ADD COLUMN `nrow_linked` INT NOT NULL DEFAULT '0' COMMENT 'Numero del rigo al quale è vincolato. Ad esempio un rigo tipo 6 (testo) derivante dalla descrizione estesa di un articolo/servizio di magazzino ' AFTER `nrow`,	ADD INDEX `nrow` (`nrow`), ADD INDEX `nrow_linked` (`nrow_linked`);
ALTER TABLE `gaz_XXXrigdoc`	ADD COLUMN `nrow` INT NOT NULL DEFAULT '0' COMMENT 'Numero del rigo sul documento' AFTER `id_rig`, ADD COLUMN `nrow_linked` INT NOT NULL DEFAULT '0' COMMENT 'Numero del rigo al quale è vincolato. Ad esempio un rigo tipo 6 (testo) derivante dalla descrizione estesa di un articolo/servizio di magazzino o, ad esempio, per creare task per la gestione dei diagrammi di Gantt' AFTER `nrow`,	ADD INDEX `nrow` (`nrow`), ADD INDEX `nrow_linked` (`nrow_linked`);
ALTER TABLE `gaz_XXXdistinta_base` ADD COLUMN `sort_order` INT NOT NULL COMMENT 'Per ordinamento visualizzazione componente, ad esempio su esplodo della distinta base' AFTER `id_movmag`,	ADD INDEX `sort_order` (`sort_order`);
ALTER TABLE `gaz_XXXartico`	ADD COLUMN `sort_order` INT NOT NULL COMMENT 'Per ordinamento articolo, ad esempio su catalogo' AFTER `ref_ecommerce_id_product`,	ADD INDEX `sort_order` (`sort_order`);
ALTER TABLE `gaz_XXXcatmer`	ADD COLUMN `sort_order` INT NOT NULL COMMENT 'Per ordinamento categoria merceologica, ad esempio su catalogo' AFTER `ref_ecommerce_id_category`, ADD INDEX `sort_order` (`sort_order`);
ALTER TABLE `gaz_XXXassets` ADD COLUMN `sort_order` INT NOT NULL COMMENT 'Per ordinamento bene strumentale, ad esempio su libro cespiti' AFTER `codice_artico`, ADD INDEX `sort_order` (`sort_order`);
UPDATE `gaz_XXXcompany_config` SET `val`= '2' WHERE `var` = 'ext_artico_description';
UPDATE `gaz_XXXcompany_config` SET `description`= 'Attiva lo scroll automatico sull\'ultimo rigo dei documenti (0= No, 1= Si, 9= No, ma con rigo input in testa)' WHERE `var` = 'autoscroll_to_last_row';
ALTER TABLE `gaz_XXXstaff` ADD COLUMN `codice_campi` INT(10) NULL DEFAULT NULL COMMENT 'riferimento alla tabella gaz_NNNcampi (reparto o luogo di lavoro)' AFTER `employment_status`;
ALTER TABLE `gaz_XXXstaff_work_movements`	ADD COLUMN `codice_campi` INT(10) NULL DEFAULT NULL COMMENT 'riferimento alla tabella gaz_NNNcampi per indicare il luogo/reparto dove è stato eseguito il lavoro' AFTER `id_orderman`, ADD INDEX `codice_campi` (`codice_campi`);
ALTER TABLE `gaz_XXXstaff_worked_hours`
	CHANGE COLUMN `hours_normal` `hours_normal` DECIMAL(4,2) NOT NULL AFTER `work_day`,
	CHANGE COLUMN `hours_extra` `hours_extra` DECIMAL(4,2) NOT NULL AFTER `id_work_type_extra`,
	CHANGE COLUMN `hours_absence` `hours_absence` DECIMAL(4,2) NOT NULL AFTER `id_absence_type`,
	CHANGE COLUMN `hours_other` `hours_other` DECIMAL(4,2) NOT NULL AFTER `id_other_type`;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione )

UPDATE `gaz_config` SET `cvalue` = '132' WHERE `id` =2; 
UPDATE `gaz_config` SET `cvalue`='{\r\n"ADT":"acquis",\r\n"AFA":"acquis",\r\n"AFC":"acquis",\r\n"DDR":"acquis",\r\n"ADT":"acquis",\r\n"AFT":"acquis",\r\n"DDL":"acquis", \r\n"RDL":"acquis",\r\n"DDR":"acquis",\r\n"VCO":"vendit", \r\n"VRI":"vendit", \r\n"DDT":"vendit", \r\n"FAD":"vendit", \r\n"FAI":"vendit", \r\n"FAA":"vendit", \r\n"FAQ":"vendit", \r\n"FAP":"vendit", \r\n"FNC":"vendit", \r\n"FND":"vendit", \r\n"DDV":"vendit", \r\n"RDV":"vendit", \r\n"DDY":"vendit", \r\n"DDS":"vendit",\r\n"VPR":"vendit", \r\n"VOR":"vendit", \r\n"VOW":"vendit", \r\n"VOG":"vendit", \r\n"CMR":"vendit", \r\n"CAM":"camp",\r\n"PRO":"orderman",\r\n"MAG":"magazz",\r\n"INV":"magazz"\r\n}' WHERE  `variable`='report_movmag_ref_doc';
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione )
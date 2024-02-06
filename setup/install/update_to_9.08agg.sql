UPDATE `gaz_config` SET `cvalue` = '157' WHERE `id` =2;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXpagame` ADD COLUMN `custom_field` TEXT NULL COMMENT 'Usabile per contenere le scelte dell\'utente in ambito dello specifico modulo. Normalmente in formato json: {"nome_modulo":{"nome_variabile": {"valorei_variabile"}}, nel caso specifico dei pagamenti potrebbe contenere gli importi delle singole rate da passare alla funzione CalcExpiry (expiry_calc.php) alla referenza $distribution con una matrice bidimensionale con il riparto percentuale delle singole rate (la cui somma dovrebbe essere del 100%), quando queste non sono di importo e date costanti' AFTER `numrat`;
-- STOP_WHILE ( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query su tutte le aziende dell'installazione )

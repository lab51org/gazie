UPDATE `gaz_config` SET `cvalue` = '73' WHERE `id` =2;
DROP TABLE `gaz_country`;
CREATE TABLE `gaz_country` (
  `iso` CHAR(2) NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `iso3` CHAR(3) DEFAULT NULL,
  `IBAN_prefix` VARCHAR(2) NOT NULL,
  `IBAN_lenght` INT NOT NULL,
  `bank_code_pos` TINYINT NOT NULL,
  `bank_code_lenght` TINYINT NOT NULL,
  `bank_code_fix` TINYINT NOT NULL,
  `bank_code_alpha` TINYINT NOT NULL,
  `account_number_pos` TINYINT NOT NULL,
  `account_number_lenght` TINYINT NOT NULL,
  `account_number_fix` TINYINT NOT NULL,
  `account_number_alpha` TINYINT NOT NULL,
  `VAT_number_lenght` TINYINT NOT NULL,
  `VAT_number_alpha` TINYINT NOT NULL,
  `black_list` INT NOT NULL,
  `istat_continent` INT NOT NULL,
  `istat_area` INT NOT NULL,
  `istat_country` INT NOT NULL,
  `istat_name` VARCHAR(100) NOT NULL,
  `iana` VARCHAR(4) NOT NULL,
  `un_vehicle` VARCHAR(3) NOT NULL,
  PRIMARY KEY (`iso`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `gaz_country` (`iso`, `name`, `iso3`, `IBAN_prefix`, `IBAN_lenght`, `bank_code_pos`, `bank_code_lenght`, `bank_code_fix`, `bank_code_alpha`, `account_number_pos`, `account_number_lenght`, `account_number_fix`, `account_number_alpha`, `VAT_number_lenght`, `VAT_number_alpha`, `black_list`, `istat_continent`, `istat_area`, `istat_country`, `istat_name`, `iana`, `un_vehicle`) VALUES
('AD', 'ANDORRA', 'AND', 'AD', 24, 0, 8, 1, 0, 0, 12, 1, 1, 0, 0, 5, 1, 13, 202, 'Andorra', '.ad', 'AND'),
('AT', 'AUSTRIA', 'AUT', 'AT', 20, 0, 5, 1, 0, 0, 11, 0, 0, 0, 0, 2, 1, 11, 203, 'Austria', '.at', 'A'),
('BE', 'BELGIUM', 'BEL', 'BE', 16, 0, 3, 1, 0, 0, 9, 1, 0, 0, 0, 2, 1, 11, 206, 'Belgio', '.be', 'B'),
('CH', 'SWITZERLAND (Confederation of Helvetia)', 'CHE', 'CH', 21, 0, 5, 0, 0, 0, 12, 0, 1, 0, 0, 2, 1, 13, 241, 'Svizzera', '.ch', 'CH'),
('DE', 'GERMANY (Deutschland)', 'DEU', 'DE', 22, 0, 8, 1, 0, 0, 10, 0, 0, 0, 0, 0, 1, 11, 216, 'Germania', '.de', 'D'),
('DK', 'DENMARK', 'DNK', 'DK', 18, 0, 4, 0, 0, 0, 10, 0, 0, 0, 0, 0, 1, 11, 212, 'Danimarca', '.dk', 'DK'),
('ES', 'SPAIN (España)', 'ESP', 'ES', 24, 0, 10, 1, 0, 0, 10, 1, 0, 0, 0, 0, 1, 11, 239, 'Spagna', '.es', 'E'),
('FI', 'FINLAND', 'FIN', 'FI', 18, 0, 6, 1, 0, 0, 8, 0, 0, 0, 0, 0, 1, 11, 214, 'Finlandia', '.fi', 'FIN'),
('FR', 'FRANCE', 'FRA', 'FR', 27, 0, 10, 1, 0, 0, 13, 1, 1, 0, 0, 0, 1, 11, 215, 'Francia', '.fr', 'F'),
('GB', 'UNITED KINGDOM', 'GBR', 'GB', 22, 0, 10, 1, 1, 0, 8, 0, 0, 0, 0, 0, 1, 11, 219, 'Regno Unito', '.uk', ' '),
('GR', 'GREECE', 'GRC', 'GR', 27, 0, 7, 1, 0, 0, 16, 0, 1, 0, 0, 0, 1, 11, 220, 'Grecia', '.gr', 'GR'),
('HU', 'HUNGARY', 'HUN', 'HU', 28, 0, 0, 1, 1, 0, 24, 0, 1, 0, 0, 0, 1, 11, 244, 'Ungheria', '.hu', 'H'),
('IE', 'IRELAND', 'IRL', 'IE', 22, 0, 10, 1, 1, 0, 8, 1, 0, 0, 0, 0, 1, 11, 221, 'Irlanda', '.ie', 'IRL'),
('IS', 'ICELAND', 'ISL', 'IS', 26, 0, 4, 1, 0, 0, 18, 1, 0, 0, 0, 0, 1, 13, 223, 'Islanda', '.is', 'IS'),
('LI', 'LIECHTENSTEIN (Fürstentum Liechtenstein)', 'LIE', 'LI', 21, 0, 5, 0, 0, 0, 12, 0, 1, 0, 0, 5, 1, 13, 225, 'Liechtenstein', '.li', 'FL'),
('LU', 'LUXEMBOURG', 'LUX', 'LU', 20, 0, 3, 1, 0, 0, 13, 1, 1, 0, 0, 2, 1, 11, 226, 'Lussemburgo', '.lu', 'L'),
('MC', 'MONACO', 'MCO', 'MC', 27, 0, 10, 1, 0, 0, 13, 1, 1, 0, 0, 5, 1, 13, 229, 'Monaco', '.mc', 'MC'),
('NL', 'NETHERLANDS', 'NLD', 'NL', 18, 0, 4, 1, 1, 0, 10, 0, 0, 0, 0, 0, 1, 11, 232, 'Paesi Bassi', '.nl', 'NL'),
('NO', 'NORWAY', 'NOR', 'NO', 15, 0, 4, 1, 0, 0, 7, 1, 0, 0, 0, 0, 1, 13, 231, 'Norvegia', '.no', 'N'),
('PL', 'POLAND', 'POL', 'PL', 28, 0, 8, 1, 0, 0, 16, 0, 1, 0, 0, 0, 1, 11, 233, 'Polonia', '.pl', 'PL'),
('PT', 'PORTUGAL', 'PRT', 'PT', 25, 0, 8, 1, 0, 0, 13, 1, 0, 0, 0, 0, 1, 11, 234, 'Portogallo', '.pt', 'P'),
('SE', 'SWEDEN', 'SWE', 'SE', 24, 0, 3, 1, 0, 0, 17, 0, 0, 0, 0, 0, 1, 11, 240, 'Svezia', '.se', 'S'),
('SI', 'SLOVENIA', 'SVN', 'SI', 19, 0, 5, 1, 0, 5, 10, 1, 0, 8, 1, 0, 1, 11, 251, 'Slovenia', '.si', 'SLO'),
('SM', 'SAN MARINO (Republic of)', 'SMR', 'SM', 27, 0, 11, 1, 1, 0, 12, 1, 1, 0, 0, 5, 1, 13, 236, 'San Marino', '.sm', 'RSM'),
('AF', 'AFGHANISTAN', 'AFG', 'AF', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 301, 'Afghanistan', '.af', 'AFG'),
('AL', 'ALBANIA', 'ALB', 'AL', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 201, 'Albania', '.al', 'AL'),
('DZ', 'ALGERIA (El Djazaïr)', 'DZA', 'DZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 21, 401, 'Algeria', '.dz', 'DZ'),
('CU', 'CUBA', 'CUB', 'CU', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 514, 'Cuba', '.cu', 'CU'),
('HR', 'CROATIA (Hrvatska)', 'HRV', 'HR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 250, 'Croazia', '.hr', 'HR'),
('AO', 'ANGOLA', 'AGO', 'AO', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 402, 'Angola', '.ao', ' '),
('AG', 'ANTIGUA AND BARBUDA', 'ATG', 'AG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 503, 'Antigua e Barbuda', '.ag', ' '),
('AR', 'ARGENTINA', 'ARG', 'AR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 602, 'Argentina', '.ar', 'RA'),
('AM', 'ARMENIA', 'ARM', 'AM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 358, 'Armenia', '.am', 'AM'),
('AU', 'AUSTRALIA', 'AUS', 'AU', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 701, 'Australia', '.au', 'AUS'),
('AZ', 'AZERBAIJAN', 'AZE', 'AZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 359, 'Azerbaigian', '.az', 'AZ'),
('BS', 'BAHAMAS', 'BHS', 'BS', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 505, 'Bahamas', '.bs', 'BS'),
('BH', 'BAHRAIN', 'BHR', 'BH', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 304, 'Bahrein', '.bh', 'BRN'),
('BD', 'BANGLADESH', 'BGD', 'BD', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 305, 'Bangladesh', '.bd', 'BD'),
('BB', 'BARBADOS', 'BRB', 'BB', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 506, 'Barbados', '.bb', 'BDS'),
('BY', 'BELARUS', 'BLR', 'BY', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 256, 'Bielorussia', '.by', 'BY'),
('BZ', 'BELIZE', 'BLZ', 'BZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 507, 'Belize', '.bz', 'BH'),
('BJ', 'BENIN', 'BEN', 'BJ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 406, 'Benin (ex Dahomey)', '.bj', 'DY'),
('BT', 'BHUTAN', 'BTN', 'BT', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 306, 'Bhutan', '.bt', ' '),
('BO', 'BOLIVIA', 'BOL', 'BO', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 604, 'Bolivia', '.bo', 'BOL'),
('BA', 'BOSNIA AND HERZEGOVINA', 'BIH', 'BA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 252, 'Bosnia-Erzegovina', '.ba', 'BIH'),
('BW', 'BOTSWANA', 'BWA', 'BW', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 408, 'Botswana', '.bw', 'BW'),
('BR', 'BRAZIL', 'BRA', 'BR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 605, 'Brasile', '.br', 'BR'),
('BN', 'BRUNEI DARUSSALAM', 'BRN', 'BN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 309, 'Brunei', '.bn', 'BRU'),
('BG', 'BULGARIA', 'BGR', 'BG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 209, 'Bulgaria', '.bg', 'BG'),
('BF', 'BURKINA FASO', 'BFA', 'BF', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 409, 'Burkina Faso (ex Alto Volta)', '.bf', 'BF'),
('BI', 'BURUNDI', 'BDI', 'BI', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 410, 'Burundi', '.bi', 'RU'),
('KH', 'CAMBODIA', 'KHM', 'KH', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 310, 'Cambogia', '.kh', 'K'),
('CM', 'CAMEROON', 'CMR', 'CM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 411, 'Camerun', '.cm', 'CAM'),
('CA', 'CANADA', 'CAN', 'CA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 41, 509, 'Canada', '.ca', 'CDN'),
('CV', 'CAPE VERDE', 'CPV', 'CV', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 413, 'Capo Verde', '.cv', ' '),
('CF', 'CENTRAL AFRICAN REPUBLIC', 'CAF', 'CF', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 414, 'Centrafricana, Repubblica', '.cf', 'RCA'),
('TD', 'CHAD', 'TCD', 'TD', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 415, 'Ciad', '.td', 'TCH'),
('CL', 'CHILE', 'CHL', 'CL', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 606, 'Cile', '.cl', 'RCH'),
('CN', 'CHINA', 'CHN', 'CN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 314, 'Cinese, Repubblica Popolare', '.cn', ' '),
('CO', 'COLOMBIA', 'COL', 'CO', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 608, 'Colombia', '.co', 'CO'),
('KM', 'COMOROS', 'COM', 'KM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 417, 'Comore', '.km', ' '),
('CG', 'CONGO, REPUBLIC OF', 'COG', 'CG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 418, 'Congo (Repubblica del)', '.cg', 'RCB'),
('CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE (formerly Zaire)', 'COD', 'CD', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 463, 'Congo, Repubblica democratica del (ex Zaire)', '.cd', 'ZRE'),
('CI', 'CÔTE D''IVOIRE (Ivory Coast)', 'CIV', 'CI', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 404, 'Costa d''Avorio', '.ci', 'CI'),
('CR', 'COSTA RICA', 'CRI', 'CR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 513, 'Costa Rica', '.cr', 'CR'),
('CY', 'CYPRUS', 'CYP', 'CY', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 315, 'Cipro', '.cy', 'CY'),
('CZ', 'CZECH REPUBLIC', 'CZE', 'CZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 257, 'Ceca, Repubblica', '.cz', 'CZ'),
('DJ', 'DJIBOUTI', 'DJI', 'DJ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 424, 'Gibuti', '.dj', ' '),
('DM', 'DOMINICA', 'DMA', 'DM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 515, 'Dominica', '.dm', 'WD'),
('DO', 'DOMINICAN REPUBLIC', 'DOM', 'DO', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 516, 'Dominicana, Repubblica', '.do', 'DOM'),
('EC', 'ECUADOR', 'ECU', 'EC', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 609, 'Ecuador', '.ec', 'EC'),
('EG', 'EGYPT', 'EGY', 'EG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 21, 419, 'Egitto', '.eg', 'ET'),
('SV', 'EL SALVADOR', 'SLV', 'SV', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 517, 'El Salvador', '.sv', 'ES'),
('GQ', 'EQUATORIAL GUINEA', 'GNQ', 'GQ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 427, 'Guinea Equatoriale', '.gq', ' '),
('ER', 'ERITREA', 'ERI', 'ER', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 466, 'Eritrea', '.er', ' '),
('EE', 'ESTONIA', 'EST', 'EE', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 247, 'Estonia', '.ee', 'EST'),
('ET', 'ETHIOPIA', 'ETH', 'ET', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 420, 'Etiopia', '.et', 'ETH'),
('FJ', 'FIJI', 'FJI', 'FJ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 703, 'Figi', '.fj', 'FJI'),
('GA', 'GABON', 'GAB', 'GA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 421, 'Gabon', '.ga', 'G'),
('GM', 'GAMBIA, THE', 'GMB', 'GM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 422, 'Gambia', '.gm', 'WAG'),
('GE', 'GEORGIA', 'GEO', 'GE', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 360, 'Georgia', '.ge', 'GE'),
('GH', 'GHANA', 'GHA', 'GH', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 423, 'Ghana', '.gh', 'GH'),
('GD', 'GRENADA', 'GRD', 'GD', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 519, 'Grenada', '.gd', 'WG'),
('GT', 'GUATEMALA', 'GTM', 'GT', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 523, 'Guatemala', '.gt', 'GCA'),
('GN', 'GUINEA', 'GIN', 'GN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 425, 'Guinea', '.gn', 'RG'),
('GW', 'GUINEA-BISSAU', 'GNB', 'GW', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 426, 'Guinea Bissau', '.gw', ' '),
('GY', 'GUYANA', 'GUY', 'GY', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 612, 'Guyana', '.gy', 'GUY'),
('HT', 'HAITI', 'HTI', 'HT', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 524, 'Haiti', '.ht', 'RH'),
('HN', 'HONDURAS', 'HND', 'HN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 525, 'Honduras', '.hn', ' '),
('IN', 'INDIA', 'IND', 'IN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 330, 'India', '.in', 'IND'),
('ID', 'INDONESIA', 'IDN', 'ID', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 331, 'Indonesia', '.id', 'RI'),
('IR', 'IRAN (Islamic Republic of Iran)', 'IRN', 'IR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 332, 'Iran, Repubblica Islamica del', '.ir', 'IR'),
('IQ', 'IRAQ', 'IRQ', 'IQ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 333, 'Iraq', '.iq', 'IRQ'),
('IL', 'ISRAEL', 'ISR', 'IL', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 334, 'Israele', '.il', 'IL'),
('JM', 'JAMAICA', 'JAM', 'JM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 518, 'Giamaica', '.jm', 'JA'),
('JP', 'JAPAN', 'JPN', 'JP', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 326, 'Giappone', '.jp', 'J'),
('JO', 'JORDAN (Hashemite Kingdom of Jordan)', 'JOR', 'JO', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 327, 'Giordania', '.jo', 'HKJ'),
('KZ', 'KAZAKHSTAN', 'KAZ', 'KZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 356, 'Kazakhstan', '.kz', 'KZ'),
('KE', 'KENYA', 'KEN', 'KE', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 428, 'Kenya', '.ke', 'EAK'),
('KI', 'KIRIBATI', 'KIR', 'KI', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 708, 'Kiribati', '.ki', ' '),
('KP', 'KOREA, Democratic People''s Republic of [North] Korea)', 'PRK', 'KP', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 319, 'Corea, Repubblica Popolare Democratica (Corea del Nord)', '.kp', ' '),
('KR', 'KOREA, Republic of [South]', 'KOR', 'KR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 320, 'Corea, Repubblica (Corea del Sud)', '.kr', 'ROK'),
('KW', 'KUWAIT', 'KWT', 'KW', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 335, 'Kuwait', '.kw', 'KWT'),
('KG', 'KYRGYZSTAN', 'KGZ', 'KG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 361, 'Kirghizistan', '.kg', 'KS'),
('LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC', 'LAO', 'LA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 336, 'Laos', '.la', 'LAO'),
('LV', 'LATVIA', 'LVA', 'LV', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 248, 'Lettonia', '.lv', 'LV'),
('LB', 'LEBANON', 'LBN', 'LB', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 337, 'Libano', '.lb', 'RL'),
('LS', 'LESOTHO', 'LSO', 'LS', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 429, 'Lesotho', '.ls', 'LS'),
('LR', 'LIBERIA', 'LBR', 'LR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 430, 'Liberia', '.lr', 'LB'),
('LY', 'LIBYA (Libyan Arab Jamahirya)', 'LBY', 'LY', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 21, 431, 'Libia', '.ly', 'LAR'),
('LT', 'LITHUANIA', 'LTU', 'LT', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 249, 'Lituania', '.lt', 'LT'),
('MK', 'MACEDONIA (Former Yugoslav Republic of Macedonia)', 'MKD', 'MK', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 253, 'Macedonia, Repubblica di', '.mk', 'MK'),
('MG', 'MADAGASCAR', 'MDG', 'MG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 432, 'Madagascar', '.mg', 'RM'),
('MW', 'MALAWI', 'MWI', 'MW', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 434, 'Malawi', '.mw', 'MW'),
('MY', 'MALAYSIA', 'MYS', 'MY', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 340, 'Malaysia', '.my', 'MAL'),
('MV', 'MALDIVES', 'MDV', 'MV', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 339, 'Maldive', '.mv', ' '),
('ML', 'MALI', 'MLI', 'ML', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 435, 'Mali', '.ml', 'RMM'),
('MT', 'MALTA', 'MLT', 'MT', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 227, 'Malta', '.mt', 'M'),
('MH', 'MARSHALL ISLANDS', 'MHL', 'MH', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 712, 'Marshall, Isole', '.mh', ' '),
('MR', 'MAURITANIA', 'MRT', 'MR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 437, 'Mauritania', '.mr', 'RIM'),
('MU', 'MAURITIUS', 'MUS', 'MU', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 438, 'Mauritius', '.mu', 'MS'),
('MX', 'MEXICO', 'MEX', 'MX', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 527, 'Messico', '.mx', 'MEX'),
('FM', 'MICRONESIA (Federated States of Micronesia)', 'FSM', 'FM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 713, 'Micronesia, Stati Federati', '.fm', ' '),
('MD', 'MOLDOVA', 'MDA', 'MD', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 254, 'Moldova', '.md', 'MD'),
('MN', 'MONGOLIA', 'MNG', 'MN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 341, 'Mongolia', '.mn', 'MGL'),
('ME', 'MONTENEGRO', 'MNE', 'ME', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 270, 'Montenegro', '.me', 'MNE'),
('MA', 'MOROCCO', 'MAR', 'MA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 21, 436, 'Marocco', '.ma', 'MA'),
('MZ', 'MOZAMBIQUE (Moçambique)', 'MOZ', 'MZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 440, 'Mozambico', '.mz', 'MOC'),
('MM', 'MYANMAR (formerly Burma)', 'MMR', 'MM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 307, 'Myanmar (ex Birmania)', '.mm', 'BUR'),
('NA', 'NAMIBIA', 'NAM', 'NA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 441, 'Namibia', '.na', 'NAM'),
('NR', 'NAURU', 'NRU', 'NR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 715, 'Nauru', '.nr', 'NAU'),
('NP', 'NEPAL', 'NPL', 'NP', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 342, 'Nepal', '.np', 'NEP'),
('NZ', 'NEW ZEALAND', 'NZL', 'NZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 719, 'Nuova Zelanda', '.nz', 'NZ'),
('NI', 'NICARAGUA', 'NIC', 'NI', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 529, 'Nicaragua', '.ni', 'NIC'),
('NE', 'NIGER ', 'NER', 'NE', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 442, 'Niger', '.ne', 'RN'),
('NG', 'NIGERIA', 'NGA', 'NG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 443, 'Nigeria', '.ng', 'WAN'),
('OM', 'OMAN ', 'OMN', 'OM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 343, 'Oman', '.om', ' '),
('PK', 'PAKISTAN', 'PAK', 'PK', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 344, 'Pakistan', '.pk', 'PK'),
('PW', 'PALAU', 'PLW', 'PW', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 720, 'Palau', '.pw', ' '),
('PS', 'PALESTINIAN TERRITORIES', 'PSE', 'PS', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 324, 'Territori dell''Autonomia Palestinese', '.ps', ' '),
('PA', 'PANAMA', 'PAN', 'PA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 530, 'Panama', '.pa', 'PA'),
('PG', 'PAPUA NEW GUINEA', 'PNG', 'PG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 721, 'Papua Nuova Guinea', '.pg', 'PNG'),
('PY', 'PARAGUAY', 'PRY', 'PY', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 614, 'Paraguay', '.py', 'PY'),
('PE', 'PERU', 'PER', 'PE', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 615, 'Perù', '.pe', 'PE'),
('PH', 'PHILIPPINES', 'PHL', 'PH', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 323, 'Filippine', '.ph', 'RP'),
('QA', 'QATAR', 'QAT', 'QA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 345, 'Qatar', '.qa', 'Q'),
('RO', 'ROMANIA', 'ROU', 'RO', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 235, 'Romania', '.ro', 'RO'),
('RU', 'RUSSIAN FEDERATION', 'RUS', 'RU', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 245, 'Russa, Federazione', '.ru', 'RUS'),
('RW', 'RWANDA', 'RWA', 'RW', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 446, 'Ruanda', '.rw', 'RWA'),
('KN', 'SAINT KITTS AND NEVIS', 'KNA', 'KN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 534, 'Saint Kitts e Nevis', '.kn', ' '),
('LC', 'SAINT LUCIA', 'LCA', 'LC', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 532, 'Saint Lucia', '.lc', 'WL'),
('VC', 'SAINT VINCENT AND THE GRENADINES', 'VCT', 'VC', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 533, 'Saint Vincent e Grenadine', '.vc', 'WV'),
('WS', 'SAMOA (formerly Western Samoa)', 'WSM', 'WS', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 727, 'Samoa', '.ws', 'WS'),
('ST', 'SAO TOME AND PRINCIPE', 'STP', 'ST', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 448, 'São Tomé e Principe', '.st', ' '),
('SA', 'SAUDI ARABIA (Kingdom of Saudi Arabia)', 'SAU', 'SA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 302, 'Arabia Saudita', '.sa', 'SA'),
('SN', 'SENEGAL', 'SEN', 'SN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 450, 'Senegal', '.sn', 'SN'),
('RS', 'SERBIA (Republic of Serbia)', 'SRB', 'RS', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 271, 'Serbia, Repubblica di', '.rs', ' '),
('SC', 'SEYCHELLES', 'SYC', 'SC', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 449, 'Seychelles', '.sc', 'SY'),
('SL', 'SIERRA LEONE', 'SLE', 'SL', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 451, 'Sierra Leone', '.sl', 'WAL'),
('SG', 'SINGAPORE', 'SGP', 'SG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 346, 'Singapore', '.sg', 'SGP'),
('SK', 'SLOVAKIA (Slovak Republic)', 'SVK', 'SK', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, 255, 'Slovacchia', '.sk', 'SK'),
('SB', 'SOLOMON ISLANDS', 'SLB', 'SB', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 725, 'Salomone, Isole', '.sb', ' '),
('SO', 'SOMALIA', 'SOM', 'SO', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 453, 'Somalia', '.so', 'SO'),
('ZA', 'SOUTH AFRICA (Zuid Afrika)', 'ZAF', 'ZA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 454, 'Sud Africa', '.za', 'ZA'),
('ZZ', 'STATELESS', 'ZZZ', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6, 60, 999, 'Apolide', '', ''),
('LK', 'SRI LANKA (formerly Ceylon)', 'LKA', 'LK', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 311, 'Sri Lanka (ex Ceylon)', '.lk', 'CL'),
('SD', 'SUDAN', 'SDN', 'SD', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 21, 455, 'Sudan', '.sd', 'SUD'),
('SR', 'SURINAME', 'SUR', 'SR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 616, 'Suriname', '.sr', 'SME'),
('SZ', 'SWAZILAND', 'SWZ', 'SZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 24, 456, 'Swaziland', '.sz', 'SD'),
('SY', 'SYRIAN ARAB REPUBLIC', 'SYR', 'SY', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 348, 'Siria', '.sy', 'SYR'),
('TW', 'TAIWAN ("Chinese Taipei" for IOC)', 'TWN', 'TW', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 363, 'Taiwan (ex Formosa)', '.tw', ' '),
('TJ', 'TAJIKISTAN', 'TJK', 'TJ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 362, 'Tagikistan', '.tj', 'TJ'),
('TZ', 'TANZANIA', 'TZA', 'TZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 457, 'Tanzania', '.tz', ' '),
('TH', 'THAILAND', 'THA', 'TH', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 349, 'Thailandia', '.th', 'T'),
('TL', 'TIMOR-LESTE (formerly East Timor)', 'TLS', 'TL', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 338, 'Timor Orientale', '.tl', ' '),
('TG', 'TOGO', 'TGO', 'TG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 22, 458, 'Togo', '.tg', 'TG'),
('TO', 'TONGA', 'TON', 'TO', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 730, 'Tonga', '.to', ' '),
('TT', 'TRINIDAD AND TOBAGO', 'TTO', 'TT', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 617, 'Trinidad e Tobago', '.tt', 'TT'),
('TN', 'TUNISIA', 'TUN', 'TN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 21, 460, 'Tunisia', '.tn', 'TN'),
('TR', 'TURKEY', 'TUR', 'TR', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 351, 'Turchia', '.tr', 'TR'),
('TM', 'TURKMENISTAN', 'TKM', 'TM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 364, 'Turkmenistan', '.tm', 'TM'),
('TV', 'TUVALU', 'TUV', 'TV', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 731, 'Tuvalu', '.tv', ' '),
('UG', 'UGANDA', 'UGA', 'UG', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 461, 'Uganda', '.ug', 'EAU'),
('UA', 'UKRAINE', 'UKR', 'UA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 12, 243, 'Ucraina', '.ua', 'UA'),
('AE', 'UNITED ARAB EMIRATES', 'ARE', 'AE', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 322, 'Emirati Arabi Uniti', '.ae', ' '),
('US', 'UNITED STATES', 'USA', 'US', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 41, 536, 'Stati Uniti d''America', '.us', 'USA'),
('UY', 'URUGUAY', 'URY', 'UY', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 618, 'Uruguay', '.uy', 'ROU'),
('UZ', 'UZBEKISTAN', 'UZB', 'UZ', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 32, 357, 'Uzbekistan', '.uz', 'UZ'),
('VU', 'VANUATU', 'VUT', 'VU', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 50, 732, 'Vanuatu', '.vu', ' '),
('VA', 'VATICAN CITY (Holy See)', 'VAT', 'VA', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 13, 246, 'Stato della Città del Vaticano', '.va', 'V'),
('VE', 'VENEZUELA', 'VEN', 'VE', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, 42, 619, 'Venezuela', '.ve', 'YV'),
('VN', 'VIET NAM', 'VNM', 'VN', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 33, 353, 'Vietnam', '.vn', 'VN'),
('IT', 'ITALY', 'ITA', 'IT', 27, 6, 10, 1, 0, 16, 12, 0, 1, 0, 0, 0, 0, 0, 0, 'Italia', '.it', 'I'),
('YE', 'YEMEN (Yemen Arab Republic)', 'YEM', 'YE', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 31, 354, 'Yemen', '.ye', 'YAR'),
('ZM', 'ZAMBIA (formerly Northern Rhodesia)', 'ZMB', 'ZM', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 464, 'Zambia', '.zm', 'RNR'),
('ZW', 'ZIMBABWE', 'ZWE', 'ZW', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 23, 465, 'Zimbabwe (ex Rhodesia)', '.zw', 'ZW');
ALTER TABLE `gaz_anagra` CHANGE `country` `country` VARCHAR( 3 ) NOT NULL ;
ALTER TABLE `gaz_anagra` ADD `counas` VARCHAR( 3 ) NOT NULL AFTER `pronas` ;
INSERT INTO `gaz_menu_module` SELECT MAX(id)+1, '6', 'select_comopril.php', '', '', '7', '', '7'  FROM `gaz_menu_module`;
-- START_WHILE ( questo e' un tag che serve per istruire install.php ad INIZIARE ad eseguire le query seguenti su tutte le aziende dell'installazione)
ALTER TABLE `gaz_XXXclfoco` ADD `op_type` INT NOT NULL AFTER `ritenuta`; 
-- STOP_WHILE( questo e' un tag che serve per istruire install.php a SMETTERE di eseguire le query seguenti su tutte le aziende dell'installazione)
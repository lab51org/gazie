<?php
/* $Id: lang.spanish.php,v 1.10 2011/01/01 11:07:28 devincen Exp $
 --------------------------------------------------------------------------
                            Gazie - Gestione Azienda
    Copyright (C) 2004-2011 - Antonio De Vincentiis Montesilvano (PE)
                                (www.devincentiis.it)
                        <http://gazie.sourceforge.net>
 --------------------------------------------------------------------------
    Questo programma e` free software;   e` lecito redistribuirlo  e/o
    modificarlo secondo i  termini della Licenza Pubblica Generica GNU
    come e` pubblicata dalla Free Software Foundation; o la versione 2
    della licenza o (a propria scelta) una versione successiva.

    Questo programma  e` distribuito nella speranza  che sia utile, ma
    SENZA   ALCUNA GARANZIA; senza  neppure  la  garanzia implicita di
    NEGOZIABILITA` o di  APPLICABILITA` PER UN  PARTICOLARE SCOPO.  Si
    veda la Licenza Pubblica Generica GNU per avere maggiori dettagli.

    Ognuno dovrebbe avere   ricevuto una copia  della Licenza Pubblica
    Generica GNU insieme a   questo programma; in caso  contrario,  si
    scriva   alla   Free  Software Foundation,  Inc.,   59
    Temple Place, Suite 330, Boston, MA 02111-1307 USA Stati Uniti.
 --------------------------------------------------------------------------
*/

/*
 -- TRANSLATED BY : Dante Becerra Lagos (softenglish@gmail.com)
*/

$strScript = array ("admin_aziend.php" =>
                   array(  'title'=>'Administraci&oacute;n de la Empresa',
                           'ins_this'=>'Introduzca la configuraci&oacute;n de la empresa',
                           'upd_this'=>'Cambiar la configuraci&oacute;n de la empresa ',
                           'errors'=>array('Debe introducir un nombre de empresa',
                                           'Debe introducir un sexo',
                                           'Fecha de nacimiento incorrecta',
                                           'Debe introducir una direcci&oacute;n',
                                           'Debe introducir una ciudad',
                                           'Debe introducir una provincia',
                                           'C&oacute;digo de impuestos es formalmente incorrecto',
                                           'C&oacute;digo de impuestos no es un natural',
                                           'El c&oacute;digo IVA es formalmente incorrecto',
                                           'El c&oacute;digo de impuestos no es legal',
                                           'Usted debe proporcionar su c&oacute;digo de impuestos',
                                           'La imagen debe estar en PNG',
                                           'La imagen tiene un tama&ntilde;o mayor a 64kb',
                                           'El color que usted elija tiene una luminosidad de menos de 408 (hex88 +88 +88)',
                                           'Debe introducir una imagen para el logotipo de la empresa'
                                    ),
                           'codice'=>"Codigo ",
                           'ragso1'=>"Nombre de la empresa 1",
                           'ragso2'=>"Nombre de la empresa 2",
                           'image'=>"Logotipo de la empresa<br />(solo formato PNG, aprox. 400x400 max 64kb)",
                           'sedleg'=>"Domicilio legal",
                           'legrap'=>"Representante legal ",
                           'sexper'=>"Genero de la persona jur&iacute;dica ",
                           'sexper_value'=>array(''=>'-','M'=>'Masculino','F'=>'Femenino','G'=>'Legal'),
                           'datnas'=>'Fecha de Nacimiento',
                           'luonas'=>'Lugar de Nacimiento - Pais',
                           'indspe'=>'Dirrecci&oacute;n',
                           'capspe'=>'Codigo postal',
                           'citspe'=>'Ciudad - Provincia',
                           'country'=>'Pais',
                           'telefo'=>'Telefono',
                           'fax'=>'Fax',
                           'codfis'=>'Codigo de Impuesto',
                           'pariva'=>'Codigo de IVA',
                           'rea'=>'R.E.A.',
                           'e_mail'=>'e mail',
                           'web_url'=>'Web url<br />(es: http://companyname.com)',
                           'cod_ateco'=>'Codigo de Actividad (ATECOFIN)',
                           'regime'=>'Regimen de Contabilidad',
                           'regime_value'=>array('0'=>'Ordinario','1'=>'Semplificado'),
                           'decimal_quantity'=>'N&ordm; cantidad decimal',
                           'decimal_quantity_value'=>array(0,1,2,3,9=>'Float'),
                           'decimal_price'=>'N&ordm; precio decimal',
                           'stock_eval_method'=>'Metodo de evaluaci&oacute;n de Stock',
                           'stock_eval_method_value'=>array(0=>'Estandar',1=>'Costo promedio ponderado',2=>'LIFO',3=>'FIFO'),
                           'mascli'=>'Cuenta Maestra de Clientes ',
                           'masfor'=>'Cuenta Maestra de Proveedores',
                           'masban'=>'Cuenta Maestra de Bancos',
                           'cassa_'=>'Cuenta de Efectivo',
                           'ivaacq'=>'Cuenta de IVA Compras',
                           'ivaven'=>'Cuenta de IVA Ventas',
                           'ivacor'=>'Cuenta de IVA Tickets',
                           'ivaera'=>'Cuenta de IVA Tesoreria',
                           'impven'=>'Cuenta de Ventas Tributables',
                           'imptra'=>'Cuenta de Ingresos Transporte',
                           'impimb'=>'Cuenta de Ingresos Empaque',
                           'impspe'=>'Cuenta de Ingresos Cobranza',
                           'impvar'=>'Cuenta de Ingresos Miscellaneos',
                           'boleff'=>'Cuenta de Estampilla',
                           'omaggi'=>'Cuenta de Regalos',
                           'sales_return'=>'Cuenta volver ventas',
                           'impacq'=>'Cuenta de Compras Tributables',
                           'cost_tra'=>'Cuenta de Costos de Transporte',
                           'cost_imb'=>'Cuenta de Costos de Empaque',
                           'cost_var'=>'Cuenta de Costos Miscellaneos',
                           'purchases_return'=>'Cuenta volver compras',
                           'coriba'=>'Portafolio cuenta Ri.Ba',
                           'cotrat'=>'Portfolio cuenta draft',
                           'cocamb'=>'Portfolio cuenta bills',
                           'c_ritenute'=>'Cuenta de retenci&oacute;n',
                           'ritenuta'=>'% Retencion',
                           'upgrie'=>'Ultima pagina de registro resumen de IVA ',
                           'upggio'=>'Ultima pagina de diario',
                           'upginv'=>'Ultima pagina de los inventarios contables',
                           'upgve'=>'Ultimas paginas de registro factura de venta',
                           'upgac'=>'Ultimas paginas de registro factura de compras',
                           'upgco'=>'Ultimas paginas de registro factura Tickets',
                           'sezione'=>'Seccion IVA',
                           'acciva'=>'Porcentaje Avance IVA(%)',
                           'ricbol'=>'Importe de impuesto de timbre sobre ingresos',
                           'perbol'=>'Porcentaje de Estampillas sobre draft (%)',
                           'round_bol'=>'Redondeo de Estampillas',
                           'round_bol_value'=>array(1=>'cent',5=>'cents',10=>'cents',
                                                    50=>'cents',100=>'cents (unidad)'),
                           'sperib'=>'RIBA gastos de recaudaci&oacute;n a ser cargados ',
                           'desez'=>'Descripcion de ',
                           'fatimm'=>'Secci&oacute;n de factura inmediata',
                           'fatimm_value'=>array('R'=>'Seccion Reporte','U'=>'Seccion de la ultima entrada',
                                                 '1'=>'Siempre 1','2'=>'Siempre 2','3'=>'Siempre 3'),
                           'artsea'=>'Buscar art&iacute;culos por',
                           'artsea_value'=>array('C'=>'Codigo','B'=>'Codigo de Barras','D'=>'Descripcion'),
                           'templ_set'=> 'Conjunto de plantillas de los documentos',
                           'colore'=>'Color de fondo de los documentos',
                           'conmag'=>'Registro de Existencias (Stock)',
                           'conmag_value'=>array(0=>'Nunca',1=>'Manual (no recomendado)',2=>'Automatico'),
                           'ivam_t'=>'Frecuencia de pago del IVA',
                           'ivam_t_value'=>array('M'=>'Mensualmente','T'=>'Trimestralmente'),
                           'alliva'=>'Generalmente porcentaje IVA',
                           'interessi'=>'Interes sobre IVA Trimestral'
                         ),
                     "report_aziend.php" =>
                   array(  'title'=>'Lista de las empresa(s) instalada(s)',
                           'ins_this'=>'Crear una nueva empresa',
                           'upd_this'=>'Actualizaci&oacute;n de empresa ',
                           'codice'=>'ID',
                           'ragso1'=>'Nombre de la empresa',
                           'e_mail'=>'Internet',
                           'telefo'=>'Telefono',
                           'regime'=>'Regimen',
                           'regime_value'=>array('0'=>'Ordinario','1'=>'Semplificado'),
                           'ivam_t'=>'Frequencia IVA',
                           'ivam_t_value'=>array('M'=>'Mensualmente','T'=>'Trimestralmente')
                         ),
                     "create_new_enterprise.php" =>
                   array(  'title'=>'Crear una nueva empresa',
                           'errors'=>array('El c&oacute;digo debe estar entre 1 y 999!',
                                           'C&oacute;digo de la compa&ntilde;&iacute;a ya est&aacute; en uso!'
                                          ),
                           'codice'=>'numero ID (codigo)',
                           'ref_co'=>'Empresa de referencia para rellenar los datos',
                           'clfoco'=>'Crear un plano de contabilidad',
                           'users'=>'Permitir a los usuarios de la empresa de referencia ',
                           'clfoco_value'=>array(0=>'No (no recomendado)',
                                                 1=>'S&iacute;, pero sin clientes, proveedores y bancos',
                                                 2=>'S&iacute;, incluyendo clientes, proveedores y bancos'),
                           'base_arch'=>'Rellenando archivo de base',
                           'base_arch_value'=>array(0=>'No (no recomendado)',
                                                    1=>'S&iacute;, pero sin transporte y embalaje',
                                                    2=>'S&iacute;, incluyendo transporte y embalaje')
                         ),
                    "admin_pagame.php" =>
                   array(  "Modalidad; el pago",
                           "C&oacute;digo de pago",
                           "Descripci&oacute;n",
                           "Tipo de Pago",
                           "D&eacute;bito",
                           "Tipo de efecto",
                           "Comenzando el d&iacute;a",
                           "Mes excluido",
                           "Pr&oacute;ximo Mes",
                           "Al d&iacute;a siguiente",
                           "N&uacute;mero de cuotas (tasa)",
                           "Tipo de tasa",
                           "La cuenta bancaria para el abono",
                           "Anotaciones",
                           array('C' => 'efectivo','D' => 'env&iacute;o de remesas directas','B' => 'recibo bancario','R' => 'recibo con sello','T' => 'letra de cambio','V' => 'solicitud de pago'),
                           array('S' => 'Si','N' => 'No'),
                           array('D'=>'fecha de la factura', 'G'=>'d&iacute;a fijado','F'=>'fin de mes'),
                           array('Q' => 'quincenal','M' => 'mensual','B' => 'bimestral','T' => 'trimestral','U' => 'cuatrimestral','S' => 'semestral','A' => 'anual'),
                           "El c&oacute;digo elegido ya est&aacute; en uso!",
                           "La descripci&oacute;n est&aacute; vac&iacute;a!",
                           "El c&oacute;digo debe estar entre 1 y 99",
                           'ins_this'=>'Insertar nueva modalidad el pago'
                           ),
                    "report_aliiva.php" =>
                   array(  'title'=>"Tasas I.V.A.",
                           'ins_this'=>'Inserte nueva tasa IVA',
                           'codice'=>"Codigo",
                           'descri'=>"Descripcion",
                           'type'=>"Tipo",
                           'aliquo'=>"Percentual"
                        ),
                    "admin_aliiva.php" =>
                   array(  "Tasa IVA",
                           "Codigo",
                           "Descripcion",
                           "% tasa",
                           "Nota",
                           "El c&oacute;digo elegido ya se ha utilizado!",
                           "El c&oacute;digo debe estar entre 1 y 99",
                           "La descripci&oacute;n est&aacute; vac&iacute;a!",
                           "% Tasa no valida",
                           "Tipo IVA"
                         ),
                    "admin_banapp.php" =>
                   array(  'title'=>'Administraci&oacute;n de Apoyo de Banco',
                           'ins_this'=>'Inserte nuevo Apoyo de Banco',
                           'upd_this'=>'Actualizar Apoyo de Banco',
                           'errors'=>array('Codigo no valido (min=1 max=99)!',
                                           'El c&oacute;digo elegido ya est&aacute; siendo usado!',
                                           'Ingrese descripcion!',
                                           'Codigo ABI no valido!',
                                           'Codigo CAB no valido!'
                                          ),
                           'codice'=>"Codigo ",
                           'descri'=>"Descripcion ",
                           'codabi'=>"Codigo ABI",
                           'codcab'=>"Codigo CAB ",
                           'locali'=>"Ciudad",
                           'codpro'=>"Region",
                           'annota'=>"Nota",
                           'report'=>'Lista Apoyos de Bancos',
                           'del_this'=>'Apoyos de Bancos'
                         ),
                    "admin_imball.php" =>
                   array(  'title'=>'Administraci&oacute;n de Empaque',
                           'ins_this'=>'Inserte nuevo tipo de empaque',
                           'upd_this'=>'Actualizar empaque',
                           'errors'=>array('Codigo no valido (min=1 max=99)!',
                                           'El c&oacute;digo elegido ya est&aacute; siendo usado!',
                                           'Ingrese descripcion!',
                                           'El peso no puede ser negativo!'
                                          ),
                           'codice'=>"Codigo ",
                           'descri'=>"Descripcion ",
                           'weight'=>"Peso",
                           'annota'=>"Nota",
                           'report'=>'Lista de Empaques',
                           'del_this'=>'empaque'
                         ),
                    "admin_portos.php" =>
                   array(  'title'=>'Administracion de puertos / rendimiento',
                           'ins_this'=>'Inserte nuevo puerto / rendimiento',
                           'upd_this'=>'Actualizar puerto / rendimiento',
                           'errors'=>array('Codigo no valido  (min=1 max=99)!',
                                           'El c&oacute;digo elegido ya est&aacute; siendo usado!',
                                           'Ingrese descripcion!'
                                          ),
                           'codice'=>"Codigo ",
                           'descri'=>"Descripcion ",
                           'annota'=>"Nota",
                           'report'=>'Lista de puertos / rendimiento',
                           'del_this'=>'puertos / rendimiento'
                         ),
                    "admin_spediz.php" =>
                   array(  'title'=>'Gesti&oacute;n de la entrega',
                           'ins_this'=>'Inserte nueva entrega',
                           'upd_this'=>'Actualice entrega',
                           'errors'=>array('Codigo no valido  (min=1 max=99)!',
                                           'El c&oacute;digo elegido ya est&aacute; siendo usado!',
                                           'Ingrese descripcion!'
                                          ),
                           'codice'=>"Codigo ",
                           'descri'=>"Descripcion ",
                           'annota'=>"Nota",
                           'report'=>'Lista de entregas',
                           'del_this'=>'entrega'
                         ),
                    "report_banche.php" =>
                   array(  'title'=>"Cuentas Bancarias",
                           'ins_this'=>'Inserte nueva Cuenta Bancaria',
                           'msg'=>array('EXISTENTES DE CUENTA BANCARIA EN SOLO PLAN DE CUENTAS','Ver e/o imprimir los libros de contabilidad'),
                           'codice'=>"Codigo",
                           'ragso1'=>"Nome",
                           'iban'=>"Codigo IBAN",
                           'citspe'=>"Ciudad",
                           'prospe'=>"Prov.",
                           'telefo'=>"Telefono"
                        ),
                    "admin_bank_account.php" =>
                   array(  "Cuenta Bancaria ",
                           "Numero Codigo (desde plan de contabilidad) ",
                           "Descripcion ",
                           "Credito Bancario (elegir en lugar de la descripci&oacute;n)",
                           "Dirreci&oacute;n ",
                           "Codigo Postal ",
                           "Ciudad - Codigo de Pais ",
                           "Nacion ",
                           "Codigo IBAN ",
                           "Sede ",
                           "Telefono ",
                           "Fax ",
                           "e-mail ",
                           "Nota ",
                           "Las cuentas del plan no tiene los bancos maestros!",
                           "En la configuraci&oacute;n de la empresa no se seleccion&oacute; bancos maestros!",
                           "IBAN es incorrecto!",
                           "Codigo existente!",
                           "Codigo menor que 1!",
                           "Descripcion vacia!",
                           "La naci&oacute;n es incompatible con el IBAN!"),
                    "admin_vettore.php" =>
                    array( 'title'=>' Administracion de transportistas',
                           'ins_this'=>'Introduzca un nuevo transportista',
                           'upd_this'=>'Modificar el transportista n.',
                           'errors'=>array('No se incluy&oacute; el nombre de la empresa',
                                           'No se incluy&oacute; la direcci&oacute;n',
                                           'La ciudad no fue incluida',
                                           'No se ha insertado el c&oacute;digo postal',
                                           'El c&oacute;digo de impuestos es formalmente incorrecto',
                                           'El IVA es formalmente incorrecto',
                                           'No se incluy&oacute; el IVA'
                                          ),
                           'codice'=>"Codigo ",
                           'ragione_sociale'=>'Nombre de la empresa',
                           'indirizzo'=>'Direcci&oacute;n',
                           'cap'=>'CAP',
                           'citta'=>'Ciudad',
                           'provincia'=>'Region',
                           'partita_iva'=>'Registro de IVA',
                           'codice_fiscale'=>'C&oacute;digo Tributario',
                           'n_albo'=>'Muchas inscripciones conductores albo',
                           'telefo'=>'Entrega telefonica',
                           'descri'=>'Otras descripciones',
                           'annota'=>'Anotaciones',
                           'report'=>'Lista de transportistas',
                           'del_this'=>'transportista'
                           ),
                    "admin_utente.php" =>
                   array(  'title'=>'Administraci&oacute;n de Usuarios',
                           'ins_this'=>'Inserte nuevo Usuario',
                           'upd_this'=>'Actualizar Usuario',
                           'errors'=>array('El Pseud&oacute;nimo elegido ya est&aacute; siendo usado!',
                                           'Ingrese apellido!',
                                           "Ingrese Pseud&oacute;nimo!",
                                           "Ingrese la contrase&ntilde;a!",
                                           "La contrase&ntilde;a no es suficientemente largo!",
                                           "La contrase&ntilde;a es diferente de la de la confirmaci&oacute;n!",
                                           "No puede aumentar su nivel de competencia de la operaci&oacute;n est&aacute; reservado para el administrador!",
                                           "El archivo debe estar en formato JPG",
                                           "La imagen no debe ser mayor de 10 KB",
                                           "No se puede tener un nivel inferior al 9 porque es el administrador del pasado!"
                                          ),
                           'Login'=>"Pseud&oacute;nimo",
                           'Cognome'=>"Apellido",
                           'Nome'=>"Nombre",
                           'image'=>'Icon de lo usuario<br />(solo formato JPG, max 10kb)',
                           'Abilit'=>"Nivel",
                           'Access'=>"Acceso",
                           'pre_pass'=>'Contrase&ntilde;a (min.',
                           'post_pass'=>'caracteres)',
                           'rep_pass'=>'Repite la Contrase&ntilde;a',
                           'lang'=>'Idioma',
                           'style'=>'Tema / estilo',
                           'mod_perm'=>'Permiso de m&oacute;dulos',
                           'report'=>'Lista de Usuarios',
                           'del_this'=>'Usuario',
                           'del_err'=>'No se puede eliminar porque eres el &uacute;nico con derechos de administrador!'
                         )
                    );
?>
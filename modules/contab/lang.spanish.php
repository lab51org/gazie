<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2013 - Antonio De Vincentiis Montesilvano (PE)
                                (www.devincentiis.it)
                        <http://gazie.it>
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

$strScript = array ("select_liqiva.php" =>
                    array( 'title'=>'Seleccione vista y/o impresion de Liquidacion de IVA Periodica',
                           'errors'=>array('La fecha esta incorrecta!',
                                           'La fecha de inicio no puede ser posterior a la fecha de termino!'
                                          ),
                           'page_ini'=>'N. pagina de inicio',
                           'sta_def'=>'Impresion Final',
                           'sta_def_title'=>'Si se selecciona cambia el valor de la ultima pagina impresa de la configuracion registro de esta empresa',
                           'descri'=>'Descripcion',
                           'descri_value'=>array('M'=>'de  ','T'=>'el cuarto '
                                           ),
                           'date_ini'=>'Fecha de Inicio  ',
                           'sem_ord'=>' Regimen ',
                           'sem_ord_value'=>array(0=>' Contabilidad Ordinaria  ',1=>' Contabilidad Semplificada '
                                           ),
                           'cover'=>'Imprimir Portada',
                           'date_fin'=>'Fecha de Termino ',
                           'header'=>array('Seccion'=>'','Registro'=>'','Seleccionar descripcion'=>'','Tributable'=>'',
                                           'Seleccionar'=>'','Impuesto'=>'','No deducible'=>'','Cantidad'=>''
                                           ),
                           'regiva_value'=>array(0=>'Ninguno',2 =>'Facturas de Venta',4=>'Tickets de Venta',6=>'Facturas de Compra'),
                           'of'=>' de ',
                           'tot'=>' cantidad ',
                           't_pos'=>' IVA DEBITO',
                           't_neg'=>' IVA CREDITO',
                           'carry'=>'Credito de periodo previo'
                           ),
                    "stampa_liqiva.php" =>
                    array( 'title'=>'Liquidacion IVA Periodico ',
                           'cover_descri'=>'Libro de res&uacute;menes de IVA del a&ntilde;o ',
                           'page'=>'Pagina',
                           'sez'=>'Seccion',
                           'regiva_value'=>array(0=>'Ninguna',2 =>'Registrar Facturas de Venta',4=>'Registrar de Tickets de Venta',6=>'Registrar Facturas de Compra'),
                           'code'=>'Codigo',
                           'descri'=>'Descripcion de tasa IVA',
                           'imp'=>'Tributable',
                           'iva'=>'Impuesto',
                           'rate'=>'%',
                           'ind'=>'No Deducible',
                           'tot'=>'Cantidad',
                           't_reg'=>'IVA total del registro ',
                           't_pos'=>' IVA DEBITO ',
                           't_neg'=>' IVA CREDITO ',
                           'inter'=>'Aumento de interes ',
                           'pay'=>' a pagare',
                           'carry'=>'Credito de periodo previo',
                           'pay_date'=>'Pagado en ',
                           'co'=>'a las ',
                           'abi'=>'Cod.Banca',
                           'cab'=>'Cod.Agencia'
                           ),
                    "select_partit.php" =>
                    array( 'title'=>'Seleccione vista y/o impresion entradas de cuentas libro mayor',
                           'mesg'=>array('La busqueda no produjo resultados!',
                                         'Inserte al menos 2 caracteres!',
                                         'Cambiando cliente / proveedor'
                                          ),
                           'errors'=>array('La fecha es incorrecta!',
                                           'La fecha de inicio de las entradas del libro mayor no pueden ser impresas despues de la ultima !',
                                           'The date of the press can not be earlier than the last ledger entries!',
                                           'La cuenta inicial no puede ser posterior a la final!',
                                           'No hay movimientos seleccionados'
                                          ),
                           'date'=>'Imprimir Fecha ',
                           'master_ini'=>'Iniciar Cuenta Maestra',
                           'account_ini'=>'Iniciar Sub Cuenta',
                           'date_ini'=>'Fecha de Inicio  ',
                           'master_fin'=>'Terminar Cuenta Maestra ',
                           'account_fin'=>'Terminar Sub Cuenta ',
                           'date_fin'=>'Fecha de Termino ',
                           'selfin' => 'Copia cuenta iniciar',
                           'header1'=>array('Cuenta'=>'','Num.Mov.'=>'','Descripcion'=>'',
                                            'Deuda'=>'','Credito'=>'','Progressivo<br />balance'=>''
                                           ),
                           'header2'=>array('Fecha'=>'','ID'=>'','Descripcion'=>'','N.Doc.'=>'',
                                            'Fecha Doc.' =>'','Credito'=>'','Deuda'=>'',
                                            'Progressivo<br />balance'=>''
                                           )
                           ),
                    "admin_caucon.php" =>
                    array( 'title'=>'Administracion de Causal de Contabilidad ',
                           'ins_this'=>'Ingresar nueva Causal de Contabilidad ',
                           'upd_this'=>'Actualizar de Causal de Contabilidad',
                           'mesg'=>array('La busqueda no produjo resultados!',
                                         'Inserte al menos 2 caracteres!',
                                         'Cambiando cliente / proveedor'
                                          ),
                           'errors'=>array('Ingrese un codigo valido!',
                                           'Ingrese una descripcion!',
                                           'Codigo existente use el procedimiento apropiado si desea cambiar!',
                                           'Debe definir al menos una cuenta!',
                                           'Codigo reservado para Cuentas de CIERRE AUTOMATICO!',
                                           'Codigo reservado para Cuentas de APERTURA AUTOMATICO!'
                                          ),
                           'head'=>'Cuentas a ser movidas ',
                           'codice'=>'Codigo causal *',
                           'descri'=>'Descripcion *',
                           'insdoc'=>'Documento de Referencia de Entrada de Datos',
                           'insdoc_value'=>array(0=>'No',1=>'Si'),
                           'regiva'=>'Registro IVA',
                           'regiva_value'=>array(0=>'Ninguno',2 =>'Factura de venta',4=>'Tickets',6=>'Factura de compra'),
                           'operat'=>'Operador',
                           'operat_value'=>array(0=>'No',1=>'Sumar',2=>'Restar'),
                           'contr'=>'Cuenta (min. 1) *',
                           'tipim'=>'Tipo de cantidad ',
                           'tipim_value'=>array(''=>'','A'=>'Total','B'=>'Tributable','C'=>'Impuesto'),
                           'daav'=>'DEBITOS/CREDITOS',
                           'daav_value'=>array('D'=>'DEBITOS','A'=>'CREDITOS'),
                           'report'=>'Listar de las Causal de Contabilidad',
                           'del_this'=>'Causal de Contabilidad '
                           ),
                    "admin_piacon.php" =>
                   array(  'title'=>'Administrar plan de cuentas',
                           'ins_this'=>'Insertar cuenta',
                           'upd_this'=>'Actualizar cuenta',
                           'errors'=>array('Ingrese un codigo valido!',
                                           'Codigo existente use el procedimiento apropiado si desea cambiar!',
                                           'Debe ingresar una descripcion!'
                                          ),
                           'codice'=>"Codigo ",
                           'mas'=>"Maestro",
                           'sub'=>"Subcuenta",
                           'descri'=>"Descripcion",
                           'ceedar'=>"Reclassificacion de hoja de balance EEC / DEBITOS",
                           'ceeave'=>"Reclassificacion de hoja de balance EEC / CREDITOS",
                           'annota'=>"Note"
                         ),
                   "admin_movcon.php" =>
                  array( 'title'=>'Administraci&oacute;n de entradas de libro contabilidad',
                         'ins_this'=>'Insertar nuevas entradas de libro contabilidad',
                         'upd_this'=>'Actualizar nuevas entradas de libro contabilidad',
                         'mesg'=>array('La b&uacute;squeda no di&oacute; resultados!',
                                         'Inserte al menos 2 caracteres!',
                                         'Cambiando cliente/ proveedor'
                                        ),
                         'errors'=>array('Al menos una fila no tiene cuentas!',
                                         'Al menos una fila tiene valor cero!',
                                         'La entrada de contabilidad est&aacute; descuadrada!',
                                         'Total de las filas DEUDA no debe ser cero!',
                                         'Total de las filas CREDITO no debe ser cero!',
                                         'La entrada IVA es cero!',
                                         'La entrada IVA tiene una cantidad diferente de la entrada de contabiliodad!',
                                         'Debe insertar una descripci&oacute;n!',
                                         'La fecha de registro es incorrecta!',
                                         'La fecha del documento es incorrecta!!',
                                         'Ha olvidado colocar el n&uacute;mero de registro!',
                                         'Ha olvidado colocar el n&uacute;mero de documento!',
                                         'La fecha del documento no debe ser posterior a la del registro!',
                                         'ADVERTENCIA que est&aacute; editando un movimiento que involucra un registro de IVA!',
                                         'Usted está tratando de grabar un documento que ya está registrado'
                                        ),
                         'id_testata'=>'Nuacute;mero de entrada',
                         'date_reg'=>'Fecha de registro',
                         'descri'=>'Descripci&oacute;n',
                         'caucon'=>'Causal de Contabilidad',
                         'v_caucon'=>'Confirmar Causal!',
                         'insdoc'=>'Detalles del documento de referencia',
                         'insdoc_value'=>array(0=>'Si',1=>'No'),
                         'regiva'=>'Registro de IVA',
                         'regiva_value'=>array(0=>'Ninguno',2 =>'Facturas ventas',4=>'Impuesto de Recibos',6=>'Facturas compras'),
                         'operat'=>'Operador',
                         'operat_value'=>array(0=>'No',1=>'Sumar',2=>'Restar'),
                         'date_doc'=>'Fecha de Documento',
                         'seziva'=>'Secci&oacuten IVA',
                         'protoc'=>'N&uacute;mero de registro',
                         'numdoc'=>'Numero',
                         'partner'=>'Cliente / Proveedor',
                         'insiva'=>'Entrada IVA',
                         'vat'=>'Tasa IVA',
                         'taxable'=>'Tributable',
                         'tax'=>'Impuesto',
                         'mas'=>"Maestro",
                         'sub'=>"Cuenta",
                         'amount'=>'Cantidad',
                         'daav'=>'DEUDA/CREDITO',
                         'daav_value'=>array('D'=>'DEUDA','A'=>'CREDITO'),
                         'bal_title'=>"Balance respecto a este valor!",
                         'bal'=>"Cuadrado",
                         'addval'=>"Aumentar el valor de ",
                         'subval'=>"Disminuir el valor de ",
                         'zero'=>"Las entradas de contabilidad son cero!",
                         'diff'=>"Diferencia",
                         'tot_d'=>'Total DEBE',
                         'tot_a'=>'Total HABER',
                         'visacc'=>'Ver los libros de contabilidad',
                         'report'=>'Listar de las entradas de libros de contabilidad',
                         'del_this'=>'Entradas de libros de contabilidad',
                           'sourcedoc'=>'Fuente del documento',
                           'source'=>'Fluente'
                         ),
                    "report_piacon.php" =>
                   array(  'title'=>'Plan de cuentas',
                           'ins_this'=>'Inserte nueva cuenta',
                           'view_this'=>'Ver y/o imprimir reporte de cuenta',
                           'print_this'=>'Imprimir el plan de cuentas',
                           'header'=>array('Maestro'=>'','Cuenta'=>'','Descripcion'=>'','Debitos'=>'',
                                            'Creditos'=>'','Balance'=>'','Ver<br />y/o imprimir'=>'',
                                            'Borrar'=>''),
                           'msg1'=>'Recuerde que Ud debe ser admin para introducir las actividades entre 100 y 199, entre 200 y 299 pasivos, costos entre 300 y 399, ingreso entre 400 y 499 y las cuentas memorandum o transitorias entre 500 y 599',
                           'msg2'=>'Balances para un a&ntilde;o '
                         ),
                    "select_regiva.php" =>
                    array( 'title'=>'Seleccione vista previa y/o impresion de registro IVA',
                           'errors'=>array('Fecha Incorrecta!',
                                           'La fecha de inicio no puede ser posterior a la fecha de termino !',
                                           'P'=>'La secuencia de numeros de protocolo no es correcta',
                                           'N'=>'La secuencia de numeros de documentos no es correcta',
                                           'T'=>'Hay un movimiento sin tasa de IVA',
                                           'err'=>'Hay algunos errores que no justifican la impresion de of the register'
                                          ),
                           'vat_reg'=>'VAT register print:',
                           'vat_reg_value'=>array(2=>'Sale invoices',4=>'Charges',6=>'Purchase invoices'),
                           'vat_section'=>'VAT section ',
                           'page_ini'=>'N. pagina de inicio',
                           'sta_def'=>'Impresion Final',
                           'sta_def_title'=>'Si se selecciona cambia el valor de la ultima pagina impresa de la configuracion registro de esta empresa',
                           'descri'=>'Description',
                           'descri_value'=>array('M'=>' of the month ','T'=>'of the quarter '
                                           ),
                           'date_ini'=>'Start date entry  ',
                           'sem_ord'=>' Accounting system ',
                           'sem_ord_value'=>array(0=>' Ordinary ',1=>' Simplified '
                                           ),
                           'cover'=>'Print the cover',
                           'date_fin'=>'End date entry',
                           'header'=>array('Protocol'=>'','Date - ID movement'=>'','Document description'=>'','Customer or Supplier'=>'',
                                            'Taxable' =>'','VAT rate'=>'','Tax'=>''
                                           ),
                           'of'=>' of the ',
                           'tot'=>' TOTAL',
                           't_gen'=>' GENERAL'
                           ),
                    "select_libgio.php" =>
                    array( 'title'=>'Seleccione vista previa y/o impresion de Libro Mayor',
                           'errors'=>array('Fecha de inicio incorrecta!',
                                           'Fecha de termino incorrecta!',
                                           'La fecha de inicio no puede ser posterior a la fecha de termino !'
                                          ),
                           'pagini'=>'N. pagina de inicio',
                           'stadef'=>'Impresion Final',
                           'stadef_title'=>'Si se selecciona cambia el valor de la ultima pagina impresa de la configuracion registro de esta empresa',
                           'date_ini'=>'Fecha de Inicio ',
                           'cover'=>' Imprimir Portada -> ',
                           'date_fin'=>'Fecha de Termino ',
                           'valdar'=>'DEBE (inicio)',
                           'valave'=>'HABER (inicio)',
                           'nrow'=>'N&uacute;mero de filas:',
                           'tot_a'=>' Total HABER ',
                           'tot_d'=>' Total DEBE '
                           )
                    );

?>
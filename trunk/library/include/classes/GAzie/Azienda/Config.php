<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2018 - Antonio De Vincentiis Montesilvano (PE)
  (http://www.devincentiis.it)
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
  scriva   alla   Free  Software Foundation, 51 Franklin Street,
  Fifth Floor Boston, MA 02110-1335 USA Stati Uniti.
  --------------------------------------------------------------------------
 */

namespace GAzie\Azienda;

/**
 * Class for manage azienda table
 */
class Config extends \Database\Table {

	public function __construct( $id = NULL ) {
		parent::__construct('company_config');
		$this->load( $id );
	}
	
	/**
	 * Getting variable into config
	 *
	 */
	public function getVar(string $var) {
		$sql = $this->query->select()
			->from( $this->getTableName() )
		 	->where("`var`='$var'");
		$result = $sql->execute($sql);
		if ( count($result) == 1 ) {
			$this->importResult( $result );
		}
	}
	

	public function exist() {
		// Controllo se esiste l'azioneda
		$sql = $this->query->select()
			->from( $this->getTableName() )
			->where(  "`codice` = '" . $this->getCodice() . "'");  
		return  $this->execute($sql)->count() > 0;
	}

}


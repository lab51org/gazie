<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2020 - Antonio De Vincentiis Montesilvano (PE)
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

namespace Database\ORM;


/**
 *  Class for creation query
 *  from databasae
 *
 */
class Update extends Query {

	private $_where;
	
	public function write() {
		if ( empty( $this->getColumns() ) || ! $this->getColumns() )
			return '';

		if ( ! $this->getTable() )
			return '';
	   
		foreach( $this->getColumns() as $k => $v ) {
                        $f[] = "`$k` = '".$this->escape($v)."'";
                }
                $query = "UPDATE `".$this->getTable()."` SET  " . implode(',',$f);
                if ( $this->_where )
                        $query .= " WHERE $this->_where";
                return $query;
	
	}
    
	public function where( $where ) {
		$this->_where = $where;
		return $this;
        }

}


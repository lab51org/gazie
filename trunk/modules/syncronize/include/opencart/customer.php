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

namespace Opencart;

class Customer {
	
  private $customer_id;
  private $customer_group_id;
  private $store_id;
  private $firstname;
  private $lastname;
  private $email;
  private $telephone;
  private $fax;
  private $password;
  private $salt;
  private $cart;
  private $wishlist;
  private $newsletter;
  private $address_id;
  private $custom_field;
  private $ip;
  private $status;
  private $approved;
  private $safe;
  private $token;
  private $date_added;


  public function __construct( ) {
  }

  /**
   * Return a list of customer from api
   */
  public static function list_from_array( array $l ) {
	  $r = [];
	  foreach ($l as $c ) {
		  $customer = new Customer;
		  $customer->setCustomerId($c['customer_id']);
		  $customer->setStoreId($c['store_id']);
		  $customer->setDataAnagr($c['firstname'],$c['lastname'],$c['email'],$c['telephone'],$c['fax']);
		  $r[] = $customer;
	  }
	  return  $r;
  }

  public function setCustomerId( $customer_id ) {
	$this->customer_id = $customer_id;
  }

  public function setStoreId( $store_id ) {
	$this->store_id = $store_id;
  }

  public function setDataAnagr( $firstname, $lastname, $email, $telephone = NULL, $fax=NULL) {
	$this->firstname = $firstname;
	$this->lastname = $lastname;
	$this->email = $email;
	$this->telephone = $telephone;
	$this->fax = $fax;
  }

  public function getCustomerId() {
	return $this->customer_id;
  }

  public function getStoreId() {
	return $this->store_id;
  }

  public function getFirstname() {
	return $this->firstname;
  }

  public function getLastname() {
	return $this->lastname;
  }

  public function getTelephone() {
	return $this->telephone;
  }

  public function getEmail() {
	return $this->email;
  }

}
?>

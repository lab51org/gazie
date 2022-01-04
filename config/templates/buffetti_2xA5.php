<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2022 - Antonio De Vincentiis Montesilvano (PE)
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
 
// Antonio Germani
// ATTENZIONE!! QUESTO TEMPLATE è STUDIATO PER IL MODULO RICEVUTE FISCALI DELLA DITTA BUFFETTI ART.8205L2000

require('../../library/tcpdf/tcpdf.php');
require('../../library/tcpdf/tcpdi.php');

class Template_2xA5 extends TCPDI {

    function setVars(&$docVars, $Template = '') {
        $this->docVars = & $docVars;
        $this->gaz_path = '../../';
        $this->rigbro = $docVars->gTables['rigbro'];
        $this->aliiva = $docVars->gTables['aliiva'];
        $this->tesdoc = $docVars->tesdoc;
        $this->testat = $docVars->testat;
        $this->pagame = $docVars->pagame;
        $this->banapp = $docVars->banapp;
        $this->banacc = $docVars->banacc;
        $this->logo = $docVars->logo;
        $this->link = $docVars->link;
        $this->intesta1 = $docVars->intesta1;
        $this->intesta1bis = $docVars->intesta1bis;
        $this->intesta2 = $docVars->intesta2;
        $this->intesta3 = $docVars->intesta3 . $docVars->intesta4;
        $this->intesta4 = $docVars->codici;
        $this->colore = $docVars->colore;
        $this->decimal_quantity = $docVars->decimal_quantity;
        $this->decimal_price = $docVars->decimal_price;
        $this->perbollo = $docVars->perbollo;
        $this->codice_partner = $docVars->codice_partner;
        $this->descri_partner = $docVars->descri_partner;
        $this->cod_univoco = $docVars->cod_univoco;
        $this->cliente1 = $docVars->cliente1;
        $this->cliente2 = $docVars->cliente2;
        $this->cliente3 = $docVars->cliente3;
        $this->cliente4 = $docVars->cliente4;  // CAP, Città, Provincia
        $this->cliente4b = $docVars->cliente4b; // Nazione
        $this->cliente5 = $docVars->cliente5;  // P.IVA e C.F.
        $this->agente = $docVars->name_agente;
        $this->destinazione = $docVars->destinazione;
        $this->clientSedeLegale = '';
        if (!empty($docVars->clientSedeLegale)) {
            foreach ($docVars->clientSedeLegale as $value) {
                $this->clientSedeLegale .= $value . ' ';
            }
        }
        $this->c_Attenzione = $docVars->c_Attenzione;
        $this->min = $docVars->min;
        $this->ora = $docVars->ora;
        $this->day = $docVars->day;
        $this->month = $docVars->month;
        $this->year = $docVars->year;
        $this->withoutPageGroup = $docVars->withoutPageGroup;
		$this->descriptive_last_row = $docVars->descriptive_last_row;
    }

    function Header() {
            $this->SetFillColor(hexdec(substr($this->colore, 0, 2)), hexdec(substr($this->colore, 2, 2)), hexdec(substr($this->colore, 4, 2)));
			// INTESTAZIONE 1
			$this->sety(7);
            $this->SetFont('times', 'B', 11);
            $this->Cell(80, 6, $this->intesta1, 0, 0, 'L');
			$this->Cell(70);
            $this->Cell(80, 6, $this->intesta1, 0, 1, 'L');
            $this->SetFont('helvetica', '', 7);
            $interlinea = 14;
            if (!empty($this->intesta1bis)) {
                $this->Cell(80, 4, $this->intesta1bis, 0, 0, 'L');
				$this->Cell(70);
                $this->Cell(80, 4, $this->intesta1bis, 0, 1, 'L');
                $interlinea = 10;
            }
			
			$y = $this->gety();
			$this->SetFont('helvetica', '', 12);
			$this->multiCell(8, 5, 'XX', 0, 'L', 0, 0,89,5);
			$this->multiCell(8, 5, 'XX', 0, 'L', 0, 1,245,5);
			$this->multiCell(10, 5, $this->tesdoc['numdoc'].'/'.$this->tesdoc['seziva'], 0, 'L', 0, 0,115,24);
			$this->multiCell(10, 5, $this->tesdoc['numdoc'].'/'.$this->tesdoc['seziva'], 0, 'L', 0, 1,265,24);
			$this->SetFont('helvetica', '', 10);
			$this->multiCell(30, 5, $this->datdoc, 0, 'L', 0, 0,110,33);
			$this->multiCell(30, 5, $this->datdoc, 0, 'L', 0, 1,260,33);
		
			$this->sety($y);
			$this->SetFont('helvetica', '', 10);
            $this->Cell(50, 4, $this->intesta2, 0, 0, 'L',0,'',1);// indirizzo
			
			$this->Cell(100);
            $this->Cell(50, 4, $this->intesta2, 0, 1, 'L',0,'',1);
			
            $this->Cell(50, 2, $this->intesta3, 0, 0, 'L',0,'',1);// telefono email
			$this->Cell(100);
            $this->Cell(50, 2, $this->intesta3, 0, 1, 'L',0,'',1);
            $this->Cell(50, 4, $this->intesta4, 0, 0, 'L',0,'',1);// codice fiscale piva
			$this->Cell(100);
            $this->Cell(50, 4, $this->intesta4, 0, 1, 'L',0,'',1);
            $this->Image('@' . $this->logo, 61, 12, 25, 0, '', $this->link);
            $this->Image('@' . $this->logo, 211, 12, 25, 0, '', $this->link);
            $this->Ln($interlinea);
            $this->SetFont('helvetica', '', 9);
            
			$this->Cell(70);
            
			$this->Cell(70);
            /*
            if ($this->tesdoc['tipdoc'] == 'NOP' || $this->withoutPageGroup) {
                $this->Cell(170, 5);
            } else {
                $this->Cell(20, 5, 'Pag. ' . $this->getGroupPageNo() . ' di ' . $this->getPageGroupAlias(), 0, 0, 'L');
				$this->Cell(130);
                $this->Cell(20, 5, 'Pag. ' . $this->getGroupPageNo() . ' di ' . $this->getPageGroupAlias(), 0, 0, 'L');
            }
			*/
            $this->Ln(2);
            $interlinea = $this->GetY();
            
            $this->SetFont('helvetica', '', 7);
			/*
            if (!empty($this->destinazione)) {
	            $start_destinazione = $this->GetY();
                if (is_array($this->destinazione)) { //quando si vuole indicare un titolo diverso da destinazione si deve passare un array con titolo index 0 e descrizione index 1
                    $this->Cell(60, 5, $this->destinazione[0], 'LTR', 0, 'L', 1);
					$this->Cell(90);
                    $this->Cell(60, 5, $this->destinazione[0], 'LTR', 1, 'L', 1);
                    $this->MultiCell(60, 4, $this->destinazione[1], 'LBR', 'L');
					$this->SetXY(160, $start_destinazione + 5);
                    $this->MultiCell(60, 4, $this->destinazione[1], 'LBR', 'L');
                } else {
                    $this->Cell(60, 5, "Destinazione :", 'LTR', 0, 'L', 1);
					$this->Cell(90);
                    $this->Cell(60, 5, "Destinazione :", 'LTR', 1, 'L', 1);
                    $this->MultiCell(60, 4, $this->destinazione, 'LBR', 'L');
					$this->SetXY(160, $start_destinazione + 5);
                    $this->MultiCell(60, 4, $this->destinazione, 'LBR', 'L');
                }
            }
			*/
			/*
			if ($this->codice_partner > 0){
				$this->SetXY(28, $interlinea - 5);
				$this->Cell(10, 4, $this->descri_partner, 'LT', 0, 'R', 1);
				$this->Cell(55, 4, ': ' . $this->cliente5, 'TR', 1);
				$this->Cell(18);
				$this->Cell(18, 4, ' cod.: ' . $this->codice_partner, 'LB', 0, 'L');
				$this->Cell(30, 4, ' cod.univoco: ' . $this->cod_univoco, 'RB', 0, 'L');
				$this->Cell(17, 4, '', 'T');
				$this->SetXY(178, $interlinea - 5);
				$this->Cell(10, 4, $this->descri_partner, 'LT', 0, 'R', 1);
				$this->Cell(55, 4, ': ' . $this->cliente5, 'TR', 1);
				$this->Cell(168);
				$this->Cell(18, 4, ' cod.: ' . $this->codice_partner, 'LB', 0, 'L');
				$this->Cell(30, 4, ' cod.univoco: ' . $this->cod_univoco, 'RB', 0, 'L');
				$this->Cell(17, 4, '', 'T');

			}
			*/
			
            $this->SetFont('helvetica', '', 15);
            
            $this->Cell(130, 0, 'Egr. ' . $this->cliente1 ." ". $this->cliente2, 0, 0, 'L', 0, '', 1);
			$this->Cell(20);
            
            $this->Cell(130, 0, 'Egr. ' .$this->cliente1 ." ". $this->cliente2, 0, 1, 'L', 0, '', 1);
            
           
           
            $this->Cell(130, 10, $this->cliente3, 0, 0, 'L', 0, '', 1);//indirizzo
            $this->Cell(20);
            $this->Cell(130, 10, $this->cliente3, 0, 1, 'L', 0, '', 1);
            
            $this->Cell(130, 8, $this->cliente4 ." ". $this->cliente4b, 0, 0, 'L', 0, '', 1); // località
            $this->Cell(20);
            $this->Cell(130, 8, $this->cliente4 ." ". $this->cliente4b, 0, 1, 'L', 0, '', 1);
           
           
			/*
            if (!empty($this->c_Attenzione)) {
                $this->SetFont('helvetica', '', 10);
                $this->Cell(80, 8, 'alla C.A.', 0, 0, 'R');
                $this->Cell(55, 8, $this->c_Attenzione, 0, 1, 'L', 0, '', 1);
            }
            $this->SetFont('helvetica', '', 7);
			*/
			/*
            if (!empty($this->clientSedeLegale)) {
                $this->Cell(80, 8, 'Sede legale: ', 0, 0, 'R');
                $this->Cell(55, 8, $this->clientSedeLegale, 0, 0, 'L', 0, '', 1);
                $this->Cell(12);
                $this->Cell(80, 8, 'Sede legale: ', 0, 0, 'R');
                $this->Cell(55, 8, $this->clientSedeLegale, 0, 1, 'L', 0, '', 1);
            } else {
                $this->Ln(4);
            }
			*/
			$this->Line(149, 5, 149, 205, array('dash' => '5,5','width' => 0.2,'color' => array(150, 150, 150))); // linea mediana di perforazione
    }

}

?>
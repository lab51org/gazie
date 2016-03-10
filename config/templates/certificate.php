<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2016 - Antonio De Vincentiis Montesilvano (PE)
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
require('template.php');

class Certificate extends Template {

    function setTesDoc() {
        $this->tesdoc = $this->docVars->tesdoc;
        $this->giorno = substr($this->tesdoc['datemi'], 8, 2);
        $this->mese = substr($this->tesdoc['datemi'], 5, 2);
        $this->anno = substr($this->tesdoc['datemi'], 0, 4);
        if ($this->tesdoc['tipdoc'] == 'FAD' || substr($this->tesdoc['tipdoc'], 0, 2) == 'DD') {
            $descri = ' D.d.T. n.';
        } else {
            $descri = ' Fattura n.';
        }
        $this->tipdoc = "Documenti, certificati d'origine, dichiarazioni di prestazione";
        $this->destinazione = array(' I prodotti sono stati venduti con: ', $descri . $this->tesdoc['numdoc'] . '/' . $this->tesdoc['seziva'] . ' del ' . $this->giorno . '-' . $this->mese . '-' . $this->anno);
        $this->noPageGroup = true;
    }

    function newPage() {
        $this->AddPage();
        $this->SetFillColor(hexdec(substr($this->colore, 0, 2)), hexdec(substr($this->colore, 2, 2)), hexdec(substr($this->colore, 4, 2)));
        $this->MultiCell(0, 8, 'Nel ringraziarVi per la fiducia accordataci con il Vostro acquisto alleghiamo alla presente i documenti di origine relativi ai prodotti di seguito elencati:', 0, 'L', 0, 1);
        $this->Ln(6);
        $this->SetFont('helvetica', '', 9);
        $this->Cell(100, 6, 'Codice - Descrizione del materiale', 1, 0, 'L', 1);
        $this->Cell(10, 6, 'U.m.', 1, 0, 'C', 1);
        $this->Cell(25, 6, 'Quantità', 1, 0, 'R', 1);
        $this->Cell(12, 6, 'ID', 1, 0, 'C', 1);
        $this->Cell(27, 6, 'Lotto', 1, 0, 'C', 1);
        $this->Cell(8, 6, 'Pagina', 1, 1, 'R', 1, '', 1);
    }

    function pageHeader() {
        $this->StartPageGroup();
        $this->newPage();
    }

    function compose() {
        $lines = $this->docVars->getLots();
        while (list($key, $rigo) = each($lines)) {
            if ($this->GetY() >= 215) {
                $this->Cell(155, 6, '', 'T', 1);
                $this->SetFont('helvetica', '', 20);
                $this->SetY(225);
                $this->Cell(185, 12, '>>> --- SEGUE SU PAGINA SUCCESSIVA --- >>> ', 1, 1, 'R');
                $this->SetFont('helvetica', '', 9);
                $this->newPage();
                $this->Cell(185, 5, '<<< --- SEGUE DA PAGINA PRECEDENTE --- <<< ', 0, 1);
            }
            $this->Cell(100, 6, $rigo['codart'] . '-' . $rigo['descri'], 1, 0, 'L', 0, '', 1);
            $this->Cell(10, 6, $rigo['unimis'], 1, 0, 'C');
            $this->Cell(25, 6, gaz_format_quantity($rigo['quanti'], 1, $this->decimal_quantity), 1, 0, 'R');
            $this->Cell(12, 6, $rigo['id_lotmag'], 1, 0, 'C', 0, '', 1);
            $this->Cell(27, 6, $rigo['identifier'], 1, 0, 'C', 0, '', 1);
            $this->Cell(8, 6, '', 'RTB', 1, 'C');
        }
    }

    function pageFooter() {
        
    }

    function Footer() {
        //Page footer
        $this->SetY(-25);
        $this->Line(10, 270, 197, 270);
        $this->SetFont('helvetica', '', 8);
        $this->MultiCell(186, 4, $this->intesta1 . ' ' . $this->intesta2 . ' ' . $this->intesta3 . ' ' . $this->intesta4 . ' ', 0, 'C', 0);
    }

}

?>
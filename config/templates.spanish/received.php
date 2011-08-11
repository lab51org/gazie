<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2011 - Antonio De Vincentiis Montesilvano (PE)
                                (www.devincentiis.it)
                        <http://gazie.altervista.org>
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

require("../../library/include/calsca.inc.php");
require('template.php');

class Received extends Template
{

    function setTesDoc()
    {
        $this->tesdoc = $this->docVars->tesdoc;
        $this->giorno = substr($this->tesdoc['datfat'],8,2);
        $this->mese = substr($this->tesdoc['datfat'],5,2);
        $this->anno = substr($this->tesdoc['datfat'],0,4);
        $this->nomemese = ucwords(strftime("%B", mktime (0,0,0,substr($this->tesdoc['datfat'],5,2),1,0)));
        $this->sconto = $this->tesdoc['sconto'];
        $this->trasporto = $this->tesdoc['traspo'];
        $this->tipdoc = 'Recibido n.'.$this->tesdoc['numfat'].' del '.$this->giorno.' '.$this->nomemese.' '.$this->anno;
    }

    function newPage() {
        $this->AddPage();
        $this->SetFont('helvetica','',9);
        $this->Cell(105,6,'Descripcion',1,0,'L',1);
        $this->Cell(7, 6,'U.M.',1,0,'C',1);
        $this->Cell(16,6,'Cantidad',1,0,'C',1);
        $this->Cell(18,6,'Precio',1,0,'C',1);
        $this->Cell(8, 6,'%Desc.',1,0,'C',1);
        $this->Cell(20,6,'Importe',1,0,'C',1);
        $this->Cell(12,6,'%IVA',1,1,'C',1);
    }

    function pageHeader() {
        $this->StartPageGroup();
        $this->SetFillColor(hexdec(substr($this->colore,0,2)),hexdec(substr($this->colore,2,2)),hexdec(substr($this->colore,4,2)));
        $this->newPage();
    }


    function compose()
    {
        $this->body();
    }

    function body()
    {
        $lines = $this->docVars->getRigo();
        while (list($key, $rigo) = each($lines)) {
            if ($this->GetY() >= 195) {
                $this->Cell(186,6,'','T',1);
                $this->SetFont('helvetica','',14);
                $this->SetY(225);
                $this->Cell(186,12,'>>> --- CONTINUA EN LA PAGINA SIGUIENTE --- >>> ',1,1,'R');
                $this->SetFont('helvetica','',9);
                $this->newPage();
                $this->Cell(186,5,'<<< --- VIENE DE LA PAGINA ANTERIOR --- <<< ',0,1);
            }
                switch($rigo['tiprig']) {
                case "0":
                    $this->Cell(105, 5, $rigo['descri'],1,0,'L');
                    $this->Cell(7, 5, $rigo['unimis'],1,0,'C');
                    $this->Cell(16, 5, gaz_format_quantity($rigo['quanti'],1,$this->decimal_quantity),1,0,'R');
                    $this->Cell(18, 5, number_format($rigo['prelis'],$this->decimal_price,',',''),1,0,'R');
                    if ($rigo['sconto']>0) {
                       $this->Cell(8, 5,  number_format($rigo['sconto'],1,',',''),1,0,'C');
                    } else {
                       $this->Cell(8, 5, '',1,0,'C');
                    }
                    $this->Cell(20, 5, gaz_format_number($rigo['importo']),1,0,'R');
                    $this->Cell(12, 5, gaz_format_number($rigo['pervat']),1,1,'R');
                    break;
                case "1":
                    $this->Cell(105, 5, $rigo['descri'],1,0,'L');
                    $this->Cell(49, 5, '',1);
                    $this->Cell(20, 5, gaz_format_number($rigo['importo']),1,0,'R');
                    $this->Cell(12, 5, gaz_format_number($rigo['pervat']),1,1,'R');
                    break;
                case "2":
                    $this->Cell(105,5,$rigo['descri'],'LR',0,'L');
                    $this->Cell(81,5,'','R',1);
                    break;
                }
        }
    }

    function pageFooter()
    {
        $y = $this->GetY();
        $this->Rect(10,$y,186,212-$y); //questa marca le linee dx e sx del documento
        //stampo il castelletto
        $this->SetY(212);
        $this->Cell(62,6,'Pago','LTR',0,'C',1);
        $this->Cell(68,6,'Resumen Tasas I.V.A.','LTR',1,'C',1);
        $this->SetFont('helvetica','',8);
        $this->Cell(62,6,$this->pagame['descri'],'LBR',0,'L');
        $this->Cell(18,4,'Imponible','LR',0,'C',1);
        $this->Cell(32,4,'Tasa','LR',0,'C',1);
        $this->Cell(18,4,'Impuesto','LR',1,'C',1);
        $totTrasporto = $this->tottraspo;
        $this->docVars->setTotal($totTrasporto);
        foreach ($this->docVars->cast as $key => $value) {
                if ($this->tesdoc['id_tes'] > 0) {
                   $this->Cell(62);
                   $this->Cell(18, 4, gaz_format_number($value['impcast']).' ','LR', 0, 'R');
                   $this->Cell(32, 4, $value['descriz'],0,0,'C');
                   $this->Cell(18, 4, gaz_format_number($value['ivacast']).' ','LR',1,'R');
                } else {
                   $this->Cell(62);
                   $this->Cell(68, 4,'','LR',1);
                }
        }
        //azzero il castelletto
        foreach ($this->docVars->castel as $i => $value) {
            unset($this->docVars->castel[$i]);
        }
        //azzero il trasporto
        $this->tottraspo = 0.00;

        $totimpmer = $this->docVars->totimpmer;
        $speseincasso = $this->docVars->speseincasso;
        $totimpfat = $this->docVars->totimpfat;
        $totivafat = $this->docVars->totivafat;
        $vettor = $this->docVars->vettor;
        $impbol = $this->docVars->impbol;
        $totriport = $this->docVars->totriport;
        if ($impbol > 0) {
            $this->Cell(62);
            $this->Cell(18, 4, gaz_format_number($impbol).' ', 0, 0, 'R');
            $this->Cell(32, 4, $this->docVars->iva_bollo['descri'], 'LR', 0, 'C');
            $this->Cell(18, 4,gaz_format_number($this->docVars->iva_bollo['aliquo']*$impbol).' ',0,1,'R');
        }

        //effettuo il calcolo degli importi delle scadenze
        $totpag = $totimpfat + $impbol + $totriport + $totivafat;
        $ratpag = CalcolaScadenze($totpag, $this->giorno, $this->mese, $this->anno, $this->pagame['tipdec'],$this->pagame['giodec'],$this->pagame['numrat'],$this->pagame['tiprat'],$this->pagame['mesesc'],$this->pagame['giosuc']);
        if ($ratpag){
           //allungo l'array fino alla 4^ scadenza
           $ratpag['import'] = array_pad($ratpag['import'],4,'');
           $ratpag['giorno'] = array_pad($ratpag['giorno'],4,'');
           $ratpag['mese'] = array_pad($ratpag['mese'],4,'');
           $ratpag['anno'] = array_pad($ratpag['anno'],4,'');
        } else {
           for ($i = 0; $i <= 3; $i++) {
               $ratpag['import'][$i] = "";
               $ratpag['giorno'][$i] = "";
               $ratpag['mese'][$i] = "";
               $ratpag['anno'][$i] = "";
           }
        }
        //stampo i totali
        $this->SetXY(140,212);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(56, 4,'Gasto en efectivo','LTR',2,'C',1);
        if ($speseincasso > 0) {
           $this->Cell(56, 3, gaz_format_number($speseincasso),'LBR',2,'C');
        } else {
           $this->Cell(56, 3,'','LBR',2);
        }
        $this->Cell(56, 4,'Tot.Imponible','LTR',2,'C',1);
        if ($totimpfat > 0) {
           $this->Cell(56, 3, gaz_format_number($totimpfat),'LBR',2,'C');
        } else {
           $this->Cell(56, 3,'','LBR',2);
        }
        $this->Cell(56, 4,'Tot. I.V.A.','LTR',2,'C',1);
        if ($totivafat > 0) {
           $this->Cell(56, 3, gaz_format_number($totivafat),'LBR',2,'C');
        } else {
           $this->Cell(56, 3,'','LBR',2);
        }
        $this->Cell(56, 4,'Sellos','LTR',2,'C',1);
        if ($impbol > 0) {
           $this->Cell(56, 3, gaz_format_number($impbol),'LBR',1,'C');
        } else {
           $this->Cell(56, 3,'','LBR',1);
        }
        $this->SetY(218);
        $this->Cell(130);
        $totale = $totimpfat + $totivafat + $impbol;
        $this->SetY(224);
        $this->SetFont('helvetica','',9);
        if (!empty($this->banapp['descri']) and $this->pagame['tippag'] != 'D') {
           $this->Cell(62, 6, 'Banco de apoyo','LTR',1,'C',1);
           $this->Cell(62, 6, $this->banapp['descri'],'LR',1);
           $this->Cell(62, 6, ' ABI '.sprintf("%05d",$this->banapp['codabi']).' CAB '.$this->banapp['codcab'],'LRB',1,'C');
        } elseif (!empty($this->banacc['iban'])){
           $this->Cell(62, 6, 'Banca de abono','LTR',1,'C',1);
           $this->Cell(62, 5, $this->banacc['ragso1'],'LR',1);
           $this->Cell(62, 6, 'IBAN '.$this->banacc['iban'],'LRB',1,'C');
        } else {
           $this->Cell(62, 6, '','LTR',1,'',1);
           $this->Cell(62, 6, '','LR',1);
           $this->Cell(62, 6, '','LRB',1);
        }
        $this->Cell(130,6, 'Fecha de vencimiento y el importe a tanto alzado','LTR',0,'C',1);
        $this->Cell(56, 6, 'T O T A L ','LTR',1,'C',1);
        $this->Cell(32, 6, $ratpag['giorno']['0'].'-'.$ratpag['mese']['0'].'-'.$ratpag['anno']['0'],'LR',0,'C');
        $this->Cell(33, 6, $ratpag['giorno']['1'].'-'.$ratpag['mese']['1'].'-'.$ratpag['anno']['1'],'LR',0,'C');
        $this->Cell(32, 6, $ratpag['giorno']['2'].'-'.$ratpag['mese']['2'].'-'.$ratpag['anno']['2'],'LR',0,'C');
        $this->Cell(33, 6, $ratpag['giorno']['3'].'-'.$ratpag['mese']['3'].'-'.$ratpag['anno']['3'],'LR',0,'C');
        $this->SetFont('helvetica','B',18);
        if ($this->tesdoc['id_tes'] > 0) {
           $this->Cell(56, 12, '$ '.gaz_format_number($totale),'LBR', 1, 'C');
        } else {
           $this->Cell(56, 12,'','LBR',1);
        }
        $this->Ln(-6);
        $this->SetFont('helvetica','',9);
        if ($ratpag['import']['0'] != 0) {
            $this->Cell(32, 6, gaz_format_number($ratpag['import']['0']),'LBR',0,'C');
            } else {
            $this->Cell(32, 6,'','LBR');
        }
        if ($ratpag['import']['1'] != 0) {
           $this->Cell(33, 6, gaz_format_number($ratpag['import']['1']),'LBR',0,'C');
           } else {
           $this->Cell(33, 6,'','LBR');
        }
        if ($ratpag['import']['2'] != 0) {
           $this->Cell(32, 6, gaz_format_number($ratpag['import']['2']),'LBR',0,'C');
           } else {
           $this->Cell(32, 6,'','LBR');
        }
        if ($ratpag['import']['3'] != 0) {
           $this->Cell(33, 6, gaz_format_number($ratpag['import']['3']),'LBR',0,'C');
           } else {
           $this->Cell(33, 6,'','LBR');
        }
    }

    function Footer()
    {
        //Document footer
        $this->SetY(-20);
        $this->SetFont('helvetica','',8);
        $this->MultiCell(184,4,$this->intesta1.' '.$this->intesta2.' '.$this->intesta3.' '.$this->intesta4.' ',0,'C',0);
    }
}
?>
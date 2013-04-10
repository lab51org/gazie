<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2013 - Antonio De Vincentiis Montesilvano (PE)
         (www.facebook.com/antonio.devincentiis.9)
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
require('template.php');

class OrdineFornitore extends Template
{
    function setTesDoc()
    {
        $this->tesdoc = $this->docVars->tesdoc;
        $this->giorno = substr($this->tesdoc['datemi'],8,2);
        $this->mese = substr($this->tesdoc['datemi'],5,2);
        $this->anno = substr($this->tesdoc['datemi'],0,4);
        $this->nomemese = ucwords(strftime("%B", mktime (0,0,0,substr($this->tesdoc['datemi'],5,2),1,0)));
        $this->sconto = $this->tesdoc['sconto'];
        $this->trasporto = $this->tesdoc['traspo'];
        $this->tipdoc = 'Ordine a fornitore n.'.$this->tesdoc['numdoc'].'/'.$this->tesdoc['seziva'].' del '.$this->giorno.' '.$this->nomemese.' '.$this->anno;
    }
    function newPage() {
        $this->AddPage();
        $this->SetFillColor(hexdec(substr($this->colore,0,2)),hexdec(substr($this->colore,2,2)),hexdec(substr($this->colore,4,2)));
        $this->Ln(4);
        $this->SetFont('helvetica','',9);
        $this->Cell(25,6,'Codigo',1,0,'L',1);
        $this->Cell(80,6,'Descripcion',1,0,'L',1);
        $this->Cell(7, 6,'U.M.',1,0,'C',1);
        $this->Cell(16,6,'Cantidad',1,0,'R',1);
        $this->Cell(18,6,'Precio',1,0,'R',1);
        $this->Cell(8, 6,'%Desc.',1,0,'C',1);
        $this->Cell(20,6,'Importe',1,0,'R',1);
        $this->Cell(12,6,'%IVA',1,1,'R',1);
    }

    function pageHeader()
    {
        $this->setTesDoc();
        $this->StartPageGroup();
        $this->newPage();
    }
    function body()
    {
        $lines = $this->docVars->getRigo();
        while (list($key, $rigo) = each($lines)) {
            if ($this->GetY() >= 205) {
                $this->Cell(186,6,'','T',1);
                $this->SetFont('helvetica', '', 20);
                $this->SetY(225);
                $this->Cell(186,12,'>>> --- CONTINUA EN LA PAGINA SIGUIENTE --- >>> ',1,1,'R');
                $this->SetFont('helvetica', '', 9);
                $this->newPage();
                $this->Cell(186,5,'<<< --- VIENE DE LA PAGINA ANTERIOR --- <<< ',0,1);
            }
                switch($rigo['tiprig']) {
                case "0":
                    $this->Cell(25, 6, $rigo['codart'],1,0,'L');
                    $this->Cell(80, 6, $rigo['descri'],1,0,'L');
                    $this->Cell(7,  6, $rigo['unimis'],1,0,'C');
                    $this->Cell(16, 6, gaz_format_quantity($rigo['quanti'],1,$this->decimal_quantity),1,0,'R');
                    if ($rigo['prelis'] > 0) {
                       $this->Cell(18, 6, number_format($rigo['prelis'],$this->decimal_price,',',''),1,0,'R');
                    } else {
                       $this->Cell(18, 6, '',1);
                    }
                    if ($rigo['sconto']> 0) {
                       $this->Cell(8, 6,  number_format($rigo['sconto'],1,',',''),1,0,'C');
                    } else {
                       $this->Cell(8, 6, '',1);
                    }
                    if ($rigo['importo'] > 0) {
                       $this->Cell(20, 6, gaz_format_number($rigo['importo']),1,0,'R');
                    } else {
                       $this->Cell(20, 6, '',1);
                    }
                    $this->Cell(12, 6, gaz_format_number($rigo['pervat']),1,1,'R');
                    break;
                case "1":
                    $this->Cell(25, 6, '','LBR',0,'L');
                    $this->Cell(80, 6, $rigo['descri'],'LBR',0,'L');
                    $this->Cell(49, 6,'',1);
                    $this->Cell(20, 6, gaz_format_number($rigo['importo']),1,0,'R');
                    $this->Cell(12, 6, gaz_format_number($rigo['pervat']),1,1,'R');
                    break;
                case "2":
                    $this->Cell(25,6,'','L');
                    $this->Cell(80,6,$rigo['descri'],'LR',0,'L');
                    $this->Cell(81,6,'','R',1);
                    break;
                case "3":
                    $this->Cell(25,6,'',1,0,'L');
                    $this->Cell(80,6,$rigo['descri'],'B',0,'L');
                    $this->Cell(49,6,'','B',0,'L');
                    $this->Cell(20,6,gaz_format_number($rigo['prelis']),1,0,'R');
                    $this->Cell(12,6,'',1,1,'R');
                    break;
                }
        }
    }

    function compose()
    {
        $this->body();
    }

    function pageFooter()
    {
        $y = $this->GetY();
        $this->Rect(10,$y,186,208-$y); //questa marca le linee dx e sx del documento
        $this->SetFont('helvetica','',12);
        $this->Cell(186,8,'Cualquier cambio en los datos antes mencionados deben ser aprobados con anticipación.','T',1);
        //stampo il castelletto
        $this->SetFont('helvetica', '', 9);
        $this->SetY(218);
        $this->Cell(62,6, 'Pago','LTR',0,'C',1);
        $this->Cell(68,6, 'Resumen Tasas    I.V.A.','LTR',0,'C',1);
        $this->Cell(56,6, 'T O T A L ','LTR',1,'C',1);
        $this->Cell(62,6, $this->pagame['descri'],'LR',0,'C');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(18,4, 'Imponible','LR',0,'C',1);
        $this->Cell(32,4, 'Tasa','LR',0,'C',1);
        $this->Cell(18,4, 'Impuesto','LR',1,'C',1);
        $this->docVars->setTotal($this->tesdoc['traspo']);
        foreach ($this->docVars->cast as $key => $value) {
                if ($this->tesdoc['id_tes'] > 0) {
                   $this->Cell(62);
                   $this->Cell(18, 4, gaz_format_number($value['impcast']).' ', 'R', 0, 'R');
                   $this->Cell(32, 4, $value['descriz'],0,0,'C');
                   $this->Cell(18, 4, gaz_format_number($value['ivacast']).' ','L',1,'R');
                } else {
                   $this->Cell(62);
                   $this->Cell(68, 4,'','LR',1);
                 }
        }

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
        //stampo i totali
        $this->SetY(208);
        $this->SetFont('helvetica','',9);
        $this->Cell(36, 5,'Tot. Cuerpo','LTR',0,'C',1);
        $this->Cell(16, 5,'% Descuento','LTR',0,'C',1);
        $this->Cell(24, 5,'Gasto en efectivo','LTR',0,'C',1);
        $this->Cell(26, 5,'Trasporte','LTR',0,'C',1);
        $this->Cell(36, 5,'Tot.Imponible','LTR',0,'C',1);
        $this->Cell(26, 5,'Tot. I.V.A.','LTR',0,'C',1);
        $this->Cell(22, 5,'Sellos','LTR',1,'C',1);
        if ($totimpmer > 0) {
           $this->Cell(36, 5, gaz_format_number($totimpmer),'LBR',0,'C');
        } else {
           $this->Cell(36, 5,'','LBR');
        }
        if ($this->tesdoc['sconto'] > 0) {
           $this->Cell(16, 5, gaz_format_number($this->tesdoc['sconto']),'LBR',0,'C');
        } else {
           $this->Cell(16, 5,'','LBR');
        }
        if ($speseincasso > 0) {
           $this->Cell(24, 5, gaz_format_number($speseincasso),'LBR',0,'C');
        } else {
           $this->Cell(24, 5,'','LBR');
        }
        if ($this->tesdoc['traspo'] > 0) {
           $this->Cell(26, 5, gaz_format_number($this->tesdoc['traspo']),'LBR',0,'C');
        } else {
           $this->Cell(26, 5,'','LBR');
        }
        if ($totimpfat > 0) {
           $this->Cell(36, 5, gaz_format_number($totimpfat),'LBR',0,'C');
        } else {
           $this->Cell(36, 5,'','LBR');
        }
        if ($totivafat > 0) {
           $this->Cell(26, 5, gaz_format_number($totivafat),'LBR',0,'C');
        } else {
           $this->Cell(26, 5,'','LBR');
        }
        if ($impbol > 0) {
            $this->Cell(22, 5, gaz_format_number($impbol),'LBR', 0,'C');
        } else {
           $this->Cell(22, 5,'','LBR');
        }

        $this->SetY(224);
        $this->Cell(130);
        $totale = $totimpfat + $totivafat + $impbol;
        $this->SetFont('helvetica','B',16);
        if ($totale > 0) {
           $this->Cell(56, 18, '€ '.gaz_format_number($totale), 'LR', 1, 'C');
        } else {
           $this->Cell(56, 18,'','LR',1);
        }
        $this->SetY(230);
        $this->SetFont('helvetica','',9);
        $this->Cell(62, 6,'Envio','LTR',1,'C',1);
        $this->Cell(62, 6,$this->tesdoc['spediz'],'LBR',1,'C');
        $this->Cell(72, 6,'Puerto','LTR',0,'C',1);
        $this->Cell(29, 6,'Peso neto','LTR',0,'C',1);
        $this->Cell(29, 6,'Peso brupo','LTR',0,'C',1);
        $this->Cell(28, 6,'N. paquetes','LTR',0,'C',1);
        $this->Cell(28, 6,'Volumen','LTR',1,'C',1);
        $this->Cell(72, 6,$this->tesdoc['portos'],'LBR',0,'C');
        if ($this->tesdoc['net_weight'] > 0) {
            $this->Cell(29, 6,gaz_format_number($this->tesdoc['net_weight']),'LRB',0,'C');
        } else {
            $this->Cell(29, 6,'','LRB');
        }
        if ($this->tesdoc['gross_weight'] > 0) {
            $this->Cell(29, 6,gaz_format_number($this->tesdoc['gross_weight']),'LRB',0,'C');
        } else {
            $this->Cell(29, 6,'','LRB');
        }
        if ($this->tesdoc['units'] > 0) {
            $this->Cell(28, 6,$this->tesdoc['units'],'LRB',0,'C');
        } else {
            $this->Cell(28, 6,'','LRB');
        }
        if ($this->tesdoc['volume'] > 0) {
            $this->Cell(28, 6,gaz_format_number($this->tesdoc['volume']),'LRB',1,'C');
        } else {
            $this->Cell(28, 6,'','LRB',1);
        }
    }

    function Footer()
    {
        //Page footer
        $this->SetY(-25);
        $this->SetFont('helvetica', '', 8);
        $this->MultiCell(184, 4, $this->intesta1.' '.$this->intesta2.' '.$this->intesta3.' '.$this->intesta4.' ', 0, 'C', 0);
    }
}

?>
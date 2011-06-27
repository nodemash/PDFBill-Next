<?php
/** 
 * -----------------------------------------------------------------------------------------
 * PDF-RechnungNEXT by Robert Hoppe
 * Copyright 2011 Robert Hoppe - xtcm@katado.com - http://www.katado.com
 *
 * Please visit http://pdfnext.katado.com for newer Versions
 * -----------------------------------------------------------------------------------------
 *  
 * Released under the GNU General Public License 
 * Initial Version By Leonid Lezner. leolezner@yahoo.de 
 */
require_once(DIR_FS_CATALOG . 'includes/classes/FPDF/PdfBrief.php');

class PdfRechnung extends PdfBrief
{
    // BillNr, Date
	var $rechnungsdaten_x = 135; 
	var $rechnungsdaten_y = 70;
	var $rechnung_start = 100;
	var $menge_len = 20;
	var $artikel_len = 80;
	var $artikelnr_len = 20;
	var $einzelpreis_len = 28;
	var $preis_len = 28;
	
    /**
     * Constructor
     * 
     * @access public
     * @var int $customers_id
     * @var int $bill_nr
     * @var int $oID
     * @var string $orders_date
     * @var $string $payment_method
     * @var boolean $deliverSlip
     * @return void
     */
	function Rechnungsdaten($customers_id, $bill_nr, $oID, $orders_date, $payment_method, $deliverSlip = false)  
	{ 
		$this->SetX($this->rechnungsdaten_x); 
		$this->SetLeftMargin($this->rechnungsdaten_x); 
		$this->SetY($this->rechnungsdaten_y); 
		$this->SetFont($this->fontfamily,'', 11); 
	
        // Top Text
        if(!$deliverSlip) {
            $this->Cell(0, 6, TEXT_PDF_RECHNUNG_HEAD, 0, 1, 'C', 1);
        } else {
            $this->Cell(0, 6, TEXT_PDF_LIEFERSCHEIN_HEAD, 0, 1, 'C', 1);
        }

        // spacer
        $this->Cell(0, 1, '', 0, 1);

        // customers_id
        $this->Cell(0, 6, TEXT_PDF_KUNDENNUMMER . ': ' . $customers_id, 0, 1, '', 1);


        // no develiperSlip? We have a bill!
		if(!$deliverSlip) {	
            // use oID instead of bill_nr
            if (PDF_USE_ORDERID == 'true') {
                $this->Cell(0, 6, TEXT_PDF_RECHNUNGSNUMMER . ': '. PDF_USE_ORDERID_PREFIX . $oID . PDF_USE_ORDERID_SUFFIX, 0, 1, '', 1); 
            } else { 
                $this->Cell(0, 6, TEXT_PDF_RECHNUNGSNUMMER . ': '. $bill_nr, 0, 1, '', 1); 
            }
		} else { 
			$this->Cell(0, 6, TEXT_PDF_BESTELLNUMMER . ': '. $oID, 0, 1, '', 1);
		} 
		
		$this->Cell(0, 6, TEXT_PDF_DATUM . ': ' . $orders_date, 0, 1, '', 1); 

    
        if(!$deliverSlip) {
            $this->Cell(0, 6, TEXT_PDF_ZAHLUNGSWEISE . ': ' . html_entity_decode($payment_method), 0, 1, '', 1);
        }
	}
	
    /**
     * Start PDFBill
     *
     * @access public
     * @var string $customers_name
     * @var string $gender
     * @var boolean $deliverSlip
     * @return void
     */
	function RechnungStart($customers_name, $gender, $deliverSlip = false)
	{
		$this->SetX($this->left_textoffset);
		$this->SetLeftMargin($this->left_textoffset);
		$this->SetY($this->rechnung_start);

		$this->SetFont($this->fontfamily, 'B', 16);
		$this->Cell(0, 6, ($deliverSlip)? TEXT_PDF_LIEFERSCHEIN : TEXT_PDF_RECHNUNG, 0, 1);
		$this->Ln();
		
		$this->SetFont($this->fontfamily, '', 12);
		
        switch($gender)
        {
            case 'm':
                $message = xtc_utf8_decode(TEXT_PDF_DANKE_MANN);
                break;
            case 'f':
                $message = xtc_utf8_decode(TEXT_PDF_DANKE_FRAU);
                break;
            case 'u':
            default:
                $message = xtc_utf8_decode(TEXT_PDF_DANKE_UNISEX);
        }
	
		$this->MultiCell(0, 6, sprintf($message, xtc_utf8_decode($customers_name)), 0);
	}
	
    /**
     * Listhead
     *
     * @access public
     * @var boolean $deliverSlip
     * @return void
     */
	function ListeKopf($deliverSlip = false) 
	{ 
		$this->SetFont($this->fontfamily, 'B', 10); 
		$this->Ln();

		$this->Cell($this->menge_len, 6, TEXT_PDF_MENGE.' ', 'B', 0, 'R'); 
		$this->Cell($this->artikel_len, 6, TEXT_PDF_ARTIKEL, 'B', 0); 
		$this->Cell($this->artikelnr_len, 6, TEXT_PDF_ARTIKELNR, 'B', 0); 

		$this->Cell($this->einzelpreis_len, 6, ($deliverSlip)? '' : TEXT_PDF_EINZELPREIS, 'B', 0, 'R'); 
        $this->Cell($this->preis_len, 6, ($deliverSlip)? '' : TEXT_PDF_PREIS, 'B', 0, 'R');

		$this->Ln(8); 
	}
	
    /**
     * Add Product to list
     *
     * @access public
     * @var int $menge
     * @var string $artikel
     * @var string $zuinfos
     * @var int $artnr
     * @var string $zuinfosartnr
     * @var double $einzelpreis
     * @var double $preis 
     * @return void
     */
	function ListeProduktHinzu($menge, $artikel, $zusinfos, $artnr, $zusinfoartnr, $einzelpreis, $preis)
	{
		$this->SetFont($this->fontfamily,'', 11);
				
        // split products description into parts
		$parts = preg_split("/[\s]+/", xtc_utf8_decode(html_entity_decode($artikel)), -1, PREG_SPLIT_DELIM_CAPTURE);
		$line = 0;
		
		foreach($parts as $part) {
			// Sum words until max length is reached
			if($this->GetStringWidth($newtext.$part) < $this->artikel_len) {
				$newtext .= $lastpart.$part.' ';
				$lastpart = "";
            // Word count is now longer than allowed
			} else {
                // Also ouput in the first line some addtional informations
				if($line == 0) {
					$this->Cell($this->menge_len, 6, $menge.' x ', 0, 0, 'R');		
				} else {
					$this->Cell($this->menge_len, 6, '', 0);
                }
				
				$this->Cell($this->artikel_len, 6, $newtext, 0);
				
				if($line == 0) {
					$this->Cell($this->artikelnr_len, 6, $artnr, 0, 0, '');
					$this->Cell($this->einzelpreis_len, 6, $einzelpreis, 0, 0, 'R');
					$this->Cell($this->preis_len, 6, $preis, 0, 0, 'R');		
				} else {
					$this->Cell($this->artikelnr_len, 6, '', 0);
					$this->Cell($this->einzelpreis_len, 6, '', 0);
					$this->Cell($this->preis_len, 6, '', 0);
				}
				
				$this->Ln();
				$newtext = "";
				$lastpart = $part.' ';
				$line++;
			}
		}
		
        // if there is some text left
		if($newtext) {
			if($line == 0) {
				$this->Cell($this->menge_len, 6, $menge.' x ', 0, 0, 'R');		
			} else {
				$this->Cell($this->menge_len, 6, '', 0);
            }
			
			$this->Cell($this->artikel_len, 6, $newtext, 0);
			
			if($line == 0) {
				$this->Cell($this->artikelnr_len, 6, $artnr, 0, 0, '');
				$this->Cell($this->einzelpreis_len, 6, $einzelpreis, 0, 0, 'R');
				$this->Cell($this->preis_len, 6, $preis, 0, 0, 'R');
			} else {
				$this->Cell($this->artikelnr_len, 6, '', 0);
				$this->Cell($this->einzelpreis_len, 6, '', 0);
				$this->Cell($this->preis_len, 6, '', 0);
			}
			$this->Ln();
		}
		
        // Additional Informations available?
		if($zusinfos != '') {
			$this->SetFont($this->fontfamily, 'I', 9);
			
			$zusinfos_arr = explode("\n", xtc_utf8_decode($zusinfos));
			$zusinfoartnr_arr = explode("\n", $zusinfoartnr);
			
			for($i = 0; $i < count($zusinfos_arr); $i++) {
				$this->Cell($this->menge_len, 6, '', 0, 0, '');
				$this->Cell($this->artikel_len, 6, $zusinfos_arr[$i], 0, 0, '');
				
				if($i < count($zusinfoartnr_arr)) {
					$this->Cell($this->artikelnr_len, 6, $zusinfoartnr_arr[$i], 0, 0, '');
                }
					
				$this->Ln();
			}
		}
		$this->Ln(2);
	}
	
    /**
     * Order Total
     * 
     * @access public
     * @param mixed array $orderdata
     * @return void 
     */
	function Betrag($orderdata)
	{
		$this->SetFont($this->fontfamily, '', 11);
		
		$this->Cell($this->menge_len + $this->artikel_len + $this->einzelpreis_len + $this->artikelnr_len + $this->preis_len, 6, '', 'T');
		$this->Ln(2);
		
		foreach($orderdata as $info) {
			$text = $info['text'];
            $text = html_entity_decode($text);

			$info['title'] = str_replace("::", ":", $info['title']);
            $info['title'] = html_entity_decode($info['title']);
			
			if(strpos($text, "<b>") !== false) {
				$this->SetFont($this->fontfamily, 'B', 11);

				$text = strip_tags($text);
				$info['title'] = strip_tags($info['title']);
				
				if($info['class'] == 'ot_total') {
					$this->Ln(2);
					
					$sum_len = 25;
					
					$this->Cell($this->menge_len + $this->artikel_len + $this->einzelpreis_len + $this->artikelnr_len - $sum_len, 1, "", '', 0);
					$this->Cell($this->preis_len + $sum_len - 1, 1, "", 'T', 1);
					$this->Cell($this->menge_len + $this->artikel_len + $this->einzelpreis_len + $this->artikelnr_len - $sum_len, 1, "", '', 0);
					$this->Cell($this->preis_len + $sum_len - 1, 1, "", 'T', 1);
				}
			} else if(strpos($text, "<font") !== false) {
				$text = strip_tags($text);
				$this->SetTextColor(205,0,0);
			} else {
				$info['title'] = strip_tags($info['title']);
				$text = strip_tags($text);
				$this->SetFont($this->fontfamily, '', 11);
				$this->SetTextColor(0,0,0);
			}
			
			$this->Cell($this->menge_len + $this->artikel_len + $this->einzelpreis_len + $this->artikelnr_len, 6, $info['title'], 0, 0, 'R');
			$this->Cell($this->preis_len, 6, $text, 0, 1, 'R');
		}
	}
	
    /**
     * Add Comment
     *
     * @access public
     * @param string $text
     * @return void
     */
	function Kommentar($text)
	{
		if($text == '') {
            return;
        }
		
		$this->Ln(10);
		
		$this->SetFont($this->fontfamily, 'B', 11);
		$this->Cell($this->preis_len, 10, TEXT_PDF_KOMMENTAR, 0, 1);
		
		$this->SetFont($this->fontfamily, '', 11);
		$this->MultiCell(0, 4, $text);
	}
	
    /**
     * Bill End
     *
     * @access public
     * @param boolean $deliverSlip
     * @return void
     */
	function RechnungEnde($deliverSlip = false) {
		$this->Ln(10); 
        $this->SetFont($this->fontfamily, '', 12); 
        $endText = ($deliverSlip === true)? xtc_utf8_decode(TEXT_PDF_LSCHLUSSTEXT) : xtc_utf8_decode(TEXT_PDF_SCHLUSSTEXT);
        $this->MultiCell(0, 6, $endText); 
    }
}
?>

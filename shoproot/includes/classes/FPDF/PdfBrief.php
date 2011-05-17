<?php
/** 
 * -----------------------------------------------------------------------------------------
 * PDFBill NEXT by Robert Hoppe 
 * http://www.katado.com - xtcm@katado.com
 * Please visit http://pdfnext.katado.com for newer Versions
 * -----------------------------------------------------------------------------------------
 *  
 * Released under the GNU General Public License 
 * Initial Version By Leonid Lezner. leolezner@yahoo.de 
 */

// get fpdf_protection
require_once (DIR_FS_CATALOG . 'includes/classes/FPDF/fpdf_protection.php');

class PdfBrief extends FPDF_Protection
{
    // margins
	var $left_margin = 25;
	var $left_textoffset = 20;
	var $top_margin = 10;
	var $footer_y = -35;

	// Addressfield for the letter window
	var $addresswindowmaxlen = 80;
	var $addresswindowtop = 50;

	// Font-Type of Bill
	var $fontfamily = 'Arial';

	// Shop-Logo Please adjust if needed
	var $logo_x = 145;
	var $logo_y = 30;
	
    /**
     * Generate Footer
     * 
     * @access public
     * @return void
     */
	function Footer()
	{
		$this->SetFont($this->fontfamily,'',8);
        
        // add mid line
		$this->Line(5, 297.0/2.0, 8, 297.0/2.0);
    
        // add info boxes
		$left = $this->PutInfoBlock(0, xtc_utf8_decode(TEXT_PDF_ANSCHRIFT), $this->footer_y);
		$left = $this->PutInfoBlock($left, xtc_utf8_decode(TEXT_PDF_KONTAKT), $this->footer_y);
		$left = $this->PutInfoBlock($left, xtc_utf8_decode(TEXT_PDF_BANK), $this->footer_y);
		$left = $this->PutInfoBlock($left, xtc_utf8_decode(TEXT_PDF_GESCHAEFT), $this->footer_y);

        // add bottom line
        $this->SetLineWidth(0.1); 
        $this->Line(20, $this->GetY() - 15, 195, $this->GetY() - 15);  
	}
	
    /**
     * Generate PDF-Title
     * 
     * @access public
     * @param string $title
     * @return void
     */
	function Init($title)
	{
		$this->SetAutoPageBreak(true, abs($this->footer_y) + 10);
		$this->SetCreator("PdfBrief, PDF-RechnungNEXT (c) 2011 Robert Hoppe www.katado.com");	
		$this->AliasNbPages();
		$this->AddPage();
		$this->SetFillColor(230,230,230);
		$this->SetDisplayMode('fullwidth');
		$this->SetTitle($title);
		$this->SetProtection(array('print'),'', PDF_MASTER_PASS); // nur Drucken erlaubt, kein User-Passwort, jedoch ein Master-Passwort
	}
	
    /**
     * Build Header
     * 
     * @access public
     * @return void
     */
	function Header()
	{
		$this->SetFont($this->fontfamily,'',9);
		
		$this->SetX($this->left_margin);
		$this->SetLeftMargin($this->left_margin);
		
		// Ausgabe des Headertextes
		$this->Cell(0, 4, TEXT_PDF_HEADER, 0, 1);
		// Schiebe die Zeile auf die Anfangsposition
		$this->SetY($this->top_margin);
		// Ausgabe der Seitenzahl
		$this->Cell(0, 4, TEXT_PDF_SEITE.' '.$this->PageNo().' '.TEXT_PDF_SEITE_VON.' {nb}', 0, 0, 'R');
		
		// Für die nachfolgenden Seiten der Abstand vom Header
		$this->Ln();
		$this->Ln();
	}
	
    /**
     * Maxlegth
     *
     * @access public
     * @param string array $strings
     * @return void 
     */
	function maxlen($strings)
	{
		$max = 0;
		for ($i = 0; $i < count($strings); $i++) {
			if($this->GetStringWidth($strings[$i]) > $max) {
				$max = $this->GetStringWidth($strings[$i]);
            } 
		}
		
		return $max + 6;
	}

    /**
     * Info Block
     *
     * @access public
     * @param int $left
     * @param string $body
     * @param int $y
     * @return void
     */
	function PutInfoBlock($left, $body, $y)
	{
		$this->SetFont($this->fontfamily,'',8);
		
		$this->SetY($y);
		
		//$body .= "\n";
		$body_arr = split("\n", $body);
		$maxlen = $this->maxlen($body_arr);
		
		$this->SetLeftMargin($left + $this->left_margin);
		$this->SetX($left + $this->left_margin);
		
		//$this->MultiCell($maxlen, 4, $body, 'T');
		$this->MultiCell($maxlen, 4, $body, 0);
		
		return $left + $maxlen;
	}
	

    /**
     * Address Block
     * 
     * @access public
     * @param string $kundenadresse
     * @param string $geschaeftsadresse
     * @return void
     */
	function Adresse($kundenadresse, $geschaeftsadresse)
	{
		// Kundenadresse ausgeben
		$this->SetX($this->left_textoffset + 5);
		$this->SetLeftMargin($this->left_textoffset + 5);
		
		$this->SetY($this->addresswindowtop + 7);
		
		$this->SetFont($this->fontfamily,'',12); // normal
		$this->MultiCell($this->addresswindowmaxlen - 5, 4, xtc_utf8_decode($kundenadresse));
		
		// Shopadresse ausgeben
		$this->SetX($this->left_textoffset);
		$this->SetLeftMargin($this->left_textoffset);
		
		$this->SetY($this->addresswindowtop);
		
		$this->SetFont($this->fontfamily,'',6); // klein
		$this->Cell($this->addresswindowmaxlen, 4, xtc_utf8_decode($geschaeftsadresse));
		
		//$this->Rect($this->left_textoffset, $this->addresswindowtop, $this->addresswindowmaxlen, 40);
	}
	
	function Logo($pfad)
	{
        $size = Getimagesize($pfad);
        $this->Image($pfad, $this->logo_x, $this->logo_y, $size[0] / 300 * 25.4);
	}
}

?>

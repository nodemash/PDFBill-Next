<?php
//HTML2PDF by Clément Lavoillotte
//ac.lavoillotte@noos.fr
//webmaster@streetpc.tk
//http://www.streetpc.tk

// Modified by Leonid Lezner

define('FPDF_FONTPATH','font/');

include('PdfBrief.php');

//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
	$R = substr($couleur, 1, 2);
	$rouge = hexdec($R);
	$V = substr($couleur, 3, 2);
	$vert = hexdec($V);
	$B = substr($couleur, 5, 2);
	$bleu = hexdec($B);
	$tbl_couleur = array();
	$tbl_couleur['R']=$rouge;
	$tbl_couleur['G']=$vert;
	$tbl_couleur['B']=$bleu;
	return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
	return $px*25.4/72;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}
////////////////////////////////////

class PDF extends PdfBrief
{
	//variables of html parser
	var $B;
	var $I;
	var $U;
	var $HREF;
	var $fontList;
	var $issetfont;
	var $issetcolor;
	var $fontsize = 12;
	var $listn = 0;
	
	function init($var)
	{
		//Call parent constructor
		parent::init($var);
		//Initialization
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
		$this->fontlist=array("arial","times","courier","helvetica","symbol");
		$this->issetfont=false;
		$this->issetcolor=false;
	}
	
	//////////////////////////////////////
	//html parser
	
	function WriteHTML($html)
	{
		$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><h1><h2><h3><li><ol><ul>"); //remove all unsupported tags
		$html=str_replace("\n",' ',$html); //replace carriage returns by spaces
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explodes the string
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				//Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,stripslashes(txtentities($e)));
			}
			else
			{
				//Tag
				if($e{0}=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					//Extract attributes
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$attr=array();
					foreach($a2 as $v)
						if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
							$attr[strtoupper($a3[1])]=$a3[2];
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}
	
	function OpenTag($tag,$attr)
	{
		//Opening tag
		switch($tag){
			case 'STRONG':
				$this->SetStyle('B',true);
				break;
			case 'EM':
				$this->SetStyle('I',true);
				break;
			case 'B':
			case 'I':
			case 'U':
				$this->SetStyle($tag,true);
				break;
			case 'A':
				$this->HREF=$attr['HREF'];
				break;
			case 'IMG':
				if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
					if(!isset($attr['WIDTH']))
						$attr['WIDTH'] = 0;
					if(!isset($attr['HEIGHT']))
						$attr['HEIGHT'] = 0;
					$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
				}
				break;
			case 'TR':
			case 'BLOCKQUOTE':
			case 'BR':
				$this->Ln(5);
				break;
			case 'H1':
				$this->SetStyle('B',true);
				$this->SetFontSize($this->fontsize + 8);
				break;
			case 'H2':
				$this->SetStyle('B',true);
				$this->SetFontSize($this->fontsize + 6);
				break;
			case 'H3':
				$this->SetFontSize($this->fontsize + 4);
				break;
			case 'P':
				$this->Ln(10);
				break;
			case 'OL':
				$this->listn = 0;
				$this->Ln(5);
				break;
			case 'UL':
				$this->listn = -1;
				$this->Ln(5);
				break;	
			case 'LI':
				$this->Ln(5);
				if($this->listn != -1)
					$this->Cell(10, 5, (++$this->listn) . '.', 0, 0, 'R');
				else
					$this->Cell(10, 5, '     »', 0, 0, 'R');	
				break;
			case 'FONT':
				if (isset($attr['COLOR']) and $attr['COLOR']!='') {
					$coul=hex2dec($attr['COLOR']);
					$this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
					$this->issetcolor=true;
				}
				if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
					$this->SetFont(strtolower($attr['FACE']));
					$this->issetfont=true;
				}
				break;
		}
	}
	
	function CloseTag($tag)
	{
		//Closing tag
		if($tag=='STRONG')
			$tag='B';
		if($tag=='EM')
			$tag='I';
		if($tag=='B' or $tag=='I' or $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF='';
		if($tag=='FONT'){
			if ($this->issetcolor==true) {
				$this->SetTextColor(0);
			}
			if ($this->issetfont) {
				$this->SetFont('arial');
				$this->issetfont=false;
			}
		}
		if($tag=='H1' || $tag=='H2' || $tag=='H3')
		{
			$this->SetFontSize($this->fontsize);
			
			if($tag=='H1' || $tag=='H2')
			{
				$this->Ln(9);	
				$this->SetStyle('B', false);
			}
				
			if($tag=='H3')
				$this->Ln(5);
		}
	}
	
	function SetStyle($tag,$enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
			if($this->$s>0)
				$style.=$s;
		$this->SetFont('',$style);
	}
	
	function PutLink($URL,$txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
	
	function setFontEx($fam, $style, $size)
	{
		$this->fontsize = $size;
		$this->SetFont($fam, $style, $size);
	}

}//end of class
?>

<?php
/** 
 * -----------------------------------------------------------------------------------------
 * PDFBill NEXT by Robert Hoppe by Robert Hoppe
 * Copyright 2011 Robert Hoppe - xtcm@katado.com - http://www.katado.com
 *
 * Please visit http://pdfnext.katado.com for newer Versions
 * -----------------------------------------------------------------------------------------
 *  
 * Released under the GNU General Public License 
 * 
 */
function xtc_utf8_decode($string, $force=false) 
{
    // check if string is utf8
    if (function_exists('mb_detect_encoding') && mb_detect_encoding($str) === 'UTF-8') {
        $is_utf8 = true;
    
    // use different way to check for UTF-8 - Does not always work!
    } else {
        $is_utf8 = is_utf8($string); 
    }

    // decode UTF-8
    if ($is_utf8 === true || $force === true) {
        $string = utf8_decode($string);
    }

    return $string;
}

/**
 * alternative way to check for utf8
 * 
 * @param string $str
 * @return boolean
 */
function is_utf8($str){
    $strlen = strlen($str);

    for($i=0; $i<$strlen; $i++){
        $ord = ord($str[$i]);
        if($ord < 0x80) { 
            continue; // 0bbbbbbb
        } elseif(($ord&0xE0)===0xC0 && $ord>0xC1) {
            $n = 1; // 110bbbbb (exkl C0-C1)
        } elseif(($ord&0xF0)===0xE0) {
            $n = 2; // 1110bbbb
        } elseif(($ord&0xF8)===0xF0 && $ord<0xF5) {
            $n = 3; // 11110bbb (exkl F5-FF)
        } else {
            return false; // invalid UTF-8 characater-
        }

        for($c=0; $c<$n; $c++) {
            // $n Following bytes? // 10bbbbbb
            if(++$i===$strlen || (ord($str[$i])&0xC0)!==0x80) {
                return false; // invalid UTF-8 character
            }
        }
    }

    return true; // no invalid UTF-8 character
}

?>

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
function xtc_get_bill_nr ($oID, $override = true)
{
    if ($override == true && PDF_USE_ORDERID == 'true') {
        return $oID;
    }

    // get bill_nr
    $sqlBillNr = "SELECT bill_nr FROM " . TABLE_ORDERS . " WHERE orders_id = '" . (int) $oID . "'";
    $resBillNr = xtc_db_query($sqlBillNr);
    $rowBillNr = xtc_db_fetch_array($resBillNr);
    if(!empty($rowBillNr['bill_nr'])) {
        return $rowBillNr['bill_nr'];
    } else {
        return null;
    }
}

?>

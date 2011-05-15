<?php
/** 
 * -----------------------------------------------------------------------------------------
 * PDFBill NEXT by Robert Hoppe
 * Copyright 2011 Robert Hoppe - xtcm@katado.com - http://www.katado.com
 *
 * Please visit http://pdfnext.katado.com for newer Versions
 * -----------------------------------------------------------------------------------------
 *  
 * Released under the GNU General Public License 
 * 
 */
require_once ('includes/application_top.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?php echo PDF_BILL_NR_TITLE; ?></title>
</head>
<body>
<?php
// Bestellungsnummer holen
$oID = $_GET['oID'];
$last_bill = 0;

$sqlBillNr = "SELECT bill_nr FROM " . TABLE_ORDERS . " WHERE orders_id = '" . $oID . "';";
$resBillNr = xtc_db_query($sqlBillNr);

// check for order
if (!xtc_db_num_rows($resBillNr)) {
    die('Given orders_id does not exist!');   
}

// get data
$rowBillNr = xtc_db_fetch_array($resBillNr);
if (isset($order_bill['bill_nr']) && $order_bill['bill_nr'] != '') {
    $order_bill = $rowBillNr['bill_nr'];

    echo PDF_BILL_NR_GIVEN . $order_bill;
    echo '<br /><br /><input type="button" value="' . PDF_CLOSE_WINDOW . '" onclick="window.close()" style="padding:5px;"/>';
} else {
    // Get last BILL_NR

    $sqlLastBill = "SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'PDF_BILL_LASTNR'";
    $resLastBill = xtc_db_query($sqlLastBill);
    $rowLastBill = xtc_db_fetch_array($resLastBill);
    $last_bill = $rowLastBill['configuration_value'];

    // check given bill_nr
    if(!isset($_POST['new_billnr'])) {
        $new_billnr = $last_bill + 1;
?>
<h3><?php echo PDF_BILL_NR_HEAD; ?></h3>
<?php echo PDF_BILL_NR_INFO; ?><br /><br />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?oID=<?php echo $_GET['oID']; ?>" method="post">
    <input type="text" name="new_billnr" value="<?php echo $new_billnr; ?>"/>
    <input type="submit" name="confirm" value="<?php echo PDF_BILL_NR_SUBMIT; ?>" style="font-weight:bold; padding:2px;"/>
    <input type="button" value="<?php echo PDF_CANCEL; ?>" onclick="window.close()" style="padding:2px;"/>
</form>
<?php
    } else {
        // get bill_nr from POST 
        $new_billnr = $_POST['new_billnr'];

        // save new bill_nr 
        $sqlUpOrder = "UPDATE " . TABLE_ORDERS . " SET bill_nr = '" . $new_billnr . "' WHERE orders_id = '" . $oID . "'";
        $resUpOrder = xtc_db_query($sqlUpOrder);    

        // update last bill_nr
        $sqlUpLast = "UPDATE " . TABLE_CONFIGURATION . " SET configuration_value='" . $new_billnr . "' WHERE configuration_key = 'PDF_BILL_LASTNR'";
        $resUpLast = xtc_db_query($sqlUpLast);

        // redirec to print_order_pdf
        xtc_redirect(xtc_href_link(FILENAME_PRINT_ORDER_PDF, 'oID=' . $_GET['oID']));
    }
}
?>
</body>
</html>

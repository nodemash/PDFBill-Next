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
require('includes/application_top.php');
define('FPDF_FONTPATH', DIR_FS_CATALOG . DIR_WS_CLASSES . 'FPDF/font/');

// include needed classes
require_once(DIR_WS_CLASSES . 'order.php');
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'FPDF/PdfRechnung.php');

// include needed functions
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
require_once(DIR_FS_INC . 'xtc_pdf_bill.inc.php');

require_once(DIR_FS_INC . 'xtc_get_order_data.inc.php');
require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
require_once(DIR_FS_INC . 'xtc_format_price_order.inc.php');
require_once(DIR_FS_INC . 'xtc_utf8_decode.inc.php');

// check for oID
if (!isset($_GET['oID'])) {
    die('Something went wrong! No oID was given!');
} else {
    $oID = xtc_db_input($_GET['oID']);
}

// Send PDF to customer if requested
if (isset($_GET['send'])) {
    // generate bill and send to customer
    xtc_pdf_bill($oID, true, true);

// without Mail - just generate
} else { 
    xtc_pdf_bill($oID, false, true);
}

// replace Variables for filePrefix
$sqlODetail = "
SELECT 
    customers_id,
    customers_cid,
    bill_nr
FROM  " . TABLE_ORDERS . "
WHERE orders_id = '" . $oID . "'
";
$resODetail = xtc_db_query($sqlODetail);
$rowODetail = xtc_db_fetch_array($resODetail);

// use customers_id as the real id?
if (PDF_USE_CUSTOMER_ID == 'true') {
    $customers_id = $rowODetail['customers_id'];
} else {
    $customers_id = $rowODetail['customers_cid'];
}

// bill_nr if exists
$order_bill = $rowODetail['bill_nr'];

// create FilePrefix
$filePrefix = trim(PDF_FILENAME_SLIP); 
$filePrefix = str_replace('{oID}', $oID, $filePrefix);
$filePrefix = str_replace('{bill}', $order_bill, $filePrefix);
$filePrefix = str_replace('{cID}', $customers_id, $filePrefix);
$filePrefix = str_replace(' ', '_', $filePrefix);
if ($filePrefix == '') $filePrefix = $oID;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?php echo PDF_PRINT_PACKINGSLIP_PDF_TITLE; ?></title>
</head>
<body <?php if (isset($_GET['send'])) echo "onload='window.close();'"; ?>>
<h3><?php echo PDF_PRINT_PACKINGSLIP_PDF_TITLE; ?></h3>

<?php echo PDF_PRINT_PACKINGSLIP_SEND_TEXT; ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?oID=<?php echo $_GET['oID']; ?>&send=1"><?php echo PDF_PRINT_PACKINGSLIP_SEND; ?></a>
<br/>
<br/>
<?php echo PDF_PRINT_PACKINGSLIP_DL_TEXT; ?> <a href="invoice/<?php echo $filePrefix; ?>.pdf"><?php echo PDF_PRINT_PACKINGSLIP_DL; ?></a>
<br/>
<br/>
<input type="button" value="<?php echo PDF_CLOSE_WINDOW; ?>" onclick="window.close()" />
</body>
</html>

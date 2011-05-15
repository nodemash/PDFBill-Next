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

// check for oID
if (!isset($_GET['oID'])) {
    die('Something went wrong! No oID was given!');
}

// Send PDF to customer if requested
if (isset($_GET['send'])) {
    // generate bill and send to customer
    xtc_pdf_bill($_GET['oID'], true);

// without Mail - just generate
} else { 
    xtc_pdf_bill($_GET['oID'], false);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?php echo PDF_PRINT_ORDER_PDF_TITLE; ?></title>
</head>
<body <?php if (isset($_GET['send'])) echo "onload='window.close();'"; ?>>
<h3><?php echo PDF_PRINT_ORDER_PDF_TITLE; ?></h3>

<?php echo PDF_PRINT_ORDER_SEND_TEXT; ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?oID=<?php echo $_GET['oID']; ?>&send=1"><?php echo PDF_PRINT_ORDER_SEND; ?></a>
<br/>
<br/>
<?php echo PDF_PRINT_ORDER_DL_TEXT; ?> <a href="invoice/<?php echo PDF_FILENAME_SLIP . $_GET['oID']; ?>.pdf"><?php echo PDF_PRINT_ORDER_DL; ?></a>
<br/>
<br/>
<input type="button" value="<?php echo PDF_CLOSE_WINDOW; ?>" onclick="window.close()" />
</body>
</html>

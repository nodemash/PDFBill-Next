-----------------------------------------------------------------------------------------
PDF-RechnungNEXT by Robert Hoppe
Copyright 2012 Robert Hoppe - xtcm@katado.com - http://www.katado.com

Please visit http://pdfnext.katado.com for newer Versions
 
Released under the GNU General Public License 
-----------------------------------------------------------------------------------------

### => ACHTUNG:

In dieses Modul ist viel Zeit geflossen. Erfuelle mir doch einfach einen meiner 
Wuensche auf Amazon um deine Dankbarkeit zu zeigen:

http://www.amazon.de/registry/wishlist/2DRXJOGW34YRV

Jeder erfuellter Wunsch steigert meine Motivation an diesem Projekt weiter zu arbeiten!!


Initiale Version von MarcusReis aus dem xtcModified Forum
http://www.xtc-modified.org/forum/topic.php?id=12939

###
-----------------------------------------------------------------------------------------

-----------------------------------------------------------------------------------------

Anleitung um das Modul PDFBill-Next in eine "beliebige" Version zu integrieren. Fuer diese 
Anleitung werden folgende Dateien aus dem Ordner shoproot benoetigt:

admin/bill_nr.php
admin/invoice
admin/print_order_pdf.php
admin/print_packingslip_pdf.php
inc/xtc_pdf_bill.inc.php
inc/xtc_utf8_decode.inc.php
inc/xtc_get_bill_nr.inc.php
includes/classes/FPDF/font/courier.php
includes/classes/FPDF/font/desktop.ini
includes/classes/FPDF/font/helvetica.php
includes/classes/FPDF/font/helveticab.php
includes/classes/FPDF/font/helveticabi.php
includes/classes/FPDF/font/helveticai.php
includes/classes/FPDF/font/makefont
includes/classes/FPDF/font/makefont/cp1250.map
includes/classes/FPDF/font/makefont/cp1251.map
includes/classes/FPDF/font/makefont/cp1252.map
includes/classes/FPDF/font/makefont/cp1253.map
includes/classes/FPDF/font/makefont/cp1254.map
includes/classes/FPDF/font/makefont/cp1255.map
includes/classes/FPDF/font/makefont/cp1257.map
includes/classes/FPDF/font/makefont/cp1258.map
includes/classes/FPDF/font/makefont/cp874.map
includes/classes/FPDF/font/makefont/iso-8859-1.map
includes/classes/FPDF/font/makefont/iso-8859-11.map
includes/classes/FPDF/font/makefont/iso-8859-15.map
includes/classes/FPDF/font/makefont/iso-8859-16.map
includes/classes/FPDF/font/makefont/iso-8859-2.map
includes/classes/FPDF/font/makefont/iso-8859-4.map
includes/classes/FPDF/font/makefont/iso-8859-5.map
includes/classes/FPDF/font/makefont/iso-8859-7.map
includes/classes/FPDF/font/makefont/iso-8859-9.map
includes/classes/FPDF/font/makefont/koi8-r.map
includes/classes/FPDF/font/makefont/koi8-u.map
includes/classes/FPDF/font/makefont/makefont.php
includes/classes/FPDF/font/symbol.php
includes/classes/FPDF/font/times.php
includes/classes/FPDF/font/timesb.php
includes/classes/FPDF/font/timesbi.php
includes/classes/FPDF/font/timesi.php
includes/classes/FPDF/font/zapfdingbats.php
includes/classes/FPDF/fpdf.php
includes/classes/FPDF/fpdf_protection.php
includes/classes/FPDF/html2pdf.php
includes/classes/FPDF/PdfBrief.php
includes/classes/FPDF/PdfRechnung.php
lang/english/admin/bill_nr.php
lang/english/admin/print_order_pdf.php
lang/english/admin/print_packingslip_pdf.php
lang/english/modules/contribution
lang/english/modules/contribution/pdfbill.php
lang/german/admin/bill_nr.php
lang/german/admin/print_order_pdf.php
lang/german/admin/print_packingslip_pdf.php
lang/german/modules/contribution/pdfbill.php
templates/xtc5/admin/mail/english/invoice_mail.html
templates/xtc5/admin/mail/english/invoice_mail.txt
templates/xtc5/admin/mail/german/invoice_mail.html
templates/xtc5/admin/mail/german/invoice_mail.txt
templates/xtc5/img/logo_invoice.png 

Diese koennen uebernommen werden. Wie die bestehenden Dateien geaendert werden muessen wird hier beschrieben.
-----------------------------------------------------------------------------------------


###################################################
checkout_process.php

____________________________________________________
Zeile39 
Suche nach: 
require_once (DIR_FS_INC.'changedatain.inc.php');

Danach folgendes einfuegen:
// includes for PDFBill NEXT
require_once(DIR_WS_CLASSES . 'class.phpmailer.php');
require_once(DIR_WS_CLASSES . 'FPDF/PdfRechnung.php');
require_once(DIR_WS_CLASSES . 'order.php');

require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
require_once(DIR_FS_INC . 'xtc_pdf_bill.inc.php');
require_once(DIR_FS_INC . 'xtc_get_order_data.inc.php');
require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
require_once(DIR_FS_INC . 'xtc_format_price_order.inc.php');
require_once(DIR_FS_INC . 'xtc_utf8_decode.inc.php');

____________________________________________________
Zeile 376
Suche nach 	
// NEW EMAIL configuration !
	
$order_totals = $order_total_modules->apply_credit();
	
include ('send_order.php');

Danach folgendes einfuegen:
    // PDFBill NEXT - Send invoice if needed
    if (PDF_SEND_ORDER == 'true') {
        // get current maxbil
        $sqlBill = "SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'PDF_BILL_LASTNR'";
        $resBill = xtc_db_query($sqlBill);
        $rowBill = xtc_db_fetch_array($resBill);

        // fallback if user did something wrong
        if (is_numeric($rowBill['configuration_value'])) {
            $new_billnr = $rowBill['configuration_value'] + 1;
        } else {
            $new_billnr = 1;
        }

        // generate bill_nr
        $sqlUpOrder = "UPDATE " . TABLE_ORDERS . " SET bill_nr = '" . $new_billnr . "' WHERE orders_id = '" . $insert_id . "'";
        $resUpOrder = xtc_db_query($sqlUpOrder);

        // update last bill_nr
        $sqlUpLast = "UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '" . $new_billnr . "' WHERE configuration_key = 'PDF_BILL_LASTNR'";
        $resUpLast = xtc_db_query($sqlUpLast);

        // gernate and send bill
        xtc_pdf_bill($insert_id, true);
    }


###################################################
admin/orders.php

____________________________________________________
Nach der Zeile mit

        if (AFTERBUY_ACTIVATED == 'true') {
          $contents[] = array ('align' => 'center', 'text' => '<a class="button" href="'.xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).'oID='.$oInfo->orders_id.'&action=afterbuy_send').'">'.BUTTON_AFTERBUY_SEND.'</a>');
        }

folgendes Einfügen:

        // PDFBill NEXT Start 
        // rh s
        $contents[] = array('align' => 'center', 'text' => '<br/>');
        $order_bill = xtc_get_bill_nr($oInfo->orders_id);
        if(is_numeric($order_bill)) {
            $contents[] = array('align' => 'center', 'text' => '<a class="button" target="_blank" href="'.xtc_href_link(FILENAME_PRINT_ORDER_PDF, 'oID='.$oInfo->orders_id.'&download=1').'">' . BUTTON_INVOICE_PDF . '</a>');
        } else {
            $contents[] = array('align' => 'center', 'text' => '<a class="button" href="javascript:void(0)"  onClick="window.open(\'' . xtc_href_link(FILENAME_PDF_BILL_NR,'oID='. $oInfo->orders_id) . '\', \'popup\', \'toolbar=0, width=640, height=600\')">' . BUTTON_SET_BILL_NR . '</a>');
        }
        // PDFBill NEXT End

____________________________________________________
Nach der Zeile mit

require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');

folgendes Einfügen:

// PDFBill NEXT Start
require_once (DIR_FS_INC.'xtc_get_bill_nr.inc.php');
// PDFBill NEXT End

____________________________________________________
Nach der Zeile mit
  case 'update_order' :

folgendes Einfügen:

    // PDFBill NEXT change Start
    // sende order with current special order status id
    $sendBill = (is_numeric(PDF_STATUS_SEND_ID))? PDF_STATUS_SEND_ID : 1;
    if (PDF_STATUS_SEND == 'true' && isset($_POST['status']) && $sendBill == $_POST['status']) {
        if (!defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', DIR_FS_CATALOG . DIR_WS_CLASSES . 'FPDF/font/');
        }
        require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.phpmailer.php');
        require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'FPDF/PdfRechnung.php');
    
        // include needed functions
        require_once(DIR_FS_INC . 'xtc_get_order_data.inc.php');
        require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
        require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
        require_once(DIR_FS_INC . 'xtc_format_price_order.inc.php');
        require_once(DIR_FS_INC . 'xtc_utf8_decode.inc.php');
        require_once(DIR_FS_INC . 'xtc_pdf_bill.inc.php');


        // generate bill and send to customer
        xtc_pdf_bill(xtc_db_prepare_input($_GET['oID']), true);
    }
    // PDFBill NEXT change End

____________________________________________________
Zeile 55

Ersetze:
//select default fields
$order_select_fields = 'o.orders_id,
                        o.customers_id,
                        o.customers_name,
                        o.payment_method,
                        o.last_modified,
                        o.date_purchased,
                        o.orders_status,
                        o.currency,
                        o.currency_value,
                        o.afterbuy_success,
                        o.afterbuy_id,
                        o.language,
                        o.delivery_country,
                        o.delivery_country_iso_code_2,
                        ot.text as order_total
                        ';

Mit:
//select default fields
$order_select_fields = 'o.orders_id,
                        o.customers_id,
                        o.customers_name,
                        o.payment_method,
                        o.last_modified,
                        o.date_purchased,
                        o.orders_status,
                        o.currency,
                        o.currency_value,
                        o.afterbuy_success,
                        o.afterbuy_id,
                        o.language,
                        o.delivery_country,
                        o.delivery_country_iso_code_2,
                        ot.text as order_total,
                        o.bill_nr
                        ';

____________________________________________________
Zeile 736
Suche nach
<a class="button" href="Javascript:void()" onclick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_ORDER,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600')"><?php echo BUTTON_INVOICE; ?></a>

Fuge danach ein:
<?php
// PDFBill NEXT change Start
// rh s
$order_bill = xtc_get_bill_nr($_GET['oID']);
if(is_numeric($order_bill)) {
?>
<span style="padding:5px; font-size:11pt; border:1px solid #aaaaaa; background-color: #ffffff;"><?php echo BUTTON_BILL_NR . $order_bill; ?></span>
<a class="button" href="Javascript:void(0)" onClick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_ORDER_PDF,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><?php echo BUTTON_INVOICE_PDF; ?></a>
<?php
} else {
?>
<a class="button" href="Javascript:void(0)" onclick="window.open('<?php echo xtc_href_link(FILENAME_PDF_BILL_NR, 'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=400, height=250')"><?php echo BUTTON_SET_BILL_NR; ?></a>
<?php
}
?>
<a class="button" href="Javascript:void(0)" onClick="window.open('<?php echo xtc_href_link(FILENAME_PRINT_PACKINGSLIP_PDF,'oID='.$_GET['oID']); ?>', 'popup', 'toolbar=0, width=640, height=600')"><?php echo BUTTON_PACKINGSLIP_PDF; ?></a>
<?php
// PDFBill NEXT change End
?>

____________________________________________________
Zeile 808
Suche nach
 <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ORDERS_ID; ?></td>

Fuge danach ein
                <?php //PDFBill NEXT Start ?>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_BILL_NR; ?></td> 
                <?php //PDFBill NEXT End ?>

____________________________________________________
Zeile 855
suche nach
<td class="dataTableContent" align="right"><?php echo $orders['orders_id']; ?></td>

Fuege danach ein
                <?php // PDFBill NEXT Start ?>
                <td class="dataTableContent" align="right"><?php echo $orders['bill_nr']; ?></td>
                <?php // PDFBill NEXT End ?>


###################################################
admin/includes/application_top.php

____________________________________________________
Zeile 148
suche nach
define('FILENAME_XSELL_GROUPS','cross_sell_groups.php');

fuege danach ein
 // PDFBill NEXT
  define('FILENAME_PDF_BILL_NR','bill_nr.php');
  define('FILENAME_PRINT_ORDER_PDF','print_order_pdf.php');
  define('FILENAME_PRINT_PACKINGSLIP_PDF','print_packingslip_pdf.php');


###################################################
admin/includes/column_left.php

____________________________________________________
Zeile 188
Suche nach 
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['cross_sell_groups'] == '1')) echo '<li><a href="' . xtc_href_link(FILENAME_XSELL_GROUPS, '', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_ORDERS_XSELL_GROUP . '</a></li>';

fuege danach ein
// PDFBill NETXT
  if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['configuration'] == '1')) echo '<li><a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=99', 'NONSSL') . '" class="menuBoxContentLink"> -' . BOX_CONFIGURATION_99 . '</a></li>';


###################################################
\inc\xtc_php_mail.inc.php

____________________________________________________
Zeile 154
Suche nach 
//$mail->AddAttachment($path_to_attachement);                     // add attachments

ersetze mit
$mail->AddAttachment($path_to_attachement);                     // add attachments


###################################################
\lang\english\admin\buttons.php

____________________________________________________
Zeile 88
Suche nach 
?>

fuege vorher ein
// PDFBill NEXT
define('BUTTON_INVOICE_PDF', 'Bill PDF');
define('BUTTON_PACKINGSLIP_PDF', 'Packingslip PDF');
define('BUTTON_BILL_NR', 'Billnumber:');
define('BUTTON_SET_BILL_NR', 'Set Billnumber');


###################################################
\lang\english\admin\configuration.php

____________________________________________________
Zeile 668
Suche nach 
?>

fuege vorher ein
// PDFBill NEXT
define('BILL_LASTNR_TITLE' , 'Last bill number');
define('BILL_LASTNR_DESC' , 'Last bill number. <b>Please do not change!</b>');


###################################################
\lang\english\admin\english.php

____________________________________________________
Zeile 465
Suche nach 
?>

fuege davor ein

// PDFBill NEXT - Change START
define('BOX_CONFIGURATION_99', 'PDFBill Configuration');
define('PDF_BILL_LASTNR_TITLE', 'Last Billnumber');
define('PDF_BILL_LASTNR_DESC', 'The last Billnumber for the automated generation of Bills.');
define('PDF_USE_ORDERID_PREFIX_TITLE', 'Billnumber Prefix');
define('PDF_USE_ORDERID_PREFIX_DESC', 'Prefix for the Billnumber. Only used if order number is used as the billnumber.');
define('PDF_USE_ORDERID_TITLE', 'Order number as billnumber');
define('PDF_USE_ORDERID_DESC', 'Use billnumber instead of ordernumber');
define('PDF_CUSTOM_TEXT_TITLE', 'PDF Custom Text');
define('PDF_CUSTOM_TEXT_DESC', 'Some additional Text for PDF.');
define('PDF_STATUS_COMMENT_TITLE', 'Bill-PDF Status Comment');
define('PDF_STATUS_COMMENT_DESC', 'Bill Comment Status Update on send Bill-PDF.');
define('PDF_STATUS_COMMENT_SLIP_TITLE', 'Packaging Slip-PDF Status Comment');
define('PDF_STATUS_COMMENT_SLIP_DESC', 'Packaging Slip Comment Status Update on send Packaging Slip-PDF.');
define('PDF_FILENAME_SLIP_TITLE', 'Packaging Slip Filename');
define('PDF_FILENAME_SLIP_DESC', 'Filename of Packaging Slip. <strong>Please without .pdf and Spaces. Use underscore instead of spaces!</strong>.');
define('PDF_MAIL_SUBJECT_TITLE', 'Bill-Mail Subject');
define('PDF_MAIL_SUBJECT_DESC', 'Please fill in the Bill-Mail Subject. <strong>{oID}</strong> will be replaced with the order number.');
define('PDF_MAIL_SUBJECT_SLIP_TITLE', 'Packaging Slip-Mail Subject');
define('PDF_MAIL_SUBJECT_SLIP_DESC', 'Please fill in the Packaging-Mail Subject. <strong>{oID}</strong> will be replaced with the order number.');
define('PDF_MAIL_SLIP_COPY_TITLE', 'Packaging-Slip - Forward Mail');
define('PDF_MAIL_SLIP_COPY_DESC', 'Please enter forwarding addresses for mails of the Packaging Slip.');
define('PDF_MAIL_BILL_COPY_TITLE', 'Bill - Forward Mail');
define('PDF_MAIL_BILL_COPY_DESC', 'Please enter forwarding addresses for mails of the Bill-Mail');
define('PDF_FILENAME_TITLE', 'Bill Filename');
define('PDF_FILENAME_DESC', 'Filename of Bill. <strong>Please without .pdf</strong>. Spaces will be replaced with underscores. Variables: <strong>{oID}</strong>, <strong>{bill}</strong>, <strong>{cID}</strong>.');
define('PDF_MASTER_PASS_TITLE', 'PDF Masterpassword');
define('PDF_MASTER_PASS_DESC', 'Prevent PDF from edit with a Masterpassword');
define('PDF_SEND_ORDER_TITLE', 'Automated Bill-PDF Mail Send');
define('PDF_SEND_ORDER_DESC', 'Bill-PDF will be automatically send after the order process is finished.');

define('PDF_USE_CUSTOMER_ID_TITLE', 'Use customers-id instead of customers-csid');
define('PDF_USE_CUSTOMER_ID_DESC', 'Use the customers-id instead of customers-csi. Sometime needed if csi is not automatically set.');

define('PDF_STATUS_ID_BILL_TITLE', 'Order Status ID - Bill-PDF');
define('PDF_STATUS_ID_BILL_DESC', 'You are able to find the Order Status ID in the Browser URL-Line <strong>oID=</strong> while editing the Order Status.');
define('PDF_STATUS_ID_SLIP_TITLE', 'Order Status ID - Slip-PDF');
define('PDF_STATUS_ID_SLIP_DESC', 'You are able to find the Order Status ID in the Browser URL-Line <strong>oID=</strong> while editing the Order Status..');

define('PDF_PRODUCT_MODEL_LENGTH_TITLE', 'Maximum product model length');
define('PDF_PRODUCT_MODEL_LENGTH_DESC', 'Maximum count of characters until the product model gets truncated.');
define('PDF_UPDATE_STATUS_TITLE', 'Update order status');
define('PDF_UPDATE_STATUS_DESC', 'Update status automatically after PDF-Mail.');
define('PDF_USE_ORDERID_SUFFIX_TITLE', 'Billnumber Suffix');
define('PDF_USE_ORDERID_SUFFIX_DESC', 'Suffix for the Billnumber. Only used if order number is used as the billnumber.');
define('PDF_STATUS_SEND_TITLE', 'Send Bill on Order Status Update');
define('PDF_STATUS_SEND_DESC', '');
define('PDF_STATUS_SEND_ID_TITLE', 'Send Order Status ID for Bill-PDF');
define('PDF_STATUS_SEND_ID_DESC', 'The bill will be send on update to this Order Status ID');
define('PDF_MAIL_SLIP_FORWARDER_TITLE', 'Forward Packaging Slip');
define('PDF_MAIL_SLIP_FORWARDER_DESC', '');
define('PDF_MAIL_SLIP_FORWARDER_NAME_TITLE', 'Forwarder name');
define('PDF_MAIL_SLIP_FORWARDER_NAME_DESC', 'Enter name of the forwarder, who should get the packslip');
define('PDF_MAIL_SLIP_FORWARDER_EMAIL_TITLE', 'Forwarder email');
define('PDF_MAIL_SLIP_FORWARDER_EMAIL_DESC', 'Enter email of the forwarder, who should get the packslip');
define('PDF_MAIL_SLIP_FORWARDER_SUBJECT_TITLE', 'Subject forwarder-email');
define('PDF_MAIL_SLIP_FORWARDER_SUBJECT_DESC', 'Enter Email-Subject of forwarder-email');
// PDFBill NEXT - Change END


###################################################
\lang\english\admin\orders.php

____________________________________________________
Zeile 122
suche nach
?>

fuege vorher ein
// PDFBill NEXT
define('TABLE_HEADING_BILL_NR', 'Billnr.');


###################################################
\lang\german\admin\orders.php

____________________________________________________
Zeile 122
Suche nach
?>

fuege vorher ein
// PDFBill NEXT
define('TABLE_HEADING_BILL_NR', 'Rechnungsnr.');


###################################################
\lang\german\admin\german.php

____________________________________________________
Zeile 466
suche nach
?>

fuege vorher ein
// PDFBill NEXT - Change START
define('BOX_CONFIGURATION_99', 'PDFBill Konfiguration');
define('PDF_BILL_LASTNR_TITLE', 'Letzte Rechnungsnummer');
define('PDF_BILL_LASTNR_DESC', 'Die letzte Rechnungsnummer f&uuml;r die automatische Vergabe.');
define('PDF_USE_ORDERID_PREFIX_TITLE', 'Rechnungsnummer Prefix');
define('PDF_USE_ORDERID_PREFIX_DESC', 'Prefix f&uuml;r die Rechnungsnummer, falls die Bestellnummer als Rechnungsnummer verwendet wird.');
define('PDF_USE_ORDERID_TITLE', 'Bestellnummer als Rechnungsnummer');
define('PDF_USE_ORDERID_DESC', 'Durch diese Option wird die Bestellnummer als Rechnungsnummer verwendet.');
define('PDF_CUSTOM_TEXT_TITLE', 'PDF Zusatztext');
define('PDF_CUSTOM_TEXT_DESC', 'Dieser Text wird bei jeder PDF hinzugef&uuml;gt.');
define('PDF_STATUS_COMMENT_TITLE', 'Bestell-PDF Status Kommentar');
define('PDF_STATUS_COMMENT_DESC', 'Kommentar der beim Verschicken einer Rechnung in das System hinzugef&uuml;gt wird.');
define('PDF_STATUS_COMMENT_SLIP_TITLE', 'Lieferschein-PDF Status Kommentar');
define('PDF_STATUS_COMMENT_SLIP_DESC', 'Kommentar der beim Verschicken eines Lieferschein in das System hinzugef&uuml;gt wird.');
define('PDF_FILENAME_SLIP_TITLE', 'Lieferschein Dateiname');
define('PDF_FILENAME_SLIP_DESC', 'Dateiname des Lieferscheins. Leerzeichen werden durch einen Unterstrich ersetzt. Variablen: <strong>{oID}</strong>, <strong>{bill}</strong>, <strong>{cID}</strong>. <strong>Bitte ohne .pdf</strong>.');
define('PDF_MAIL_SUBJECT_TITLE', 'Rechnungs-Mail Betreff');
define('PDF_MAIL_SUBJECT_DESC', 'Geben Sie hier den Betreff f&uuml;r die Rechnungsmail an. <strong>{oID}</strong> dient als Platzhalter f&uuml;r die Bestellnummer.');
define('PDF_MAIL_SUBJECT_SLIP_TITLE', 'Lieferschein-Mail Betreff');
define('PDF_MAIL_SUBJECT_SLIP_DESC', 'Geben Sie hier den Betreff f&uuml;r die Lieferscheinmail an. <strong>{oID}</strong> dient als Platzhalter f&uuml;r die Bestellnummer.');
define('PDF_MAIL_SLIP_COPY_TITLE', 'Lieferschein - Weiterleitungsadresse');
define('PDF_MAIL_SLIP_COPY_DESC', 'Geben Sie hier eine E-Mailaddresse an, wenn Sie eine Kopie erhalten wollen.');
define('PDF_MAIL_BILL_COPY_TITLE', 'Rechnung - Weiterleitungsadresse');
define('PDF_MAIL_BILL_COPY_DESC', 'Geben Sie hier eine E-Mailaddresse an, wenn Sie eine Kopie erhalten wollen.');
define('PDF_FILENAME_TITLE', 'Rechnung Dateiname');
define('PDF_FILENAME_DESC', 'Dateiname der Rechnung. Leerzeichen werden durch einen Unterstrich ersetzt. Variablen: <strong>{oID}</strong>, <strong>{bill}</strong>, <strong>{cID}</strong>. <strong>Bitte ohne .pdf</strong>.');
define('PDF_MASTER_PASS_TITLE', 'PDF Masterpasswort');
define('PDF_MASTER_PASS_DESC', 'Damit Ihre PDF-Rechnungen/Lieferscheine nicht ohne weiteres editiert werden k&oum;nnen.');
define('PDF_SEND_ORDER_TITLE', 'Rechnungs-PDF automatisch versenden');
define('PDF_SEND_ORDER_DESC', 'Wenn diese Option aktiviert ist, wird die Rechnungs-PDF direkt nach der Bestellung automatisch verschickt.');

define('PDF_USE_CUSTOMER_ID_TITLE', 'Nutze Kunden-ID als Kundennummer');
define('PDF_USE_CUSTOMER_ID_DESC', 'Die Kunden-ID wird als Kundennummer verwendet. Bitte auf false stellen, falls eine Kundennummer vergeben wird.');

define('PDF_STATUS_ID_BILL_TITLE', 'Bestellstatus-ID - Rechnungs-PDF');
define('PDF_STATUS_ID_BILL_DESC', 'Die Bestellstatus-ID finden Sie in der Browserzeile nach <strong>oID=</strong> wenn Sie den Bestellstatus editieren.');
define('PDF_STATUS_ID_SLIP_TITLE', 'Bestellstatus-ID - Lieferschein');
define('PDF_STATUS_ID_SLIP_DESC', 'Die Bestellstatus-ID finden Sie in der Browserzeile nach <strong>oID=</strong> wenn Sie den Bestellstatus editieren.');

define('PDF_PRODUCT_MODEL_LENGTH_TITLE', 'Maximall&auml;nge Artikelnummer');
define('PDF_PRODUCT_MODEL_LENGTH_DESC', 'Anzahl der Zeichen nachdem eine Artikelnummer abgeschnitten wird. Bitte beachten, dass zu Lange Artikelnummer das Layout der PDF zerst&ouml;ren k&ouml;nnen.');
define('PDF_UPDATE_STATUS_TITLE', 'Bestellstatus aktualisieren');
define('PDF_UPDATE_STATUS_DESC', 'Bestellstatus wird nach dem Mailversand der PDF automatisch aktualisiert.');
define('PDF_USE_ORDERID_SUFFIX_TITLE', 'Rechnungsnummer Suffix');
define('PDF_USE_ORDERID_SUFFIX_DESC', 'Suffix f&uuml;r die Rechnungsnummer, falls die Bestellnummer als Rechnungsnummer verwendet wird.');
define('PDF_STATUS_SEND_TITLE', 'Rechnung bei Umstellung auf Bestellstatus versenden');
define('PDF_STATUS_SEND_DESC', '');
define('PDF_STATUS_SEND_ID_TITLE', 'Sende Bestellstatus-ID - Rechnungs-PDF');
define('PDF_STATUS_SEND_ID_DESC', 'Bei Umstellung auf diese ID wird die Rechnung verschickt.');

define('PDF_STATUS_SEND_TITLE', 'Rechnung bei Umstellung auf Bestellstatus versenden');
define('PDF_STATUS_SEND_DESC', '');
define('PDF_STATUS_SEND_ID_TITLE', 'Sende Bestellstatus-ID - Rechnungs-PDF');
define('PDF_STATUS_SEND_ID_DESC', 'Bei Umstellung auf diese ID wird die Rechnung verschickt.');
define('PDF_MAIL_SLIP_FORWARDER_TITLE', 'Lieferschein weiterleiten');
define('PDF_MAIL_SLIP_FORWARDER_DESC', '');
define('PDF_MAIL_SLIP_FORWARDER_NAME_TITLE', 'Logistiker Name');
define('PDF_MAIL_SLIP_FORWARDER_NAME_DESC', 'Geben Sie hier den Namen des Logistikers ein, der den Lieferschein erhält');
define('PDF_MAIL_SLIP_FORWARDER_EMAIL_TITLE', 'Logistikers Email');
define('PDF_MAIL_SLIP_FORWARDER_EMAIL_DESC', 'Geben Sie hier die E-Mail-Addresse des Logistikers ein, der den Lieferschein erhält');
define('PDF_MAIL_SLIP_FORWARDER_SUBJECT_TITLE', 'Betreff Logistiker E-Mails');
define('PDF_MAIL_SLIP_FORWARDER_SUBJECT_DESC', 'Geben Sie hier den Betreff des Logistiker-Emails ein');
// PDFBill NEXT - Change END


###################################################
\lang\german\admin\configuration.php

____________________________________________________
Zeile 670
suche nach 
?>

fuege vorher ein
// PDFBillNext
define('BILL_LASTNR_TITLE' , 'Letzte Rechnungsnummer');
define('BILL_LASTNR_DESC' , 'Letzte Rechnungsnummer. <b>Bitte nicht ver&auml;ndern!</b>');


###################################################
\lang\german\admin\buttons.php

____________________________________________________
Zeile 88
suche nach 
?>

fuege vorher ein
// PDFBill NEXT
define('BUTTON_INVOICE_PDF', 'Rechnung PDF');
define('BUTTON_PACKINGSLIP_PDF', 'Lieferschein PDF');
define('BUTTON_BILL_NR', 'Rechnungsnummer:');
define('BUTTON_SET_BILL_NR', 'Rechnungsnummer vergeben');



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
function xtc_pdf_bill ($oID, $send=false, $deliverSlip=false)
{
    // Create Order object from $oID
    $order = new order($oID);

    // Set language for bill/slip
    $language = $order->info['language'];
    if ($language == '') {
        $language = $_SESSION['language'];
    }

    // get language file
    require_once(DIR_FS_CATALOG .'lang/' . $language . '/modules/contribution/pdfbill.php');

    // Create PDF Object
    $pdf = new PdfRechnung();
    $pdf->Init("Rechnung");

    // Get Customers ID
    $sqlGetCustomer = "SELECT customers_id FROM ".TABLE_ORDERS." WHERE orders_id = '" . (int)$oID . "'";
    $resGetCustomer = xtc_db_query($sqlGetCustomer);
    $rowGetCustomer = xtc_db_fetch_array($resGetCustomer);
    $customers_id = $rowGetCustomer['customers_id'];

    // Get customer gender
    $sqlGetGender = "SELECT customers_gender FROM ".TABLE_CUSTOMERS." WHERE customers_id = '" . (int)$customers_id . "'";
    $resGetGender = xtc_db_query($sqlGetGender);
    $rowGetGender = xtc_db_fetch_array($resGetGender);
    $customer_gender = $rowGetGender['customers_gender'];

    // Change Adress on Delivery Slip
    if ($deliverSlip === true) { 
        $customer_address = xtc_address_format($order->customer['format_id'], $order->delivery, 1, '', '<br>'); 
    } else { 
        $customer_address = xtc_address_format($order->customer['format_id'], $order->billing, 1, '', '<br>'); 
    }

    // PDF Address and Logo PDF-Output
    $pdf->Adresse(str_replace("<br>", "\n", $customer_address), TEXT_PDF_SHOPADRESSEKLEIN);
    $pdf->Logo(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/logo_invoice.png');

    // Convert Datum into  tt.mm.jj umwandeln
    preg_match("/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/", $order->info['date_purchased'], $dt);
    $date_purchased = time(); // Current Daet

    // Get Payment method
    if ($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment') {
        $paymentFile = DIR_FS_CATALOG . 'lang/' . $language . '/modules/payment/' . $order->info['payment_method'] . '.php';
        include($paymentFile);

        $payment_method = constant(strtoupper('MODULE_PAYMENT_' . $order->info['payment_method'] . '_TEXT_TITLE'));
    }

    // Get bill_nr
    $sqlBill = "SELECT bill_nr FROM " . TABLE_ORDERS . " WHERE orders_id = '" . $oID . "'";
    $resBill = xtc_db_query($sqlBill);
    $rowBill = xtc_db_fetch_array($resBill);
    $order_bill = $rowBill['bill_nr'];

    // Create Bill Data
    $pdf->Rechnungsdaten($customers_id, $order_bill, $oID, date("d.m.y", $date_purchased), $payment_method, $deliverSlip);
    $pdf->RechnungStart($order->customer['lastname'], $customer_gender, $deliverSlip);
    $pdf->ListeKopf($deliverSlip);

    // Product Informations
    $sqlProdInfos = "
    SELECT
        products_id,
        orders_products_id,
        products_model,
        products_name,
        products_price,
        final_price,
        products_quantity
    FROM " . TABLE_ORDERS_PRODUCTS."
    WHERE orders_id = '" . (int)$oID . "'";
    $resProdInfos = xtc_db_query($sqlProdInfos);

    // init order_data
    $order_data = array();

    // Add Products with attributes to PDF
    while ($order_data_values = xtc_db_fetch_array($resProdInfos)) {
        $sqlAttributes = "
        SELECT
            products_options,
            products_options_values,
            price_prefix,
            options_values_price
        FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
        WHERE orders_products_id = '" . $order_data_values['orders_products_id'] . "'";
        $resAttributes = xtc_db_query($sqlAttributes);

        // init attribute strings
        $attributes_data = '';
        $attributes_model = '';

        // fetch attributes
        while ($attributes_data_values = xtc_db_fetch_array($resAttributes)) {
            $attributes_data .= $attributes_data_values['products_options'] . ': ' . $attributes_data_values['products_options_values'] . "\n";
            $attributes_model .= xtc_get_attributes_model (
                $order_data_values['products_id'], 
                $attributes_data_values['products_options_values'], 
                $attributes_data_values['products_options']
            )."\n";
        }

        // Deliverslip is without price
        if ($deliverSlip == true) { 
            $pdf->ListeProduktHinzu (
                $order_data_values['products_quantity'],
                $order_data_values['products_name'], 
                trim(html_entity_decode($attributes_data)), 
                $order_data_values['products_model'], 
                trim(html_entity_decode($attributes_model)), 
                '', 
                ''
            ); 
        } else { 
            $pdf->ListeProduktHinzu(
                $order_data_values['products_quantity'], 
                $order_data_values['products_name'], 
                trim(html_entity_decode($attributes_data)), 
                substr($order_data_values['products_model'], 0,7), 
                trim(html_entity_decode($attributes_model)), 
                xtc_format_price_order($order_data_values['products_price'], 1, $order->info['currency']), 
                xtc_format_price_order($order_data_values['final_price'], 1, $order ->info['currency'])
            ); 
        }
    }
    
    // Add Total to PDF
    $sqlOrderTotal = "
    SELECT
        title,
        text,
        class,
        value,
        sort_order
    FROM " . TABLE_ORDERS_TOTAL . "
    WHERE orders_id = '" . (int)$oID . "'
    ORDER BY sort_order ASC";
    $resOrderTotal = xtc_db_query($sqlOrderTotal);

    // init order_data
    $order_data = array();

    // fetch order data
    while ($oder_total_values = xtc_db_fetch_array($resOrderTotal)) {
        $order_data[] = array (
            'title' => $oder_total_values['title'], 
            'class'=> $oder_total_values['class'], 
            'value'=> $oder_total_values['value'], 
            'text' => $oder_total_values['text']
        );
    }

    // Generate PDF
    $pdf->Betrag($order_data);
    $pdf->RechnungEnde($deliverSlip);
    $pdf->Kommentar($order->info['comments']);

    // Generate into given Directory
    if (!$deliverSlip) {
        $filePrefix = PDF_FILENAME;
    } else {
        $filePrefix = PDF_FILENAME_SLIP;
    }

    // Filename for BILL or SLIP
    $filename = DIR_FS_DOCUMENT_ROOT . 'admin/invoice/' . $filePrefix . $oID . '.pdf';
    $pdf->Output($filename , 'F');

    // Send PDF to Customer and maybe to copy-Address
    if ($send == true) {
        // attachment file
        $attachement_filename = $filename; 

        // mail name
        $name = $order->customer['firstname']." ".$order->customer['lastname'];

        // create new Smarty Object
        $smarty = new Smarty;

        // assign language to template for caching
        $smarty->assign('language', $_SESSION['language']);
        $smarty->caching = false;

        // set dirs manual
        $smarty->template_dir = DIR_FS_CATALOG.'templates';
        $smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
        $smarty->config_dir = DIR_FS_CATALOG.'lang';

        $smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
        $smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

        // text assigns
        if ($deliverSlip == true) {
            $smarty->assign('PDF_TYPE', TEXT_PDF_MAIL_LIEFERSCHEIN);
        } else {
            $smarty->assign('PDF_TYPE', TEXT_PDF_MAIL_RECHNUNG);
        }

        $html_mail = $smarty->fetch(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/admin/mail/' . $_SESSION['language'] . '/invoice_mail.html');
        $txt_mail = $smarty->fetch(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/admin/mail/' . $_SESSION['language'] . '/invoice_mail.txt');

        // generate mail subject
        if ($deliverSlip == true) {
            $subject_text = PDF_MAIL_SUBJECT_SLIP;
        } else {
            $subject_text = PDF_MAIL_SUBJECT;
        }
        $mail_subject = str_replace('{oID}', $oID, $subject_text);

        // send customer mail
        xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->customer['email_address'], $name, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, $attachement_filename, '', $mail_subject, $html_mail, $txt_mail);

        // send copy if needed
        if ($deliverSlip === true && PDF_MAIL_SLIP_COPY != '') {
            $copyMail = PDF_MAIL_SLIP_COPY;
        } else if ($deliverSlip === false && PDF_MAIL_BILL_COPY != '') {
            $copyMail = PDF_MAIL_BILL_COPY;
        }

        // copy mail needed?
        if (isset($copyMail) && $copyMail != '') {
            xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $copyMail, $name, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, $attachement_filename, '', $mail_subject, $html_mail, $txt_mail);
        }

        // Update Status to notified
        $customer_notified = '1';

        if ($deliverSlip == true) {
            $comments = PDF_STATUS_COMMENT_SLIP;
        } else { 
            $comments = PDF_STATUS_COMMENT;
        }

        $sqlStatus = "
        INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . " (
            orders_id, 
            orders_status_id, 
            date_added, 
            customer_notified, 
            comments
        ) VALUES (
            '" . xtc_db_input($oID) ."', 
            '" . xtc_db_input($status) . "', 
            now(), 
            '" . $customer_notified . "',
             '" . xtc_db_input($comments) . "'
        )";
        $resStatus = xtc_db_query($sqlStatus);
    }
}

?>

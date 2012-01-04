-----------------------------------------------------------------------------------------
PDF-RechnungNEXT by Robert Hoppe
Copyright 2011 Robert Hoppe - xtcm@katado.com - http://www.katado.com

Please visit http://pdfnext.katado.com for newer Versions
 
Released under the GNU General Public License 
-----------------------------------------------------------------------------------------
### => ACHTUNG:

In dieses Modul ist viel Zeit geflossen. Erfuelle mir doch einfach einen meiner 
Wuensche auf Amazon um deine Dankbarkeit zu zeigen:

http://www.amazon.de/registry/wishlist/2DRXJOGW34YRV

Jeder erfuellter Wunsch steigert meine Motivation an diesem Projekt weiter zu arbeiten!!

###
-----------------------------------------------------------------------------------------

I ANLEITUNG

1. Erstelle bitte zuerst ein Backup deines Shops

2. Den Inhalt des shoproot-Ordners ins root-Verzeichnis kopieren und Dateien ueberschreiben

3. Bitte die Dateirechte in admin/invocie so anpassen, dass in diesem Order vom Shop geschrieben werden kann. Die Quick and Dirty loesung ist hier ein "chmod 777 admin/invoice" oder per FTP-Tool den Ordner auf 777 zu setzen. 

4. Um die Aenderungen fuer die Datenbank einzuspielen gibt es zwei Moeglichkeiten (BITTE NUR EINE DAVON ANWENDEN):
(a) Die "pdfbill_installer.php" aus dem Ordner "installer" in das root-Verzeichnis des Shops kopieren und per http://urlzumshop/pdfbill_installer.php aufrufen.
(b) Die 'PDFBillNEXT.sql' mittels phpMyAdmin in die Datenbank einspielen

5. Das Logo (logo_invoice.png) sollte sich in dem Template-Ordner in 'img' befinden und eine Aufloesung von 300 dpi haben. (Das Image resizing ist darauf eingestellt. Eine niedrigere Aufloesung ergibt ein kleineres Logo.) 

6. Im Ordner bitte passe die 'pdfbill.php' fuer die jeweiligen Sprachen an. Diese findest du unter 'lang/SPRACHE/modules/contribution/'. Dort werden alle festen Textaenderungen fuer die PDF-Rechnung und den Lieferschein vorgenommen

7. Es erscheint nun ein neues Menu unter Konfiguration im Adminpanel. Bitte nehme hier alle Einstellungen vor!


-----------------------------------------------------------------------------------------

II Fragen und Antworten

1. Wo finde ich das Logo fŸr die PDF? 

Das Logo ist unter templates/#DEIN_TEMPLATE#/img/logo_invoice.png zu finden.
--

2. Es kommt die Fehlermeldung "FPDF error: Alpha channel not supported:"

Bitte das Logo erneut als PNG ohne Transparenz abspeichern.
--

3. Wie kombiniere ich die ANLEITUNG: Bestellnummern mit "Jahr Monat Tag - Nummer fortlaufend" mit diesem Modul?

Bitte in der xtc_pdf_bill.inc.php nach der Zeile "$order_bill = $rowBill['bill_nr'];" suchen und danach folgendes einfuegen:

if (!function_exists('xtc_build_order_id')) require_once(DIR_FS_INC . 'xtc_build_order_id.inc.php');
$oID = xtc_build_order_id($order->info['date_purchased'], $oID));

--

4. Beim oeffnen der Popups werde ich auf die account.php weitergeleitet, was nun?

Bitte im phpMyAdmin folgenden SQL-Befehl ausfuehren:
UPDATE admin_access SET print_order_pdf = '1' WHERE customers_id = '1';

--

5. Ich moechte zusaetzlich zur Rechnungs-PDF die AGBS mitschicken.

Hierfuer einfach die AGB.pdf in den Ordner media im Root-Verzeichnis des Shops kopieren und folgende Dateiaenderungen vollziehen:

Im Ordner "inc" in der Datei "xtc_php_mail.inc.php" folgende Zeile auskommentieren
//$mail->addAttachment($path_to_more_attachements);

und dann wenn die AGB.pdf im Ordner "media" liegt noch folgende Aenderung in der "xtc_pdf_bill.inc.php" vollziehen:

// send customer mail
xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->customer['email_address'], $name, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, $attachement_filename, '', $mail_subject, $html_mail, $txt_mail);

in folgenden Code aendern 

// send customer mail
$agbpdf = DIR_FS_DOCUMENT_ROOT . 'media/AGB.pdf';
xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->customer['email_address'], $name, '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, $attachement_filename, $agbpdf, $mail_subject, $html_mail, $txt_mail);

--
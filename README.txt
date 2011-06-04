-----------------------------------------------------------------------------------------
PDF-RechnungNEXT by Robert Hoppe
Copyright 2011 Robert Hoppe - xtcm@katado.com - http://www.katado.com

Please visit http://pdfnext.katado.com for newer Versions
 
Released under the GNU General Public License 
-----------------------------------------------------------------------------------------
### => ACHTUNG:

In dieses Modul is viel Zeit geflossen. Erfuelle mir doch einfach einen meiner 
Wuensche auf Amazon um deine Dankbarkeit zu zeigen:

http://www.amazon.de/registry/wishlist/2DRXJOGW34YRV

Jeder erfuellter Wunsch steigert meine Motivation an diesem Projekt weiter zu arbeiten!!

-----------------------------------------------------------------------------------------

I ANLEITUNG

1. Erstelle bitte zuerst ein Backup deines Shops

2. Den Inhalt des shoproot-Ordners ins root-Verzeichnis kopieren und Dateien ueberschreiben

3. Bitte die Dateirechte in admin/invocie so anpassen, dass in diesem Order vom Shop geschrieben werden kann. Die Quick and Dirty loesung ist hier ein "chmod 777 admin/invoice" oder per FTP-Tool den Ordner auf 777 zu setzen. 

4. Die 'PDFBillNEXT.sql' mittels phpMyAdmin in die Datenbank einspielen

5. Das Logo (logo_invoice.png) sollte sich in dem Template-Ordner in 'img' befinden und eine Aufloesung von 300 dpi haben. (Das Image resizing ist darauf eingestellt. Eine niedrigere Aufloesung ergibt ein kleineres Logo.) 

6. Im Ordner bitte passe die 'pdfbill.php' fuer die jeweiligen Sprachen an. Diese findest du unter 'lang/SPRACHE/modules/contribution/'. Dort werden alle festen Textaenderungen fuer die PDF-Rechnung und den Lieferschein vorgenommen

7. Es erscheint nun ein neues Menu unter Konfiguration im Adminpanel. Bitte nehme hier alle Einstellungen vor!

ALTER TABLE orders ADD bill_nr INT( 10 ) NULL ;

ALTER TABLE admin_access ADD bill_nr INT( 1 ) NOT NULL ;
UPDATE admin_access SET bill_nr = '1' WHERE customers_id = '1';

ALTER TABLE admin_access ADD print_order_pdf INT( 1 ) NOT NULL ;
UPDATE admin_access SET print_order_pdf = '1' WHERE customers_id = '1';

ALTER TABLE admin_access ADD print_packingslip_pdf INT( 1 ) NOT NULL ;
UPDATE admin_access SET print_packingslip_pdf = '1' WHERE customers_id = '1';

INSERT INTO `configuration_group` (`configuration_group_id`, `configuration_group_title`, `configuration_group_description`, `sort_order`, `visible`) VALUES
(99, 'PDFBill Configuration', 'PDFBill Overall Configuration', NULL, 99);

INSERT INTO `configuration` (`configuration_id`, `configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES
(158, 'SMTP_PASSWORD', 'Please Enter', 12, 7, NULL, '0000-00-00 00:00:00', NULL, NULL),
(262, 'PDF_BILL_LASTNR', '1', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
(263, 'PDF_SEND_ORDER', 'true', 99, 0, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
(264, 'PDF_MASTER_PASS', 'heresomepass', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
(265, 'PDF_FILENAME', 'SomeBill', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
(266, 'PDF_MAIL_BILL_COPY', '', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
(267, 'PDF_MAIL_SLIP_COPY', '', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
(268, 'PDF_MAIL_SUBJECT', 'Your PDFBill NEXT', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
(269, 'PDF_STATUS_COMMENT', 'Rechnung versendet', 99, 1, NULL, '0000-00-00 00:00:00', NULL, NULL),
(270, 'PDF_CUSTOM_TEXT', '', 99, 2, NULL, '0000-00-00 00:00:00', NULL, NULL),
(271, 'PDF_USE_ORDERID', 'true', 99, 0, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
(272, 'PDF_USE_ORDERID_PREFIX', 'RE', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
(273, 'PDF_FILENAME_SLIP', 'SomeSlip', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
(274, 'PDF_STATUS_COMMENT_SLIP', 'Lieferschein verschickt', 99, 1, NULL, '0000-00-00 00:00:00', NULL, NULL),
(275, 'PDF_MAIL_SUBJECT_SLIP', 'Ihr Lieferschein', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL);

ALTER TABLE orders ADD bill_nr INT( 10 ) NULL ;

ALTER TABLE admin_access ADD bill_nr INT( 1 ) NOT NULL ;
UPDATE admin_access SET bill_nr = '1' WHERE customers_id = '1';

ALTER TABLE admin_access ADD print_order_pdf INT( 1 ) NOT NULL ;
UPDATE admin_access SET print_order_pdf = '1' WHERE customers_id = '1';

ALTER TABLE admin_access ADD print_packingslip_pdf INT( 1 ) NOT NULL ;
UPDATE admin_access SET print_packingslip_pdf = '1' WHERE customers_id = '1';

INSERT INTO `configuration_group` (`configuration_group_id`, `configuration_group_title`, `configuration_group_description`, `sort_order`, `visible`) VALUES
(99, 'PDFBill Configuration', 'PDFBill Overall Configuration', NULL, 99);

INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES
('PDF_BILL_LASTNR', '0', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_SEND_ORDER', 'true', 99, 0, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('PDF_MASTER_PASS', 'heresomepass', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_FILENAME', 'SomeBill{oID}', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_MAIL_BILL_COPY', '', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_MAIL_SUBJECT', 'Your PDFBill NEXT', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_STATUS_COMMENT', 'Rechnung versendet', 99, 1, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_CUSTOM_TEXT', '', 99, 2, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_USE_ORDERID', 'true', 99, 0, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('PDF_USE_CUSTOMER_ID', 'false', 99, 0, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('PDF_STATUS_ID_BILL', '1', 99, 3, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_STATUS_ID_SLIP', '1', 99, 3, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_PRODUCT_MODEL_LENGTH', '7', 99, 3, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_UPDATE_STATUS', 'true', 99, 0, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('PDF_USE_ORDERID_PREFIX', 'RE', 99, 84, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_USE_ORDERID_SUFFIX', '', 99, 0, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_STATUS_SEND', 'false', 99, 4, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('PDF_STATUS_SEND_ID', '1', 99, 4, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_FILENAME_SLIP', 'SomeSlip{oID}', 99, 80, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_STATUS_COMMENT_SLIP', 'Lieferschein verschickt', 99, 79, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_MAIL_SUBJECT_SLIP', 'Ihr Lieferschein', 99, 80, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_MAIL_SLIP_FORWARDER_NAME', '', 99, 81, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_MAIL_SLIP_FORWARDER_EMAIL', '', 99, 82, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_MAIL_SLIP_COPY', '', 99, 83, NULL, '0000-00-00 00:00:00', NULL, NULL),
('PDF_MAIL_SLIP_FORWARDER', 'false', 99, 85, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');

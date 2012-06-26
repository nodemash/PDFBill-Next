<?php
/* --------------------------------------------------------------
   $Id: application_top.php 2798 2012-04-28 09:45:32Z web28 $

   http://www.xtc-modified.org
   Copyright (c) 2010 xtcModified
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.158 2003/03/22); www.oscommerce.com
   (c) 2003 nextcommerce (application_top.php,v 1.46 2003/08/24); www.nextcommerce.org
   (c) 2006 XT-Commerce 8application_top.php 1323 2005-10-27) ; www.xt-commerce.com

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c) Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  // Define the project version
  define('PROJECT_VERSION', 'xtcModified v1.05 dated: 2010-07-18 SP1d');
  // Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime(true));

  // security
  define('_VALID_XTC',true);

  // Disable use_trans_sid as xtc_href_link() does this manually
  if (function_exists('ini_set')) {
    @ini_set('session.use_trans_sid', 0);
  }

  // Set the local configuration parameters - mainly for developers or the main-configure
  if (file_exists('includes/local/configure.php')) {
    include('includes/local/configure.php');
  } else {
    require('includes/configure.php');
  }

  // set the level of error reporting
  if (file_exists(DIR_FS_CATALOG.'export/_error_reporting.all') || file_exists(DIR_FS_CATALOG.'export/_error_reporting.admin')) {
    error_reporting(E_ALL & ~E_NOTICE);
    //error_reporting(-1); // Development value
  } else {
    error_reporting(0);
  }

  // solve compatibility issues
  require_once (DIR_WS_FUNCTIONS.'compatibility.php');

// default time zone
  if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
	date_default_timezone_set('Europe/Berlin');
  }

  // Base/PHP_SELF/SSL-PROXY
  $PHP_SELF = $_SERVER['SCRIPT_NAME'];

  //compatibility for xtcModified 1.06 files
  define('DIR_WS_BASE', '');

  // SQL caching dir
  define('SQL_CACHEDIR',DIR_FS_CATALOG.'cache/');

  define('TAX_DECIMAL_PLACES', 0);

  // Used in the "Backup Manager" to compress backups
  define('LOCAL_EXE_GZIP', '/usr/bin/gzip');
  define('LOCAL_EXE_GUNZIP', '/usr/bin/gunzip');
  define('LOCAL_EXE_ZIP', '/usr/local/bin/zip');
  define('LOCAL_EXE_UNZIP', '/usr/local/bin/unzip');

  // define the filenames used in the project
  define('FILENAME_ACCOUNTING', 'accounting.php');
  define('FILENAME_BACKUP', 'backup.php');
  define('FILENAME_BANNER_MANAGER', 'banner_manager.php');
  define('FILENAME_BANNER_STATISTICS', 'banner_statistics.php');
  define('FILENAME_CACHE', 'cache.php');
  define('FILENAME_CAMPAIGNS', 'campaigns.php');
  define('FILENAME_CATALOG_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
  define('FILENAME_CATALOG_NEWSLETTER', 'newsletter.php');
  define('FILENAME_CATEGORIES', 'categories.php');
  define('FILENAME_CONFIGURATION', 'configuration.php');
  define('FILENAME_COUNTRIES', 'countries.php');
  define('FILENAME_CURRENCIES', 'currencies.php');
  define('FILENAME_CUSTOMERS', 'customers.php');
  define('FILENAME_CUSTOMERS_STATUS', 'customers_status.php');
  define('FILENAME_DEFAULT', 'start.php');
  define('FILENAME_DEFINE_LANGUAGE', 'define_language.php');
  define('FILENAME_FORMS', 'forms.php');
  define('FILENAME_FORM_VALUES', 'form_values.php');
  define('FILENAME_GEO_ZONES', 'geo_zones.php');
  define('FILENAME_LANGUAGES', 'languages.php');
  define('FILENAME_MAIL', 'mail.php');
  define('FILENAME_MANUFACTURERS', 'manufacturers.php');
  define('FILENAME_MODULES', 'modules.php');
  define('FILENAME_ORDERS', 'orders.php');
  define('FILENAME_ORDERS_INVOICE', 'invoice.php');
  define('FILENAME_ORDERS_PACKINGSLIP', 'packingslip.php');
  define('FILENAME_ORDERS_STATUS', 'orders_status.php');
  define('FILENAME_ORDERS_EDIT', 'orders_edit.php');
  define('FILENAME_POPUP_IMAGE', 'popup_image.php');
  define('FILENAME_PRODUCTS_ATTRIBUTES', 'products_attributes.php');
  define('FILENAME_PRODUCTS_EXPECTED', 'products_expected.php');
  define('FILENAME_REVIEWS', 'reviews.php');
  define('FILENAME_SERVER_INFO', 'server_info.php');
  define('FILENAME_SHIPPING_MODULES', 'shipping_modules.php');
  define('FILENAME_SPECIALS', 'specials.php');
  define('FILENAME_STATS_CUSTOMERS', 'stats_customers.php');
  define('FILENAME_STATS_PRODUCTS_PURCHASED', 'stats_products_purchased.php');
  define('FILENAME_STATS_PRODUCTS_VIEWED', 'stats_products_viewed.php');
  define('FILENAME_TAX_CLASSES', 'tax_classes.php');
  define('FILENAME_TAX_RATES', 'tax_rates.php');
  define('FILENAME_WHOS_ONLINE', 'whos_online.php');
  define('FILENAME_ZONES', 'zones.php');
  define('FILENAME_START', 'start.php');
  define('FILENAME_STATS_STOCK_WARNING', 'stats_stock_warning.php');
  define('FILENAME_TPL_BOXES','templates_boxes.php');
  define('FILENAME_TPL_MODULES','templates_modules.php');
  define('FILENAME_NEW_ATTRIBUTES','new_attributes.php');
  define('FILENAME_LOGOUT','../logoff.php');
  define('FILENAME_LOGIN','../login.php');
  define('FILENAME_CREATE_ACCOUNT','create_account.php');
  define('FILENAME_CREATE_ACCOUNT_SUCCESS','create_account_success.php');
  define('FILENAME_CUSTOMER_MEMO','customer_memo.php');
  define('FILENAME_CONTENT_MANAGER','content_manager.php');
  define('FILENAME_CONTENT_PREVIEW','content_preview.php');
  define('FILENAME_SECURITY_CHECK','security_check.php');
  define('FILENAME_PRINT_ORDER','print_order.php');
  define('FILENAME_CREDITS','credits.php');
  define('FILENAME_PRINT_PACKINGSLIP','print_packingslip.php');
  define('FILENAME_MODULE_NEWSLETTER','module_newsletter.php');
  define('FILENAME_GV_QUEUE', 'gv_queue.php');
  define('FILENAME_GV_MAIL', 'gv_mail.php');
  define('FILENAME_GV_SENT', 'gv_sent.php');
  define('FILENAME_COUPON_ADMIN', 'coupon_admin.php');
  define('FILENAME_POPUP_MEMO', 'popup_memo.php');
  define('FILENAME_SHIPPING_STATUS', 'shipping_status.php');
  define('FILENAME_SALES_REPORT','stats_sales_report.php');
  define('FILENAME_MODULE_EXPORT','module_export.php');
  define('FILENAME_EASY_POPULATE','easypopulate.php');
  define('FILENAME_BLACKLIST', 'blacklist.php');
  define('FILENAME_PRODUCTS_VPE','products_vpe.php');
  define('FILENAME_CAMPAIGNS_REPORT','stats_campaigns.php');
  define('FILENAME_XSELL_GROUPS','cross_sell_groups.php');
  define('FILENAME_GOOGLE_SITEMAP', '../google_sitemap.php');
  define('FILENAME_PAYPAL','paypal.php');
  define('FILENAME_PAYPAL_CHECKOUT', 'paypal_checkout.php');

  // PDFBill NEXT - Change START
  define('FILENAME_PDF_BILL_NR','bill_nr.php');
  define('FILENAME_PRINT_ORDER_PDF','print_order_pdf.php');
  define('FILENAME_PRINT_PACKINGSLIP_PDF','print_packingslip_pdf.php');
  // PDFBill NEXT - Change END


  // list of project database tables
  require_once(DIR_FS_CATALOG.DIR_WS_INCLUDES.'database_tables.php');

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_close.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_error.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_queryCached.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_perform.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_data_seek.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_insert_id.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_free_result.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_fields.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_output.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_prepare_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_ip_address.inc.php');
  require_once(DIR_FS_INC . 'xtc_setcookie.inc.php');
  require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
  require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
  require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_qty.inc.php');
  require_once(DIR_FS_INC . 'xtc_product_link.inc.php');
  require_once(DIR_FS_INC . 'xtc_cleanName.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_top_level_domain.inc.php');
  require_once (DIR_FS_INC . 'xtc_update_whos_online.inc.php');

  // design layout (wide of boxes in pixels) (default: 125)
  define('BOX_WIDTH', 125);

  // Define how do we update currency exchange rates
  // Possible values are 'oanda' 'xe' or ''
  define('CURRENCY_SERVER_PRIMARY', 'oanda');
  define('CURRENCY_SERVER_BACKUP', 'xe');

  // make a connection to the database... now
  xtc_db_connect() or die('Unable to connect to database server!');

  // set application wide parameters
  $configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . '');
  while ($configuration = xtc_db_fetch_array($configuration_query)) {
    if ($configuration['cfgKey'] != 'STORE_DB_TRANSACTIONS') {
      define($configuration['cfgKey'], $configuration['cfgValue']);
    }
  }

  define('FILENAME_IMAGEMANIPULATOR',IMAGE_MANIPULATOR);

  // move to xtc_db_queryCached.inc.php
  function xtDBquery($query) {
    if (DB_CACHE=='true') {
      $result=xtc_db_queryCached($query);
    } else {
      $result=xtc_db_query($query);
    }
  return $result;
  }

  // initialize the logger class
  require(DIR_WS_CLASSES . 'logger.php');

  // shopping cart class
  require(DIR_WS_CLASSES . 'shopping_cart.php');

  // todo
  require(DIR_WS_FUNCTIONS . 'general.php');

  // define how the session functions will be used
  require(DIR_WS_FUNCTIONS . 'sessions.php');

    // define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'html_output.php');

  // set the session name and save path
  session_name('XTCsid');
	if (STORE_SESSIONS != 'mysql') {
    session_save_path(SESSION_WRITE_DIRECTORY);
  }

  // set the type of request (secure or not)
  if (file_exists(DIR_WS_INCLUDES . 'request_type.php')) {
    include (DIR_WS_INCLUDES . 'request_type.php');
  } else {
    $request_type = 'NONSSL';
  }

  // set the top level domains
  $http_domain = xtc_get_top_level_domain(HTTP_SERVER);
  //$https_domain = xtc_get_top_level_domain(HTTPS_SERVER);
  //$current_domain = (($request_type == 'NONSSL') ? $http_domain : $https_domain);
  $current_domain = $http_domain; //currently no https_domain support

  // set the session cookie parameters
  if (function_exists('session_set_cookie_params')) {
    session_set_cookie_params(0, '/', (xtc_not_null($current_domain) ? '.' . $current_domain : ''));
  } elseif (function_exists('ini_set')) {
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_domain', (xtc_not_null($current_domain) ? '.' . $current_domain : ''));
  }

  // set the session ID if it exists
  if (isset($_POST[session_name()])) {
    session_id($_POST[session_name()]);
  } elseif (($request_type == 'SSL') && isset($_GET[session_name()])) {
    session_id($_GET[session_name()]);
  }

  @ini_set('session.use_only_cookies', (SESSION_FORCE_COOKIE_USE == 'True') ? 1 : 0); //DokuMan - 2011-01-06 - set session.use_only_cookies when force cookie is enabled

  // start the session
  $session_started = false;
  if (SESSION_FORCE_COOKIE_USE == 'True') {
    xtc_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, '/', $current_domain);
    if (isset($_COOKIE['cookie_test'])) {
        session_start();
        $session_started = true;
      }
  } elseif (CHECK_CLIENT_AGENT == 'True') {
    $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
    $spider_flag = false;
    if ($spider_flag == false) {
      session_start();
      $session_started = true;
    }
  } else {
    session_start();
    $session_started = true;
  }

  // verify the ssl_session_id if the feature is enabled
  if ( ($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true) ) {
    $ssl_session_id = getenv('SSL_SESSION_ID');
    if (!isset($_SESSION['SESSION_SSL_ID'])) {
      $_SESSION['SESSION_SSL_ID'] = $ssl_session_id;
    }
    if ($_SESSION['SESSION_SSL_ID'] != $ssl_session_id) {
      session_destroy();
      xtc_redirect(xtc_href_link(FILENAME_SSL_CHECK));
    }
  }

  // verify the browser user agent if the feature is enabled
  if (SESSION_CHECK_USER_AGENT == 'True') {
    $http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $http_user_agent2 = strtolower(getenv("HTTP_USER_AGENT"));
    $http_user_agent = ($http_user_agent == $http_user_agent2) ? $http_user_agent : $http_user_agent.';'.$http_user_agent2;
    if (!isset($_SESSION['SESSION_USER_AGENT'])) {
      $_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
    }
    if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
      session_destroy();
      xtc_redirect(xtc_href_link(FILENAME_LOGIN));
    }
  }

  // verify the IP address if the feature is enabled
  if (SESSION_CHECK_IP_ADDRESS == 'True') {
    $ip_address = xtc_get_ip_address();
    if (!isset($_SESSION['SESSION_IP_ADDRESS'])) {
      $_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
    }
    if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
      session_destroy();
      xtc_redirect(xtc_href_link(FILENAME_LOGIN));
    }
  }

  // set the language
  if (!isset($_SESSION['language']) || isset($_GET['language'])) {
    include(DIR_WS_CLASSES . 'language.php');
    $lng = new language($_GET['language']);
    if (!isset($_GET['language'])) {
      $lng->get_browser_language();
    }
    $_SESSION['language'] = $lng->language['directory'];
    $_SESSION['languages_id'] = $lng->language['id'];
    $_SESSION['language_charset'] = $lng->language['language_charset']; //web28 - 2012-04-29 - add $_SESSION['language_charset']
    $_SESSION['language_code'] = $lng->language['code']; //web28 - 2010-09-05 - add $_SESSION['language_code']
}

  // include the language translations
  require(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$_SESSION['language'] . '.php');
  require(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/buttons.php');
  $current_page = basename($_SERVER['SCRIPT_NAME']);
  if (file_exists(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.$current_page)) {
    include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/'.  $current_page);
  }

  // write customers status in session
  require(DIR_FS_CATALOG.DIR_WS_INCLUDES.'write_customers_status.php');

  // for tracking of customers
  $_SESSION['user_info'] = array();
  if (!isset($_SESSION['user_info']['user_ip'])) {
  $_SESSION['user_info']['user_ip'] = $_SERVER['REMOTE_ADDR'];
  //$_SESSION['user_info']['user_host'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $_SESSION['user_info']['user_host'] = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : '';
  $_SESSION['user_info']['advertiser'] = isset($_GET['ad']) ? $_GET['ad'] : '';
  $_SESSION['user_info']['referer_url'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
}

  // define our localization functions
  require(DIR_WS_FUNCTIONS . 'localization.php');

  // Include validation functions (right now only email address)
  //require(DIR_WS_FUNCTIONS . 'validations.php');

  // setup our boxes
  require(DIR_WS_CLASSES . 'table_block.php');
  require(DIR_WS_CLASSES . 'box.php');

  // initialize the message stack for output messages
  require(DIR_WS_CLASSES . 'message_stack.php');
  $messageStack = new messageStack();

  // split-page-results
  require(DIR_WS_CLASSES . 'split_page_results.php');

  // entry/item info classes
  require(DIR_WS_CLASSES . 'object_info.php');

  // file uploading class
  require(DIR_WS_CLASSES . 'upload.php');

  // calculate category path
  $cPath = isset($_GET['cPath']) ? $_GET['cPath'] : '';
  if (strlen($cPath) > 0) {
    $cPath_array = explode('_', $cPath);
    $current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
  } else {
    $current_category_id = 0;
  }

  // default open navigation box
  if (!isset($_SESSION['selected_box'])) {
    $_SESSION['selected_box'] = 'configuration';
  } else if(!empty($_GET['selected_box'])) {
    $_SESSION['selected_box'] = $_GET['selected_box'];
  }

  // the following cache blocks are used in the Tools->Cache section
  // ('language' in the filename is automatically replaced by available languages)
  $cache_blocks = array (array ('title' => TEXT_CACHE_CATEGORIES,
                               'code' => 'categories',
                               'file' => 'categories_box-language.cache',
                               'multiple' => true),
                        array ('title' => TEXT_CACHE_MANUFACTURERS,
                                'code' => 'manufacturers',
                                'file' => 'manufacturers_box-language.cache',
                                'multiple' => true),
                        array ('title' => TEXT_CACHE_ALSO_PURCHASED,
                                'code' => 'also_purchased',
                                'file' => 'also_purchased-language.cache',
                                'multiple' => true));

  // check if a default currency is set
  if (!defined('DEFAULT_CURRENCY')) {
    $messageStack->add(ERROR_NO_DEFAULT_CURRENCY_DEFINED, 'error');
  }

  // check if a default language is set
  if (!defined('DEFAULT_LANGUAGE')) {
    $messageStack->add(ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
  }

  // for Customers Status
  xtc_get_customers_statuses();

  $pagename = strtok($current_page, '.');
  if (!isset($_SESSION['customer_id'])) {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN));
  }

  if (xtc_check_permission($pagename) == '0') {
    xtc_redirect(xtc_href_link(FILENAME_LOGIN));
  }

  // Include Template Engine
  require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'Smarty_2.6.26/Smarty.class.php');

  // xs:booster
  define('FILENAME_XTBOOSTER','xtbooster.php');
?>

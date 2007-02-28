<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: global.php,v 1.78 2006/01/13 19:14:04 matthieu_ Exp $

/**
 * Maybe you want to edit these values (but be careful!)
 */
define('TIME_BEFORE_NEW_DAY_ARCHIVE', 0);//3600 * 3); // time before the current day is again archived when stats visited
define('INTERNAL_STATS', false); // define if phpmyvisites application statistics are saved
define('SMARTY_DEBUG', false); // define if smarty is in debug mode (means CACHE activated, template build on each page, etc.)
define('CURRENT_DAY_SIMPLE_ARCHIVE', false); // define if current day archive is simplified or not
define('CURRENT_PERIOD_SIMPLE_ARCHIVE', false); // define if temp period archive is simplified or not
define('COMPRESS_DB_DATA', true); // try to compress data before Database recording ? (can slow the process)
define('CATEGORY_DELIMITER', '/'); // category delimiter. Use '/' to make your categories automatically
define('TIME_TO_WAIT_FOR_PARALLEL_ARCHIVE', 5 * 60); // seconds to wait before try a new archive when a parallel archiving process is detected (also used in crontab task)
define('NB_IPS_RANGE_AVAILABLE', 10); // number of input for ip range exclude in the administration area
define('NB_URLS_ALIAS_AVAILABLE', 5); // number of input for urls alias in the administration area
define('NB_ELEMENTS_TO_DISPLAY', 10); // number of elements to display in tables (search engines, keywords, etc.)
define('DEFAULT_PAGE_NAME',  'index'); // default page name, when is like toto.com/ or toto.com/group1/group2/
define('PARAM_URL_PARTNER', 'pid'); // param name for identifying partner
define('PARAM_URL_NEWSLETTER', 'nid'); // param name for identifying newsletter
define('DEFAULT_DAY_TODAY', false); // is the default day today or yesterday?
define('PREFIX_FILES', 'FILE:'); // prefix for files Ex : pagename = 'FILE:group1/group2/group3/documentation.pdf'
if(!defined('DEBUG')) define('DEBUG', false);

/**
 * You shouldn't edit the values after this comment
 */
define('CACHE_SMARTY', 60*60*48); // define smarty cache lifetime 
define('DEFAULT_TIME_PAGE',  20); // 20 sec per page if not page following (time standard!)
define('TIME_SLOW_QUERY', 0.05); // slow queries are loggued into database
define('PERCENT_MAX', 1000); // percent maximum born
define('LANG_DEFAULT', 'en-utf-8.php'); // default lang file used
define('THEME_DEFAULT', 'default'); // default lang file used
define('STORE_PAG_ARRAY_IN_FILE', false); // store big page arrays in files. Increase performance for stat report, but uses disk space
define('NB_DAYS_FOR_RSS_ITEMS', 3); // days to compute to syndicate in the RSS sites (to multiply by number of sites ! be careful !)
define('ADMINISTRATOR_MAIL', 'auto-mailer@phpmyvisites.net'); // mail that appears in the mail sent from phpmyvisites
define('CACHE_RSS_LIFETIME', 3600); // lifetime of the rss syndication file cache
define('CACHE_MAIL_LIFETIME', 3600); // lifetime of the mail file cache
define('PHP_VERSION_NEEDED', '4.3'); // version of php needed for phpmyvisites to work correctly
define('MEMORY_LIMIT', 16); // good value for memory limit
define('DEFAULT_MODULE', 'view_visits'); // name of the default module loaded
define('COOKIE_PMVLOG_NAME', 'pmv_ck_'); // cookie name for log
define('COOKIE_NAME_VIEW', 'pmv_ck_view_conf'); // cookie name for stats view
define('COOKIE_NAME_SESSION', 'pmv_ck_session'); // cookie name for session
define('COOKIE_NAME_NO_STAT', 'pmv_ck_no_stat'); // cookie name for session
define('MAX_LENGTH_ONE_VALUE_IN_COOKIE', 500); // length in byte
define('MAX_URL_IN_COOKIE', 3); // number of urls stocked in the cookie
define('MAX_CATEGORY_IN_COOKIE', 3); // number of categories stocked in the cookie
define('TIME_ONE_VISIT', 30*60); // time max for one visit (the time is normed) in seconds

// Changing these values will make the link 'Next' and 'Previous' buggy, but it will work
define('MAX_DISTINCT_ELEMENTS', 300); // max elements (ex. number of keywords saved, number of distinct ISP)
define('MAX_DISTINCT_DETAILS_ELEMENTS', 300); // max distinct elements for details table (ex. number of keywords for a specific search engine)
define('MAX_DISTINCT_INTEREST_ELEMENTS', 150); // max elements used in Interest analysis

// Other
define( "DEFAULT_ACTION", false);

error_reporting( E_ALL );

if(DEBUG)
{
	define('PRINT_TIME', false);
	define('PRINT_QUERY_COUNT', true);
	define('SAVE_DB_LOG', true);
	define('PRINT_QUERY', true);
} 
else
{
	define('PRINT_QUERY', false);
	define('PRINT_TIME', false);
	define('PRINT_QUERY_COUNT', false);
	define('SAVE_DB_LOG', false);
}

// directories
define('DIR_IMG_THEMES', _DIR_PLUGIN_PHPMV . 'themes/default/images/');
define('DIR_IMG_BROWSERS', _DIR_PLUGIN_PHPMV . 'images/browsers');
define('DIR_IMG_OS',       _DIR_PLUGIN_PHPMV . 'images/os');
define('DIR_IMG_PLUGINS',  _DIR_PLUGIN_PHPMV . 'images/plugins');
define('DIR_IMG_LOGOS',  _DIR_PLUGIN_PHPMV . 'images/logos');
define('DIR_IMG_COUNTRIES_FLAGS', _DIR_PLUGIN_PHPMV . 'images/countries_flags');
define('DIR_IMG_SCREENS', _DIR_PLUGIN_PHPMV . 'images/screens');
define('LANGS_PATH', INCLUDE_PATH . '/langs');
define('DIR_IMG_MAPS', INCLUDE_PATH . '/images/maps');
define('DIR_IMG_SEARCH_ENGINES', INCLUDE_PATH . '/images/search_engines');
define('DIR_CACHE_RSS', _PHPMV_DIR_DATA . '/cache_lite/');
define('DIR_CACHE_MAIL', _PHPMV_DIR_DATA . '/cache_lite/');
define('FONTS_PATH', INCLUDE_PATH . "/libs/artichow/font/");

// xhtml 
define('HREF_DOC_OPEN', "<strong><a href='./docs/documentation.htm'>");
define('HREF_DOC_CLOSE', "</a></strong>");
define('HREF_FORUMS', "<strong><a href=\"http://www.phpmyvisites.net/forums/\" alt=\"{'head_titre'|translate}\" title=\"{'head_titre'|translate}\">http://www.phpmyvisites.net/forums/</a></strong>");
define('URL_SITE_EXAMPLE', 'http://www.site.com');
define('IP_EXAMPLE', 'A.B.C.D or A.B.C.x...');
define('GET_LIST_EXAMPLE', '<i>id;data;page</i>');

// archiving
define('REF_TYPE_SEARCH_ENGINE', 1);
define('REF_TYPE_SITE', 2);
define('REF_TYPE_PARTNER', 3);
define('REF_TYPE_NEWSLETTER', 4);
define('REF_TYPE_DIRECT_ENTRY', 5);

define('DB_ARCHIVES_PERIOD_DAY', 1);	
define('DB_ARCHIVES_PERIOD_WEEK', 2);	
define('DB_ARCHIVES_PERIOD_MONTH', 3);	
define('DB_ARCHIVES_PERIOD_YEAR', 4);	
define('DB_ARCHIVES_PERIOD_MISC_PERIOD', 5);	

define('DB_ARCHIVES_DONE', 1);
define('DB_ARCHIVES_TEMP', 2);
define('DB_ARCHIVES_FAIL', 3);

define('ARRAY_INDEX_COUNT', 1);
define('ARRAY_INDEX_PAGE_NAME', 2);
define('ARRAY_INDEX_IDPAGE', 3);
define('ARRAY_INDEX_IDCATEGORY', 4);
define('ARRAY_INDEX_LEVEL', 5);
define('ARRAY_INDEX_IDPARENT', 6);
define('ARRAY_INDEX_TIME_TOTAL' , 7);
define('ARRAY_INDEX_ENTRYPAGE', 8);
define('ARRAY_INDEX_EXITPAGE', 9);
define('ARRAY_INDEX_VARS', 10);
define('ARRAY_INDEX_TYPE', 11);
define('ARRAY_INDEX_PAGES_VISIT_ONEPAGE', 12);
define('ARRAY_INDEX_PAGES_COUNT', 13);

define('ARRAY_INDEX_RETURNING_COUNT', 14);
define('ARRAY_INDEX_NEW_COUNT', 15);

define('ARRAY_INDEX_VAR_COUNT', 1);
define('ARRAY_INDEX_VAR_VALUE', 2);

define('ARRAY_TYPE_PAGE', 1);
define('ARRAY_TYPE_FILE', 2);

define('STAT_PAGE_PER_VISIT', 0); 
define('STAT_PAGE_PER_VISIT_SIGNIFICANT', 1);
define('STAT_ONE_PAGE_RATE', 2);
define('STAT_TIME_PER_VISIT', 3);

define('DATE_NORMAL', 0);
define('DATE_GRAPH', 1);
define('DATE_GRAPH_LONG_AXIS', 2);
// Require
require_once INCLUDE_PATH."/version.php";
require_once INCLUDE_PATH."/core/datas/browserFamilies.php";
require_once INCLUDE_PATH."/core/datas/browsers.php";
require_once INCLUDE_PATH."/core/datas/countryList.php";
require_once INCLUDE_PATH."/core/datas/osLists.php";
require_once INCLUDE_PATH."/core/datas/pagesGap.php";
require_once INCLUDE_PATH."/core/datas/searchEngines.php";
require_once INCLUDE_PATH."/core/datas/timeGap.php";
require_once INCLUDE_PATH."/core/datas/fontList.php";

/**
 * Simple function to replicate PHP 5 behaviour
 */
function getMicrotime()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

$GLOBALS['time_start'] = getMicrotime();

$GLOBALS['query_count'] = 0; // number of queries of the page
$GLOBALS['a_content'] = array(); // cookie content
$GLOBALS['header_message_tpl'] = '';
$GLOBALS['header_error_message_tpl'] = '';


// id used for caching
define('SMARTY_CACHE_ID', md5(
			$_SERVER['PHP_SELF'].
			serialize($_GET).
			serialize($_POST).
			serialize(@$_COOKIE[COOKIE_NAME_SESSION]).
			serialize(@$_COOKIE[COOKIE_NAME_VIEW]['lang']).
			TIME_BEFORE_NEW_DAY_ARCHIVE.
			PHPMV_VERSION.
			_DIR_PLUGIN_PHPMV.
			INTERNAL_STATS.
			date("Y-m-d")
		)
	); 
?>
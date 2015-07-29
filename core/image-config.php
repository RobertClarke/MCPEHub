<?php
/**
 * mthumb-config.php
 *
 * Example mThumb configuration file.
 *
 * @created   4/2/14 11:52 AM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2006-2015
 * @link      http://www.mindsharelabs.com/
 *
 */

// Debugging variables
$mthumb_debug = TRUE;

if ( $mthumb_debug ) {
	if(!defined('BROWSER_CACHE_DISABLE')) {
		define ('BROWSER_CACHE_DISABLE', TRUE);
	}
	if(!defined('FILE_CACHE_ENABLED')) {
		define ('FILE_CACHE_ENABLED', FALSE);
	}
	if(!defined('DISPLAY_ERROR_MESSAGES')) {
		define ('DISPLAY_ERROR_MESSAGES', TRUE);
	}
}

// Max sizes
if(!defined('MAX_WIDTH')) {
	define('MAX_WIDTH', 1500);
}
if(!defined('MAX_HEIGHT')) {
	define('MAX_HEIGHT', 1500);
}
if(!defined('MAX_FILE_SIZE')) {
	define ('MAX_FILE_SIZE', 10485760); // 10MB
}
if(!defined('DEFAULT_Q')) {
	define ('DEFAULT_Q', 95);
}
if(!defined('BROWSER_CACHE_MAX_AGE')) {
	define ('BROWSER_CACHE_MAX_AGE', 24 * 60 * 60 * 7); // 7 days
}
/*if(!defined('FILE_CACHE_DIRECTORY')) {
	define ('FILE_CACHE_DIRECTORY', FALSE);
}*/
if(!defined('FILE_CACHE_TIME_BETWEEN_CLEANS')) {
	define ('FILE_CACHE_TIME_BETWEEN_CLEANS', 24 * 60 * 60 * 7);
}
if(!defined('FILE_CACHE_MAX_FILE_AGE')) {
	define ('FILE_CACHE_MAX_FILE_AGE', 24 * 60 * 60 * 30);
}

/*
 *  External Sites
 */
global $ALLOWED_SITES;
$ALLOWED_SITES = array(
	'mcpehub.com'
);

// The rest of the code in this config only applies to Apache mod_userdir  (URIs like /~username)

if(mthumb_in_url('~')) {
	$_SERVER['DOCUMENT_ROOT'] = mthumb_find_wp_root();
}

/**
 *  We need to set DOCUMENT_ROOT in cases where /~username URLs are being used.
 *  In a default WordPress install this should result in the same value as ABSPATH
 *  but ABSPATH and all WP functions are not accessible in the current scope.
 *
 *  This code should work in 99% of cases.
 *
 * @param int $levels
 *
 * @return bool|string
 */
function mthumb_find_wp_root($levels = 9) {

	$dir_name = dirname(__FILE__).'/';

	for($i = 0; $i <= $levels; $i++) {
		$path = realpath($dir_name.str_repeat('../', $i));
		if(file_exists($path.'/wp-config.php')) {
			return $path;
		}
	}

	return FALSE;
}

/**
 *
 * Gets the current URL.
 *
 * @return string
 */
function mthumb_get_url() {
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
	$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")).$s;
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);

	return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

/**
 *
 * Checks to see if $text is in the current URL.
 *
 * @param $text
 *
 * @return bool
 */
function mthumb_in_url($text) {
	if(stristr(mthumb_get_url(), $text)) {
		return TRUE;
	} else {
		return FALSE;
	}
}

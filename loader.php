<?php

/**
 * Website Loader
 *
 * This file includes the config and core files for the website to
 * function. It also contains any important checks required for the
 * website to properly function.
 *
 * You will find this file at the top of most pages, since it loads
 * in all the main functions for the website.
**/

// Time at which page begins to load.
$load_start = microtime(true);

// Define absolute directories for including files.
define('ABS', dirname(__FILE__) . '/');
define('CORE', ABS . 'core/');

define('SITEURL', 'http://' . filter_input(INPUT_SERVER, 'SERVER_NAME'));

// Check if the config file exists, otherwise die with an error.
if ( file_exists( ABS . 'config.php' ) ) {
	require_once( ABS . 'config.php' );
	require_once( CORE . 'core.php' );
} else {
	die('Website Error: Main configuration file is missing.');
}
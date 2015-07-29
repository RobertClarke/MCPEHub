<?php

/**
 * Website Core
 *
 * Includes all the necessary files for the website to function.
 * Also sets any necessary variables, constants and cookies.
**/

// Set default website-wide timezone.
date_default_timezone_set('America/Toronto');

// Today's date for easy access.
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');

// MySQLi Database
require_once( CORE . 'classes/database.php' );
$db = new Database( DB_HOST, DB_USER, DB_PASS, DB_TABL, DB_PORT );

// Cache Class
require_once( CORE . 'classes/cache.php');
$cache = new Cache;

// Main Functions
require_once( CORE . 'functions.php' );

// Error Class
require_once( CORE . 'classes/error.php' );
$errors = new ErrorContainer;

// User Class
require_once( CORE . 'classes/user.php' );

// Create global object for storing logged in user (if any)
global $u;
if ( $current_user = logged_in() )
    $u = new User( $current_user['id'] );

// Page Structure
require_once( CORE . 'classes/page.php' );
$page = new Page;

require_once( CORE . 'classes/email.php' );

require_once( CORE . 'smarty/Smarty.class.php' );
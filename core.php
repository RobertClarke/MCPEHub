<?php

/**
  * Website Core
  *
  * This file contains all necessary constants, includes and
  * functions required for the website to function.
  *
  * Don't touch this file unless you know what you're doing.
**/

date_default_timezone_set('America/Toronto');

define( 'ABS', dirname(__FILE__) . '/' );

// Define different $request_uri depending on if on homepage or not.
if ( substr( $_SERVER['REQUEST_URI'], -4 ) != '.php' ) $request_uri = $_SERVER['REQUEST_URI'] . 'index.php';
else $request_uri = $_SERVER['REQUEST_URI'];

$request_uri = rtrim( dirname( $request_uri ) , '/' );
define( 'MAINURL', 'http://' . $_SERVER['SERVER_NAME'] . $request_uri . '/' );

require_once('config.php');

require_once( ABS . 'core/general.php' );
require_once( ABS . 'core/structure.php' );

define( 'AUTHCOOKIE', sha1( AUTH_COOKIE ) );

// Set views and downloads cookies, if don't exist.
if ( !isset( $_COOKIE['mcpe_v'] ) ) $_COOKIE['mcpe_v'] = '';
if ( !isset( $_COOKIE['mcpe_d'] ) ) $_COOKIE['mcpe_d'] = '';

// SQL database
require_once( ABS . 'core/classes/sql.php' );
$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// User functions
require_once( ABS . 'core/classes/user.php' );
$user = new User($db);

// Error generator
require_once( ABS . 'core/classes/error.php' );
$error = new ErrorContainer();

require_once( ABS . 'core/classes/email.php' );
$mail = new Email;

require_once( ABS . 'core/classes/url.php' );
$url = new Url();

require_once( ABS . 'core/classes/pagination.php' );
$pagination = new Pagination($url);

// URL generator
//require_once( ABS . 'core/classes/url.php' );
//$c_url = new Url();

// Cookie functions
//require_once( ABS . 'core/cookie.php' );

// Post functions
require_once( ABS . 'core/classes/post.php' );
$post_tools = new Post($db, $user);

// Commenting functions
//require_once( ABS . 'core/classes/comment.php' );
//$c_comment = new Comment( $db, $user );

// Script enqueue
//require_once( ABS . 'core/classes/enqueue.php' );
//$enqueue = new Enqueue;

// PHPMailer class
//require_once( ABS . 'core/PHPMailer/PHPMailerAutoload.php' );
//require_once( ABS . 'core/email.php' );

require_once( ABS . 'core/classes/form.php' );
$form = new Form();

require_once( ABS . 'core/classes/comments.php' );
$comments = new Comments($db, $user);

?>
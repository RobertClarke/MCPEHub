<?php

require_once( '../config.php' );

ob_start();
session_start();

define( 'MAINPATH', realpath( __DIR__ . '/..' ) . '/' );
define( 'ADMINPATH', MAINPATH . 'admin/' );

date_default_timezone_set( 'America/Toronto' );

require_once( MAINPATH . 'core/core.php' );

require_once( MAINPATH . 'core/class-sql.php' );
$db = new Database( DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT );

require_once( MAINPATH . 'core/class-user.php' );
$user = new User( $db );

require_once( ADMINPATH . 'core/class-auth.php' );
$auth = new Auth( $db, $user );

require_once( ADMINPATH . 'core/structure.php' );

require_once( MAINPATH . 'core/class-post.php' );
$post_tools = new Post( $db, $user );

?>
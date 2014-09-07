<?php

$page_id = '';

function show_header( $page_title = '', $authenticate = FALSE, $page_id = '', $main_class = '', $page_description = '', $page_tags = '' ) {
	
	global $user, $footer_page_id;
	
	// Do this to pass the $page_id to the footer.
	$footer_page_id = $page_id;
	
	// Authenticate, if needed.
	if ( $authenticate ) $user->auth();
	
	$user->last_active();
	
	include_once( ABSPATH . 'structure/header.php' );
	
}

function show_footer() {
	
	global $footer_page_id;
	
	include_once( ABSPATH . 'structure/footer.php' );
	
}

?>
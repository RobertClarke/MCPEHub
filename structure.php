<?php

function show_header( $page_title = '', $authenticate = TRUE, $page_id = '', $banner_title = '', $banner_tagline = '' ) {
	
	global $auth;
	
	// Authenticate user, if requested.
	if ( $authenticate ) $auth->auth();
	
	// Update last active time, if logged in.
	$auth->lastActive();
	
	require_once( MAINPATH . 'structure/header.php' );
	
}

function show_footer() {
	
	require_once( MAINPATH . 'structure/footer.php' );
	
}

?>
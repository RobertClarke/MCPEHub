<?php

function show_header( $page_title = '', $authenticate = TRUE, $page_id = '' ) {
	
	global $auth;
	
	// Force authentication on the admin side.
	$authenticate = TRUE;
	
	// Authenticate user, if requested.
	if ( $authenticate ) $auth->auth();
	
	// Update last active time, if logged in.
	$auth->lastActive();
	
	require_once( ADMINPATH . 'structure/header.php' );
	
}

function show_footer() {
	
	require_once( ADMINPATH . 'structure/footer.php' );
	
}

?>
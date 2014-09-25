<?php

/**
  
  * Page Structure
  *
  * Includes functions that are used to easily display the
  * header and footer across the website. Also allows for quick
  * authentication in the header of page, before HTML load.
  *
  * show_header();		Outputs the website header + handles auth.
  * show_footer();		Outputs the website footer.
  
**/

// Outputs the website header + handles auth.
function show_header($page_title='', $authenticate=FALSE, $get_pi=[]) {
	
	global $user, $pi;
	
	$pi = $get_pi;
	
	// Authenticate, if requested by page.
	if ( $authenticate ) $user->auth();
	
	// Update user activity, if logged in.
	$user->update_activity();
	
	include_once(ABS . 'structure/header.php');
	
}

// Outputs the website footer.
function show_footer() {
	include_once(ABS . 'structure/footer.php');
}

?>
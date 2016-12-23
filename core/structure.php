<?php

/**
  *
  * Page Structure
  *
  * Includes functions that are used to easily display the
  * header and footer across the website. Also allows for quick
  * authentication in the header of page, before HTML load.
  *
  * show_header();		Outputs the website header + handles authentication.
  * show_footer();		Outputs the website footer.
  *
**/

// Outputs the website header + handles authentication.
function show_header($page_title='', $authenticate=FALSE, $page_set=[]) {
	
	global $user, $pg_set;
	$pg_set = $page_set;
	
	if ( $authenticate ) $user->auth();
	$user->update_activity();
	
	include_once(ABS.'structure/header.php');
	
}

// Outputs the website footer.
function show_footer() {
	global $pg;
	include_once(ABS.'structure/footer.php');
}

?>
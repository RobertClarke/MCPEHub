<?php

/**
  * Content Download Redirect
**/

require_once('core.php');

$types = ['map', 'seed', 'texture', 'skin', 'mod', 'server'];

// Redirect if GET values missing.
if ( empty($_GET['post']) || empty($_GET['type']) || !is_numeric($_GET['post']) || !in_array($_GET['type'], $types) ) redirect('/');
else {
	
	$id		= $_GET['post'];
	$type	= $_GET['type'];
	
	// Check if post exists.
	$post = $db->select('id, author, dl_link')->from('content_'.$type.'s')->where(['id' => $id])->fetch()[0];
	
	// Redirect if post doesn't exist.
	if ( $db->affected_rows == 0 ) redirect('/');
	else {
		
		// If owner downloading, don't count view.
		if ( $post['author'] !== $user->info('id') ) $post_tools->update_downloads($id, $type);
		
		// Redirect to download link.
		redirect($post['dl_link']);
		
	} // End: Redirect if post doesn't exist.
	
} // End: Redirect if GET values missing.

?>
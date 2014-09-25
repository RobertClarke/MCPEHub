<?php

require_once( 'core.php' );

$allowed_types = array( 'map', 'seed', 'texture', 'skin', 'mod', 'server' );

// Check if post id and type are invalid.
if ( empty( $_GET['post'] ) || empty( $_GET['type'] ) || !is_numeric( $_GET['post'] ) || !in_array( $_GET['type'], $allowed_types ) ) redirect('/');

else {
	
	$post_id = $_GET['post']; 	// Must be numeric, no cleaning required.
	$post_type = $_GET['type']; // Must be specific value, no cleaning required.
	
	// Check if post exists + grab info.
	$query = $db->select( 'id, author, dl_link' )->from( 'content_'.$post_type.'s' )->where( array( 'id' => $post_id ) )->fetch();
	$num = $db->affected_rows;
	
	// Check if post exists.
	if ( $num == 0 ) redirect( '/' );
	else {
		
		$post = $query[0];
		
		// If owner is downloading, dont' add to count.
		if ( $post['author'] != $user->info('id') ) $post_tools->update_downloads( $post_id, $post_type );
		
		// Redirect user to download link.
		redirect( $post['dl_link'] );
		
	}
	
}

?>
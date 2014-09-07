<?php

require_once( 'core.php' );

// Must be logged in to comment.
$user->auth();

// Allowed post types.
$post_types = array( 'map', 'seed', 'texture', 'mod', 'server' );

// Post slug and type are missing or invalid.
if ( empty( $_GET['post'] ) && empty( $_GET['type'] ) && !in_array( $_GET['type'], $post_types ) ) redirect( '/' );
else {
	
	$post['slug'] = $db->escape( $_GET['post'] );
	$post['type'] = $_GET['type']; // No escape because already verified.
	
	// Check if user typed in a comment.
	if ( empty( $_POST['comment'] ) ) redirect( $post['type'].'/'.urlencode($post['slug']).'?comment_missing#comment-form' );
	else {
		
		// Grab post id from database.
		$db_post = $db->select('id,author')->from( 'content_'.$post['type'].'s' )->where( array( 'slug' => $post['slug'] ) )->fetch();
		
		// Check if post exists in database.
		if ( $db->affected_rows == 0 ) redirect( '/' );
		else {
			
			$post_id = $db_post[0]['id'];
			
			// Check if user has posted more than 10 times on the post *today*.
			// Excludes: post owner, admins and mods.
			$max_check = $db->select('id')->from('comments')->limit(10)->where( '`user_id` = '.$user->info('id').' AND `post_id` = "'.$post_id.'" AND DATE(`posted`) = "'.date('Y-m-d').'"' )->fetch();
			
			if ( $db->affected_rows > 9 && $db_post[0]['author'] != $user->info('id') && !$user->is_mod() && !$user->is_admin() )
				redirect( $post['type'].'/'.urlencode($post['slug']).'?comment_denied#comment-form' );
			
			else {
				
				// Clean up comment HTML using HTMLPurifier.
				require( 'core/htmlpurifier/HTMLPurifier.standalone.php' );
				$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );
				
				// Set values to enter into database.
				$comment_vals = array(
					'user_id'		=> $user->info('id'),
					'post_id'		=> $post_id,
					'post_type'		=> $post['type'],
					'comment'		=> $purifier->purify( $_POST['comment'] ),
					'posted'		=> date( 'Y-m-d H:i:s' ),
					'user_ip'		=> $_SERVER['REMOTE_ADDR']
				);
				
				// Insert into database.
				$db->insert( 'comments', $comment_vals );
				
				redirect( $post['type'].'/'.urlencode($post['slug']).'?comment_posted#comment-form' );
				
			}
		
		}
		
	}
	
}

?>
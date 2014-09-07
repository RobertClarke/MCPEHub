<?php

require_once( 'core.php' );

if ( $_POST ) {
	
	// Liking and unliking posts.
	if ( $_POST['action'] == 'like' ) {
		
		// User must be logged in to like posts.
		if ( $user->logged_in() ) {
			
			$post['post_id'] = strip_tags( $_POST['post_id'] );
			$post['post_type'] = strip_tags( $_POST['post_type'] );
			$post['user_id'] = $user->info()['id'];
			
			// Check if the user has already liked the post.
			$db->from( 'likes' )->where( $post )->fetch();
			
			// User hasn't liked the post yet (like).
			if ( $db->affected_rows == 0 ) {
				$db->insert( 'likes', $post );
				$post['status'] = 'liked';
			}
			
			// User already liked post (unlike).
			else {
				$db->delete()->from( 'likes' )->where( $post )->execute();
				$post['status'] = 'unliked';
			}
			
			// Return the action details to AJAX.
			echo json_encode( $post );
			
		} else return FALSE; // User not logged in, can't like.
		
	}
	
	// Favoriting and unfavoriting posts.
	if ( $_POST['action'] == 'fav' ) {
		
		// User must be logged in to favorite posts.
		if ( $user->logged_in() ) {
			
			$post['post_id'] = strip_tags( $_POST['post_id'] );
			$post['post_type'] = strip_tags( $_POST['post_type'] );
			$post['user_id'] = $user->info()['id'];
			
			// Check if the user has already liked the post.
			$db->from( 'favorites' )->where( $post )->fetch();
			
			// User hasn't liked the post yet (like).
			if ( $db->affected_rows == 0 ) {
				$db->insert( 'favorites', $post );
				$post['status'] = 'favorited';
			}
			
			// User already liked post (unlike).
			else {
				$db->delete()->from( 'favorites' )->where( $post )->execute();
				$post['status'] = 'unfavorited';
			}
			
			// Return the action details to AJAX.
			echo json_encode( $post );
			
		} else return FALSE; // User not logged in, can't like.
		
	}
	
	// Subscribing and unsubscribing from users.
	if ( $_POST['action'] == 'subscribe' ) {
		
		// User must be logged in to subscribe.
		if ( $user->logged_in() ) {
			
			$post['user_subscribed'] = strip_tags( $_POST['user_subscribed'] );
			$post['user_sub'] = $user->info()['id'];
			
			// Check if the user has already liked the post.
			$db->from( 'subscriptions' )->where( $post )->fetch();
			
			// User hasn't liked the post yet (like).
			if ( $db->affected_rows == 0 ) {
				$db->insert( 'subscriptions', $post );
				$post['status'] = 'subscribed';
			}
			
			// User already liked post (unlike).
			else {
				$db->delete()->from( 'subscriptions' )->where( $post )->execute();
				$post['status'] = 'unsubscribed';
			}
			
			// Return the action details to AJAX.
			echo json_encode( $post );
			
		} else return FALSE; // User not logged in, can't like.
		
	}
	
	// Featuring posts.
	if ( $_POST['action'] == 'feature' ) {
		
		// User must be logged in and admin or mod.
		if ( $user->logged_in() && $user->is_admin() || $user->is_mod() ) {
			
			$post['post_id'] = strip_tags( $_POST['post_id'] );
			$post['post_type'] = strip_tags( $_POST['post_type'] );
			$post['user_id'] = $user->info()['id'];
			
			$return = $post_tools->toggleFeature( $post['post_id'], $post['post_type'] );
			
			$post['status'] = $return;
			
			// Return the action details to AJAX.
			echo json_encode( $post );
			
		} else return FALSE; // User not logged in, can't like.
		
	}
	
} else return FALSE; // Form not submitted.

?>
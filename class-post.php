<?php

class Post {
	
	private $db;
	public function __construct( $db, $user ) {
		
		$this->db = $db;
		$this->user = $user;
		
	}
	
	function updateViews( $post_id, $post_type, $over_ride = FALSE ) {
		
		$post_key = $post_type .'-'. $post_id;
		$current_views = json_decode( $_COOKIE['post_views'] );
		
		if ( !in_array( $post_key, $current_views ) ) {
			
			if ( $over_ride == FALSE ) $key = 'content_'; else $key = '';
			
			// Get current viewcount from post.
			$query = $this->db->select( 'views' )->from( $key.$post_type.'s' )->where( array( 'id' => $post_id ) )->fetch();
			$views = $query[0]['views'];
			
			// Update viewcount on post.
			$update_vals = array( 'views' => $views + 1 );
			$this->db->where( array( 'id' => $post_id ) )->update( $key.$post_type.'s', $update_vals );
			
			// Push to array and update user cookie.
			array_push( $current_views, $post_key );
			setcookie( 'post_views', json_encode( $current_views ), time() + (365 * 24 * 60 * 60), '/' );
			
			return TRUE;
			
		} else return FALSE;
		
	}
	
	function updateDownloads( $post_id, $post_type ) {
		
		$post_key = $post_type .'-'. $post_id;
		$current_downloads = json_decode( $_COOKIE['post_downloads'] );
		
		if ( !in_array( $post_key, $current_downloads ) ) {
			
			// Get current download from post.
			$query = $this->db->select( 'downloads' )->from( 'content_'.$post_type.'s' )->where( array( 'id' => $post_id ) )->fetch();
			$downloads = $query[0]['downloads'];
			
			// Update download on post.
			$update_vals = array( 'downloads' => $downloads + 1 );
			$this->db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', $update_vals );
			
			// Push to array and update user cookie.
			array_push( $current_downloads, $post_key );
			setcookie( 'post_downloads', json_encode( $current_downloads ), time() + (365 * 24 * 60 * 60), '/' );
			
			return TRUE;
			
		} else return FALSE;
		
	}
	
	// Toggle like on a post.
	function toggleLike( $post_id, $post_type ) {
		
		$post = array( 'post_id' => $post_id, 'user_id' => $this->user->info()['id'], 'post_type' => $post_type );
		$this->db->from( 'likes' )->where( $post )->fetch();
		
		// Determine if the user has liked the post yet.
		if ( $this->db->affected_rows == 0 ) {
			
			$this->db->insert( 'likes', $post );
			
		} else { // User already liked the post, unlike it.
			
			$this->db->delete()->from( 'likes' )->where( $post )->execute();
			
		}
		
		return TRUE;
		
	}
	
	function isFeatured( $post_id, $post_type ) {
		
		$post = array( 'id' => $post_id );
		$db = $this->db->from( 'content_'.$post_type.'s' )->where( $post )->fetch();
		
		$post = $db[0];
		
		if ( $this->db->affected_rows != 0 ) {
			
			if ( $post['featured'] == 1 ) return TRUE;
			else return FALSE;
			
		} else return FALSE;
		
	}
	
	function toggleFeature( $post_id, $post_type ) {
		
		if ( $this->user->isAdmin() || $this->user->isMod() ) {
			
			$post = array( 'id' => $post_id );
			$db = $this->db->from( 'content_'.$post_type.'s' )->where( $post )->fetch();
			
			$post = $db[0];
			
			// Searching for post.
			if ( $this->db->affected_rows != 0 ) {
				
				// Post not featured yet, feature.
				if ( $post['featured'] == 0 ) {
					
					$update_vals = array(
						'featured' => 1,
						'featured_date' => date( 'Y-m-d H:i:s' )
					);
					
					$this->db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', $update_vals );
					
					return 'featured';
					
				}
				
				// Post already featured, unfeature.
				else { 
					
					$update_vals = array(
						'featured' => 0,
						'featured_date' => 0
					);
					
					$this->db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', $update_vals );
					
					return 'unfeatured';
					
				}
				
				//$this->db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', $update_vals );
				//return TRUE;
				
			}
			
			// Post not found.
			else return FALSE;
			
		} else return FALSE; // Not admin/mod.
		
	}
	
	
}

?>
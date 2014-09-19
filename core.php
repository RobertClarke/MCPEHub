<?php

/**
  * Website Core
  *
  * This file contains all of the necessary constants, includes and
  * functions necessary for the website to not spew out a million
  * different error messages.
  *
  * No touchy this file!
**/

require_once( 'config.php' );

// Absolute path to website directory.
define( 'ABSPATH', dirname(__FILE__) . '/' );

// Define different $request_uri depending on if on homepage or not.
if ( substr( $_SERVER['REQUEST_URI'], -4 ) != '.php' ) $request_uri = $_SERVER['REQUEST_URI'] . 'index.php';
else $request_uri = $_SERVER['REQUEST_URI'];

$request_uri = rtrim( dirname( $request_uri ) , '/' );

// Main URL to website (with trailing slash).
//define( 'MAINURL', 'http://' . $_SERVER['SERVER_NAME'] . rtrim( dirname( $_SERVER['REQUEST_URI'] ), '/' ) . '/' );
define( 'MAINURL', 'http://' . $_SERVER['SERVER_NAME'] . $request_uri . '/' );

// Define auth cookie name.
define( 'AUTHCOOKIE', 'mcpe_' . sha1( 'McpeAuthCookie' ) );

// Force the timezone so everyone has the same time.
date_default_timezone_set( 'America/Toronto' );

require_once( ABSPATH . 'core/core.php' );

require_once( ABSPATH . 'core/page.php' );

require_once( ABSPATH . 'core/classes/url.php' );
$c_url = new Url();

require_once( ABSPATH . 'core/classes/sql.php' );
$db = new Database( DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT );

require_once( ABSPATH . 'core/classes/errors.php' );
$error = new Error();

require_once( ABSPATH . 'core/classes/user.php' );
$user = new User( $db );

require_once( ABSPATH . 'core/classes/ping.php' );

require_once( ABSPATH . 'core/classes/comment.php' );
$comment_tools = new Comment( $db, $user );

require 'core/PHPMailer/PHPMailerAutoload.php';

function send_email( $email, $name, $subject, $content ) {
	
	$mail = new PHPMailer();
	
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Host = 'smtp.mandrillapp.com';
	$mail->Port = 587;
	$mail->SMTPAuth = TRUE;
	$mail->Username = 'admin@cubemotion.com';
	$mail->Password = 'eiKnvMUNhH-6IqPHXHdB-A';
	
	$mail->setFrom( 'noreply@mcpehub.com', 'MCPE Hub' );
	$mail->addAddress( $email, $name );
	
	$mail->Subject = $subject;
	
	$mail->IsHTML( TRUE );
	$mail->Body = $content;
	
	$mail->send();
	
}




// Set post views cookie, if doesn't exist.
if ( !isset( $_COOKIE['mcpe_v'] ) || empty( $_COOKIE['mcpe_v'] ) ) {
	//set_cookie( 'mcpe_v', '', time() + (365 * 24 * 60 * 60) );
	$_COOKIE['mcpe_v'] = '';
}

// Set post downloads cookie, if doesn't exist.
if ( !isset( $_COOKIE['mcpe_d'] ) || empty( $_COOKIE['mcpe_d'] ) ) {
	//set_cookie( 'mcpe_d', '', time() + (365 * 24 * 60 * 60) );
	$_COOKIE['mcpe_d'] = '';
}


// Function to clean up old cookies into new ones.
function clean_cookie( $c_original, $c_new ) {
	
	// Push all old views into new cookie.
	if ( isset( $_COOKIE[$c_original] ) ) {
		
		// Old cookie empty.
		if ( $_COOKIE[$c_original] == '[]' || empty( $_COOKIE[$c_original] ) )
			set_cookie( $c_original, '', time() - 1 );
		
		// Old cookie has data, need to move it.
		else {
			
			$new_cookie = '';
			$old_cookie = json_decode( $_COOKIE[$c_original] );
			
			// New identifier codes (for shorter cookies).
			$codes = array( 'map' => 'mp', 'seed' => 'se', 'texture' => 'tx', 'mod' => 'md', 'server' => 'sr' );
			
			foreach( $old_cookie as $val ) {
				
				$val = explode( '-', $val );
				
				// Remove all old update post views.
				if ( $val[0] != 'update_new' ) $new_cookie .= $codes[$val[0]] . $val[1] .',';
				
			}
			
			$new_cookie = rtrim( $new_cookie, ',');
			
			// New cookie already set, we need to append.
			if ( !empty( $_COOKIE[$c_new] ) ) {
				
				$cookie_value = $_COOKIE[$c_new] .','. $new_cookie;
				
				$_COOKIE[$c_new] = $cookie_value;
				set_cookie( $c_new, $cookie_value, time() + (365 * 24 * 60 * 60) );
				
			}
			
			// New cookie empty, just set the cookie.
			else {
				$_COOKIE[$c_new] = $new_cookie;
				set_cookie( $c_new, $new_cookie, time() + (365 * 24 * 60 * 60) );
			}
			
			// Expire cookie.
			set_cookie( $c_original, '', time() - 1 );
			
		}
		
	}
	
}

clean_cookie( 'post_views', 'mcpe_v' );
clean_cookie( 'post_downloads', 'mcpe_d' );







class Post {
	
	private $db;
	public function __construct( $db, $user ) {
		
		$this->db = $db;
		$this->user = $user;
		
	}
	
	public function update_views( $post_id, $post_type ) {
		
		$current_views = explode( ',', $_COOKIE['mcpe_v'] );
		
		// New identifier codes (for shorter cookies).
		$codes = array( 'map' => 'mp', 'seed' => 'se', 'texture' => 'tx', 'skin' => 'sk', 'mod' => 'md', 'server' => 'sr' );
		
		// Convert post type to new identifier code.
		$post_iden = $codes[$post_type];
		
		// Post not in views, add to count.
		if ( !in_array( $post_iden . $post_id, $current_views ) ) {
			
			// Grab current view count and append by 1 view.
			$query = $this->db->select( 'views' )->from( 'content_'.$post_type.'s' )->where( array('id' => $post_id) )->fetch();
			$views = ( $query[0]['views'] + 1 );
			
			// Update view count.
			$this->db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', array( 'views' => $views ) );
			
			// Set new views cookie.
			if ( !empty( $_COOKIE['mcpe_v'] ) ) $cookie_value = $_COOKIE['mcpe_v'] .','. $post_iden . $post_id;
			else $cookie_value = $post_iden . $post_id;
			
			$_COOKIE['mcpe_v'] = $cookie_value;
			set_cookie( 'mcpe_v', $cookie_value, time() + (365 * 24 * 60 * 60) );
			
			return TRUE;
			
		}
		
		// Post already in views, don't add.
		else return FALSE;
		
	}
	
	public function update_downloads( $post_id, $post_type ) {
		
		$current_dls = explode( ',', $_COOKIE['mcpe_d'] );
		
		// New identifier codes (for shorter cookies).
		$codes = array( 'map' => 'mp', 'seed' => 'se', 'texture' => 'tx', 'skin' => 'sk', 'mod' => 'md', 'server' => 'sr' );
		
		// Convert post type to new identifier code.
		$post_iden = $codes[$post_type];
		
		// Post not in users downloads, add to count.
		if ( !in_array( $post_iden . $post_id, $current_dls ) ) {
			
			// Grab current downloads count and append by 1 download.
			$query = $this->db->select( 'downloads' )->from( 'content_'.$post_type.'s' )->where( array('id' => $post_id) )->fetch();
			$dls = ( $query[0]['downloads'] + 1 );
			
			// Update download count.
			$this->db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', array( 'downloads' => $dls ) );
			
			// Set new downloads cookie.
			if ( !empty( $_COOKIE['mcpe_d'] ) ) $cookie_value = $_COOKIE['mcpe_d'] .','. $post_iden . $post_id;
			else $cookie_value = $post_iden . $post_id;
			
			$_COOKIE['mcpe_d'] = $cookie_value;
			set_cookie( 'mcpe_d', $cookie_value, time() + (365 * 24 * 60 * 60) );
			
			return TRUE;
			
		}
		
		// User already downloaded file, dont' add to count.
		else return FALSE;
		
	}
	
	
	
	
	
	// Toggle like on a post.
	function toggleLike( $post_id, $post_type ) {
		
		$post = array( 'post_id' => $post_id, 'user_id' => $this->user->info('id'), 'post_type' => $post_type );
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
		
		if ( $this->user->is_admin() || $this->user->is_mod() ) {
			
			$post = array( 'id' => $post_id );
			$db = $this->db->from( 'content_'.$post_type.'s' )->where( $post )->fetch();
			
			$post = $db[0];
			
			// Searching for post.
			if ( $this->db->affected_rows != 0 ) {
				
				// Post not featured yet, feature.
				if ( $post['featured'] == 0 ) {
					
					$update_vals = array(
						'featured' => 1,
						'featured_time' => date( 'Y-m-d H:i:s' )
					);
					
					$this->db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', $update_vals );
					
					return 'featured';
					
				}
				
				// Post already featured, unfeature.
				else {
					
					$update_vals = array(
						'featured' => 0,
						'featured_time' => 0
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

$post_tools = new Post( $db, $user );




?>
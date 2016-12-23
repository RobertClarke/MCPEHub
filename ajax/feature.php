<?php

$post_types	= ['map', 'seed', 'texture', 'skin', 'mod', 'server'];

if ( empty($_GET['post']) || empty($_GET['type']) ) $return = 'error';
elseif ( !is_numeric($_GET['post']) || !in_array($_GET['type'], $post_types) ) $return = 'error';
else {
	
	$p_id	= $_GET['post'];
	$p_type	= $_GET['type'];
	
	require_once('../config.php');
	define( 'AUTHCOOKIE', sha1( AUTH_COOKIE ) );
	
	require_once('../core/classes/sql.php');
	$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
	
	require_once('../core/classes/user.php');
	$user = new User($db);
	
	// Determine if valid post.
	if ( !$user->logged_in() ) $return = 'auth';
	elseif ( !$user->is_admin() && !$user->is_mod() ) $return = 'error';
	else {
		
		$post = $db->query('SELECT COUNT(*) FROM `content_'.$p_type.'s` WHERE `id` = '.$p_id)->fetch()[0]['COUNT(*)'];
		if ( $post == 0 ) $return = 'error';
		else {
			
			$post = $db->query('SELECT * FROM `content_'.$p_type.'s` WHERE `id` = '.$p_id)->fetch()[0];
			
			// Post not featured yet.
			if ( $post['featured'] == 0 ) {
				
				$db->where(['id' => $p_id])->update('content_'.$p_type.'s', ['featured' => 1, 'featured_time' => date('Y-m-d H:i:s')]);
				$return = 'featured';
				
			}
			
			// Post already featured.
			else {
				
				$db->where(['id' => $p_id])->update('content_'.$p_type.'s', ['featured' => 0, 'featured_time' => 0]);
				$return = 'unfeatured';
				
			}
			
		}
		
	}
	
}

echo (isset($return) ) ? $return : 'error';

?>
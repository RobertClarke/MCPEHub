<?php

if ( empty($_GET['post']) && !is_numeric($_GET['post']) ) $return = 'error';
else {
	
	$p_id	= $_GET['post'];
	
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
		
		$post = $db->query('SELECT COUNT(*) FROM `content_maps` WHERE `id` = '.$p_id)->fetch()[0]['COUNT(*)'];
		if ( $post == 0 ) $return = 'error';
		else {
			
			$post = $db->query('SELECT tested FROM `content_maps` WHERE `id` = '.$p_id)->fetch()[0];
			
			// Post not marked as tested yet.
			if ( $post['tested'] == 0 ) {
				
				$db->where(['id' => $p_id])->update('content_maps', ['tested' => 1]);
				$return = 'marked';
				
			}
			
			// Post already marked as tested.
			else {
				
				$db->where(['id' => $p_id])->update('content_maps', ['tested' => 0]);
				$return = 'unmarked';
				
			}
			
		}
		
	}
	
}

echo (isset($return) ) ? $return : 'error';

?>
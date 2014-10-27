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
	else {
		
		$post = $db->query('SELECT COUNT(*) FROM `content_'.$p_type.'s` WHERE `id` = '.$p_id)->fetch()[0]['COUNT(*)'];
		if ( $post == 0 ) $return = 'error';
		else {
			
			$q_where = '`post` = \''.$p_id.'\' AND `type` = \''.$p_type.'\' AND `user` = \''.$user->info('id').'\'';
			$favorited = $db->query('SELECT "favorited" AS data, COUNT(*) FROM `favorites` WHERE '.$q_where)->fetch()[0]['COUNT(*)'];
			
			// User hasn't favorited yet.
			if ( $favorited == 0 ) {
				
				$person = $user->info('id');
				
				$id = $db->insert('favorites', ['user' => $person, 'post' => $p_id, 'type' => $p_type]);
				$return = 'favorited';
				
			}
			
			// User has already favorited.
			else {
				
				$db->delete()->from('favorites')->where($q_where)->limit(1)->execute();
				$return = 'unfavorited';
				
			}
			
		}
		
	}
	
}

echo (isset($return) ) ? $return : 'error';

?>
<?php

if ( empty($_GET['follow']) ) $return = 'error';
elseif ( !is_numeric($_GET['follow']) ) $return = 'error';
else {
	
	$follow	= $_GET['follow'];
	
	require_once('../config.php');
	define( 'AUTHCOOKIE', sha1( AUTH_COOKIE ) );
	
	require_once('../core/classes/sql.php');
	$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
	
	require_once('../core/classes/user.php');
	$user = new User($db);
	
	if ( !$user->logged_in() ) $return = 'auth';
	else {
		
		$post = $db->query('SELECT COUNT(*) FROM `users` WHERE `id` = '.$follow)->fetch()[0]['COUNT(*)'];
		if ( $post == 0 ) $return = 'error';
		else {
			
			$q_where = '`following` = \''.$follow.'\' AND `user` = \''.$user->info('id').'\'';
			$following = $db->query('SELECT "following" AS data, COUNT(*) FROM `following` WHERE '.$q_where)->fetch()[0]['COUNT(*)'];
			
			// User hasn't followed yet.
			if ( $following == 0 ) {
				
				$person = $user->info('id');
				
				$id = $db->insert('following', ['user' => $person, 'following' => $follow]);
				$return = 'followed';
				
			}
			
			// User has already followed.
			else {
				
				$db->delete()->from('following')->where($q_where)->limit(1)->execute();
				$return = 'unfollowed';
				
			}
			
		}
		
	}
	
}

echo (isset($return) ) ? $return : 'error';

?>
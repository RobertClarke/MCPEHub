<?php

$post_types	= ['map', 'seed', 'texture', 'skin', 'mod', 'server'];

require_once('../core/general.php');

if ( empty($_GET['post']) || empty($_GET['type']) ) redirect('/');
elseif ( !is_numeric($_GET['post']) || !in_array($_GET['type'], $post_types) ) redirect('/');
else {
	
	$p_id	= $_GET['post'];
	$p_type	= $_GET['type'];
	
	require_once('../config.php');
	define( 'AUTHCOOKIE', sha1( AUTH_COOKIE ) );
	
	require_once('../core/classes/sql.php');
	$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
	
	require_once('../core/classes/user.php');
	$user = new User($db);
	
	if ( !$user->logged_in() ) redirect('/login?auth');
	else {
		
		$count = $db->query('SELECT COUNT(*), slug FROM `content_'.$p_type.'s` WHERE `id` = '.$p_id)->fetch()[0];
		if ( $count['COUNT(*)'] == 0 ) redirect('/');
		else {
			
			// If no comment input, redirect back to post.
			if ( empty($_POST['comment']) ) redirect('/'.$p_type.'/'.$count['slug'].'#comments');
			else {
				
				require_once( '../core/htmlpurifier/HTMLPurifier.standalone.php' );
				$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );
				
				$comment = $purifier->purify($_POST['comment']);
				$comment = str_replace('assets/img/smilies/', '/assets/img/smilies/', $comment);
				
				$comment = $db->escape($comment);
				
				$person = $user->info('id');
				
				$id = $db->insert('comments', ['user' => $person, 'post' => $p_id, 'type' => $p_type, 'comment' => $comment, 'posted' => time_now(), 'user_ip' => current_ip()]);
				
				redirect('/'.$p_type.'/'.$count['slug'].'?cposted#comments');
				
			}
			
		}
		
	}
	
}

?>
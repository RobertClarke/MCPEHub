<?php

/**
  * Toggle Profile Badges
**/

require_once('core.php');

// Redirect if user not admin
if ( !$user->is_admin() ) redirect('/');
elseif ( empty($_GET['user']) || !is_numeric($_GET['user']) || empty($_GET['rank']) || !in_array($_GET['rank'], ['youtuber','verified','featured']) ) redirect('/moderate');

show_header('Set Profile Badge', TRUE, ['title_main' => 'Set Profile Badge', 'title_sub' => 'Admin Panel']);

$p_user	= $_GET['user'];

$error->add('INVALID', '<i class="fa fa-times"></i> The user you\'re attempting to give a badge to doesn\'t exist.');

$usr = $db->select('id, username, verified, youtuber, featured')->from('users')->where(['id' => $p_user])->fetch();

// Check if user exists in database.
if (!$db->affected_rows) $error->set('INVALID');
else {
	
	$usr = $usr[0];
	
	switch ( $_GET['rank'] ) {
		case 'youtuber':
			if ( $usr['youtuber'] == '1' ) {
				$db->where(['id' => $usr['id']])->update('users', ['youtuber' => '0']);
				redirect("/user/".$usr['username']);
			}
			else { // Moderate
				$db->where(['id' => $usr['id']])->update('users', ['youtuber' => '1']);
				redirect("/user/".$usr['username']);
			}
		break;
		case 'verified':
			if ( $usr['verified'] == '1' ) {
				$db->where(['id' => $usr['id']])->update('users', ['verified' => '0']);
				redirect("/user/".$usr['username']);
			}
			else { // Moderate
				$db->where(['id' => $usr['id']])->update('users', ['verified' => '1']);
				redirect("/user/".$usr['username']);
			}
		break;
		case 'featured':
		if ( $usr['featured'] == '1' ) {
			$db->where(['id' => $usr['id']])->update('users', ['featured' => '0']);
			redirect("/user/".$usr['username']);
		}
		else { // Moderate
			$db->where(['id' => $usr['id']])->update('users', ['featured' => '1']);
			redirect("/user/".$usr['username']);
		}
		break;
	}
	
} // End: Check if post exists in database.

?>
<div id="p-title">
    <h1>Set Profile Badge</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>
<?php $error->display(); ?>
<?php show_footer(); ?>
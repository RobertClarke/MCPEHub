<?php

/**
  * Toggle Moderate Status
**/

require_once('core.php');

// Redirect if user not admin
if ( !$user->is_admin() ) redirect('/');
elseif ( empty($_GET['user']) || !is_numeric($_GET['user']) ) redirect('/moderate');

show_header('Moderate User', TRUE, ['title_main' => 'Moderate User', 'title_sub' => 'Moderator Panel']);

$p_user	= $_GET['user'];

$error->add('INVALID', '<i class="fa fa-times"></i> The user you\'re attempting to moderate doesn\'t exist.');

$usr = $db->select('id, username, level')->from('users')->where(['id' => $p_user])->fetch();

// Check if user exists in database.
if (!$db->affected_rows) $error->set('INVALID');
else {
	
	$usr = $usr[0];
	
	// Check if user is already moderated (unmoderate).
	if ( $usr['level'] == '1' ) {
		$db->where(['id' => $usr['id']])->update('users', ['level' => '0']);
		redirect("/user/".$usr['username']."&unmoderated");
	}
	else { // Moderate
		$db->where(['id' => $usr['id']])->update('users', ['level' => '1']);
		redirect("/user/".$usr['username']."&moderated");
	}
	
} // End: Check if post exists in database.

?>
<div id="p-title">
    <h1>Moderate User</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>
<?php $error->display(); ?>
<?php show_footer(); ?>
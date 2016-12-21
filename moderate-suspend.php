<?php

/**
  * Moderator User Suspending
**/

require_once('core.php');

// Redirect if user not admin/mod.
if ( !$user->is_admin() && !$user->is_mod() ) redirect('/');
elseif ( empty($_GET['user']) || !is_numeric($_GET['user']) ) redirect('/moderate');

show_header('Suspend User', TRUE, ['title_main' => 'Suspend User', 'title_sub' => 'Moderator Panel']);

$p_user	= $_GET['user'];

$error->add('INVALID', '<i class="fa fa-times"></i> The user you\'re attempting to suspend doesn\'t exist.');

$usr = $db->select('id, username, suspended')->from('users')->where(['id' => $p_user])->fetch();

// Check if user exists in database.
if (!$db->affected_rows) $error->set('INVALID');
else {
	
	$usr = $usr[0];
	
	// Check if user is already suspended (unsuspend).
	if ( $usr['suspended'] == '1' ) {
		$db->where(['id' => $usr['id']])->update('users', ['suspended' => '0']);
		redirect('/moderate?unsuspended&user='.$usr['username']);
	}
	else { // Suspend
		$db->where(['id' => $usr['id']])->update('users', ['suspended' => '1']);
		redirect('/moderate?suspended&user='.$usr['username']);
	}
	
} // End: Check if post exists in database.

?>
<div id="p-title">
    <h1>Suspend User</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>
<?php $error->display(); ?>
<?php show_footer(); ?>
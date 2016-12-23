<?php

/**
  * Moderator Moderated List
**/

require_once('core.php');

// Redirect if user not admin/mod.
if ( !$user->is_admin() ) redirect('/');

show_header('Moderated Users', TRUE, ['body_id' => 'dashboard', 'title_main' => 'Moderated Users', 'title_sub' => 'Admin Panel']);

$users = $db->from('users')->order_by('username DESC')->where(['level' => 1])->fetch();

$error->add('NONE', 'No users are moderated at this time.', 'warning');
if (!$db->affected_rows) $error->set('NONE');

?>
<div id="p-title">
    <h1>Moderated Users</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>

<div class="input-rules" style="padding-bottom:15px;"><p>Any users listed below are currently marked as moderators. <br><br>You can un-moderate them by clicking on their username to go to their profile.</p></div>

<div class="user-list">
	<?php $error->display(); ?>
	<ul>
<?php
	
foreach( $users as $i => $p ) {
	echo '<li><a href="/user/'.$p['username'].'"><div class="img"><img src="/avatar/96x96/'.$p['avatar'].'" /></div> <p>'.$p['username'].'</p></a></li>';
}

?>
	</ul>
</div>

<?php show_footer(); ?>
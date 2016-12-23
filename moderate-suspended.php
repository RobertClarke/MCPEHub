<?php

/**
  * Moderator Suspended List
**/

require_once('core.php');

// Redirect if user not admin/mod.
if ( !$user->is_admin() && !$user->is_mod() ) redirect('/');

show_header('Suspended Users', TRUE, ['body_id' => 'dashboard', 'title_main' => 'Suspended Users', 'title_sub' => 'Moderator Panel']);

$users = $db->from('users')->order_by('username DESC')->where(['suspended' => 1])->fetch();

$error->add('NONE', 'No users are suspended at this time.', 'warning');
if (!$db->affected_rows) $error->set('NONE');

?>
<div id="p-title">
    <h1>Suspended Users</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>

<div class="input-rules" style="padding-bottom:15px;"><p>Any users listed below are currently marked as suspended. They cannot log into their accounts, or post any content while they are suspended.<br><br>You can un-suspend them by clicking on their username to go to their profile.</p></div>

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
<?php

/**
  * User Profiles
**/

require_once('core.php');

$p['user'] = isset($_GET['user']) ? $db->escape(strip_tags(substr($_GET['user'], 0, 50))) : NULL;

if ( empty($p['user']) ) {
	if ( $user->logged_in() ) $p['user'] = $user->info('username');
	else redirect('/');
}

// Check if profile exists + user not suspended.
if ( !empty($p['user']) && !$user->suspended($p['user']) && $u = $user->info('', $p['user']) ) {
	
	$url->add('user', $p['user']);
	$pg_title = $u['username'].'\'s Profile';
	
	$u['avatar'] = '/avatar/160x160/'.$u['avatar'];
	
} // End: Check if profile exists + user not suspended.

// Profile not found, 404.
else redirect('/404');

show_header($pg_title, FALSE, ['body_id' => 'profile', 'title_main' => 'Profile', 'title_sub' => 'Member']);

?>

<div class="top">
    <div class="avatar">
        <img src="<?php echo $u['avatar']; ?>" alt="<?php echo $u['username']; ?>" width="80" height="80">
    </div>
    <div class="info">
        <h1><?php echo $u['username']; ?>'s Profile</h1>
        <h5>@<?php echo $u['username'].' '; echo $user->badges($u['username']); ?><br><span class="active">Last active <?php echo since(strtotime($u['last_active'])); ?></span></h5>
    </div>
</div>

<div class="about">
    <h3>About Me</h3>
    <?php echo ( !empty( $u['bio'] ) ) ? $u['bio'] : '<p>I haven\'t updated my bio yet.</p>'; ?>
</div>

<?php show_footer(); ?>
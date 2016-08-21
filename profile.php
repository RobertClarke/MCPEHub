<?php

/**
  * User Profile
**/

require_once('core.php');

$p['user'] = isset($_GET['user']) ? $db->escape(strip_tags(substr($_GET['user'], 0, 50))) : NULL;

if ( empty($p['user']) ) {
	if ( $user->logged_in() ) $p['user'] = $user->info('username');
	else redirect('/');
}

// Check if profile exists + user not suspended.
if ( !empty($p['user']) && $u = $user->info('', $p['user']) ) {
	
	if ( $user->suspended($p['user']) && !$user->is_admin() && !$user->is_mod() ) {
		redirect('/404');
		die();
	}
	
	$url->add('user', $p['user']);
	$pg_title = $u['username'].'\'s Profile';
	
	$u['avatar'] = '/avatar/160x160/'.$u['avatar'];
	
	if ( $p['user'] == $user->info('username') ) $owner = TRUE;
	else $owner = FALSE;
	
	if ( $user->logged_in() ) {
		$q_where = '`following` = \''.$u['id'].'\' AND `user` = \''.$user->info('id').'\'';
		$p['following'] = $db->query('SELECT COUNT(*) FROM `following` WHERE '.$q_where)->fetch()[0]['COUNT(*)'];
		
		$p['following'] = ( $p['following'] != 0 ) ? TRUE : FALSE;
		
		if ( $p['following'] ) $html['bttn_follow'] = '<a href="#" class="bttn sub green follow" data-following="'.$u['id'].'"><i class="fa fa-check"></i> Following</a>';
		else $html['bttn_follow'] = '<a href="#" class="bttn sub follow" data-following="'.$u['id'].'"><i class="fa fa-rss"></i> Follow</a>';
	}
	else $html['bttn_follow'] = NULL;
	
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
        <h5>@<?php echo $u['username'].' '; echo $user->badges($u['username']); ?><br><span class="active">Last active <?php echo since($u['last_active']); ?></span></h5>
    </div>
    <div class="actions">
<?php echo $html['bttn_follow']; ?>
<?php if ( $user->is_admin() || $user->is_mod() && $u['level'] !== 9 ) { ?>
        <a href="/moderate-suspend?user=<?php echo $u['id']; ?>" class="bttn red"><i class="fa fa-ban"></i> <?php echo ($user->suspended($p['user']) ) ? 'Unsuspend' : 'Suspend'; ?></a>
<?php } if ( $user->is_admin() && $u['level'] !== 9 ) { ?>
        <a href="/moderate-set?user=<?php echo $u['id']; ?>" class="bttn <?php echo ( $u['level'] == 1 ) ? 'red' : 'green'; ?>"><i class="fa fa-gavel"></i> <?php echo ( $u['level'] == 1 ) ? 'Unmoderate' : 'Moderate'; ?></a>
<?php } if ( $owner ) { ?>
        <a href="/profile_edit" class="bttn"><i class="fa fa-pencil fa-fw"></i> Edit Profile</a>
<?php } ?>
    </div>
</div>

<div class="about">
    <h3>About Me</h3>
    <?php echo ( !empty( $u['bio'] ) ) ? $u['bio'] : '<p>I haven\'t updated my bio yet.</p>'; ?>
</div>

<?php if (!empty($u['twitter']) || !empty($u['youtube']) || !empty($u['devices']) ) { ?>
<div class="info-user">
    <h3>Info</h3>
    <?php if ( !empty($u['twitter']) ) { ?><a href="http://twitter.com/<?php echo $u['twitter']; ?>" class="bttn mini" target="_blank"><i class="fa fa-twitter fa-fw"></i> @<?php echo $u['twitter']; ?></a><?php } ?>
    <?php if ( !empty($u['youtube']) ) { ?><a href="http://youtube.com/<?php echo $u['youtube']; ?>" class="bttn mini" target="_blank"><i class="fa fa-youtube-play fa-fw"></i> <?php echo $u['youtube']; ?></a><?php } ?>
    <?php if ( !empty($u['devices']) ) { ?><a href="#" class="bttn mini"><i class="fa fa-mobile fa-fw"></i> Device: <?php echo $u['devices']; ?></a><?php } ?>
</div>
<?php } ?>

<?php show_footer(); ?>
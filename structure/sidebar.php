<?php global $user, $num_unapproved; ?>
<div id="sidebar">
    
<?php

if ( $user->logged_in() ) {
	
	$avatar_url = '/avatar/64x64/'.$user->info('avatar_file');
	
?>
    <div class="user-detail">
        <div class="avatar"><img src="<?php echo $avatar_url; ?>" alt="<?php echo $user->info('username'); ?>'s Avatar" /></div>
        <div class="info">
            <p>Howdy, <strong><?php echo $user->info('username'); ?></strong>!</p>
            <a href="/profile" class="bttn"><i class="fa fa-male"></i> My Profile</a>
            <a href="/login?logout" class="bttn"><i class="fa fa-lock"></i> Sign Out</a>
        </div>
    </div>
    <div class="links spacer">
        <h3>Quick Nav</h3>
        <ul>
            <li><a href="/dashboard"><i class="fa fa-tachometer fa-fw"></i> Dashboard</a></li>
<?php if ( $user->is_admin() ) { ?>
	        <li><a href="/admin"><i class="fa fa-tint fa-fw"></i> Admin Panel</a></li>
<?php } if ( $user->is_mod() || $user->is_admin() ) { ?>
            <li><a href="/moderate"><i class="fa fa-gavel fa-fw"></i> Moderate Posts<?php if ( $num_unapproved != 0 ) echo ' <span class="num">'.$num_unapproved.'</span>'; ?></a></li>
<?php } ?>
            <li><a href="/dashboard"><i class="fa fa-pencil fa-fw"></i> My Posts</a></li>
            <li><a href="/messages"><i class="fa fa-envelope fa-fw"></i> Messages</a></li>
            <li><a href="/account?tab=avatar"><i class="fa fa-camera fa-fw"></i> Change Avatar</a></li>
            <li><a href="/account"><i class="fa fa-cogs fa-fw"></i> Account Settings</a></li>
        </ul>
    </div>
<?php } ?>
    <div id="side-advrt">
        <div class="advrt">
            <ins class="adsbygoogle" style="display:inline-block;width:300px;height:600px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="1513409877"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>
    </div>
    
</div>
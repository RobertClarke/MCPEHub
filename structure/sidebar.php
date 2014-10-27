<?php global $user, $num_unapproved; ?>
<div id="sidebar">
    
    <div class="user"> 
<?php if ($user->logged_in()) { ?>
        <img src="/avatar/96x96/<?php echo $user->info('avatar'); ?>" alt="<?php echo $user->info('username'); ?>" width="48" height="48">
        <p>
            <strong><?php echo $user->info('username'); ?></strong>
            <a href="/profile">Profile</a>
            <a href="/account">Account</a>
            <a href="/logout">Sign Out</a>
        </p>
<?php } else { ?>
        <img src="/assets/img/core/avatar_guest.png" alt="" width="48" height="48">
        <p>
            <a href="/register" class="bttn mini gold"><i class="fa fa-edit"></i> Create Account</a>
            <a href="/login" class="bttn mini"><i class="fa fa-key"></i> Sign In</a>
        </p>
<?php } ?>
    </div>
    
<?php if ($user->logged_in()) { // User logged in. ?>

<?php if ( $user->info('activated') == 0 ) { ?>
    <div class="alert warning compact verify">You haven't verified your email yet. Verify to unlock <b>full access</b> to community features!<br><a href="/account?resend">Resend Verification Email</a> <a href="/account">Account Settings</a></div>
<?php } ?>
    
    <div class="links">
        <a href="/dashboard"><i class="fa fa-tachometer fa-fw"></i> Dashboard</a>
        <a href="/dashboard?posts"><i class="fa fa-newspaper-o fa-fw"></i> My Posts</a>
        <a href="/messages" data-toggle="modal" data-target="#modal-soon"><i class="fa fa-send fa-fw"></i> Messages</a>
        <a href="/profile"><i class="fa fa-smile-o fa-fw"></i> My Profile</a>
        <a href="/account?avatar"><i class="fa fa-camera fa-fw"></i> Change Avatar</a>
        <a href="/account"><i class="fa fa-wrench fa-fw"></i> Account Settings</a>
<?php
	if ($user->is_mod() || $user->is_admin()) {
		$unapproved_badge = ( $num_unapproved > 0 ) ? ' <span class="badge">'.$num_unapproved.'</span>' : NULL;
		echo '<a href="/moderate"><i class="fa fa-gavel fa-fw"></i> Moderate'.$unapproved_badge.'</a>';
	}
	if ($user->is_admin()) echo '<a href="/admin" data-toggle="modal" data-target="#modal-soon"><i class="fa fa-rocket fa-fw"></i> Admin Panel</a>';
?>
    </div>
    
<?php } // END: User logged in. ?>
    
    <div class="avrt">
        <ins class="adsbygoogle" style="display:inline-block;width:300px;height:600px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="1513409877"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>
    
</div><!-- End #sidebar -->
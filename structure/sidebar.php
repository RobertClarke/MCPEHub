<?php global $user, $num_unapproved, $pg; ?>
<div id="sidebar">
<?php if ( $pg['current'] != 'map' && $pg['current'] != 'seed' && $pg['current'] != 'mod' && $pg['current'] != 'server' && $pg['current'] != 'skin' && $pg['current'] != 'texture' && $pg['current'] != 'servervote') { ?>
    <div class="avrt" style="height:250px;margin-bottom:5px;">
		<ins class="adsbygoogle"
     style="display:inline-block;width:300px;height:250px"
     data-ad-client="ca-pub-3736311321196703"
     data-ad-slot="3678350279"></ins>
     	<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>
<?php } ?>

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
    <div class="alert warning compact verify">You haven't verified your email yet. Verify to unlock <b>full access</b> to community features!<br><a href="/account?tab=resend">Resend Verification Email</a> <a href="/account">Account Settings</a></div>
<?php } ?>

    <div class="links">
        <a href="/dashboard"><i class="fa fa-tachometer fa-fw"></i> Dashboard</a>
        <a href="/dashboard?posts"><i class="fa fa-newspaper-o fa-fw"></i> My Posts</a>
        <!--<a href="/messages" data-toggle="modal" data-target="#modal-soon"><i class="fa fa-send fa-fw"></i> Messages</a>-->
        <a href="/profile"><i class="fa fa-smile-o fa-fw"></i> My Profile</a>
        <a href="/profile_edit"><i class="fa fa-pencil fa-fw"></i> Edit Profile</a>
        <a href="/account?tab=avatar"><i class="fa fa-camera fa-fw"></i> Change Avatar</a>
        <a href="/account"><i class="fa fa-wrench fa-fw"></i> Account Settings</a>
<?php
	if ($user->is_mod() || $user->is_admin()) {
		$unapproved_badge = ( $num_unapproved > 0 ) ? ' <span class="badge">'.$num_unapproved.'</span>' : NULL;
		echo '<a href="/moderate"><i class="fa fa-gavel fa-fw"></i> Moderate'.$unapproved_badge.'</a>';
		echo '<a href="/moderate-suspended"><i class="fa fa-ban fa-fw"></i> Suspended Users</a>';
	}
	if ($user->is_admin()) { ?>
	<a href="/admin" data-toggle="modal" data-target="#modal-soon"><i class="fa fa-rocket fa-fw"></i> Admin Panel</a>
	<a href="/moderate-moderated"><i class="fa fa-gavel fa-fw"></i> Moderated Users</a>
<?php } ?>
    </div>

<?php } // END: User logged in. ?>

<a href="https://netherbox.com/?promo=MINE" target="_blank"><img src="/assets/img/netherbox.jpg" width="300" height="50" alt="Minecraft Server Hosting" style="margin-bottom: 5px;"></a>
<div class="twitter-follow"><a href="https://twitter.com/MCHubCommunity" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @MCHubCommunity</a></div>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
    <div class="avrt big">
        <ins class="adsbygoogle" style="display:inline-block;width:300px;height:600px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="1513409877"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    </div>

</div><!-- End #sidebar -->

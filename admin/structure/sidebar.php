<?php global $user, $auth; ?>
	        <div id="sidebar">
	            
<?php if ( $auth->loggedIn() ) { ?>
	            <div id="user-details">
	                <div class="avatar"><img src="../core/timthumb.php?src=../uploads/avatars/<?php echo urlencode( $user->info()['avatar_file'] ); ?>&h=64&w=64&zc=1" alt="<?php echo $user->info()['username']; ?>'s Avatar" /></div>
	                <div class="info">
	                    <p>Howdy, <strong><?php echo $user->info()['username']; ?></strong>!</p>
	                    <a href="profile.php" class="button"><i class="fa fa-user fa-fw"></i> Profile</a>
	                    <a href="login.php?logout=true" class="button"><i class="fa fa-lock fa-fw"></i> Logout</a>
	                </div>
	                <div class="clear"></div>
	            </div>
<?php } ?>
	            
	            <div class="links">
<?php if ( $auth->loggedIn() ) { // Links for logged in users. ?>
	                <li><a href="../dashboard.php"><i class="fa fa-tachometer fa-fw"></i> Dashboard</a></li>
<?php if ( $user->isAdmin() ) { ?>
	                <li><a href="index.php"><i class="fa fa-bolt fa-fw"></i> Admin Panel</a></li>
<?php } if ( $user->isMod() || $user->isAdmin() ) { ?>
	                <li><a href="../moderate.php"><i class="fa fa-eye fa-fw"></i> Moderate Posts</a></li>
<?php } ?>
	                <li><a href="../dashboard.php"><i class="fa fa-pencil fa-fw"></i> Edit Submissions</a></li>
	                <li><a href="../settings.php?tab=avatar"><i class="fa fa-picture-o fa-fw"></i> Change Avatar</a></li>
	                <li><a href="../settings.php"><i class="fa fa-cogs fa-fw"></i> My Account</a></li>
<?php } ?>
	            </div>
	            
	        </div><div class="clear"></div>
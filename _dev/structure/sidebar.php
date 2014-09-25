<?php global $user; ?>
            <div id="sidebar">
<?php if ($user->logged_in()) { ?>
                <div class="user">
                    <a href="#" class="avatar"><img src="<?php echo '/avatar/32x32/'.$user->info('avatar'); ?>" alt="" width="64" height="64" /></a>
                    <div class="info">
                        <p>Howdy, <b><?php echo $user->info('username'); ?></b>!</p>
                        <a href="/profile" class="bttn"><i class="fa fa-male"></i> My Profile</a>
                        <a href="/login?logout" class="bttn"><i class="fa fa-lock"></i> Sign Out</a>
                    </div>
                </div>
               <div id="nav-side">
                   <ul>
                       <li><a href="/dashboard"><i class="fa fa-tachometer fa-fw"></i> Dashboard</a></li>
                       <li><a href="/posts"><i class="fa fa-pencil fa-fw"></i> My Posts</a></li>
<?php if ($user->is_admin()) { ?>
                       <li><a href="/admin"><i class="fa fa-tint fa-fw"></i> Admin Panel</a></li>
<?php } if ($user->is_admin() || $user->is_mod()) { ?>
                       <li><a href="/moderate"><i class="fa fa-gavel fa-fw"></i> Moderate</a></li>
<?php } ?>
                       <li><a href="/profile"><i class="fa fa-male fa-fw"></i> My Profile</a></li>
                       <li><a href="/messages"><i class="fa fa-envelope fa-fw"></i> Messages</a></li>
                       <li><a href="/avatar"><i class="fa fa-camera fa-fw"></i> Change Avatar</a></li>
                       <li><a href="/account"><i class="fa fa-cogs fa-fw"></i> Account Settings</a></li>
                   </ul>
               </div>
<?php } ?>
                <div class="avrt">
                    <ins class="adsbygoogle" style="display:inline-block;width:300px;height:600px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="1513409877"></ins>
                    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
                </div>
            </div>
<?php global $user; ?>
<?php if ( !strpos($pg['body_class'], 'boxed') ) echo '</div><!-- End #content -->'; ?>

<?php if ( $pg['sidebar'] && !strpos($pg['body_class'], 'boxed') ) include_once(ABS.'structure/sidebar.php'); ?>

</div></div><!-- End #main -->

<?php

if ( !strpos($pg['body_class'], 'boxed') ) { // If page NOT boxed template.

global $pg;

?>

<div id="footer">
    <div class="wrapper">
        <p>&copy; <a href="/"><span>MCPE Hub</span></a> <?php echo date('Y'); ?> - Creations copyright of the creators.</p>
        <p class="side">Part of the CubeMotion network. <span class="links"><a href="/tos">Terms</a> / <a href="/privacy">Privacy</a> / <a href="/links">Links</a></span></p>
    </div>
</div>

<?php if ( $user->logged_in() ) { ?>
<div id="nav-user" class="dropdown dropdown-tip dropdown-anchor-right">
    <ul class="dropdown-menu">
        <li><a href="/dashboard"><i class="fa fa-tachometer fa-fw"></i> Dashboard</a></li>
        <li><a href="/profile"><i class="fa fa-smile-o fa-fw"></i> My Profile</a></a></li>
        <?php if ( $user->is_admin() || $user->is_mod() ) { ?><li><a href="/moderate"><i class="fa fa-gavel fa-fw"></i> Moderate Posts</a></li><?php } ?>
        <li><a href="/account"><i class="fa fa-wrench fa-fw"></i> Account Settings</a></a></li>
        <li><a href="/logout"><i class="fa fa-lock fa-fw"></i> Sign Out</a></a></li>
    </ul>
</div>
<?php } else { ?>
<div id="modal-auth" class="modal fade modal-sm msg">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa fa-lock"></i> Authentication</h4>
                <button class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body">
                <span class="title"><i class="fa fa-lock"></i><p>Sign in to use this member only feature.</p></span>
                <div class="bttn-group">
                    <a href="/register" class="bttn mid gold">Create Account</a>
                    <a href="/login" class="bttn mid">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div id="modal-error" class="modal fade modal-sm msg">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa fa-exclamation-triangle"></i> Website Error</h4>
                <button class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body">
                <span class="title"><i class="fa fa-exclamation-triangle"></i><p>An error occurred. Please try again later.</p></span>
                <a href="/" data-dismiss="modal" class="bttn mid full">Close Window</a>
            </div>
        </div>
    </div>
</div>
<div id="modal-soon" class="modal fade modal-sm msg">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa fa-magic"></i> Under Construction</h4>
                <button class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body">
                <span class="title"><i class="fa fa-magic"></i><p>This feature is coming soon!</p></span>
                <a href="/" data-dismiss="modal" class="bttn mid full">Close Window</a>
            </div>
        </div>
    </div>
</div>
<?php } // END: If page NOT boxed template. ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="/assets/js/main-min.js"></script>

</body>
</html><?php ob_end_flush(); ?>
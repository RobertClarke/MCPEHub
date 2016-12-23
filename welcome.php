<?php

/**
  * Welcome Page: Shown after user registration.
**/

require_once('core.php');
if ( $user->logged_in() ) redirect('/dashboard');

show_header('Welcome', FALSE, ['body_id' => 'message', 'body_class' => 'boxed']);

?>

<div class="body round">
    <div class="icon-top"><i class="fa fa-smile-o"></i></div>
    <h2>Welcome to MCPE Hub!</h2>
    <p>We've sent you an activation email with a link. Click that link to activate your account.</p>
    <p>Enjoy the community!</p>
</div>
<div class="footer round">
    <a href="/login" class="bttn mini">Sign Into My Account</a>
</div>

<?php show_footer(); ?>
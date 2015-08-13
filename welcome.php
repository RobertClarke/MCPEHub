<?php

/**
 * Welcome Page
 *
 * User gets redirected here after they register for an account.
**/

require_once('loader.php');

$page->body_id = 'error';
$page->body_class = 'boxed';

$page->header('Welcome!');

?>
<div id="logo"><a href="/">MCPE Hub</a></div>
<div id="body">
	<div id="content">
		<i class="icon-smile"></i>
		<h1>Welcome!</h1>
		<p>We can't wait to see what you have to share!</p>
	    <p>We sent you a welcome email with an activation link, click it to activate your account.</p>
	    <p>Enjoy the community!</p>
	</div>
	<div id="footer"><a href="/login" class="bttn">Log into my account</a></div>
</div>
<?php $page->footer(); ?>
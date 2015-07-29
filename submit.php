<?php

/**
 * Submission Form
 *
 * Where users are directed to post a new piece of content for
 * the website.
**/

require_once('loader.php');

$page->auth = true;
$page->body_id = 'submit';
$page->title_h1 = 'Content Upload';

$page->header('Submit Content');

?>
<div class="fullmessage">
	<h2>What are you uploading?</h2>
	<p>We can't wait to see what you have to share, <?php echo $u->username; ?>! Select what type of content you're uploading.</p>
	<div class="type-select">
		<a href="#" class="bttn"><i class="icon-map"></i> Map</a>
		<a href="#" class="bttn"><i class="icon-seed"></i> Seed</a>
		<a href="#" class="bttn"><i class="icon-texture"></i> Texture</a>
		<a href="#" class="bttn"><i class="icon-skin"></i> Skin</a>
		<a href="#" class="bttn"><i class="icon-mod"></i> Mod</a>
		<a href="#" class="bttn"><i class="icon-server"></i> Server</a>
	</div>
</div>
<?php $page->footer(); ?>
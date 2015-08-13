<?php

/**
 * 410 Error Page
**/

//header('HTTP/1.0 410 Gone');
http_response_code(410);

require_once('loader.php');

$page->body_id = 'error';
$page->body_class = 'boxed';

$page->header('Page Gone');

?>
<div id="logo"><a href="/">MCPE Hub</a></div>
<div id="body">
	<div id="content">
		<i class="icon-sad"></i>
		<h1>Page Gone</h1>
		<p>The page you're looking for has been removed.</p>
	</div>
	<div id="footer"><a href="/"><i class="icon-home"></i> Go to homepage</a></div>
</div>
<?php $page->footer(); ?>
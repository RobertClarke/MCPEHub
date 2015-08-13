<?php

/**
 * 404 Error Page
**/

//header('HTTP/1.0 404 Not Found');
http_response_code(404);

require_once('loader.php');

$page->body_id = 'error';
$page->body_class = 'boxed';

$page->header('Page Not Found');

?>
<div id="logo"><a href="/">MCPE Hub</a></div>
<div id="body">
	<div id="content">
		<i class="icon-sad"></i>
		<h1>Page Not Found</h1>
		<p>Sorry, we can't find that page!<br>It might be an old link or maybe it moved.</p>
	</div>
	<div id="footer"><a href="/"><i class="icon-home"></i> Go to homepage</a></div>
</div>
<?php $page->footer(); ?>
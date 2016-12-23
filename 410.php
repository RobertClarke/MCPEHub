<?php

/**
  * 410 Page
**/

header("HTTP/1.0 410 Page Gone");

require_once('core.php');
show_header('Page Gone', FALSE, ['body_id' => 'message', 'body_class' => 'boxed']);

?>

<div class="body round">
    <div class="icon-top"><i class="fa fa-exclamation-triangle smaller"></i></div>
    <h2>410 - Page Gone</h2>
    <p>The page you're looking for has been removed.</p>
    <p>Ensure you typed the URL correctly or this page may have been deleted.</p>
</div>
<div class="footer round">
    <a href="/" class="bttn mini"><i class="fa fa-home"></i> Go To Homepage</a>
</div>

<?php show_footer(); ?>
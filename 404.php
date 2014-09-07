<?php

header("HTTP/1.0 404 Not Found");
require_once( 'core.php' );

show_header( 'Not Found!', FALSE );

?>
<center style="padding: 15px 0;">
<p>The page or post you're looking for can't be found.</p>
<p>Make sure you typed in the URL correctly or this page might have been deleted.</p>

<br />

<a href="index.php" class="bttn"><i class="fa fa-home fa-fw"></i> Return To Homepage</a>

</center>
<?php show_footer(); ?>
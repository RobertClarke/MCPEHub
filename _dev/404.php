<?php

/**
  
  * 404
  
**/

require_once('core.php');

show_header('Not Found!', FALSE, ['body_id' => 'boxed', 'modal_class' => 'wide']);

?>

<div class="title"><h2>404: Not Found</h2></div>
<div class="body">
    <center>
    The page or post you're looking for can't be found.<br><br>
    Make sure you typed in the URL correctly or this page might have been deleted.
    </center>
</div>
<div class="footer">
    <a href="/" class="full">Return to Homepage</a>
</div>

<?php show_footer(); ?>
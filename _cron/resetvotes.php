<?php
//prevent public access
if (php_sapi_name() != "cli")exit; 

require_once( '../core.php' );

$db->update('content_servers', ['votes' => 0]);
?>
<?php

global $email_title, $email_user, $email_content;

// Email default values (to avoid PHP errors in emails).
$email_title = !empty( $email_title ) ? $email_title : 'MCPE Hub';
$email_user = !empty( $email_user ) ? $email_user : '';
$email_content = !empty( $email_content ) ? $email_content : 'This message has no content.';

?>
<!DOCTYPE html>
<html>
<head>
    <style>
    * { margin: 0; padding: 0; outline: 0; }
    html {
	    
	    font: 13px/1.9em Arial, sans-serif;
	    color: #333;
	    background-color: #f7f7f7;
    }
    body {
	    padding: 10px;
    }
    h1 {
	    font-size: 25px;
	    font-weight: normal;
	    margin-bottom: 20px;
	    padding-bottom: 20px;
	    border-bottom: 1px dashed #ddd;
    }
    p {
	    margin-bottom: 15px;
    }
    #container {
	    
	    margin: 0 auto;
	    padding: 20px;
	    background-color: #fff;
    }
    .bottom {
	    margin-top: 20px;
	    padding-top: 20px;
	    color: #999;
	    border-top: 1px dashed #ddd;
    }
    .bottom a {
	    color: #666;
    }
    </style>
</head>
<body>
    <div id="container">
        <h1><?php echo $email_title; ?></h1>
        <?php if ( !empty( $email_user ) ) { ?><p>Hey <strong><?php echo $email_user; ?></strong>!</p><?php } ?>
        <?php echo $email_content; ?>
    </div>
</body>
</html>
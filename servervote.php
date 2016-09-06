<?php

/**
  * Server Post
**/

require_once('core.php');

$type = 'server';
if ( !isset($_GET['post']) || empty($_GET['post']) ) redirect( '/'.$type.'s' );

$slug = $post_tools->cleanSlug($db->escape($_GET['post']));

// Check if post exists + grab info.
$query = $db->from( 'content_'.$type.'s' )->where('`slug` = \''.$slug.'\'')->fetch();
$num = $db->affected_rows;

// If post not found, redirect to post list.
if ( $num == 0 ) redirect('/404');
elseif ( $query[0]['active'] == '-2' ) redirect('/410');

$p = $query[0];
$p['url']		= '/'.$type.'/'.$p['slug'];

$pg = [
	'title_main'	=> 'Server',
	'title_sub'		=> 'Minecraft PE',
];
$p_owner = ( $p['author'] == $user->info('id') ) ? TRUE : FALSE;


$voter->set_server($p);

if($_GET['vrc']){
	if(!$p_owner){
		$error->add('NOT_OWNER', 'You are not the owner of that server.', 'error');
		$error->set('NOT_OWNER');
	}else{
		header('Content-type: text/plain');
		header('Content-Disposition: attachment; filename="mcpehub.com.vrc"');
		echo $voter->votereward_vrc();
		exit;
	}
}

show_header('Vote for '.$p['title'], FALSE, $pg);

if($_POST['vote']){
	$captcha_success = false;
	$error->add('RECAPTCHA_FAIL', 'The captcha was entered incorrectly. Please try again.', 'error');
	$error->add('VOTE_FAIL', 'You have already voted for this server. Try again in '.$voter->vote['remain'].'H', 'info');
	$error->add('VOTE_SUCCESS', 'You have placed your vote for this server!', 'success');
	
	if(!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == ''){
		$error->set('RECAPTCHA_FAIL');
	}else{
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array('secret' => '6LfORSkTAAAAAN6HM9zuQio0gnQYxwaiOLXL9Z0E', 'response' => $_POST['g-recaptcha-response']);

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if($result['success']){
			$emsg = $voter->vote($_POST['mcuser']);
			$error->set($emsg);
			
			if($emsg == 'VOTE_SUCCESS')$p['votes']++;
		}else{
			$error->set('RECAPTCHA_FAIL');
		}
	}
}

$vote = $voter->get_existing_vote();


$html['vote_form'] = '
	<div class="vote">
		<form action="'.$p['url'].'/vote" method="post">
			<div class="g-recaptcha" data-sitekey="6LfORSkTAAAAABCuMLq6a_iIDpmB-OQZqKfvULVX"></div><br/>
			<input type="text" name="mcuser" placeholder="Minecraft username" value="'.$vote['mc_user'].'" '.($vote == null ? '' : 'disabled').'/>
			<input type="hidden" name="vote" value="1"/>
			<button class="bttn big '.($vote == null ? 'green"' : '" disabled').'>
				'.($vote == null ? '<i class="fa fa-plus"></i> Vote for this server' : '<i class="fa fa-clock-o"></i>  Vote again in '.$vote['remain'].'H').'
			</button> 
		</form>
	</div>';

?>

<div id="post" data-id="<?php echo $p['id']; ?>" data-type="<?php echo $type; ?>">
    
    <div id="p-title" class="solo">
		<a href="<?php echo $p['url']; ?>" class="bttn mini green" style="margin-right:10px;"><i class="fa fa-arrow-left"></i> Back</a>
        <h1 style="text-align:center;">Vote for <?php echo $p['title']; ?></h1>
		<div class="likes"><a href="<?php echo $p['url']; ?>/vote" class="bttn mini green"><i class="fa fa-arrow-up"></i> Votes this month</a> <span><?php echo $p['votes']; ?></span></div>
    </div>
    <?php $error->display(); ?>
<?php

echo '
    
    <div class="section">
        <div class="server-info">IP: <b>'.strtolower($p['ip']).'</b><div class="spacer"></div>Port: <b>'.$p['port'].'</b></div>
    </div>
	
	<div class="section">
        '.$html['vote_form'].'
    </div>
    
    <div id="avrt-post" class="section">
        <div class="avrt"><ins class="adsbygoogle" style="display:inline-block;width:336px;height:280px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="9036676673"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script></div>
    </div>
   
    
'; ?>
 
</div>

<?php show_footer(); ?>
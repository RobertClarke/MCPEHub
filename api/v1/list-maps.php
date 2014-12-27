<?php

/**
  * API: List Posts
**/

header('Content-Type: application/json');

require_once('../../core.php');

ob_start('ob_gzhandler');

$where['active'] = 1;

if ( isset($_GET['featured']) ) $where['featured'] = 1;
if ( isset($_GET['tested']) ) $where['tested'] = 1;

$posts = $db->from('content_maps')->order_by('`published` DESC')->where($where)->fetch();

// Set output array, will be encoded later.
$output = [];

require_once('../../core/htmlpurifier/HTMLPurifier.standalone.php');
$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );

// Primary post list.
foreach( $posts as $i => $p ) {
	
	$p['auth']				= $user->info('username', $p['author']);
	
	$map['MapTitle']		= $p['title'];
	$map['Description']		= $purifier->purify($p['description']);
	
	$map['Author']			= $p['auth'];
	$map['AuthorUri']		= 'http://mcpehub.com/user/'.$p['auth'];
	
	$map['MapDownloadUri']	= $p['dl_link'];
	$map['MapImageUri']		= 'http://mcpehub.com/uploads/700x200/maps/'.urlencode($p['images'][0]);
	
	$output['MapList'][] = $map;
	
} // End post foreach loop.

echo json_encode($output);

?>
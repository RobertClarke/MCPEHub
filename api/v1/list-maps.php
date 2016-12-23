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

$htmlconfig = HTMLPurifier_Config::createDefault();
$htmlconfig->set('AutoFormat.RemoveEmpty', true);

$purifier = new HTMLPurifier( $htmlconfig );

// Primary post list.
foreach( $posts as $i => $p ) {
	
	$p['images']			= explode(',', $p['images']);
	$p['auth']				= $user->info('username', $p['author']);
	
	$map['MapTitle']		= $p['title'];
	$map['Description']		= $purifier->purify($p['description']);
	$map['Description']		= str_replace( '<p>'.chr( 194 ) . chr( 160 ).'</p>', '', $map['Description'] );
	$map['Description']		= utf8_encode($map['Description']);
	
	$map['Author']			= $p['auth'];
	$map['AuthorUri']		= 'http://mcpehub.com/user/'.$p['auth'];
	
	$map['MapDownloadUri']	= $p['dl_link'];
	
	foreach( $p['images'] as $img ) {
		$map['MapImageUriList'][] = array('MapImageUri' => 'http://mcpehub.com/uploads/720x500/maps/'.urlencode($img));
	}
	
	$map['NumViews']		= $p['views'];
	$map['Tested']			= $p['tested'];
	$map['Featured']		= $p['featured'];
	
	$output['MapList'][] = $map;
	
	$map = NULL;
	
} // End post foreach loop.

echo json_encode($output);

?>
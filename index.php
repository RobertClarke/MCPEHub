<?php

function redirect( $target ) {
	header('Location: '.$target, true, 301);
	exit;
}

require_once('core/MysqliDb.php');

$config = require_once('301_config.php');

$domain = 'http://minecrafthub.com';

$db = new MysqliDb($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_REDIRECT_TABLE']);

$url = rtrim( rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), '/');
$urlParts = explode('/', $url);

// User visiting just `mcpehub.com/`
if ( !isset($urlParts[1]) || $urlParts[1] === 'index.php' ) {
	redirect($domain.'/pe');
}

switch ( $urlParts[1] ) {
	case 'blog-post':
	case 'map':
	case 'seed':
	case 'texture':
	case 'skin':
	case 'mod':
	case 'server':
		if ( isset($urlParts[2]) ) {
			
			// Attempt to get from DB
			$db_url = $db->query('SELECT * FROM redirect WHERE old = "/'.$urlParts[1].'/'.$urlParts[2].'" AND type = "post" LIMIT 1');

			if ( $db_url ) { redirect($domain.$db_url[0]['new']); } // Found in DB
			else { redirect($domain.'/pe'.$url); } // Not found in DB

		} else {
			redirect($domain.'/'.$urlParts[1]); // ie: User visits just `/map` with no slug
		}
	break;
	case 'user':
		redirect($domain.'/user/'.$urlParts[2]);
	break;
	case 'maps':
	case 'seeds':
	case 'textures':
	case 'skins':
	case 'mods':
	case 'servers':
		redirect($domain.'/pe/'.$urlParts[1]);
	break;
	case 'uploads':
		if ( isset($urlParts[4]) ) {
			
			// Attempt to get from DB
			$db_url = $db->query('SELECT * FROM redirect WHERE old = "/'.$urlParts[3].'/'.$urlParts[4].'" AND type = "image" LIMIT 1');

			if ( $db_url ) { redirect($domain.$db_url[0]['new']); } // Found in DB
			else { redirect($domain.'/404'); } // Not found in DB

		} else {
			redirect($domain.'/404');
		}
	break;
	case 'avatar':
		if ( isset($urlParts[3]) ) {
			
			// Attempt to get from DB
			$db_url = $db->query('SELECT * FROM redirect WHERE old = "'.$urlParts[3].'" AND type = "avatar" LIMIT 1');

			if ( $db_url ) { redirect($domain.$db_url[0]['new']); } // Found in DB
			else { redirect($domain.'/404'); } // Not found in DB

		} else {
			redirect($domain.'/404');
		}
	break;
	default:
		redirect($domain.$url);
	break;
}
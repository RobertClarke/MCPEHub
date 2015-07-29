<?php

/**
 * Map Post Page
 *
 * This is the page used to display individual map posts.
**/

require_once('loader.php');

$page->body_id	= 'post-page';

$page->title_h1	= 'Pocket Nightmare v1.1 - FNaF 4 Map';
$page->title_h2	= 'Minecraft PE Map';

$page->seo_desc	= 'Minecraft PE map posted by anatolie on MCPE Hub, the #1 Minecraft PE community in the world.';
$page->seo_tags	= 'minecraft pe map, minecraft pe, mcpe, mcpe map, ************ ADD TAGS ***************';

$page->fb_title	= 'Pocket Nightmare v1.1 - FNaF 4 Map | Minecraft PE Map';
$page->fb_url	= 'http://CHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURL';
$page->fb_img	= 'http://CHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURL';

$page->fb_article = true;
$page->share_apis = true;

$page->canonical = 'http://CHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURLCHANGEURL';

$page->header('Pocket Nightmare v1.1 - FNaF 4 Map | Minecraft PE Map on MCPE Hub', true);

$smarty = new Smarty;
$smarty->setTemplateDir(CORE.'templates/');

$smarty->assign('type',			'map');
$smarty->assign('title',		'TITLE');
$smarty->assign('author',		'AUTHOR');
$smarty->assign('url',			'http://google.com');
$smarty->assign('views',		'12,345');
$smarty->assign('likes',		'12,345');
$smarty->assign('comments',		'12,345');
$smarty->assign('downloads',	'12,345');
$smarty->assign('avatar',		'AVATAR');
$smarty->assign('images',		['example.jpg', 'example2.jpg']);
$smarty->assign('description',	'DESCRIPTION');

// Generate post page
$smarty->display('post.tpl');

$page->footer();
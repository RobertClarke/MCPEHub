<?php

/**
  * Seed Post
**/

require_once('core.php');

$type = 'seed';
if ( !isset($_GET['post']) || empty($_GET['post']) ) redirect( '/'.$type.'s' );

$slug = $post_tools->cleanSlug($db->escape($_GET['post']));

// Check if post exists + grab info.
$query = $db->from( 'content_'.$type.'s' )->where('`slug` = \''.$slug.'\'')->fetch();
$num = $db->affected_rows;

// If post not found, redirect to post list.
if ( $num == 0 ) redirect('/404');
elseif ( $query[0]['active'] == '-2' ) redirect('/410');

$p = $query[0];

$pg = [
	'title_main'	=> 'Seed',
	'title_sub'		=> 'Minecraft PE',
	//'seo_desc'		=> 'Collection of the best Minecraft PE Maps and game worlds for download including adventure, survival, and parkour maps.',
	//'seo_keywords'	=> 'minecraft pe maps, survival, parkour, adventure, minecraft pe, mcpe'
];

show_header($p['title'], FALSE, $pg);

$p_owner = ( $p['author'] == $user->info('id') ) ? TRUE : FALSE;

// Show messages, if user is post author.
if ( $p_owner ) {
	
	$error->add('SUBMITTED',	'You\'ve submitted your '.$type.' for approval! Once a moderator approves it, it\'ll show up on the public website. You can link your friends to this '.$type.' and they\'ll be able to view it.', 'check');
	$error->add('PENDING',		'Your '.$type.' hasn\'t been approved by a moderator yet and isn\'t shown on the public website yet. You can link your friends to this '.$type.' and they\'ll be able to view it.', 'warning');
	$error->add('REJECTED',		'Your '.$type.' was rejected by a moderator and won\'t appear on the public website.', 'error');
	$error->add('EDITED',		'You\'ve edited your '.$type.'. Once your changes are approved by a moderator, your '.$type.' will be visible on the public website again.', 'warning');
	
	if 		( $p['active'] == 0 )		$error->set('PENDING');
	elseif	( $p['active'] == '-1' )	$error->set('REJECTED');
	
	elseif	( $p['active'] == 0 && $p['edited'] != 0 ) $error->set('EDITED');
	
	if		( isset($_GET['created']) )	$error->set('SUBMITTED');
	if		( isset($_GET['edited']) )	$error->set('EDITED');
	
}

// Update view count on post.
$post_tools->update_views($p['id'], $type);

// Determine number of likes & comments for post.
$db_count = $db->query('
	(SELECT "likes"		AS data, COUNT(*) FROM `likes`		WHERE post="'.$p['id'].'" AND type="'.$type.'") UNION ALL
	(SELECT "comments" 	AS data, COUNT(*) FROM `comments`	WHERE post="'.$p['id'].'" AND type="'.$type.'")
')->fetch();

foreach( $db_count as $key ) $p[$key['data']] = $key['COUNT(*)'];

// Check if user has liked or favorited the post already.
if ( $user->logged_in() ) {
	
	$q_where = '`post` = \''.$p['id'].'\' AND `type` = \''.$type.'\' AND `user` = \''.$user->info('id').'\'';
	$us_count = $db->query('
		(SELECT "liked"		AS data, COUNT(*) FROM `likes`		WHERE '.$q_where.') UNION ALL
		(SELECT "favorited"	AS data, COUNT(*) FROM `favorites`	WHERE '.$q_where.')
	')->fetch();
	
	foreach( $us_count as $key ) $p[$key['data']] = $key['COUNT(*)'];
	
	$p['liked']		= ( $p['liked'] != 0 ) ? TRUE : FALSE;
	$p['favorited']	= ( $p['favorited'] != 0 ) ? TRUE : FALSE;
	
	$q_where = '`following` = \''.$p['author'].'\' AND `user` = \''.$user->info('id').'\'';
	$p['following'] = $db->query('SELECT COUNT(*) FROM `following` WHERE '.$q_where)->fetch()[0]['COUNT(*)'];
	
	$p['following'] = ( $p['following'] != 0 ) ? TRUE : FALSE;
	
}

$p['auth'] 		= $user->info('username', $p['author']);
$p['url']		= '/'.$type.'/'.$p['slug'];
$p['url_auth']	= '/user/'.$p['auth'];

$p['thumb_a']	= '/avatar/112x112/'.$user->info('avatar', $p['auth']);

$p['published']	= ( $p['published'] != 0 ) ? 'Posted '.date( 'd/m/Y', strtotime($p['published']) ) : 'Not Approved Yet';

// Slideshow images.
$p['images']	= explode(',', $p['images']);

foreach( $p['images'] as $img ) {
	$p['img_full'][]	= '/uploads/690x250/'.$type.'s/'.urlencode($img);
	$p['img_thumb'][]	= '/uploads/120x70/'.$type.'s/'.urlencode($img);
}

// Sanitize post description.
require_once( 'core/htmlpurifier/HTMLPurifier.standalone.php' );
$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );

$p['description'] = $purifier->purify($p['description']);

if ( $user->logged_in() && $p['liked'] ) {
	$html['bttn_like'] = '<a href="'.$p['url'].'" class="bttn mini green like"><i class="fa fa-thumbs-up"></i> Liked</a>';
	$html['bttn_like_top'] = '<a href="'.$p['url'].'" class="bttn mini green like"><i class="fa fa-thumbs-up solo"></i> Liked</a>';
} else {
	$html['bttn_like'] = '<a href="'.$p['url'].'" class="bttn mini like"><i class="fa fa-thumbs-up"></i> Like</a>';
	$html['bttn_like_top'] = '<a href="'.$p['url'].'" class="bttn mini green like"><i class="fa fa-thumbs-up solo"></i> Like</a>';
}

if ( $user->logged_in() && $p['favorited'] ) $html['bttn_fav'] = '<a href="'.$p['url'].'" class="bttn mini red fav"><i class="fa fa-heart"></i> Favorited</a>';
else $html['bttn_fav'] = '<a href="'.$p['url'].'" class="bttn mini fav"><i class="fa fa-heart"></i> Favorite</a>';

if ( $user->logged_in() && $p['following'] ) $html['bttn_follow'] = '<a href="'.$p['url'].'" class="bttn mini sub green follow" data-following="'.$p['author'].'"><i class="fa fa-check"></i> Following</a>';
else $html['bttn_follow'] = '<a href="'.$p['url'].'" class="bttn mini sub follow" data-following="'.$p['author'].'"><i class="fa fa-rss"></i> Follow</a>';

?>

<div id="post" data-id="<?php echo $p['id']; ?>" data-type="<?php echo $type; ?>">
    
    <div id="p-title" class="solo">
        <h1><?php echo $p['title']; ?></h1>
        <div class="likes"><?php echo $html['bttn_like_top']; ?> <span><?php echo $p['likes']; ?></span></div>
    </div>
    
    <?php $error->display(); ?>
    <?php $post_tools->mod_toolkit($p['id'], $type, $p, $p['author']); ?>
    
    <div id="slideshow">
        <div id="slider" class="flexslider">
            <ul class="slides"><?php foreach( $p['img_full'] as $img ) echo '<li><img src="'.$img.'" alt="'.$p['title'].'" width="690" height="250"></li>'; ?></ul>
        </div>
        <div id="carousel" class="flexslider carousel">
            <ul class="slides"><?php foreach( $p['img_thumb'] as $img ) echo '<li><img src="'.$img.'" alt="'.$p['title'].'" width="120" height="70"></li>'; ?></ul>
        </div>
    </div>
    
<?php

echo '
    <div id="details" class="section">
        <div class="author">
            <a href="'.$p['url_auth'].'"><img src="'.$p['thumb_a'].'" alt="'.$p['auth'].'" width="56" height="56"></a>
            <p>
                <span class="poster">
                    <a href="'.$p['url_auth'].'">'.$p['auth'].'</a>
                    '.$user->badges($p['author']).'
                </span>
                '.$html['bttn_follow'].'
                <span class="posted">'.$p['views'].' Views ~ '.$p['published'].'</span>
            </p>
        </div>
        <div class="info">
            <div class="copy">'.$p['seed'].'</div>
        </div>
        <div class="actions">
            '.$html['bttn_like'].'
            '.$html['bttn_fav'].'
            <a href="#comments" class="bttn mini"><i class="fa fa-comments"></i> '.$p['comments'].' Comments</a>
            <div class="extra">
                <a href="'.$p['url'].'#report" class="bttn mini" data-toggle="modal" data-target="#modal-soon"><i class="fa fa-flag"></i> Report '.ucwords($type).'</a>
            </div>
        </div>
    </div>
    
    <div id="avrt-post" class="section">
        <div class="avrt"><ins class="adsbygoogle" style="display:inline-block;width:336px;height:280px" data-ad-client="ca-pub-3736311321196703" data-ad-slot="9036676673"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script></div>
    </div>
    
    <div id="description" class="section">'.$p['description'].'</div>
    
'; ?>

<?php $comments->show($p['id'], $type); ?>
    
</div>

<?php show_footer(); ?>
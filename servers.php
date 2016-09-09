<?php

/**
  * Servers List
**/

require_once('core.php');

$pg = [
	'title_main'	=> 'Servers',
	'title_sub'		=> 'Minecraft PE',
	'seo_desc'		=> 'The best Minecraft PE servers for you to play on your friends. Find MCPE Servers for the latest version of Minecraft PE.',
	'seo_keywords'	=> 'minecraft pe servers, mcpe servers, minecraft pe server, minecraft pe, mcpe'
];

show_header('Minecraft PE Servers', FALSE, $pg);

$current_page = ( isset($_GET['page']) ) ? $_GET['page'] : 1;

// Sort options.
if ( !empty($_GET['sort'])) {
	$sort = in_array($_GET['sort'], ['views','published','votes']) ? $_GET['sort'] : 'votes';
	
	$url->add('sort', $sort);

	$db_sort = $sort.' DESC';
	$db->order_by($db_sort);
}

// If user searching, add onto query.
if ( !empty($_GET['search']) ) {

	$search = $_GET['search'];
	$url->add('search', urlencode($search));

	$db->like(['title' => $db->escape(strip_tags($search))]);

}

$count	= $db->select('COUNT(*) AS count')->from('content_servers')->where(['active' => 1])->fetch()[0]['count'];
$offset	= $pagination->build($count, 10, $current_page);

// Must set this again since SQL class reset itself after fetch above.
if ( isset($search) ) $db->like(['title' => $db->escape(strip_tags($search))]);

$posts = $db->from('content_servers')->limit($offset, 10)->order_by($db_sort)->where(['active' => 1])->fetch();

// If no posts are found, display an error.
if ( $count == 0 ) {
	if ( !empty($_GET['search']) ) $error->add('NULL', 'No posts were found under "<b>'.htmlspecialchars($_GET['search']).'</b>".', 'warning');
	$error->set('NULL');
}

// If search results are found, display a message.
elseif ( !empty($_GET['search']) && $current_page == 1 ) {
	$error->add('S_RESULT', 'Your search for "<b>'.htmlspecialchars($_GET['search']).'</b>" returned '.$count.' results.', 'info');
	$error->set('S_RESULT');
}

?>

<div id="p-title">
    <h1>Minecraft PE Servers</h1>
    <div class="tabs">
        <a href="/servers" class="bttn mid tip search" data-tip="Search Servers"><i class="fa fa-search solo"></i></a>
        <a href="https://netherbox.com/?promo=MINE" class="bttn mid gold" target="_blank"><i class="fa fa-flag-checkered"></i> Minecraft Server Hosting</a>
        <a href="/submit?type=server" class="bttn mid green"><i class="fa fa-upload"></i> Submit Server</a>
    </div>
</div>

<div id="search"<?php if ( !empty($_GET['search']) ) echo ' class="visible"'; ?>>
    <form action="<?php echo $url->show('', TRUE); ?>" method="GET" class="form">
        <input type="text" name="search" id="search" placeholder="Search Servers..." value="<?php $form->get_val('search'); ?>" maxlength="150">
        <button type="submit" id="submit" class="bttn mid gold"><i class="fa fa-search"></i> Search</button>
    </form>
</div>

<div class="posts-tools">

<?php if ( $count != 0 ) { ?>
    <select data-placeholder="Sort By" class="chosen redirect">
        <option value="<?php echo $url->show('sort=votes'); ?>"<?php if ( empty($sort) ) echo ' selected'; ?>>Most Voted</option>
        <option value="<?php echo $url->show('sort=views'); ?>"<?php if ( $sort == 'views' ) echo ' selected'; ?>>Most Viewed</option>
		<option value="<?php echo $url->show('sort=published'); ?>"<?php if ( $sort == 'published' ) echo ' selected'; ?>>Latest Servers</option>
    </select>
<?php $pagination->html(); } ?>

</div>

<?php $error->display(); ?>

<div id="posts" class="servers compact">

<?php

// Lifeboat sponsored server
if ( $count != 0 && $current_page == 1 ) {
	$sponsored = $db->from('content_servers')->limit(1)->where(['id' => 4])->fetch();
	array_unshift($posts, $sponsored[0]);
}

// Primary post list.
foreach( $posts as $i => $p ) {

$p['auth']		= $user->info('username', $p['author']);
$p['url']		= '/server/'.$p['slug'];
$p['url_auth']	= '/user/'.$p['auth'];

// Grab first image for post thumbnail.
$p['images']	= explode(',', $p['images']);
$p['thumb']		= '/uploads/700x200/servers/'.urlencode($p['images'][0]);
$p['thumb_a']	= '/avatar/64x64/'.$user->info('avatar', $p['auth']);

// Sponsored server tag
if ( $p['id'] == 4 )
	$p['f_html'] = '<div class="featured"><i class="fa fa-star fa-fw"></i> Sponsored</div>';
else
	$p['f_html'] = ( $p['featured'] == 1 ) ? '<div class="featured"><i class="fa fa-star fa-fw"></i> Featured</div>' : NULL;

// Determine number of likes & comments for post.
$db_count = $db->query('
	(SELECT "likes"		AS data, COUNT(*) FROM `likes`		WHERE post="'.$p['id'].'" AND type="server") UNION ALL
	(SELECT "comments" 	AS data, COUNT(*) FROM `comments`	WHERE post="'.$p['id'].'" AND type="server")
')->fetch();

foreach( $db_count as $key ) $p[$key['data']] = $key['COUNT(*)'];

echo '
<div class="post" data-server="'.$p['id'].'">
    <div class="img">'.$p['f_html'].'
        <a href="'.$p['url'].'"><img src="'.$p['thumb'].'" alt="'.$p['title'].'" width="700" height="200"></a>
        <div class="over">
            <a href="'.$p['url_auth'].'"><img src="'.$p['thumb_a'].'" alt="'.$p['auth'].'" width="32" height="32" class="avatar"></a>
            <h2><a href="'.$p['url'].'">'.$p['title'].'</a></h2>
        </div>
    </div>
    <div class="info">
        <span><i class="fa fa-globe"></i> <strong><span class="status">Loading...</span></strong></span>
        <span><i class="fa fa-thumbs-up"></i> <strong>'.$p['likes'].'</strong> likes</span>
        '.($sort == 'votes' ? '<span><i class="fa fa-arrow-up"></i> <strong>'.$p['votes'].'</strong> votes</span>' : '<span><i class="fa fa-eye"></i> <strong>'.($p['views'] >= 1000 ? floor($p['views']/1000).'K' : $p['views']).'</strong> views</span>').'
        <span><i class="fa fa-comments"></i> <strong>'.$p['comments'].'</strong> comments</span>
		
        <span class="players"></span>
';

// Sponsored server button
if ( $p['id'] == 4 )
	echo '<a href="'.$p['url'].'" class="bttn mid"><i class="fa fa-star"></i> Check It Out</a>';
else
	echo '<a href="'.$p['url'].'" class="bttn mid"><i class="fa fa-gamepad"></i> Show Server Details</a>';

echo '
    </div>
</div>
';

} // End post foreach loop.

?>

</div>

<?php if ( $count != 0 ) $pagination->html(); ?>

<p class="tracking">Tracking <?php echo number_format($count); ?> MCPE Servers</p>

<?php show_footer(); ?>

<?php

/**
  * Servers Post List
**/

require_once('core.php');

$pi = [
	'title_main'		=> 'Servers',
	'title_sub'			=> 'Minecraft PE',
	'seo_description'	=> 'The best Minecraft PE servers for you to play on your friends. Find MCPE Servers for the latest version of Minecraft PE.',
	'seo_keywords'		=> 'minecraft pe servers, minecraft pe server, mcpe server, minecraft pe, mcpe'
];

show_header('Minecraft PE Servers', FALSE, $pi);

$current_page = ( isset($_GET['page']) ) ? $_GET['page'] : 1;

// Sort options.
if ( !empty($_GET['sort']) && in_array($_GET['sort'], ['views']) ) {
	$sort = $_GET['sort'];
	$url->add('sort', $_GET['sort']);
	
	$db_sort = $sort.' DESC';
	$db->order_by($db_sort);
	
} else {
	$sort = NULL;
	$db_sort = 'published DESC';
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

$posts	= $db->from('content_servers')->limit($offset, 10)->order_by($db_sort)->where(['active' => 1])->fetch();

// If no posts are found, display an error.
if ( $count == 0 ) {
	
	// If user was searching something.
	if ( !empty($_GET['search']) ) $error->add('NULL', 'No posts were found under "<b>'.htmlspecialchars($_GET['search']).'</b>".', 'warning');
	else $error->add('NULL', 'Sorry, there are no posts found in this category.', 'warning');
	
	$error->set('NULL');
	
}

// If search results are found, display a message.
elseif ( !empty($_GET['search']) && $current_page == 1 ) {
	$error->add('S_RESULT', 'Your search for "<b>'.htmlspecialchars($_GET['search']).'</b>" returned '.$count.' results.', 'info');
	$error->set('S_RESULT');
}

?>

<div id="page-title">
    <h1>Minecraft PE Servers</h1>
    <div class="links">
        <a href="http://netherbox.com/p/mcpehub" class="bttn" target="_blank"><i class="fa fa-flag-checkered"></i> Start Your Own Server!</a>
        <a href="#search" class="bttn search-show"><i class="fa fa-search"></i> Search</a>
        <a href="/submit?type=server" class="bttn green"><i class="fa fa-plus"></i> Submit Server</a>
    </div>
</div>

<div class="search<?php if ( !empty($_GET['search']) ) echo ' visible'; ?>">
    <form action="<?php echo $url->show('', 1); ?>" method="GET" class="form">
        <input type="text" name="search" id="search" placeholder="Search Servers..." value="<?php $form->GET_value('search'); ?>" maxlength="150">
        <button type="submit" id="submit" class="bttn purple">Search</button>
    </form>
</div>

<div class="catnav">

<form class="cat">
    <select data-placeholder="Sort By" class="chosen redirect">
        <option value=""></option>
        <option value="<?php echo $url->show('sort=latest'); ?>"<?php if ( empty($sort) ) echo ' selected'; ?>>Latest Servers</option>
        <option value="<?php echo $url->show('sort=views'); ?>"<?php if ( $sort == 'views' ) echo ' selected'; ?>>Most Viewed</option>
    </select>
</form>

<?php $pagination->html('right'); ?>

</div>

<?php $error->display(); ?>

<?php if ( $count != 0 ) { // If posts are found in the query. ?>

<div id="server-list" class="posts compact">
    
<?php

// Primary post list.
foreach( $posts as $i => $p ) {
	
	// Determine number of likes & comments for post.
	$db_count = $db->query('
		(SELECT "likes"		AS data, COUNT(*) FROM `likes`		WHERE post="'.$p['id'].'" AND type="server") UNION ALL
		(SELECT "comments" 	AS data, COUNT(*) FROM `comments`	WHERE post="'.$p['id'].'" AND type="server")
	')->fetch();
	
	foreach( $db_count as $key ) $p[$key['data']] = $key['COUNT(*)'];
	
	// Set additional post info.
	$p['auth']		= $user->info('username', $p['author']);
	$p['url']		= '/server/'.$p['slug'];
	$p['url_a']		= '/user/'.$p['auth'];
	
	// Grab first image for post thumbnail.
	$p['images']	= explode(',', $p['images']);
	$p['thumb']		= '/uploads/700x50/servers/'.urlencode($p['images'][0]);
	$p['thumb_a']	= '/avatar/32x32/'.$user->info('avatar', $p['auth']);
	
	$p['featured_html'] = ( $p['featured'] == 1 ) ? '<div class="featured"><i class="fa fa-star"></i> Featured</div>' : NULL;
	$p['title_width']	= ( $p['featured'] == 1 ) ? ' style="width: 475px;"' : NULL;
	
echo '
<div class="post" data-server="'.$p['id'].'">
    <div class="img">
        <div class="overlay">
            <a href="'.$p['url_a'].'"><img src="'.$p['thumb_a'].'" class="avatar" alt="'.$p['auth'].'" width="32" height="32"></a>
            <h2'.$p['title_width'].'><a href="'.$p['url'].'">'.$p['title'].'</a></h2>
            <div class="status"><i class="fa fa-spinner"></i></div>
            '.$p['featured_html'].'
        </div>
        <a href="'.$p['url'].'"><img src="'.$p['thumb'].'" alt="'.$p['title'].'" width="700" height="50"></a>
    </div>
    <div class="info">
        <ul>
            <li><i class="fa fa-thumbs-up"></i> <b>'.$p['likes'].'</b> likes</li>
            <li><i class="fa fa-eye"></i> <b>'.$p['views'].'</b> views</li>
            <li><i class="fa fa-comments"></i> <b>'.$p['comments'].'</b> comments</li>
        </ul>
        <div class="link"><a href="'.$p['url'].'" class="button dl silver"><i class="fa fa-gamepad"></i> Server Details</a></div>
    </div>
</div>';

} // End: Primary post list.

?>

</div>

<?php $pagination->html(); ?>

<?php } // End: If posts are found in the query. ?>

<?php show_footer(); ?>
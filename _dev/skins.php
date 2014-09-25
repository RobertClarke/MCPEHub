<?php

/**
  * Skins Post List
**/

require_once('core.php');

$pi = [
	'title_main'		=> 'Skins',
	'title_sub'			=> 'Minecraft PE',
	'seo_description'	=> 'All kinds Minecraft PE skins, to change the look of your Minecraft PE player in your game.',
	'seo_keywords'		=> 'minecraft pe skins, skins, minecraft pe, mcpe'
];

show_header('Minecraft PE Skins', FALSE, $pi);

$current_page = ( isset($_GET['page']) ) ? $_GET['page'] : 1;

// Sort options.
if ( !empty($_GET['sort']) && in_array($_GET['sort'], ['views', 'downloads']) ) {
	$sort = $_GET['sort'];
	$url->add('sort', $_GET['sort']);
	
	$db_sort = $sort.' DESC';
	$db->order_by($db_sort);
	
} else {
	$sort = NULL;
	$db_sort = 'published DESC';
}

// Set allowed categories.
$cats = ['Boy', 'Girl', 'Mob', 'Animal', 'TV', 'Movies', 'Games', 'Fantasy', 'Other'];
foreach( $cats as $cat ) $post_cats[$cat] = preg_replace("/[\s_]/", "-", strtolower($cat), 1);

// If user selected a category.
if ( !empty($_GET['category']) ) {
	
	if ( array_search($_GET['category'], $post_cats) ) {
		$db->like(['tags' => $_GET['category']]);
		$url->add('category', $_GET['category']);
	}
	else $_GET['category'] = NULL;
	
}

// If user searching, add onto query.
elseif ( !empty($_GET['search']) ) {
	
	$search = $_GET['search'];
	$url->add('search', urlencode($search));
	
	$db->like(['title' => $db->escape(strip_tags($search))]);
	
}

$count	= $db->select('COUNT(*) AS count')->from('content_skins')->where(['active' => 1])->fetch()[0]['count'];
$offset	= $pagination->build($count, 10, $current_page);

// Must set this again since SQL class reset itself after fetch above.
if ( !empty($_GET['category']) && array_search($_GET['category'], $post_cats) ) $db->like(['tags' => $_GET['category']]);
elseif ( isset($search) ) $db->like(['title' => $db->escape(strip_tags($search))]);

$posts	= $db->from('content_skins')->limit($offset, 10)->order_by($db_sort)->where(['active' => 1])->fetch();

// If no posts are found, display an error.
if ( $count == 0 ) {
	
	// If user was searching something.
	if ( !empty($_GET['search']) ) $error->add('NULL', 'No posts were found under "<b>'.htmlspecialchars($_GET['search']).'</b>".', 'warning');
	else $error->add('NULL', 'Sorry, there are no posts found in this category.', 'warning');
	
	$error->set('NULL');
	
}

// If category search requested, disply a message.
elseif( !empty($_GET['category']) && $current_page == 1 ) {
	$error->add('C_RESULT', 'There were '.$count.' skins found under the category <b>'.array_search($_GET['category'], $post_cats).'</b>.', 'info');
	$error->set('C_RESULT');
}

// If search results are found, display a message.
elseif ( !empty($_GET['search']) && $current_page == 1 ) {
	$error->add('S_RESULT', 'Your search for "<b>'.htmlspecialchars($_GET['search']).'</b>" returned '.$count.' results.', 'info');
	$error->set('S_RESULT');
}

?>

<div id="page-title">
    <h1>Minecraft PE Skins</h1>
    <div class="links">
        <a href="#search" class="bttn search-show"><i class="fa fa-search"></i> Search</a>
        <a href="/submit?type=skin" class="bttn green"><i class="fa fa-plus"></i> Submit Skin</a>
    </div>
</div>

<div class="search<?php if ( !isset($_GET['category']) && !empty($_GET['search']) ) echo ' visible'; ?>">
    <form action="<?php echo $url->show('', 1); ?>" method="GET" class="form">
        <input type="text" name="search" id="search" placeholder="Search Skins..." value="<?php $form->GET_value('search'); ?>" maxlength="150">
        <button type="submit" id="submit" class="bttn purple">Search</button>
    </form>
</div>

<div class="catnav">

<form class="cat">
<?php if ( empty($_GET['search']) ) { ?>
    <select data-placeholder="Choose a Category" class="chosen redirect"><option value=""></option>
<?php

if ( !empty($_GET['category']) ) echo '<option value="'.$url->show(NULL, 1).'">All Skins</option>';

// Echo out post category selector.
foreach( $post_cats as $title => $id ) {
	$cat_sel	= ( !empty($_GET['category']) && $_GET['category'] == $id) ? ' selected' : NULL;
	echo '<option value="'.$url->show('category='.$id, 1).'"'.$cat_sel.'>'.$title.'</option>';
}

?>
    </select>
<?php } ?>
    <select data-placeholder="Sort By" class="chosen redirect">
        <option value=""></option>
        <option value="<?php echo $url->show('sort=latest'); ?>"<?php if ( empty($sort) ) echo ' selected'; ?>>Latest Skins</option>
        <option value="<?php echo $url->show('sort=views'); ?>"<?php if ( $sort == 'views' ) echo ' selected'; ?>>Most Viewed</option>
        <option value="<?php echo $url->show('sort=downloads'); ?>"<?php if ( $sort == 'downloads' ) echo ' selected'; ?>>Most Downloaded</option>
    </select>
</form>

<?php $pagination->html('right'); ?>

</div>

<?php $error->display(); ?>

<?php if ( $count != 0 ) { // If posts are found in the query. ?>

<div class="posts">
    
<?php

// Primary post list.
foreach( $posts as $i => $p ) {
	
	// Determine number of likes & comments for post.
	$db_count = $db->query('
		(SELECT "likes"		AS data, COUNT(*) FROM `likes`		WHERE post="'.$p['id'].'" AND type="skin") UNION ALL
		(SELECT "comments" 	AS data, COUNT(*) FROM `comments`	WHERE post="'.$p['id'].'" AND type="skin")
	')->fetch();
	
	foreach( $db_count as $key ) $p[$key['data']] = $key['COUNT(*)'];
	
	// Set additional post info.
	$p['auth']		= $user->info('username', $p['author']);
	$p['url']		= '/skin/'.$p['slug'];
	$p['url_a']		= '/user/'.$p['auth'];
	
	// Grab first image for post thumbnail.
	$p['images']	= explode(',', $p['images']);
	$p['thumb']		= '/uploads/700x200/skins/'.urlencode($p['images'][0]);
	$p['thumb_a']	= '/avatar/32x32/'.$user->info('avatar', $p['auth']);
	
	$p['featured_html'] = ( $p['featured'] == 1 ) ? '<div class="featured"><i class="fa fa-star"></i> Featured Skin</div>' : NULL;
	
echo '
<div class="post">
    <div class="img">
        '.$p['featured_html'].'
        <div class="overlay">
            <a href="'.$p['url_a'].'"><img src="'.$p['thumb_a'].'" class="avatar" alt="'.$p['auth'].'" width="32" height="32"></a>
            <h2><a href="'.$p['url'].'">'.$p['title'].'</a></h2>
        </div>
        <a href="'.$p['url'].'"><img src="'.$p['thumb'].'" alt="'.$p['title'].'" width="700" height="200"></a>
    </div>
    <div class="info">
        <ul>
            <li><i class="fa fa-thumbs-up"></i> <b>'.$p['likes'].'</b> likes</li>
            <li><i class="fa fa-eye"></i> <b>'.$p['views'].'</b> views</li>
            <li><i class="fa fa-comments"></i> <b>'.$p['comments'].'</b> comments</li>
            <li class="solo"><i class="fa fa-clock-o"></i> '.since(strtotime($p['published'])).'</li>
            <li class="solo"><i class="fa fa-tags"></i> '.ucwords($p['tags']).'</li>
        </ul>
        <div class="link"><a href="'.$p['url'].'" class="button dl"><i class="fa fa-download"></i> Download <span>'.$p['downloads'].'</span></a></div>
    </div>
</div>';

} // End: Primary post list.

?>

</div>

<?php $pagination->html(); ?>

<?php } // End: If posts are found in the query. ?>

<?php show_footer(); ?>
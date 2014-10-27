<?php

/**
  * Seeds List
**/

require_once('core.php');

$pg = [
	'title_main'	=> 'Seeds',
	'title_sub'		=> 'Minecraft PE',
	'seo_desc'		=> 'MCPE\'s largest collection of Minecraft PE seeds. The most up to date list of MCPE seeds in Minecraft.',
	'seo_keywords'	=> 'minecraft pe seeds, flat seeds, minecraft pe, mcpe'
];

show_header('Minecraft PE Seeds', FALSE, $pg);

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

// Set allowed categories.
$cats = ['Caverns', 'Diamonds', 'Flat', 'Lava', 'Mountain', 'Overhangs', 'Waterfall'];
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

$count	= $db->select('COUNT(*) AS count')->from('content_seeds')->where(['active' => 1])->fetch()[0]['count'];
$offset	= $pagination->build($count, 10, $current_page);

// Must set this again since SQL class reset itself after fetch above.
if ( !empty($_GET['category']) && array_search($_GET['category'], $post_cats) ) $db->like(['tags' => $_GET['category']]);
elseif ( isset($search) ) $db->like(['title' => $db->escape(strip_tags($search))]);

$posts = $db->from('content_seeds')->limit($offset, 10)->order_by($db_sort)->where(['active' => 1])->fetch();

// If no posts are found, display an error.
if ( $count == 0 ) {
	if ( !empty($_GET['search']) ) $error->add('NULL', 'No posts were found under "<b>'.htmlspecialchars($_GET['search']).'</b>".', 'warning');
	else $error->add('NULL', 'Sorry, there are no posts found in this category.', 'warning');
	$error->set('NULL');
}

// If category search requested, disply a message.
elseif( !empty($_GET['category']) && $current_page == 1 ) {
	$error->add('C_RESULT', 'There were '.$count.' seeds found under the category <b>'.array_search($_GET['category'], $post_cats).'</b>.', 'info');
	$error->set('C_RESULT');
}

// If search results are found, display a message.
elseif ( !empty($_GET['search']) && $current_page == 1 ) {
	$error->add('S_RESULT', 'Your search for "<b>'.htmlspecialchars($_GET['search']).'</b>" returned '.$count.' results.', 'info');
	$error->set('S_RESULT');
}


?>

<div id="p-title">
    <h1>Minecraft PE Seeds</h1>
    <div class="tabs">
        <div class="bttn-group">
            <a href="/seeds" class="bttn mid tip search" data-tip="Search Seeds"><i class="fa fa-search solo"></i></a>
        </div>
        <a href="/submit?type=seed" class="bttn mid green"><i class="fa fa-upload"></i> Submit Seed</a>
    </div>
</div>

<div id="search"<?php if ( !isset($_GET['category']) && !empty($_GET['search']) ) echo ' class="visible"'; ?>>
    <form action="<?php echo $url->show('', TRUE); ?>" method="GET" class="form">
        <input type="text" name="search" id="search" placeholder="Search Seeds..." value="<?php $form->get_val('search'); ?>" maxlength="150">
        <button type="submit" id="submit" class="bttn mid gold"><i class="fa fa-search"></i> Search</button>
    </form>
</div>

<div class="posts-tools">
    
    <select data-placeholder="Choose a Category" class="chosen redirect">
        <option value=""></option>
<?php
if ( !empty($_GET['category']) ) echo '<option value="'.$url->show(NULL, 1).'">All Seeds</option>';
foreach( $post_cats as $title => $id ) {
	$cat_sel	= ( !empty($_GET['category']) && $_GET['category'] == $id) ? ' selected' : NULL;
	echo '<option value="'.$url->show('category='.$id, 1).'"'.$cat_sel.'>'.$title.'</option>';
}
?>
    </select>
<?php if ( $count != 0 ) { ?>
    <select data-placeholder="Sort By" class="chosen redirect">
        <option value="<?php echo $url->show('sort=latest'); ?>"<?php if ( empty($sort) ) echo ' selected'; ?>>Latest Seeds</option>
        <option value="<?php echo $url->show('sort=views'); ?>"<?php if ( $sort == 'views' ) echo ' selected'; ?>>Most Viewed</option>
    </select>
<?php $pagination->html(); } ?>
    
</div>

<?php $error->display(); ?>

<div id="posts">
    
<?php

// Primary post list.
foreach( $posts as $i => $p ) {

$p['auth']		= $user->info('username', $p['author']);
$p['url']		= '/seed/'.$p['slug'];
$p['url_auth']	= '/user/'.$p['auth'];

// Grab first image for post thumbnail.
$p['images']	= explode(',', $p['images']);
$p['thumb']		= '/uploads/700x200/seeds/'.urlencode($p['images'][0]);
$p['thumb_a']	= '/avatar/64x64/'.$user->info('avatar', $p['auth']);

$p['f_html']	= ( $p['featured'] == 1 ) ? '<div class="featured"><i class="fa fa-star fa-fw"></i> Featured Seed</div>' : NULL;

// Determine number of likes & comments for post.
$db_count = $db->query('
	(SELECT "likes"		AS data, COUNT(*) FROM `likes`		WHERE post="'.$p['id'].'" AND type="seed") UNION ALL
	(SELECT "comments" 	AS data, COUNT(*) FROM `comments`	WHERE post="'.$p['id'].'" AND type="seed")
')->fetch();

foreach( $db_count as $key ) $p[$key['data']] = $key['COUNT(*)'];

echo '
<div class="post">
    <div class="img">'.$p['f_html'].'
        <a href="'.$p['url'].'"><img src="'.$p['thumb'].'" alt="'.$p['title'].'" width="700" height="200"></a>
        <div class="over">
            <a href="'.$p['url_auth'].'"><img src="'.$p['thumb_a'].'" alt="'.$p['auth'].'" width="32" height="32" class="avatar"></a>
            <h2><a href="'.$p['url'].'">'.$p['title'].'</a></h2>
        </div>
    </div>
    <div class="info">
        <span><i class="fa fa-thumbs-up"></i> <strong>'.$p['likes'].'</strong> likes</span>
        <span><i class="fa fa-eye"></i> <strong>'.$p['views'].'</strong> views</span>
        <span><i class="fa fa-comments"></i> <strong>'.$p['comments'].'</strong> comments</span>
        <a href="'.$p['url'].'" class="bttn mid"><i class="fa fa-eye"></i> Show Seed Code</a>
    </div>
</div>
';

} // End post foreach loop.

?>

</div>

<?php if ( $count != 0 ) $pagination->html(); ?>

<?php show_footer(); ?>
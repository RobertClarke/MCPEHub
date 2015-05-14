<?php

/**
  * Blog List
**/

require_once('core.php');

$pg = [
	'title_main'	=> 'Blog',
	'title_sub'		=> 'Minecraft PE',
	'seo_desc'		=> 'Collection of the best Minecraft PE Maps and game worlds for download including adventure, survival, and parkour maps.',
	'seo_keywords'	=> 'minecraft pe maps, survival, parkour, adventure, minecraft pe, mcpe'
];

show_header('MCPE Hub Blog', FALSE, $pg);

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
$cats = ['Minecraft PE Update', 'News', 'Update', 'Community'];
foreach( $cats as $cat ) $post_cats[$cat] = preg_replace("/[\s_]/", "-", strtolower($cat), 1);

// If user selected a category.
if ( !empty($_GET['category']) ) {
	
	if ( array_search($_GET['category'], $post_cats) ) {
		$db->like(['tags' => $_GET['category']]);
		$url->add('category', $_GET['category']);
	}
	else $_GET['category'] = NULL;
	
}

$count	= $db->select('COUNT(*) AS count')->from('content_blogs')->where(['active' => 1])->fetch()[0]['count'];
$offset	= $pagination->build($count, 10, $current_page);

// Must set this again since SQL class reset itself after fetch above.
if ( !empty($_GET['category']) && array_search($_GET['category'], $post_cats) ) $db->like(['tags' => $_GET['category']]);
elseif ( isset($search) ) $db->like(['title' => $db->escape(strip_tags($search))]);

$posts = $db->from('content_blogs')->limit($offset, 10)->order_by($db_sort)->where(['active' => 1])->fetch();

// If no posts are found, display an error.
if ( $count == 0 ) {
	if ( !empty($_GET['search']) ) $error->add('NULL', 'No posts were found under "<b>'.htmlspecialchars($_GET['search']).'</b>".', 'warning');
	else $error->add('NULL', 'Sorry, there are no posts found in this category.', 'warning');
	$error->set('NULL');
}

// If category search requested, disply a message.
elseif( !empty($_GET['category']) && $current_page == 1 ) {
	$error->add('C_RESULT', 'There were '.$count.' blog posts found under the category <b>'.array_search($_GET['category'], $post_cats).'</b>.', 'info');
	$error->set('C_RESULT');
}

?>

<div id="p-title">
    <h1>Minecraft PE News</h1>
</div>

<div class="posts-tools">
    
    <select data-placeholder="Choose a Category" class="chosen redirect">
        <option value=""></option>
<?php
if ( !empty($_GET['category']) ) echo '<option value="'.$url->show(NULL, 1).'">All Blog Posts</option>';
foreach( $post_cats as $title => $id ) {
	$cat_sel	= ( !empty($_GET['category']) && $_GET['category'] == $id) ? ' selected' : NULL;
	echo '<option value="'.$url->show('category='.$id, 1).'"'.$cat_sel.'>'.$title.'</option>';
}
?>
    </select>
<?php if ( $count != 0 ) { ?>
    <select data-placeholder="Sort By" class="chosen redirect">
        <option value="<?php echo $url->show('sort=latest'); ?>"<?php if ( empty($sort) ) echo ' selected'; ?>>Latest Blog Posts</option>
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
$p['url']		= '/blog-post/'.$p['slug'];
$p['url_auth']	= '/user/'.$p['auth'];

// Grab first image for post thumbnail.
$p['images']	= explode(',', $p['images']);
$p['thumb']		= '/uploads/700x200/blogs/'.urlencode($p['images'][0]);
$p['thumb_a']	= '/avatar/64x64/'.$user->info('avatar', $p['auth']);

$p['f_html']	= ( $p['featured'] == 1 ) ? '<div class="featured"><i class="fa fa-star fa-fw"></i> Featured</div>' : NULL;

if ( $p['featured'] == 1 && $p['tested'] == 1 ) $p['badge_class'] = 'multi';
else $p['badge_class'] = NULL;

// Determine number of likes & comments for post.
$db_count = $db->query('
	(SELECT "likes"		AS data, COUNT(*) FROM `likes`		WHERE post="'.$p['id'].'" AND type="map") UNION ALL
	(SELECT "comments" 	AS data, COUNT(*) FROM `comments`	WHERE post="'.$p['id'].'" AND type="map")
')->fetch();

foreach( $db_count as $key ) $p[$key['data']] = $key['COUNT(*)'];

echo '
<div class="post">
    <div class="img">
        <div class="badge-cont '.$p['badge_class'].'">'.$p['f_html'].'</div>
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
        <a href="'.$p['url'].'" class="bttn mid"><i class="fa fa-eye"></i> Read Post</a>
    </div>
</div>
';

} // End post foreach loop.

?>

</div>

<?php if ( $count != 0 ) $pagination->html(); ?>

<?php show_footer(); ?>
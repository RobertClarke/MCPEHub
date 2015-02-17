<?php

/**
  * Maps List
**/

require_once('core.php');

$pg = [
	'title_main'	=> 'Maps',
	'title_sub'		=> 'Minecraft PE',
	'seo_desc'		=> 'Collection of the best Minecraft PE Maps and game worlds for download including adventure, survival, and parkour maps.',
	'seo_keywords'	=> 'minecraft pe maps, survival, parkour, adventure, minecraft pe, mcpe'
];

show_header('Minecraft PE Maps', FALSE, $pg);

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
$cats = ['Survival', 'Creative', 'Adventure', 'Puzzle', 'Horror', 'PVP', 'Parkour', 'Minigame', 'Pixel Art', 'Roller Coaster'];
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

$count	= $db->select('COUNT(*) AS count')->from('content_maps')->where(['active' => 1])->fetch()[0]['count'];
$offset	= $pagination->build($count, 10, $current_page);

// Must set this again since SQL class reset itself after fetch above.
if ( !empty($_GET['category']) && array_search($_GET['category'], $post_cats) ) $db->like(['tags' => $_GET['category']]);
elseif ( isset($search) ) $db->like(['title' => $db->escape(strip_tags($search))]);

$posts = $db->from('content_maps')->limit($offset, 10)->order_by($db_sort)->where(['active' => 1])->fetch();

// If no posts are found, display an error.
if ( $count == 0 ) {
	if ( !empty($_GET['search']) ) $error->add('NULL', 'No posts were found under "<b>'.htmlspecialchars($_GET['search']).'</b>".', 'warning');
	else $error->add('NULL', 'Sorry, there are no posts found in this category.', 'warning');
	$error->set('NULL');
}

// If category search requested, disply a message.
elseif( !empty($_GET['category']) && $current_page == 1 ) {
	$error->add('C_RESULT', 'There were '.$count.' maps found under the category <b>'.array_search($_GET['category'], $post_cats).'</b>.', 'info');
	$error->set('C_RESULT');
}

// If search results are found, display a message.
elseif ( !empty($_GET['search']) && $current_page == 1 ) {
	$error->add('S_RESULT', 'Your search for "<b>'.htmlspecialchars($_GET['search']).'</b>" returned '.$count.' results.', 'info');
	$error->set('S_RESULT');
}

if ( !$error->exists() ) {
	$error->add('ANDROID_APP', '<center><i class="fa fa-android"></i> Have an Android device? Check out our new <b>FREE Maps Android App</b>! - <a href="https://play.google.com/store/apps/details?id=com.mcpehub.mapslite" target="_blank">Click Here</a></center>', 'success');
	$error->set('ANDROID_APP');
}

?>

<div id="p-title">
    <h1>Minecraft PE Maps</h1>
    <div class="tabs">
        <div class="bttn-group">
            <a href="/how-to-install-maps" class="bttn mid tip" data-tip="How To Install"><i class="fa fa-question-circle solo"></i></a>
            <a href="/maps" class="bttn mid tip search" data-tip="Search Maps"><i class="fa fa-search solo"></i></a>
        </div>
        <a href="/submit?type=map" class="bttn mid green"><i class="fa fa-upload"></i> Submit Map</a>
    </div>
</div>

<div id="search"<?php if ( !isset($_GET['category']) && !empty($_GET['search']) ) echo ' class="visible"'; ?>>
    <form action="<?php echo $url->show('', TRUE); ?>" method="GET" class="form">
        <input type="text" name="search" id="search" placeholder="Search Maps..." value="<?php $form->get_val('search'); ?>" maxlength="150">
        <button type="submit" id="submit" class="bttn mid gold"><i class="fa fa-search"></i> Search</button>
    </form>
</div>

<div class="posts-tools">
    
    <select data-placeholder="Choose a Category" class="chosen redirect">
        <option value=""></option>
<?php
if ( !empty($_GET['category']) ) echo '<option value="'.$url->show(NULL, 1).'">All Maps</option>';
foreach( $post_cats as $title => $id ) {
	$cat_sel	= ( !empty($_GET['category']) && $_GET['category'] == $id) ? ' selected' : NULL;
	echo '<option value="'.$url->show('category='.$id, 1).'"'.$cat_sel.'>'.$title.'</option>';
}
?>
    </select>
<?php if ( $count != 0 ) { ?>
    <select data-placeholder="Sort By" class="chosen redirect">
        <option value="<?php echo $url->show('sort=latest'); ?>"<?php if ( empty($sort) ) echo ' selected'; ?>>Latest Maps</option>
        <option value="<?php echo $url->show('sort=views'); ?>"<?php if ( $sort == 'views' ) echo ' selected'; ?>>Most Viewed</option>
        <option value="<?php echo $url->show('sort=downloads'); ?>"<?php if ( $sort == 'downloads' ) echo ' selected'; ?>>Most Downloaded</option>
    </select>
<?php $pagination->html(); } ?>
    
</div>

<?php $error->display(); ?>

<div id="posts">
    
<?php

// Primary post list.
foreach( $posts as $i => $p ) {

$p['auth']		= $user->info('username', $p['author']);
$p['url']		= '/map/'.$p['slug'];
$p['url_auth']	= '/user/'.$p['auth'];

// Grab first image for post thumbnail.
$p['images']	= explode(',', $p['images']);
$p['thumb']		= '/uploads/700x200/maps/'.urlencode($p['images'][0]);
$p['thumb_a']	= '/avatar/64x64/'.$user->info('avatar', $p['auth']);

$p['f_html']	= ( $p['featured'] == 1 ) ? '<div class="featured"><i class="fa fa-star fa-fw"></i> Featured Map</div>' : NULL;
$p['t_html']	= ( $p['tested'] == 1 ) ? '<div class="tested"><i class="fa fa-check fa-fw"></i> Tested Map</div>' : NULL;

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
        <div class="badge-cont '.$p['badge_class'].'">'.$p['f_html'].$p['t_html'].'</div>
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
        <a href="'.$p['url'].'" class="bttn mid gold"><i class="fa fa-download"></i> Download <span class="side">'.$p['downloads'].'</span></a>
    </div>
</div>
';

} // End post foreach loop.

?>

</div>

<?php if ( $count != 0 ) $pagination->html(); ?>

<?php show_footer(); ?>

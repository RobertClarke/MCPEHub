<?php

/**
  * Moderator Panel
**/

require_once('core.php');

// Redirect if user not admin/mod.
if ( !$user->is_admin() && !$user->is_mod() ) redirect('/');

show_header('Moderate Posts', TRUE, ['body_id' => 'dashboard', 'title_main' => 'Moderate Posts', 'title_sub' => 'Moderator Panel']);

$error->add('NONE', 'There are no posts to moderate at this time.', 'success');

$current_page = ( isset($_GET['page']) ) ? $_GET['page'] : 1;

$q_cols		= 'id,title,slug,author,images,active,views,edited,submitted,featured';
$q_where	= '`active` = 0';

$posts = $db->query('
	(SELECT "map"	 	AS type, '.$q_cols.' FROM `content_maps` 	 WHERE '.$q_where.') UNION ALL
	(SELECT "seed" 		AS type, '.$q_cols.' FROM `content_seeds` 	 WHERE '.$q_where.') UNION ALL
	(SELECT "texture" 	AS type, '.$q_cols.' FROM `content_textures` WHERE '.$q_where.') UNION ALL
	(SELECT "skin" 		AS type, '.$q_cols.' FROM `content_skins` 	 WHERE '.$q_where.') UNION ALL
	(SELECT "mod" 		AS type, '.$q_cols.' FROM `content_mods` 	 WHERE '.$q_where.') UNION ALL
	(SELECT "server" 	AS type, '.$q_cols.' FROM `content_servers`  WHERE '.$q_where.')
')->fetch();

$count = count($posts);
$offset	= $pagination->build($count, 10, $current_page);

if ( $count != 0 ) {
	
	$slice = [];
	foreach ( $posts as $key => $col ) $slice[$key] = $col['submitted'];
	array_multisort($slice, SORT_DESC, $posts);
	
	$posts = array_slice($posts, $offset, 10);
	
} else $error->set('NONE');

$icns = [
	'map'		=> 'map-marker',
	'seed'		=> 'leaf',
	'texture'	=> 'paint-brush',
	'skin'		=> 'male',
	'mod'		=> 'puzzle-piece',
	'server'	=> 'gamepad'
];

$status = [
	'-1'	=> '<span class="red"><i class="fa fa-times"></i> Rejected</span>',
	'0'		=> '<span class="yellow"><i class="fa fa-clock-o"></i> Under Review</span>',
	'1'		=> '<span class="green"><i class="fa fa-check"></i> Approved</span>'
];

?>
<div id="p-title">
    <h1>Moderate Posts</h1>
    <div class="tabs">
        <a href="/" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Website</a>
    </div>
</div>

<div class="posts-tools">
<?php if ( $count != 0 ) $pagination->html(); ?>
</div>

<?php $error->display(); ?>

<div id="posts">
    
<?php

foreach ( $posts as $post ) {

if ( isset($_GET['type']) && in_array($_GET['type'], $post_types) ) {
	if ( substr($type, -1) == 's' ) $type = substr($type, 0, -1);
	$post['type'] = $type;
}

$post['author']		= $user->info('username', $post['author']);
$post['url']		= '/'.$post['type'].'/'.$post['slug'];

$post['images']		= explode(',', $post['images']);
$post['image']		= '/uploads/700x80/'.$post['type'].'s/'.urlencode($post['images'][0]);

$post['type_html'] = '<span><i class="fa fa-'.$icns[$post['type']].'"></i> '.ucwords($post['type']).'</span>';
$post['edited_html'] = ($post['edited'] != 0) ? '<span class="yellow"><i class="fa fa-pencil"></i> Edits Pending</span>' : NULL;

echo '
<div class="post" data-post="'.$post['id'].'" data-type="'.$post['type'].'">
    <div class="img">
        <div class="status">'.$post['type_html'].$post['edited_html'].'</div>
        <img src="'.$post['image'].'" alt="'.$post['title'].'" width="700" height="80">
        <div class="over">
            <h2><a href="'.$post['url'].'">'.$post['title'].'</a></h2>
        </div>
    </div>
    <div class="info">
        <span><i class="fa fa-male"></i> '.$post['author'].'</span>
        <span><i class="fa fa-clock-o"></i> '.since($post['submitted']).'</span>
        <div class="bttn-group">
            <a href="/moderate-edit?post='.$post['id'].'&type='.$post['type'].'" class="bttn mid tip" data-tip="Edit Post"><i class="fa fa-pencil solo"></i></a>
            <a href="'.$post['url'].'" class="bttn mid tip" data-tip="View Post"><i class="fa fa-eye solo"></i></a>
            <a href="/moderate-delete?post='.$post['id'].'&type='.$post['type'].'" class="bttn mid tip" data-tip="Delete Post"><i class="fa fa-trash-o solo"></i></a>
            <a href="#reject" class="reject bttn mid red tip" data-tip="Reject Post"><i class="fa fa-times solo"></i></a>
            <a href="#approve" class="approve bttn mid green tip" data-tip="Approve Post"><i class="fa fa-check solo"></i></a>
        </div>
    </div>
</div>
';

} // End post foreach loop.

?>

</div>
<?php if ( $count != 0 ) $pagination->html(); ?>

<?php show_footer(); ?>
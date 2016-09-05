<?php

/**
  * Dashboard
  *
  * Allows logged in users to manage their accounts and
  * submitted content all from one place.
**/

require_once('core.php');
show_header('Dashboard', TRUE, ['body_id' => 'dashboard', 'title_main' => 'Dashboard', 'title_sub' => 'My Account']);

$error->add('WELCOME',			'<i class="fa fa-star-o"></i> Welcome back to MCPE Hub, <b>'.$user->info('username').'</b>!', 'info');
$error->add('DELETED',			'<i class="fa fa-trash-o"></i> Your post has been successfully deleted from the website.', 'success');

$error->add('P_MISSING',		'You haven\'t made any submissions yet. Contribute to the community by sharing your MCPE content!', 'warning');
$error->add('P_MISSING_TYPE',	'You haven\'t made any submissions in this category yet. Contribute to the community by sharing your MCPE content!', 'warning');

if ( isset($_GET['welcome']) ) $error->set('WELCOME');
elseif ( isset($_GET['deleted']) ) $error->set('DELETED');

$current_page = ( isset($_GET['page']) ) ? $_GET['page'] : 1;
$post_types = ['maps','seeds','textures','skins','mods','servers'];

// Showing specific type of posts on request.
if ( isset($_GET['type']) && in_array($_GET['type'], $post_types) ) {
	
	$type = $_GET['type'];
	$url->add('type', $type);
	
	$db_where = '`author` = \''.$user->info('id').'\' AND `active` <> \'-2\'';
	
	$count	= $db->select('COUNT(*) AS count')->from('content_'.$type)->where($db_where)->fetch()[0]['count'];
	$offset	= $pagination->build($count, 5, $current_page);
	
	$posts	= $db->from('content_'.$type)->limit($offset, 5)->order_by('published DESC')->where($db_where)->fetch();
	
	if ( $count == 0 ) $error->set('P_MISSING_TYPE');
	
} else { // Show all posts, no post type requested.
	
	$q_cols		= 'id,title,slug,author,images,active,views,edited,submitted,featured';
	$q_where	= '`author`=\''.$user->info('id').'\' AND `active` <> \'-2\'';
	
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
		
	} else $error->set('P_MISSING');
	
} // End: Show all posts, no post type requested.

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

// Post kind dropdown HTML generation.
$dd_cats = ['maps', 'seeds', 'textures', 'skins', 'mods', 'servers'];
$dd_cats_html = '<option value=""></option>';

if ( isset($_GET['type']) && in_array($_GET['type'], $dd_cats) )
	$dd_cats_html .= '<option value="'.$url->show('', TRUE).'">All Posts</option>';

foreach( $dd_cats as $cat ) {
	$selected = ( isset($_GET['type']) && $_GET['type'] == $cat ) ? ' selected' : NULL;
	$dd_cats_html .= '<option value="'.$url->show('type='.$cat, TRUE).'"'.$selected.'>'.ucwords($cat).'</option>';
}

// Status dropdown HTML generation.
/*$dd_stat = ['approved', 'pending', 'rejected'];
$dd_stat_html = '<option value=""></option>';

if ( isset($_GET['status']) && in_array($_GET['status'], $dd_stat) )
	$dd_stat_html .= '<option value="'.$url->show('status=').'">All Posts</option>';

foreach( $dd_stat as $stat ) {
	$selected = ( isset($_GET['status']) && $_GET['status'] == $stat ) ? ' selected' : NULL;
	$dd_stat_html .= '<option value="'.$url->show('status='.$stat).'"'.$selected.'>'.ucwords($stat).'</option>';
}*/



?>

<div id="p-title">
    <h1>My Submissions</h1>
    <div class="tabs">
        <a href="/account" class="bttn mid tip" data-tip="Account Settings"><i class="fa fa-wrench solo"></i></a>
        <a href="/submit" class="bttn mid green"><i class="fa fa-upload"></i> Submit Content</a>
    </div>
</div>

<div class="posts-tools">
    
    <select data-placeholder="Post Type" class="chosen redirect"><?php echo $dd_cats_html; ?></select>
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

$post['f_html']		= ( $post['featured'] == 1 ) ? '<div class="featured"><i class="fa fa-star fa-fw"></i> Featured</div>' : NULL;
$post['type_html'] = '<span><i class="fa fa-'.$icns[$post['type']].'"></i> '.ucwords($post['type']).'</span>';

// Compute number of likes and comments on post.
$q_where = '`post` = \''.$post['id'].'\' AND `type` = \''.$post['type'].'\'';
$count_vars = $db->query('
	(SELECT "likes"		AS type, COUNT(*) FROM `likes`		WHERE '.$q_where.') UNION ALL
	(SELECT "comments"	AS type, COUNT(*) FROM `comments`	WHERE '.$q_where.')
')->fetch();

foreach( $count_vars as $var ) $post[$var['type']] = $var['COUNT(*)'];

echo '
<div class="post" data-post="'.$post['id'].'" data-slug="'.$post['slug'].'" data-type="'.$post['type'].'">
    <div class="img">
        <div class="status">'.$post['type_html'].$status[$post['active']].'</div>'.$post['f_html'].'
        <img src="'.$post['image'].'" alt="'.$post['title'].'" width="700" height="80">
        <div class="over">
            <h2><a href="'.$post['url'].'">'.$post['title'].'</a></h2>
        </div>
    </div>
    <div class="info">
        <span><i class="fa fa-thumbs-up"></i> <strong>'.$post['likes'].'</strong> likes</span>
        <span><i class="fa fa-eye"></i> <strong>'.$post['views'].'</strong> views</span>
        <span><i class="fa fa-comments"></i> <strong>'.$post['comments'].'</strong> comments</span>
		'.($post['type'] == 'server' ? '<div class="bttn-group" style="margin-left:20px;"><a href="#link" class="bttn mid tip actn_votereward" data-tip="VoteReward Config" data-toggle="modal" data-target="#actn_votereward"><i class="fa fa-gears solo"></i></a></div>' : '').'
        <div class="bttn-group">
            <a href="'.$post['url'].'" class="bttn mid tip" data-tip="View Post"><i class="fa fa-eye solo"></i></a>
            <a href="#link" class="bttn mid tip actn_link" data-tip="Get Post Link" data-toggle="modal" data-target="#actn_link"><i class="fa fa-link solo"></i></a>
            <a href="#delete" class="bttn mid tip actn_del" data-tip="Delete Post" data-toggle="modal" data-target="#actn_del"><i class="fa fa-trash-o solo"></i></a>
            <a href="/edit?post='.$post['id'].'&type='.$post['type'].'" class="bttn mid tip" data-tip="Edit Post"><i class="fa fa-pencil solo"></i></a>
        </div>
    </div>
</div>
';

} // End post foreach loop.

?>

</div>
<?php if ( $count != 0 ) { ?>
	<?php $pagination->html(); ?>
	<div id="actn_link" class="modal fade modal-md msg">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4><i class="fa fa-link"></i> Post Link</h4>
					<button class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
				</div>
				<div class="modal-body">
					<form><input type="text" id="link_copy" value="http://mcpehub.com" readonly="true"></form>
					<a href="/dashboard" class="bttn mid full" data-dismiss="modal">Close Window</a>
				</div>
			</div>
		</div>
	</div>
	<div id="actn_del" class="modal fade modal-sm msg">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4><i class="fa fa-trash-o"></i> Delete Post</h4>
					<button class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
				</div>
				<div class="modal-body">
					<span class="title"><i class="fa fa-trash-o"></i><p>Are you sure you want to delete this post?</p></span>
					<div class="bttn-group">
						<a href="/delete?post=&type=" class="bttn mid red del">Yes, Delete Post</a>
						<a href="/dashboard" class="bttn mid" data-dismiss="modal">Cancel</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if($post['type'] == 'server'){ ?>
	<div id="actn_votereward" class="modal fade modal-md msg">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4><i class="fa fa-gears"></i> VoteReward Config File</h4>
					<button class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
				</div>
				<div class="modal-body">
					<div style="float:left;width:70%;"><p>Place this file in your VoteReward 'lists' folder, this will allow users to claim rewards by voting for your server on MCPE Hub</p></div>
					<div style="float:right;"><a href="<?php echo $post['url'].'/mcpehub.com.vrc';?>" class="bttn mid green" style="margin-bottom:30px;">Download .VRC file</a> </div>
					<a href="/dashboard" class="bttn mid full" data-dismiss="modal">Close Window</a>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

<?php } ?>

<?php show_footer(); ?>
<?php

/**
  * Deleting Posts
**/

require_once('core.php');

$post_types	= ['map', 'seed', 'texture', 'skin', 'mod', 'server'];

// If missing URL variables, redirect right away.
if ( empty($_GET['post']) || empty($_GET['type']) ) redirect('/dashboard');
elseif ( !is_numeric($_GET['post']) || !in_array($_GET['type'], $post_types) ) redirect('/dashboard');

show_header('Delete Post', TRUE, ['title_main' => 'Delete Post', 'title_sub' => 'My Account']);

$p_id	= $_GET['post'];
$p_type	= $_GET['type'];

$error->add('INVALID',		'<i class="fa fa-trash-o"></i> The '.$p_type.' you\'re attempting to delete doesn\'t exist.', 'error');
$error->add('NOT_OWNER',	'<i class="fa fa-lock"></i> You don\'t have permission to delete this '.$p_type.'.', 'error');

$post = $db->select('id, author, active')->from('content_'.$p_type.'s')->where(['id' => $p_id])->fetch();

// Check if post exists in database.
if (!$db->affected_rows) $error->set('INVALID');
else {
	
	$post = $post[0];
	
	// Check if user is owner of post.
	if ( $post['author'] != $user->info('id') ) $error->set('NOT_OWNER');
	else {
		
		// Check if post is already deleted.
		if ( $post['active'] == '-2' ) $error->set('INVALID');
		else {
			
			$editor = $user->info('id');
			
			// Mark post as deleted in database.
			$db->where(['id' => $p_id])->update('content_'.$p_type.'s', ['active' => '-2', 'editor_id' => $editor, 'edited' => time_now(), 'featured' => 0]);
			
			redirect('/dashboard?deleted');
			
		} // End: Check if post is already deleted.
		
	} // End: Check if user is owner of post.
	
} // End: Check if post exists in database.

?>
<div id="p-title">
    <h1>Delete Post</h1>
    <div class="tabs">
        <a href="/dashboard" class="bttn mid"><i class="fa fa-tachometer"></i> Back to Dashboard</a>
    </div>
</div>
<?php $error->display(); ?>
<?php show_footer(); ?>
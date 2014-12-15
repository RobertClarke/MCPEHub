<?php

/**
  * Moderator Post Deletion
**/

require_once('core.php');

// Redirect if user not admin/mod.
if ( !$user->is_admin() && !$user->is_mod() ) redirect('/');

$post_types	= ['map', 'seed', 'texture', 'skin', 'mod', 'server'];

// If missing URL variables, redirect right away.
if ( empty($_GET['post']) || empty($_GET['type']) ) redirect('/moderate');
elseif ( !is_numeric($_GET['post']) || !in_array($_GET['type'], $post_types) ) redirect('/moderate');

show_header('Toggle Post', TRUE, ['title_main' => 'Toggle Post', 'title_sub' => 'Moderator Panel']);

$p_id	= $_GET['post'];
$p_type	= $_GET['type'];

$error->add('INVALID', '<i class="fa fa-times"></i> The '.$p_type.' you\'re attempting to toggle doesn\'t exist.');

$post = $db->select('id, author, active, slug')->from('content_'.$p_type.'s')->where(['id' => $p_id])->fetch();

// Check if post exists in database.
if (!$db->affected_rows) $error->set('INVALID');
else {
	
	$post = $post[0];
	
	// Activate
	if ( $post['active'] == '-1' ) {
		
		$db->where(['id' => $post['id']])->update('content_'.$p_type.'s', ['active' => '1']);
		redirect('/'.$p_type.'/'.$post['slug']);
		
	}
	// Deactivate
	elseif ( $post['active'] == 1 ) {
		
		$db->where(['id' => $post['id']])->update('content_'.$p_type.'s', ['active' => '-1']);
		redirect('/'.$p_type.'/'.$post['slug']);
		
	}
	else $error->set('INVALID');
	
} // End: Check if post exists in database.

?>
<div id="p-title">
    <h1>Toggle Post</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>
<?php $error->display(); ?>
<?php show_footer(); ?>
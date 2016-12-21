<?php

/**
  * Edit Profile
**/

require_once('core.php');

show_header('Edit Profile', TRUE, ['title_main' => 'Edit Profile', 'title_sub' => 'Member']);

if ( !empty($_POST) ) {
	
	$error->add('SUCCESS',		'Your profile has been saved.', 'success');
	
	$f['name']		= isset( $_POST['name'] )		? $db->escape($_POST['name']) : NULL;
	$f['device']	= isset( $_POST['device'] )		? $db->escape($_POST['device']) : NULL;
	$f['twitter']	= isset( $_POST['twitter'] )	? $db->escape($_POST['twitter']) : NULL;
	$f['youtube']	= isset( $_POST['youtube'] )	? $db->escape($_POST['youtube']) : NULL;
	$f['bio']		= isset( $_POST['bio'] )		? $db->escape($_POST['bio']) : NULL;
	
	if ( isset($f['name']) ) $change['name'] = $f['name'];
	if ( isset($f['device']) ) $change['devices'] = $f['device'];
	if ( isset($f['twitter']) ) $change['twitter'] = $f['twitter'];
	if ( isset($f['youtube']) ) $change['youtube'] = $f['youtube'];
	if ( isset($f['bio']) ) $change['bio'] = $f['bio'];
	
	if ( isset($change) ) {
		$db->where(['id' => $user->info('id')])->update('users', $change); 
	}
	
	$error->set('SUCCESS');
	
} // End: Form submitted.

?>
<div id="p-title">
    <h1>Edit Profile</h1>
    <div class="tabs"><a href="/profile" class="bttn mid"><i class="fa fa fa-long-arrow-left"></i> Back to Profile</a></div>
</div>

<?php $error->display(); ?>

<form action="/profile_edit" method="POST">
    <div class="half">
        <div class="group">
            <div class="label"><label for="name">Display Name</label></div>
            <input type="text" name="name" id="name" class="full" value="<?php echo $user->info('name'); ?>">
        </div>
    </div>
    <div class="half last">
        <div class="group">
            <div class="label"><label for="device">Device</label></div>
            <select name="device" id="device" class="chosen full" data-placeholder="Click to select device"><option></option><option value="iOS"<?php if ($user->info('devices') == 'iOS') echo ' selected'; ?>>iOS</option><option value="Android"<?php if ($user->info('devices') == 'Android') echo ' selected'; ?>>Android</option></select>
        </div>
    </div>
    <div class="half">
        <div class="group">
            <div class="label"><label for="twitter"><i class="fa fa-twitter fa-fw"></i> Twitter Username</label></div>
            <input type="text" name="twitter" id="twitter" class="full" value="<?php echo $user->info('twitter'); ?>">
        </div>
    </div>
    <div class="half last">
        <div class="group">
            <div class="label"><label for="youtube"><i class="fa fa-youtube-play fa-fw"></i> YouTube Username</label></div>
            <input type="text" name="youtube" id="youtube" class="full" value="<?php echo $user->info('youtube'); ?>">
        </div>
    </div>
    
    <script src="/assets/js/tinymce/tinymce.min.js"></script>
    <script>
tinymce.init({
	selector: "textarea.visual",
	height: "120px",
	width: "100%",
	theme: "modern",
	skin: "light",
	plugins: ["link smileys paste"],
	toolbar: "bold underline italic | smileys | undo redo",
	statusbar: false,
	menubar: false,
	paste_as_text: true,
	object_resizing : false
});
    </script>
    <textarea name="bio" id="bio" class="visual"><?php echo $user->info('bio'); ?></textarea>
    <br><br>
    
    <div class="submit">
        <button type="submit" class="bttn big green"><i class="fa fa-check"></i> Save Profile</button>
    </div>
</form>

<?php show_footer(); ?>
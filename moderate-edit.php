<?php

/**
  * Moderator Post Editing
**/

require_once('core.php');

// Redirect if user not admin/mod.
if ( !$user->is_admin() && !$user->is_mod() ) redirect('/');

$post_types	= ['map', 'seed', 'texture', 'skin', 'mod', 'server', 'blog'];

// If missing URL variables, redirect right away.
if ( empty($_GET['post']) || empty($_GET['type']) ) redirect('/moderate');
elseif ( !is_numeric($_GET['post']) || !in_array($_GET['type'], $post_types) ) redirect('/moderate');

show_header('Edit Post', TRUE, ['title_main' => 'Edit Post', 'title_sub' => 'Moderator Panel']);

$post_type = $_GET['type'];

$p_id	= $_GET['post'];
$p_type	= $_GET['type'];

$error->add('INVALID', '<i class="fa fa-times"></i> The '.$p_type.' you\'re attempting to edit doesn\'t exist.');

$post = $db->select()->from('content_'.$p_type.'s')->where('`id` = \''.$p_id.'\' AND `active` <> \'-2\'')->fetch();

// Check if post exists in database.
if (!$db->affected_rows) $error->set('INVALID');
else {

	$valid = TRUE;
	$post = $post[0];

	$post['type']		= $p_type;
	$post['author_id']	= $post['author'];
	$post['author']		= $user->info('username', $post['author_id']);

	$error->add('NOTIF', 'You are editing this post on behalf of <b>'.$post['author'].'</b>.', 'info');
	$error->set('NOTIF');

	// Default POST values.
	$f['title']			= isset($_POST['title']) ? $_POST['title'] : NULL;
	$f['description']	= isset($_POST['description']) ? $_POST['description'] : NULL;

	// Switch for different inputs depending on post type.
	switch ($post['type']) {
		case 'map':
			$post_inputs = ['dl_link', 'tag_map', 'versions'];
		break;
		case 'seed':
			$post_inputs = ['seed', 'tag_seed', 'versions'];
		break;
		case 'texture':
			$post_inputs = ['dl_link', 'tag_texture', 'versions', 'devices', 'resolution'];
		break;
		case 'skin':
			$post_inputs = ['dl_link', 'tag_skin'];
		break;
		case 'mod':
			$post_inputs = ['dl_link', 'versions', 'devices'];
		break;
		case 'server':
			$post_inputs = ['ip', 'port', 'version'];
		break;
		case 'blog':
			$post_inputs = ['tag_blog'];
		break;
	} // End: Switch for different inputs depending on post type.

	// Setting default POST values for all extra inputs.
	$extra_inputs = [
		'ip' 			=> FALSE,
		'port' 			=> FALSE,
		'tag_map' 		=> FALSE,
		'tag_seed' 		=> FALSE,
		'tag_texture' 	=> FALSE,
		'tag_skin' 		=> FALSE,
		'devices'		=> FALSE,
		'resolution'	=> FALSE,
		'versions'		=> FALSE,
		'version'		=> FALSE,
		'dl_link'		=> FALSE,
		'seed'			=> FALSE,
		'tag_blog'		=> FALSE
	];

	foreach( $post_inputs as $input ) {
		$extra_inputs[$input] = TRUE;
		$f[$input] = isset($_POST[$input]) ? $_POST[$input] : NULL;
	}

	$extras = [
		'dl_link' => [
			'type'			=> 'text',
			'name'			=> 'dl_link',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-download fa-fw"></i> Download Link',
			'placeholder'	=> 'http://',
			'autocomplete'	=> TRUE,
			'spellcheck'	=> TRUE,
			'maxlength'		=> 100,
			'helper'		=> 'We recommend hosting files for free on <i class="fa fa-dropbox"></i> <a href="http://dropbox.com" target="_blank">Dropbox</a>.',

			'friendly_name' => 'Download Link',
			'required'		=> TRUE
		],
		'tag_map' => [
			'type'			=> 'select',
			'multi'			=> TRUE,
			'name'			=> 'tags',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-tags fa-fw"></i> Tags',
			'placeholder'	=> 'Click to add tags',
			'options'		=> [
				'survival' 		=> 'Survival',
				'creative' 		=> 'Creative',
				'adventure' 	=> 'Adventure',
				'puzzle' 		=> 'Puzzle',
				'horror' 		=> 'Horror',
				'pvp' 			=> 'PVP',
				'parkour' 		=> 'Parkour',
				'minigame' 		=> 'Minigame',
				'pixel-art' 	=> 'Pixel Art',
				'roller-coaster'=> 'Roller Coaster'
			],

			'friendly_name' => 'Tags',
			'required'		=> TRUE
		],
		'tag_seed' => [
			'type'			=> 'select',
			'multi'			=> TRUE,
			'name'			=> 'tags',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-tags fa-fw"></i> Tags',
			'placeholder'	=> 'Click to add tags',
			'options'		=> [
				'caverns' 		=> 'Caverns',
				'diamonds' 		=> 'Diamonds',
				'flat' 			=> 'Flat',
				'lava' 			=> 'Lava',
				'mountains' 	=> 'Mountains',
				'overhangs' 	=> 'Overhangs',
				'waterfall' 	=> 'Waterfall',
			],

			'friendly_name' => 'Tags',
			'required'		=> TRUE
		],
		'tag_texture' => [
			'type'			=> 'select',
			'name'			=> 'tags',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-tags fa-fw"></i> Texture Type',
			'placeholder'	=> 'Click to select type',
			'options'		=> [
				'standard'		=> 'Standard',
				'realistic'		=> 'Realistic',
				'simplistic'	=> 'Simplistic',
				'themed'		=> 'Themed',
				'experimental'	=> 'Experimental',
			],

			'friendly_name' => 'Type',
			'required'		=> TRUE
		],
		'tag_skin' => [
			'type'			=> 'select',
			'name'			=> 'tags',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-tags fa-fw"></i> Skin Type',
			'placeholder'	=> 'Click to select type',
			'options'		=> [
				'boy'		=> 'Boy',
				'girl'		=> 'Girl',
				'mob'		=> 'Mob',
				'animal'	=> 'Animal',
				'tv'		=> 'TV',
				'movies'	=> 'Movies',
				'games'		=> 'Games',
				'fantasy'	=> 'Fantasy',
				'other'		=> 'Other',
			],

			'friendly_name' => 'Type',
			'required'		=> TRUE
		],

		'tag_blog' => [
			'type'			=> 'select',
			'multi'			=> TRUE,
			'name'			=> 'tags',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-tags fa-fw"></i> Tags',
			'placeholder'	=> 'Click to add tags',
			'options'		=> [
				'minecraft-pe-update' 	=>	'Minecraft PE Update',
				'news'					=>	'News',
				'update'				=>	'Update',
				'community'				=>	'Community'
			],

			'friendly_name' => 'Tags',
			'required'		=> TRUE
		],

		'ip' => [
			'type'			=> 'text',
			'name'			=> 'ip',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-globe fa-fw"></i> Server IP',
			'autocomplete'	=> TRUE,
			'spellcheck'	=> TRUE,
			'maxlength'		=> 60,
			'helper'		=> 'IP cannot start with <i>192.168</i>, <i>127.0.0</i> or <i>10.0.0</i> - these will be rejected.',

			'friendly_name' => 'Server IP',
			'required'		=> TRUE
		],

		'port' => [
			'type'			=> 'text',
			'name'			=> 'port',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-crosshairs fa-fw"></i> Server Port',
			'autocomplete'	=> TRUE,
			'spellcheck'	=> TRUE,
			'maxlength'		=> 20,

			'friendly_name' => 'Server Port',
			'required'		=> TRUE
		],
		'versions' => [
			'type'			=> 'select',
			'multi'			=> TRUE,
			'name'			=> 'versions',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-slack fa-fw"></i> Compatible Versions',
			'placeholder'	=> 'Click to select versions',
			'options'		=> ['0.17.0', '0.16.0', '0.15.0', '0.14.0', '0.13.0', '0.12.0', '0.11.0', '0.10.0', '0.9.0', '0.8.0'],

			'friendly_name' => 'Versions',
			'required'		=> TRUE
		],
		'version' => [
			'type'			=> 'select',
			'name'			=> 'version',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-slack fa-fw"></i> Compatible Version',
			'placeholder'	=> 'Click to select version',
			'options'		=> ['0.17.0', '0.16.0', '0.15.0', '0.14.0', '0.13.0', '0.12.0', '0.11.0', '0.10.0', '0.9.0', '0.8.0'],

			'friendly_name' => 'Version',
			'required'		=> TRUE
		],
		'devices' => [
			'type'			=> 'select',
			'multi'			=> TRUE,
			'name'			=> 'devices',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-mobile fa-fw"></i> Compatible Devices',
			'placeholder'	=> 'Click to select devices',
			'options'		=> ['Android', 'iOS'],

			'friendly_name' => 'Devices',
			'required'		=> TRUE
		],
		'resolution' => [
			'type'			=> 'select',
			'name'			=> 'resolution',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-expand fa-fw"></i> Texture Resolution',
			'placeholder'	=> 'Click to select resolutions',
			'options'		=> ['16x16', '32x32', '64x64', '128x128', '256x256'],

			'friendly_name' => 'Resolution',
			'required'		=> TRUE
		],
		'seed' => [
			'type'			=> 'text',
			'name'			=> 'seed',
			'class_cont'	=> 'half',
			'label'			=> '<i class="fa fa-leaf fa-fw"></i> Seed',
			'placeholder'	=> 'Enter the seed here',
			'autocomplete'	=> TRUE,
			'spellcheck'	=> TRUE,
			'maxlength'		=> 100,
			'helper'		=> 'Please enter <u>only</u> the seed here.',

			'friendly_name' => 'Seed',
			'required'		=> TRUE
		],
		'title'	=> [
			'type'			=> 'text',
			'name'			=> 'title',
			'friendly_name' => 'Title',
			'required'		=> TRUE
		],
		'description' => [
			'type'			=> 'text',
			'name'			=> 'description',
			'friendly_name' => 'Description',
			'required'		=> TRUE,
			'html_allowed'	=> TRUE
		]
	];

	//$f['tags'] = isset($_POST['tags']) ? $_POST['tags'] : NULL;
	$tag_types = ['tag_map', 'tag_seed', 'tag_texture', 'tag_skin', 'tag_blog'];

	// Going through tag types and only leaving the type that is required.
	foreach( $tag_types as $type ) {
		if ( !in_array($type, $post_inputs) ) unset($extras[$type]);
		else {

			$extras['tags'] = $extras[$type];
			unset($extras[$type]);

			$f['tags'] = isset($_POST['tags']) ? $_POST['tags'] : NULL;
			unset($f[$type]);

			// Swap all specific tag types to general 'tags' label.
			$post_inputs[array_search($type, $post_inputs)] = 'tags';
		}
	}

	// Setting values of inputs (default & submitted).
	foreach ( $extras as $key => $input ) {

		if ( array_key_exists($key, $post) && array_key_exists($key, $f) ) {

			if ( $input['type'] == 'text' ) {
				if ( empty($_POST) ) $extras[$key]['value'] = $post[$key];
				else $extras[$key]['value'] = $f[$key];
			}
			elseif ( $input['type'] == 'select' ) {

				if ( empty($_POST) ) $extras[$key]['selected'] = explode(',', $post[$key]);
				else $extras[$key]['selected'] = $f[$key];
			}

		} else unset($extras[$key]);

	}

	if ( empty($_POST) ) {
		$f['title'] = $post['title'];
		$f['description'] = $post['description'];
	}

	require_once('core/htmlpurifier/HTMLPurifier.standalone.php');
	$purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());

	$f['description'] = $purifier->purify($f['description']);

	// If any changes are submitted.
	if ( !empty($_POST) ) {

		$error->reset();

		$validate = $form->validate_inputs($extras, $f, $_POST);

		// Check if any inputs missing.
		if ( !empty($validate['missing']) ) {
			$error->add('MISSING', 'The following inputs must be filled out: '.$validate['missing'].'.');
			$error->set('MISSING');
		}

		// If no errors in form, continue.
		if ( !$error->exists() ) {

			$extras = $validate['inputs'];

			$description = $extras['description']['clean_val'];

			$purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());
			$description = $purifier->purify($description);

			$description = str_replace('assets/img/smilies/', '/assets/img/smilies/', $description);

			// Escape all submitted values for database.
			$db_edit = [];
			foreach( $extras as $input => $value ) $db_edit[$input] = $db->escape($value['clean_val']);

			$db_edit['description']	= $db->escape($description);
			$db_edit['editor_id']	= $user->info('id');
			$db_edit['edited']		= time_now();
			//$db_edit['active']		= 0;

			$db->where(['id' => $p_id])->update('content_'.$p_type.'s', $db_edit);

			redirect('/moderate?edited');

		} // End: If no errors in form, continue.

	} // End: If any changes are submitted.

} // End: Check if post exists in database.

$icons = ['map' => 'map-marker', 'seed' => 'leaf', 'texture' => 'paint-brush', 'skin' => 'male', 'mod' => 'puzzle-piece', 'server' => 'gamepad', 'blog' => 'pencil'];

?>
<div id="p-title">
    <h1>Edit Post</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>

<?php $error->display(); ?>

<?php if ( isset($valid) ) { // If post valid. ?>
<form action="/moderate-edit?post=<?php echo $post['id']; ?>&type=<?php echo $post['type']; ?>" method="POST">
    <div class="main-inputs">
        <div class="input">
            <div class="badge-type <?php echo $post_type; ?>"><i class="fa fa-<?php echo $icons[$p_type]; ?>"></i> <?php echo ucwords($p_type); ?></div>
            <input type="text" name="title" class="input-title" placeholder="<?php echo ucwords($p_type); ?> Title" maxlength="100" autocomplete="off" value="<?php echo htmlspecialchars($f['title']); ?>">
            <textarea name="description" id="description" class="visual"><?php echo htmlspecialchars($f['description']); ?></textarea>
        </div>
            <script src="/assets/js/tinymce/tinymce.min.js"></script>
            <script>
tinymce.init({
	selector: "textarea.visual",
	width: "680px",
	height: "150px",
	theme: "modern",
	skin: "light",
	plugins: ["link smileys paste"],
	toolbar: "bold underline italic strikethrough | smileys | alignleft aligncenter alignright | bullist numlist | link unlink | undo redo",
	statusbar: false,
	menubar: false,
	paste_as_text: true,
	object_resizing : false
});
            </script>
    </div>
    <?php $form->show_inputs($post_inputs, $extras); ?>
    <div class="submit">
        <button type="submit" class="bttn big green"><i class="fa fa-check"></i> Save Changes</button>
    </div>
</form>
<?php } // End: If post valid. ?>

<?php show_footer(); ?>

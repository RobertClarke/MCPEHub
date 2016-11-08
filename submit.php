<?php

require_once( 'core.php' );

// Default GET variables.
$post_type = isset( $_GET['type'] ) ? $_GET['type'] : '';

$allowed_types = array( 'map', 'seed', 'texture', 'mod', 'server', 'skin' );

// Set default post type to map, if missing or invalid.
if ( empty( $post_type ) || !in_array( $post_type, $allowed_types ) ) $post_type = 'map';

// Show header with "Submit [Post Type]" as title.
show_header( 'Submit ' . ucwords($post_type), TRUE );

$error->add( 'NOT_ACTIVE', 'You cannot post content until your account has been activated. Check your e-mail for activation instructions.', 'error', 'lock' );

// Set array for rules.
$form_input = $post_rules = $post_inputs = $form_rules = array();

// Default POST variables.
$form_input['title'] = isset( $_POST['title'] ) ? $_POST['title'] : '';
$form_input['description'] = isset( $_POST['description'] ) ? $_POST['description'] : '';

// Switch for different inputs/rules depending on post type.
switch ( $post_type ) {

	case 'map': // Map

		$post_rules = array( 'author', 'screenshots', 'pc_ports' );
		$post_inputs = array( 'dl_link', 'tag_map', 'versions' );

	break;
	case 'seed': // Seed

		$post_rules = array( 'screenshots' );
		$post_inputs = array( 'seed', 'tag_seed', 'versions' );

	break;
	case 'texture': // Texture

		$post_rules = array( 'author', 'screenshots', 'pc_ports' );
		$post_inputs = array( 'dl_link', 'tag_texture', 'versions', 'devices', 'resolution' );

	break;
	case 'skin': // Skin

		$post_rules = array( 'author', 'screenshots' );
		$post_inputs = array( 'tag_skin' );

	break;
	case 'mod': // Mod

		$post_rules = array( 'author', 'screenshots' );
		$post_inputs = array( 'dl_link', 'versions', 'devices' );

	break;
	case 'server': // Server

		$post_rules = array( 'server_owner', 'server_temp', 'server_screenshots' );
		$post_inputs = array( 'ip', 'port', 'version' );

	break;

	// No default because default value forced to "map".

} // END: Switch for different inputs/rules depending on post type.

// Set array for additional inputs depending on post type.
$form_inputs = array(
	'ip' 			=> FALSE,
	'port' 			=> FALSE,
	'tag_map' 		=> FALSE,
	'tag_seed' 		=> FALSE,
	'tag_texture' 	=> FALSE,
	'tag_skin'		=> FALSE,
	'devices'		=> FALSE,
	'resolution'	=> FALSE,
	'versions'		=> FALSE,
	'version'		=> FALSE,
	'dl_link'		=> FALSE,
	'seed'			=> FALSE
);

foreach( $post_inputs as $input ) {

	$form_inputs[$input] = TRUE;

	if ( $input == 'tag_map' || $input == 'tag_seed' || $input == 'tag_texture' || $input == 'tag_skin' ) $input = 'tags';
	$form_input[$input] = isset( $_POST[$input] ) ? $_POST[$input] : '';

}

// Set empty arrays if values are missing.
if ( !isset( $form_input['title'] ) ) $form_input['title'] = '';
if ( !isset( $form_input['description'] ) ) $form_input['description'] = '';
if ( !isset( $form_input['images'] ) ) $form_input['images'] = '';

if ( empty( $form_input['tags'] ) ) $form_input['tags'] = array();
if ( empty( $form_input['versions'] ) ) $form_input['versions'] = array();
if ( empty( $form_input['version'] ) ) $form_input['version'] = array();
if ( empty( $form_input['devices'] ) ) $form_input['devices'] = array();

if ( !isset( $form_input['dl_link'] ) ) $form_input['dl_link'] = '';
if ( !isset( $form_input['ip'] ) ) $form_input['ip'] = '';
if ( !isset( $form_input['port'] ) ) $form_input['port'] = '';
if ( !isset( $form_input['resolution'] ) ) $form_input['resolution'] = '';
if ( !isset( $form_input['seed'] ) ) $form_input['seed'] = '';

$rules = array(
	'author'		=> 'You must be the author of the '.$post_type.', unless you have permission from the original author.',
	'screenshots'	=> 'You must submit proper screenshots of the '.$post_type.'.',
	'pc_ports'		=> 'PC Ports must have proper credit given to the original author.',
	'server_owner'	=> 'You must be the owner of the server unless you have permission from the owner.',
	'server_temp'	=> 'We only accept 24/7 servers. This means we do not accept "instant" servers.',
	'server_screenshots' => 'You must have proper screenshots (or custom pictures) relating to the server.',
);

foreach( $post_rules as $rule ) $form_rules[] = $rules[$rule];

// Force a rule that says not to re-post posts.
$form_rules[] = 'Do not re-post '.$post_type.'s that have already been posted by other members.';


// Declare all form inputs. Some important values also used in submission code.
$the_inputs = array(

	'dl_link' => array(
		'type'			=> 'text',
		'name'			=> 'dl_link',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-download fa-fw"></i> Download Link',
		'value'			=> $form_input['dl_link'],
		'placeholder'	=> 'http://',
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 100,
		//'helper'		=> 'We recommend hosting files for free on <i class="fa fa-dropbox"></i> <a href="http://dropbox.com" target="_blank">Dropbox</a>.',
		'helper'		=> 'Make sure to include "http://" or "https://" at the start.',
		'friendly_name' => 'Download Link',
		'required'		=> TRUE
	),

	'tag_map' => array(
		'type'			=> 'select',
		'multi'			=> TRUE,
		'name'			=> 'tags',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-tags fa-fw"></i> Tags',
		'placeholder'	=> 'Click to add tags',
		'selected'		=> $form_input['tags'],
		'options'		=> array(
			'survival' 		=> 'Survival',
			'creative' 		=> 'Creative',
			'adventure' 	=> 'Adventure',
			'puzzle' 		=> 'Puzzle',
			'pvp' 			=> 'PVP',
			'parkour' 		=> 'Parkour',
			'minigame' 		=> 'Minigame',
			'pixel-art' 	=> 'Pixel Art',
			'roller-coaster'=> 'Roller Coaster'
		),
		'db_id'			=> 'tags',

		'friendly_name' => 'Tags',
		'required'		=> TRUE
	),

	'tag_seed' => array(
		'type'			=> 'select',
		'multi'			=> TRUE,
		'name'			=> 'tags',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-tags fa-fw"></i> Tags',
		'placeholder'	=> 'Click to add tags',
		'selected'		=> $form_input['tags'],
		'options'		=> array(
			'caverns' 		=> 'Caverns',
			'diamonds' 		=> 'Diamonds',
			'flat' 			=> 'Flat',
			'lava' 			=> 'Lava',
			'mountains' 	=> 'Mountains',
			'overhangs' 	=> 'Overhangs',
			'waterfall' 	=> 'Waterfall',
		),
		'db_id'			=> 'tags',

		'friendly_name' => 'Tags',
		'required'		=> TRUE
	),

	'tag_texture' => array(
		'type'			=> 'select',
		'name'			=> 'tags',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-tags fa-fw"></i> Texture Type',
		'placeholder'	=> 'Click to select type',
		'selected'		=> $form_input['tags'],
		'options'		=> array(
			'standard'		=> 'Standard',
			'realistic'		=> 'Realistic',
			'simplistic'	=> 'Simplistic',
			'themed'		=> 'Themed',
			'experimental'	=> 'Experimental',
		),
		'db_id'			=> 'tags',

		'friendly_name' => 'Type',
		'required'		=> TRUE
	),

	'tag_skin' => array(
		'type'			=> 'select',
		'name'			=> 'tags',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-tags fa-fw"></i> Skin Type',
		'placeholder'	=> 'Click to select type',
		'selected'		=> $form_input['tags'],
		'options'		=> array(
			'boy'		=> 'Boy',
			'girl'		=> 'Girl',
			'mob'		=> 'Mob',
			'animal'	=> 'Animal',
			'tv'		=> 'TV',
			'movies'	=> 'Movies',
			'games'		=> 'Games',
			'fantasy'	=> 'Fantasy',
			'other'		=> 'Other',
		),
		'db_id'			=> 'tags',

		'friendly_name' => 'Type',
		'required'		=> TRUE
	),

	'ip' => array(
		'type'			=> 'text',
		'name'			=> 'ip',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-globe fa-fw"></i> Server IP',
		'value'			=> $form_input['ip'],
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 60,
		'helper'		=> 'IP cannot start with <i>192.168</i>, <i>127.0.0</i> or <i>10.0.0</i> - these will be rejected.',

		'friendly_name' => 'Server IP',
		'required'		=> TRUE
	),

	'port' => array(
		'type'			=> 'text',
		'name'			=> 'port',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-crosshairs fa-fw"></i> Server Port',
		'value'			=> $form_input['port'],
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 20,

		'friendly_name' => 'Server Port',
		'required'		=> TRUE
	),

	'versions' => array(
		'type'			=> 'select',
		'multi'			=> TRUE,
		'name'			=> 'versions',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-slack fa-fw"></i> Compatible Versions',
		'placeholder'	=> 'Click to select versions',
		'selected'		=> $form_input['versions'],
		'options'		=> ['0.16.0', '0.15.0', '0.14.0', '0.13.0', '0.12.0', '0.11.0', '0.10.0', '0.9.0', '0.8.0'],

		'friendly_name' => 'Versions',
		'required'		=> TRUE
	),

	'version' => array(
		'type'			=> 'select',
		'name'			=> 'version',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-slack fa-fw"></i> Compatible Version',
		'placeholder'	=> 'Click to select version',
		'selected'		=> $form_input['version'],
		'options'		=> ['0.16.0', '0.15.0', '0.14.0', '0.13.0', '0.12.0', '0.11.0', '0.10.0', '0.9.0', '0.8.0'],

		'friendly_name' => 'Version',
		'required'		=> TRUE
	),

	'devices' => array(
		'type'			=> 'select',
		'multi'			=> TRUE,
		'name'			=> 'devices',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-mobile fa-fw"></i> Compatible Devices',
		'placeholder'	=> 'Click to select devices',
		'selected'		=> $form_input['devices'],
		'options'		=> array( 'Android', 'iOS' ),

		'friendly_name' => 'Devices',
		'required'		=> TRUE
	),

	'resolution' => array(
		'type'			=> 'select',
		'name'			=> 'resolution',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-expand fa-fw"></i> Texture Resolution',
		'placeholder'	=> 'Click to select resolutions',
		'selected'		=> $form_input['resolution'],
		'options'		=> array( '16x16', '32x32', '64x64', '128x128', '256x256' )
	),

	'seed' => array(
		'type'			=> 'text',
		'name'			=> 'seed',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-leaf fa-fw"></i> Seed',
		'value'			=> $form_input['seed'],
		'placeholder'	=> 'Enter the seed here',
		'autocomplete'	=> TRUE,
		'spellcheck'	=> TRUE,
		'maxlength'		=> 100,
		'helper'		=> 'Please enter <u>only</u> the seed here.',

		'friendly_name' => 'Seed',
		'required'		=> TRUE
	),

	'title' => array( 'friendly_name' => 'Title' ),
	'description' => array( 'friendly_name' => 'Description' ),
	'tags' => array( 'friendly_name' => 'Tags' ),

);

// If user isn't activated, show a message asking them to activate.
if ( $user->info('activated') != 1 ) $error->force('NOT_ACTIVE');

// If submit form is submitted.
if ( !empty( $_POST ) && $user->info('activated') == 1 ) {

	// Clean up description HTML using HTMLPurifier.
	require( 'core/htmlpurifier/HTMLPurifier.standalone.php' );
	$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );

	$form_input['description'] = $purifier->purify( $form_input['description'] );

	// If user is activated, let them post.
	if ( $user->info('activated') == 1 ) {

		$error->reset();

		$inputs = array(
			'title'			=> strip_tags( $form_input['title'] ),
			'description'	=> $form_input['description'],
			'images'		=> $form_input['images']
		);

		$required = array( 'title', 'description' );

		// Loop through every additional input and process.
		foreach( $post_inputs as $post_input ) {

			// Set current input info var.
			$input = $the_inputs[$post_input];

			// Grab value of input from form, in var for clean access.
			$input_val = $form_input[ $input['name'] ];

			// Use db_id, if exists.
			if ( !isset( $input['db_id'] ) ) $input['db_id'] = $post_input;

			// Push input to $inputs array to insert into processing.
			$inputs[ $input['db_id'] ] = '';


			// If input is a select, we must validate every option.
			if ( $input['type'] == 'select' && isset( $input['options'] ) && is_array( $input['options'] ) ) {

				$input['clean_val'] = '';
				$allowed_options = array();

				$assocArr = array_keys($input['options']) !== range(0, count($input['options']) - 1) ? TRUE : FALSE;

				// Grab all possible options for input.
				if ( $assocArr ) foreach( $input['options'] as $option => $value ) $allowed_options[] = $option;
				else $allowed_options = $input['options'];

				// Check if input values match possiblities. Separate values using commas (value,value,value).
				foreach( $input_val as $option ) if ( in_array( $option, $allowed_options ) ) $input['clean_val'] .= $option.',';

				// Strip last comma from list, push to inputs.
				$input['clean_val'] = trim( $input['clean_val'], ',' );

				// If empty value (no validated), set default if it isn't multi-select.
				if ( !isset( $input['multi'] ) && empty( $input['clean_val'] ) ) $input['clean_val'] = $allowed_options[0];

				$inputs[ $input['db_id'] ] = $input['clean_val'];

			}

			// If input is text, clean the text and push to inputs array.
			else if ( $input['type'] == 'text' ) {

				// Handle max length + strip tags.
				if ( isset( $input['maxlength'] ) ) $input['clean_val'] = substr( $input_val, 0, $input['maxlength'] );
				else $input['clean_val'] = $input_val;

				$inputs[ $input['db_id'] ] = strip_tags( $input['clean_val'] );

			}

			if ( isset( $input['required'] ) ) $required[] = $input['db_id'];

		}


		// Check if any required inputs are missing.
		$inputs_missing = '';
		foreach ( $inputs as $input => $value ) {
			if ( in_array( $input, $required ) && empty( $value ) ) $inputs_missing .= $the_inputs[$input]['friendly_name'].', ';
		}
		$inputs_missing = trim( $inputs_missing, ', ' );

		// Show an error for missing inputs.
		if ( !empty( $inputs_missing ) ) {
			$error->add( 'INPUT_MISSING', 'The following inputs must be filled out: '.$inputs_missing.'.', 'error' );
			$error->append( 'INPUT_MISSING' );
		}

		if ( strlen($inputs['title']) < 10 ) {
			$error->add('TITLE_LENGTH', 'The post title must at least 10 characters long.', 'error');
			$error->append('TITLE_LENGTH');
		}

		$error->add( 'IMG_MISSING', 'You must upload at least one image for the post.', 'error' );
		$error->add( 'IMG_MAX', 'One or more images uploaded exceeded the maximum upload file size.', 'error' );
		$error->add( 'IMG_INVALID', 'One or more files uploaded are not valid image files.', 'error' );

		// Check if at least 1 image is uploaded.
		$uploaded_images = gather_files( $_FILES['images'] );
		if ( empty( $_FILES['images'] ) || count($uploaded_images) == 1 && $uploaded_images[0]['error'] == 4 ) $error->append( 'IMG_MISSING' );
		else {

			$i = 0;
			$images_confirmed = FALSE;
			$uploaded_images = array_slice( $uploaded_images, 0, 5 );
			foreach( $uploaded_images as $image ) {

				// No image uploaded in input, unset and ignore.
				if ( $image['error'] == 4 ) {
					unset( $uploaded_images[$i] );
				}
				else if ( $image['error'] == 1 ) {
					$error->append( 'IMG_MAX' );
					break;
				}
				else if ( @!getimagesize( $_FILES['images']['tmp_name'][$i] ) ) {
					$error->append( 'IMG_INVALID' );
					break;
				}

				$i++;

			}

			// Todo: Check for PHP max size again (new PHP standard?)

		}

		// Check if download URL is from Dropbox/Mediafire/Google
		if ( in_array('dl_link', $post_inputs) ) {

			$error->add('DL_INVALID_URL', 'The download link you provided isn\'t valid. Make sure it starts with "http://" or "https://".', 'error');
			$error->add('DL_INVALID_DOMAIN', 'We currently only allow <a href="http://mediafire.com/" target="_blank">Mediafire</a>, <a href="http://dropbox.com/" target="_blank">Dropbox</a> and <a href="http://drive.google.com/" target="_blank">Google Drive</a> download links. Please upload your '.$post_type.' to one of those services, then re-submit your '.$post_type.'.', 'error');

			if ( (strpos($value, 'http://') !== 0 || strpos($value, 'https://') !== 0) && filter_var($inputs['dl_link'], FILTER_VALIDATE_URL) !== false ) {

				$allowed_domains = ['mediafire.com', 'dropbox.com', 'drive.google.com'];
				foreach ( $allowed_domains as $d ) $allowed_domains[] = 'www.'.$d;

				$parse = parse_url($inputs['dl_link']);

				if ( !in_array($parse['host'], $allowed_domains) ) {
					$error->append('DL_INVALID_DOMAIN');
				}

				else if ( !isset($parse['path']) || $parse['path'] == '/' ) {
					$error->append('DL_INVALID_URL');
				}

			} else {
				$error->append('DL_INVALID_URL');
			}

		}

		// If we have no errors in the form, lets continue.
		if ( empty( $error->selected ) ) {

			// Escapes every user input we're sending to the database.
			foreach( $inputs as $input => $value ) $inputs[$input] = $db->escape( $value );

			// Process uploaded images.
			$images = '';
			$upload_dir = ABS . 'uploads/posts/'.$post_type.'s/';

			foreach( $uploaded_images as $i => $image ) {

				$f_ext = '.' . strtolower( end( explode( '.', $image['name'] ) ) );
				$f_name = uniqid() . strtolower(random_str(3));

				@move_uploaded_file( $_FILES['images']['tmp_name'][$i], $upload_dir . $f_name . $f_ext );

				$images .= $f_name . $f_ext.',';

				if ( $post_type == 'skin' ) $dl_link = '/uploads/posts/skins/'.$f_name . $f_ext;

			}

			// Generate slug.
			$slug = generate_slug( $inputs['title'], "'" );

			// Check if slug exists, append random string if it does.
			$check = $db->from('content_'.$post_type.'s')->where(array('slug'=>$slug))->fetch();
			if ( $db->affected_rows != 0 ) $slug .= '-'.random_str(3);

			// Set final database values.
			$inputs['images'] 		= trim( $images, ',' );

			if ( $post_type == 'skin' ) $inputs['dl_link'] = $dl_link;

			$inputs['author'] 		= $user->info( 'id' );
			$inputs['submitted'] 	= date( 'Y-m-d H:i:s' );
			$inputs['slug']			= $slug;
			$inputs['active']		= 0;

			// Insert into database.
			$db->insert( 'content_'.$post_type.'s', $inputs );

			// Redirect to post.
			redirect( '/'.$post_type.'/'.$inputs['slug'].'?created' );

		}

	}

}

// Made this into a function just so we can use it in a clean way below.
function show_extra_inputs() {

	global $form, $post_inputs, $the_inputs;

	// Push out required inputs for use in form.
	$loop_count = 0;
	foreach( $post_inputs as $input ) {

		// Open container div, as needed.
		if ( ( $loop_count + 1 ) % 2 == 1 )
			echo "<div class=\"inputs clearfix\">\n\n";

		// For every 2nd input, add the class "last" for proper spacing.
		$force = ( ( $loop_count + 1 ) % 2 != 1 ) ? array('class_cont' => 'half last') : null;

		$form->build_input( $the_inputs[$input], $force );

		// Close container div, as needed.
		if ( ( $loop_count + 1 ) % 2 != 1 || ( $loop_count + 1 ) == count( $post_inputs ) )
			echo "</div>\n";

		$loop_count++;

	}

}

// Set "type" URLs in array for easy switching.
$type_urls = '';
$type_url = array(
	'map' 		=> array( 'Map', 'map-marker' ),
	'seed' 		=> array( 'Seed', 'leaf' ),
	'texture' 	=> array( 'Texture', 'magic' ),
	'skin' 		=> array( 'Skin', 'smile-o' ),
	'mod'		=> array( 'Mod', 'codepen' ),
	'server' 	=> array( 'Server', 'gamepad' ),
);

foreach( $type_url as $id => $p ) {
	$active = ( $id == $post_type ) ? 'gold ' : '';
	$type_urls .= '<a href="'.$url->show('type='.$id, TRUE).'" class="'.$active.'bttn"><i class="fa fa-'.$p[1].'"></i> '.$p[0].'</a>';
}

// Set up icons for post types.
$icons = array( 'map' => 'map-marker', 'seed' => 'leaf', 'texture' => 'magic', 'mod' => 'codepen', 'server' => 'gamepad', 'skin' => 'smile-o' );

?>

<div id="p-title">
    <h1>Submit <?php echo ucwords($post_type); ?></h1>
    <div class="tabs"><?php echo $type_urls; ?></div>
</div>

<?php $error->display(); ?>

<form action="<?php echo $url->show('type='.$post_type, TRUE); ?>" method="POST" class="form submission" enctype="multipart/form-data">

<?php if ( $user->info('activated') != 1 ) echo '<div class="form-overlay"></div>'; ?>

<?php if ( !empty( $form_rules ) ) { ?>
    <div class="input-rules">
        <p>When submitting <?php echo $post_type; ?>s, please remember the following:</p>
        <ol><?php foreach( $form_rules as $rule ) echo "<li>$rule</li>"; ?></ol>
    </div>
<?php } ?>

    <div class="main-inputs">
        <div class="input">
            <input type="text" name="title" id="title" class="text title with-badge" value="<?php echo htmlspecialchars($form_input['title']); ?>" placeholder="<?php echo ucwords($post_type); ?> Title" maxlength="100" autocomplete="off" />
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
	toolbar: "bold underline italic strikethrough | smileys | bullist numlist | undo redo",
	statusbar: false,
	menubar: false,
	paste_as_text: true,
	object_resizing : false
});
    </script>

        <textarea name="description" id="description" class="visual"><?php echo $form_input['description']; ?></textarea>
    </div>

    <div class="main-inputs uploads clearfix">
        <div id="uploadInputs" class="clearfix">
            <input type="file" name="images[]" id="image" class="file-upload" />
        </div>
        <?php if ( $post_type != 'skin' ) { ?><div class="addUpload"><a href="#" id="addUpload" class="bttn mini"><i class="fa fa-plus"></i> Add More</a></div><?php } ?>
    </div>

    <?php show_extra_inputs(); // Show extra inputs using function created above. ?>
    <br>

    <div class="submit">
        <button type="submit" class="bttn big green"><i class="fa fa-upload"></i> Submit <?php echo ucwords($post_type); ?></button>
    </div>

</form>

<?php show_footer(); ?>

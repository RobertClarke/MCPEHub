<?php

require_once( 'core.php' );

// Default GET variables.
$post_id = isset( $_GET['post'] ) ? $_GET['post'] : '';
$post_type = isset( $_GET['type'] ) ? $_GET['type'] : '';

$allowed_types = array( 'map', 'seed', 'texture', 'skin', 'mod', 'server' );

// Redirect to dashboard if invalid inputs (+ validates data integrity)
if ( empty( $post_id ) || empty( $post_type ) || !in_array( $post_type, $allowed_types ) || !is_numeric( $post_id ) )
	redirect( './dashboard' );

// Show header with "Submit [Post Type]" as title.
show_header( 'Edit ' . ucwords($post_type), TRUE );

$error->add( 'INVALID_POST', 'The post you\'re attempting to edit doesn\'t exist.', 'error', 'times' );
$error->add( 'NOT_OWNED', 'You don\'t have permission to edit this post.', 'error', 'lock' );

// Check if post exists in database.
$post = $db->from( 'content_'.$post_type.'s' )->where( array( 'id' => $post_id ) )->fetch();

if ( !$db->affected_rows ) $error->set( 'INVALID_POST' );
else {
	
	$post = $post[0];
	
	// Check if user owns the post or user is admin/mod.
	if ( $post['author'] == $user->info('id') || $user->is_admin() || $user->is_mod() ) {
		
		// Show editing tools on page.
		$edit_area = TRUE;
		
		// Switch author ID into username.
		$post['author_id'] = $post['author'];
		$post['author'] = $user->info( 'username', $post['author_id'] );
		
		// Add notifications for admins/mods.
		$error->add( 'NOTIF_ADMIN', 'Editing this '.$post_type.' on behalf of <strong>'.$post['author'].'</strong>. Any changes made will <u>not</u> affect the published status of the post.', 'info', 'info-circle' );
		$error->add( 'NOTIF_USER', 'If you edit your '.$post_type.', it will go offline until a moderator approves any changes made.', 'info', 'info-circle' );
		
		// Show notifications depending on user.
		if ( $user->is_admin() || $user->is_mod() ) $error->force( 'NOTIF_ADMIN' );
		else $error->force( 'NOTIF_USER' );
		
		// Set empty arrays for required form elements.
		$form_input = $post_inputs = array();
		
		// Default POST variables.
		$form_input['title'] = isset( $_POST['title'] ) ? $_POST['title'] : '';
		$form_input['description'] = isset( $_POST['description'] ) ? $_POST['description'] : '';
		
		// Switch for different inputs/rules depending on post type.
		switch ( $post_type ) {
			
			case 'map': // Map
				
				$post_inputs = array( 'dl_link', 'tag_map', 'versions' );
				
			break;
			case 'seed': // Seed
				
				$post_inputs = array( 'seed', 'tag_seed', 'versions' );
				
			break;
			case 'texture': // Texture
				
				$post_inputs = array( 'dl_link', 'tag_texture', 'versions', 'devices', 'resolution' );
				
			break;
			case 'skin': // Skin
				
				$post_inputs = array( 'dl_link', 'tag_skin' );
				
			break;
			case 'mod': // Mod
				
				$post_inputs = array( 'dl_link', 'versions', 'devices' );
				
			break;
			case 'server': // Server
				
				$post_inputs = array( 'ip', 'port', 'version' );
				
			break;
			
		} // END: Switch for different inputs/rules depending on post type.
		
		// Set array for additional inputs depending on post type.
		$form_inputs = array(
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
			'seed'			=> FALSE
		);
		
		foreach( $post_inputs as $input ) {
			
			$form_inputs[$input] = TRUE;
			
			if ( $input == 'tag_map' || $input == 'tag_seed' || $input == 'tag_texture' || $input == 'tag_skin' ) $input = 'tags';
			$form_input[$input] = isset( $_POST[$input] ) ? $_POST[$input] : '';
			
		}
		
		// Set input values.
		if ( empty( $form_input['title'] ) ) $form_input['title'] = $post['title'];
		if ( empty( $form_input['description'] ) ) $form_input['description'] = $post['description'];
		
		$form_input['images'] = explode( ',', $post['images'] );
		
		foreach( array('tag_map','tag_seed','tag_texture','tag_skin','versions','version','devices') as $input ) {
			
			if ( $input == 'tag_map' || $input == 'tag_seed' || $input == 'tag_texture' || $input == 'tag_skin' ) $input = 'tags';
			
			if ( array_key_exists( $input, $post ) ) {
				
				if ( empty( $_POST ) ) $form_input[ $input ] = explode( ',', $post[ $input ] );
				//else $form_input[ $input ] = $_POST[ $input ];
				
			} else $form_input[ $input ] = array();
			
		}
		
		foreach( array('dl_link','ip','port','resolution','seed') as $input ) {
			
			if ( array_key_exists( $input, $post ) ) {
				
				if ( empty( $_POST ) ) $form_input[ $input ] = $post[ $input ];
				//else $form_input[ $input ] = $_POST[ $input ];
				
			} else $form_input[ $input ] = '';
			
		}
		
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
				'helper'		=> 'We recommend hosting files for free on <i class="fa fa-dropbox"></i> <a href="http://dropbox.com" target="_blank">Dropbox</a>.',
				
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
				'options'		=> array( '0.9.0', '0.8.0' ),
				
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
				'options'		=> array( '0.9.0', '0.8.0' ),
				
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
		
		// If edit form is submitted.
		if ( !empty( $_POST ) ) {
			
			$error->reset();
			
			// Clean up description HTML using HTMLPurifier.
			require( 'core/htmlpurifier/HTMLPurifier.standalone.php' );
			$purifier = new HTMLPurifier( HTMLPurifier_Config::createDefault() );
			
			$form_input['description'] = $purifier->purify( $form_input['description'] );
			
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
				$error->add( 'INPUT_MISSING', 'The following inputs must be filled out: '.$inputs_missing.'.', 'error', 'times' );
				$error->append( 'INPUT_MISSING' );
			}
			
			$error->add( 'IMG_MAX', 'One or more images uploaded exceeded the maximum upload file size.', 'error', 'times' );
			$error->add( 'IMG_INVALID', 'One or more files uploaded are not valid image files.', 'error', 'times' );
			
			// Check if at least 1 image is uploaded.
			/*$uploaded_images = gather_files( $_FILES['images'] );
			if ( empty( $_FILES['images'] ) || count($uploaded_images) == 1 && $uploaded_images[0]['error'] == 4 ) $images_uploaded = FALSE;
			else {
				
				$images_uploaded = TRUE;
				
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
				
			}*/
			
			// If we have no errors in the form, lets continue.
			if ( empty( $error->selected ) ) {
				
				// Escapes every user input we're sending to the database.
				foreach( $inputs as $input => $value ) $inputs[$input] = $db->escape( $value );
				
				/*// Process uploaded images, if there are any.
				if ( $images_uploaded ) {
					$images = '';
					$upload_dir = ABSPATH . 'uploads/posts/'.$post_type.'s/';
					
					foreach( $uploaded_images as $i => $image ) {
						
						$f_ext = '.' . strtolower( end( explode( '.', $image['name'] ) ) );
						$f_name = uniqid() . strtolower(random_str(3));
						
						@move_uploaded_file( $_FILES['images']['tmp_name'][$i], $upload_dir . $f_name . $f_ext );
						
						$images .= $f_name . $f_ext.',';
						
					}
					
					$inputs['images'] = trim( $images, ',' );
					
				}*/
				
				// Generate slug.
				$slug = generate_slug( $inputs['title'], "''" );
				
				// Check if slug exists, append random string if it does.
				$check = $db->from('content_'.$post_type.'s')->where(array('slug'=>$slug))->fetch();
				if ( $db->affected_rows != 0 ) $slug .= '-'.random_str(3);
				
				// Set final database values.
				$inputs['editor_id'] 	= $user->info( 'id' );
				$inputs['edited'] 		= date( 'Y-m-d H:i:s' );
				
				// If admin/mod, don't change status of the post.
				if ( !$user->is_admin() && !$user->is_mod() )
					$inputs['active']	= 0;
				
				unset( $inputs['images'] );
				
				
				// Update in database.
				$db->where( array( 'id' => $post_id ) )->update( 'content_'.$post_type.'s', $inputs );
				redirect( './'.$post_type.'/'.$post['slug'].'?edited' );
				
				
			} // END: No errors; submit, veryify & clean data.
			
		} // END: Form submitted.
		
	} else $error->set( 'NOT_OWNED' ); // User doesn't own the post.
	
} // END: Post exists in database.

// Made this into a function just so we can use it in a clean way below.
function show_extra_inputs() {
	
	global $post_inputs, $the_inputs;
	
	require( ABSPATH . 'core/classes/form.php' );
	$form = new Form;
	
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

// Set up icons for post types.
$icons = array( 'map' => 'map-marker', 'seed' => 'leaf', 'texture' => 'magic', 'skin' => 'smile-o', 'mod' => 'codepen', 'server' => 'gamepad' );

?>

<div id="page-title">
    <h2>Edit <?php echo ucwords($post_type); ?></h2>
    <ul class="tabs">
        <a href="/dashboard" class="bttn"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
    </ul>
</div>

<?php $error->display(); ?>

<?php if ( isset( $edit_area ) ) { ?>

<form action="/edit?post=<?php echo $post_id; ?>&type=<?php echo $post_type; ?>" method="POST" class="form submission" enctype="multipart/form-data">
    
    <div class="main-inputs">
        <div class="input">
            <div class="badge-type title <?php echo $post_type; ?>"><i class="fa fa-<?php echo $icons[ $post_type ]; ?>"></i> <?php echo ucwords($post_type); ?></div>
            <input type="text" name="title" id="title" class="text title with-badge" value="<?php echo htmlspecialchars($form_input['title']); ?>" placeholder="<?php echo ucwords($post_type); ?> Title" maxlength="100" autocomplete="off" />
        </div>
        <textarea name="description" id="description" class="visual"><?php echo $form_input['description']; ?></textarea>
    </div>
    
    <?php show_extra_inputs(); // Show extra inputs using function created above. ?>
    
    <div class="buttons-cont">
        <button type="submit" id="submit" class="save"><i class="fa fa-pencil"></i> Save Changes</button>
    </div>
    
</form>

<?php } ?>

<?php show_footer(); ?>
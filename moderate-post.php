<?php

require_once( 'core.php' );

if ( !$user->is_admin() && !$user->is_mod() ) redirect('/');

// Show header with "Submit [Post Type]" as title.
show_header( 'New Blog Post', TRUE );

$post_type = 'blog';

// Set array for rules.
$form_input = $post_rules = $post_inputs = $form_rules = array();

// Default POST variables.
$form_input['title'] = isset( $_POST['title'] ) ? $_POST['title'] : '';
$form_input['description'] = isset( $_POST['description'] ) ? $_POST['description'] : '';

$post_inputs = array( 'tag_blog' );

// Set array for additional inputs depending on post type.
$form_inputs = array(
	'tag_blog' 		=> FALSE,
);

foreach( $post_inputs as $input ) {
	
	$form_inputs[$input] = TRUE;
	
	if ( $input == 'tag_blog' ) $input = 'tags';
	$form_input[$input] = isset( $_POST[$input] ) ? $_POST[$input] : '';
	
}

// Set empty arrays if values are missing.
if ( !isset( $form_input['title'] ) ) $form_input['title'] = '';
if ( !isset( $form_input['description'] ) ) $form_input['description'] = '';
if ( !isset( $form_input['images'] ) ) $form_input['images'] = '';

if ( empty( $form_input['tags'] ) ) $form_input['tags'] = array();

// Declare all form inputs. Some important values also used in submission code.
$the_inputs = array(
	
	'tag_blog' => array(
		'type'			=> 'select',
		'multi'			=> TRUE,
		'name'			=> 'tags',
		'class_cont'	=> 'half',
		'label'			=> '<i class="fa fa-tags fa-fw"></i> Tags',
		'placeholder'	=> 'Click to add tags',
		'selected'		=> $form_input['tags'],
		'options'		=> array(
			'minecraft-pe-update' 	=>	'Minecraft PE Update',
			'news'					=>	'News',
			'update'				=>	'Update',
			'community'				=>	'Community'
		),
		'db_id'			=> 'tags',
		
		'friendly_name' => 'Tags',
		'required'		=> TRUE
	),
	
	'title' => array( 'friendly_name' => 'Title' ),
	'description' => array( 'friendly_name' => 'Description' ),
	'tags' => array( 'friendly_name' => 'Tags' ),
	
);

// If submit form is submitted.
if ( !empty( $_POST ) ) {
	
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
				
			}
			
			// Generate slug.
			$slug = generate_slug( $inputs['title'], "'" );
			
			// Check if slug exists, append random string if it does.
			$check = $db->from('content_'.$post_type.'s')->where(array('slug'=>$slug))->fetch();
			if ( $db->affected_rows != 0 ) $slug .= '-'.random_str(3);
			
			// Set final database values.
			$inputs['images'] 		= trim( $images, ',' );
			
			$inputs['author'] 		= $user->info( 'id' );
			$inputs['published'] 	= date( 'Y-m-d H:i:s' );
			$inputs['slug']			= $slug;
			$inputs['active']		= 1;
			
			// Insert into database.
			$db->insert( 'content_'.$post_type.'s', $inputs );
			
			// Redirect to post.
			//( '/blog-post/'.$inputs['slug'] );
			
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

?>

<div id="p-title">
    <h1>Submit Blog Post</h1>
</div>

<?php $error->display(); ?>

<form action="<?php echo $url->show('type='.$post_type, TRUE); ?>" method="POST" class="form submission" enctype="multipart/form-data">
    
<?php if ( !empty( $form_rules ) ) { ?>
    <div class="input-rules">
        <p>When submitting <?php echo $post_type; ?>s, please remember the following:</p>
        <ol><?php foreach( $form_rules as $rule ) echo "<li>$rule</li>"; ?></ol>
    </div>
<?php } ?>
    
    <div class="main-inputs">
        <div class="input">
            <input type="text" name="title" id="title" class="text title with-badge" value="<?php echo htmlspecialchars($form_input['title']); ?>" placeholder="Blog Post Title" maxlength="100" autocomplete="off" />
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
        <button type="submit" class="bttn big green"><i class="fa fa-upload"></i> Submit Blog Post</button>
    </div>
    
</form>

<?php show_footer(); ?>
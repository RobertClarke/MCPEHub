<?php

/**
 * Submission Form
 *
 * Where users are directed to post a new piece of content for
 * the website.
**/

require_once('loader.php');

// Determine if the type of post being submitted is valid (if any)
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

if ( empty($type) || !in_array($type, ['map','seed','texture','skin','mod','server']) )
	$type = null;

$title = 'Submit ' . (($type !== null) ? ucwords($type) : 'Content');

$page->auth = true;
$page->body_id = 'submit';
$page->title_h1 = $title;

if ( $type !== null ) {
	$page->enqueue(['tinymce/tinymce.min', 'validate', 'dropzone', 'submission']);
	$page->title_bttn = '<a href="/submit">Change Post Type</a>';
}

$page->header($title);

// If type in $_GET is valid and user is activated
if ( $type !== null && activated() ) {

	// Switch for setting post type-specific settings
	switch( $type ) {
		case 'map':
			$inputs = ['link', 'category', 'version'];
			$rules = [
				'We do not accept stolen content. You must be the creator of the map or have permission from the original creator to post it to our website.',
				'You must provide screenshots of either the map itself in-game, and/or a custom banner that represents the content in some way.',
				'If the map you are uploading has been ported from Minecraft PC edition, than you must provide credit to the original creator of the content.',
				'If a map has already been posted by another user, do not submit the same one again.'
			];
		break;
		case 'seed':
			$inputs = ['seed', 'category', 'version'];
			$rules = [
				'You must provide screenshots of either the seed itself in-game, and/or a custom banner that represents the seed in some way.',
				'If a seed has already been posted by another user, do not submit the same one again.'
			];
		break;
		case 'texture':
			$inputs = ['link', 'category', 'version', 'resolution'];
			$rules = [
				'We do not accept stolen content. You must be the creator of the texture pack or have permission from the original creator to post it to our website.',
				'You must provide screenshots of either the texture pack itself in-game, and/or a custom banner that represents the content in some way.',
				'If the texture pack you are uploading has been ported from Minecraft PC edition, than you must give proper credit to the original creator of the content.',
				'If a texture pack has already been posted by another user, do not submit the same one again.'
			];
		break;
		case 'skin':
			$inputs = ['category'];
			$rules = [
				'We do not accept stolen content. You must be the creator of the skin or have permission from the original creator to post it to our website.',
				'You must provide an image of the skin in PNG format.',
				'If a skin has already been posted by another user, do not submit the same one again.'
			];
		break;
		case 'mod':
			$inputs = ['link', 'category', 'version', 'device'];
			$rules = [
				'We do not accept stolen content. You must be the creator of the mod or have permission from the original creator to post it to our website.',
				'You must provide screenshots of either the mod itself in-game, and/or a custom banner that represents the content in some way.',
				'If a mod has already been posted by another user, do not submit the same one again.'
			];
		break;
		case 'server':
			$inputs = ['ip', 'port', 'category', 'version'];
			$rules = [
				'You must be the owner of the server. If you are not, you must have permission from the owner to share the server it to our website.',
				'We only accept servers that are online 24/7. This means no posting servers that are created with InstantMCPE, or any other related service.',
				'You must provide screenshots of either the server itself in-game, and/or a custom banner that represents the content in some way.',
				'If a server has already been posted by another user, do not submit the same one again.'
			];
		break;
	}

	$categories = [];
	$categories_db = get_categories($type);

	// Convert categories to correct format for field verification
	foreach ( $categories_db as $category => $data ) {
		$categories[ $category ] = $data['name'];
	}

	// Add default inputs to input array
	$inputs = array_merge(['title', 'description', 'uploaded'], $inputs);

	// Array containing all form inputs (custom only)
	$form = [
		'link' => [
			'type'			=> 'url',
			'label'			=> '<i class="icon-download"></i> Download Link',
			'placeholder'	=> 'http://',
			'autocomplete'	=> true,
			'spellcheck'	=> true,
			'maxlength'		=> 100
		],
		'version' => [
			'type'			=> 'select',
			'label'			=> '<i class="icon-version"></i> Designed For',
			'placeholder'	=> 'Click to select version',
			'options'		=> ['0.12.0' => 'Minecraft PE 0.12.0 (Latest)', '0.11.0' => 'Minecraft PE 0.11.0', '0.10.0' => 'Minecraft PE 0.10.0', '0.9.0' => 'Minecraft PE 0.9.0']
		],
		'device' => [
			'type'			=> 'select',
			'label'			=> '<i class="icon-device"></i> Compatible Platform',
			'placeholder'	=> 'Click to select devices',
			'options'		=> ['iOS', 'Android']
		],
		'seed' => [
			'label'			=> 'Seed Code',
			'placeholder'	=> 'Enter the seed code here',
			'autocomplete'	=> true,
			'spellcheck'	=> true,
			'maxlength'		=> 100
		],
		'resolution' => [
			'type'			=> 'select',
			'label'			=> 'Texture Resolution',
			'placeholder'	=> 'Click to select resolution',
			'options'		=> ['16' => '16x16', '32' => '32x32', '64' => '64x64', '128' => '128x128']
		],
		'ip' => [
			'label'			=> 'Server IP Address',
			'placeholder'	=> 'Enter IP without port here',
			'autocomplete'	=> true,
			'spellcheck'	=> true,
			'maxlength'		=> 60
		],
		'port' => [
			'label'			=> 'Server Port',
			'autocomplete'	=> true,
			'spellcheck'	=> true,
			'maxlength'		=> 20
		],
		'category' => [
			'type'			=> 'select',
			'label'			=> '<i class="icon-tag"></i> ' . ucwords($type) . ' Category',
			'placeholder'	=> 'Click to select category',
			'options'		=> $categories
		]
	];

	// Form submitted
	if ( submit_POST() ) {

		$errors->add('MISSING',		'You must fill in all inputs in the submission form.');

		$errors->add('T_LENGTH',	'The '.$type.' title must be under 70 characters long.');
		$errors->add('U_INVALID',	'The download link you submitted isn\'t a valid URL.');
		$errors->add('IMG_MISSING',	'There were no images submitted with the form.');

		// Array for holding submitted values
		$submit = [];

		// Adding $_POST values to the $inputs array, if any exist
		foreach ( $inputs as $input )
			$submit[$input] = input_POST($input);

		// Purify the description
		require_once('core/htmlpurifier/HTMLPurifier.php');
		$HTMLPurifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());

		$submit['description'] = $HTMLPurifier->purify($submit['description']);

		// Clean any inputs from invalid values using Form cleaner function
		$submit = Form::clean_inputs($form, $submit);

		// Check if any inputs missing (since they're all required)
		$missing = Form::check_missing($submit);

		// Inputs are missing
		if ( $missing !== false )
			$errors->append('MISSING');

		// Title length too short
		if ( !empty($submit['title']) && strlen($submit['title']) > 70 )
			$errors->append('T_LENGTH');

		// Download URL invalid (if applicable)
		if ( in_array('link', $inputs) && !empty($submit['link']) && !is_url($submit['link']) )
			$errors->append('U_INVALID');

		// JSON decode images submitted to check if the JSON is valid below
		$submit['uploaded'] = json_decode($submit['uploaded']);

		// No images submitted
		if ( in_array('uploaded', $inputs) && empty($submit['uploaded']) && json_last_error() !== JSON_ERROR_NONE )
			$errors->append('IMG_MISSING');

		if ( !$errors->exist() ) {

			$images = [];

			// Rename and move any temporary images into permanent storage
			foreach ( $submit['uploaded'] as $img ) {

				$current = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/uploads/temp/' . $img;

				// Make sure the file exists before moving
				if ( file_exists($current) ) {

					// Generate a unique filename for the image
					$filename = uniqid().strtolower(random_string(3)) . '.'.strtolower(end(explode('.', $img)));

					// Move file to permanent directory (will return false if failed to move)
					$move = rename( $current, filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/uploads/posts/'.$type.'/' . $filename );

					$images[] = $filename; // Add filename to images array

				}

			} // END: Moving files from temporary directory to permanent

			$slug = generate_slug( $submit['title'], "'" );

			// Append 3 random characters to end of slug if it's a dupliate slug
			if ( $db->from('content_'.$type)->where(['slug' => $slug])->fetch_first() )
				$slug .= '-' . random_string(3);

			// Unset images array since we store that in a separate table
			unset($submit['uploaded']);

			// Special db rules for specific post types
			switch ( $type ) {
				case 'mod':

					// Add device table value to $submit
					if ( $submit['device'] == 'iOS' )
						$submit['platform_ios'] = 1;
					else
						$submit['platform_android'] = 1;

					unset($submit['device']);

				break;
				case 'skin':
					// *** Special dl link ***
				break;
			}

			// Add extra values before pushing ito database
			$submit['slug']			= $slug;
			$submit['status']		= 0;
			$submit['submitted']	= $now;
			$submit['author_id']	= $u->id;

			// Replace category with category ID from DB
			$submit['category'] = category_type_code($submit['category'], $type);

			$id = $db->insert('content_'.$type, $submit);

			// Build query for inserting images into their table
			$images_query = [];
			$post_type = post_type_code($type);

			foreach ( $images as $image ) {
				$images_query[] = '("'.$id.'", "'.$post_type.'", "'.$image.'")';
			}

			$db->query('INSERT INTO content_images (post_id, post_type, filename) VALUES '.implode(',', $images_query))->execute();

			redirect('/'. $type .'/'. $slug .'?m=created');

		}

	} // END: Form submitted

?>
	<div class="rules">
		<h3>Submission Guidelines</h3>
		<ol>
			<?php foreach ( $rules as $rule ) echo '<li>'.$rule.'</li>'; ?>
		</ol>
	</div>
	<?php $errors->display(); ?>
	<form action="/submit?type=<?php echo $type; ?>" method="POST" id="submission">
		<section>
			<input type="text" name="title" id="title" value="" placeholder="<?php echo ucwords($type); ?> Title" autocomplete="off" value="<?php htmlspecialchars(input_POST('title')); ?>">
			<textarea name="description" id="description" class="tinymce-submission tinymce" placeholder="Enter some text describing your <?php echo $type; ?> here"><?php if ( isset($submit['description']) ) echo $submit['description']; ?></textarea>
		</section>
		<section class="uploader">


			<div class="dropzone">


				<div class="dz-message">
					Tap here to upload screenshots
					<span class="note">You must provide at least one screenshot for your <?php echo $type; ?>.</span>
					<span class="bttn">Add Screenshots</span>
				</div>
				<div class="dz-error"></div>
				<div id="dropzone-previews" class="files">
					<div id="template">
						<div class="file">
							<div class="thumbnail">
								<div class="preview"><img data-dz-thumbnail></div>
							</div>
							<div class="details">
								<span class="name" data-dz-name></span>
								<span class="status" data-dz-errormessage>Ready for upload</span>
							</div>
							<div class="cancel" data-dz-remove><i class="icon-cross"></i></div>
							<div class="progress" data-dz-uploadprogress></div>
						</div>
					</div>
				</div>


			<div id="actions" class="row">

			  <div class="col-lg-7">
				<!-- The fileinput-button span is used to style the file input field as button -->
				<!--<span class="btn btn-success fileinput-button">
					<i class="glyphicon glyphicon-plus"></i>
					<span>Add files...</span>
				</span>-->
				<!--<button type="submit" class="btn btn-primary start">
					<i class="glyphicon glyphicon-upload"></i>
					<span>Start upload</span>
				</button>-->
				<!--<button type="reset" class="btn btn-warning cancel">
					<i class="glyphicon glyphicon-ban-circle"></i>
					<span>Cancel upload</span>
				</button>

			<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>



			-->
			  </div>

			  <div class="col-lg-5">
				<!-- The global file processing state -->
				<span class="fileupload-process">
				  <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
					<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
				  </div>
				</span>
			  </div>

			</div>









		</div>


		</section>
		<section class="inputs">
			<?php Form::show_inputs($form, $inputs); ?>
		</section>
		<div class="bttn-center"><button type="submit">Submit <?php echo ucwords($type); ?></button></div>
		<input type="hidden" name="uploaded" id="uploaded" value="">
	</form>
<?php

} // END: If type in $_GET is valid and user is activated

// Type in $_GET is invalid or user isn't activated
else {

?>
<div class="fullmessage">
<?php if ( activated() ) { // User activated, give option to choose type of post ?>
	<h2>What are you uploading?</h2>
	<p>The community can't wait to see what you have to share, <?php echo $u->username; ?>!</p>
	<div class="type-select">
		<a href="/submit?type=map" class="bttn"><i class="icon-map"></i> Map</a>
		<a href="/submit?type=seed" class="bttn"><i class="icon-seed"></i> Seed</a>
		<a href="/submit?type=texture" class="bttn"><i class="icon-texture"></i> Texture</a>
		<a href="/submit?type=skin" class="bttn"><i class="icon-skin"></i> Skin</a>
		<a href="/submit?type=mod" class="bttn"><i class="icon-mod"></i> Mod</a>
		<a href="/submit?type=server" class="bttn"><i class="icon-server"></i> Server</a>
	</div>
<?php } else { // User not activated, display not activated message ?>
	<h2>Account not activated</h2>
	<p>You cannot post content because your account hasn't been activated. Check your email for instructions on how to activate your account.</p>
	<a href="/account?action=resend" class="bttn">I didn't recieve an email</a>
<?php } } $page->footer(); ?>
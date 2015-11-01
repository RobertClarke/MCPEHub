<?php

/**
 * Edit Submission
 *
 * Where users can edit their posts on the website.
**/

require_once('loader.php');

// Determine if the type of post being submitted is valid (if any)
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT);

// Whether or not an AJAX request to fetch thumbnails is being made to the page
$ajax = ( isset($_GET['fetch_thumbs']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' );

if ( empty($type) || !in_array($type, ['map','seed','texture','skin','mod','server']) )
	$type = null;

if ( $type == null || empty($id) || !is_numeric($id) ) {
	if ( $ajax ) echo json_encode(['status' => 'error', 'details' => 'Missing or invalid post id or type.']);
	else redirect('/dashboard');
}

$post = $db->from('content_'.$type)->where(['id' => $id])->where_not_in('status', ['-2'])->fetch_first();
$type_id = post_type_code($type);

$query = '
	SELECT
		post.*,
		GROUP_CONCAT(filename ORDER BY img.post_id) AS images
	FROM content_'.$type.' post
	LEFT OUTER JOIN content_images img ON
		img.post_id = post.id AND
		img.post_type = '.$type_id.'
	WHERE post.id = '.$id.' AND post.status <> "-2"
	GROUP BY post.id
';

$post = $db->query($query)->fetch_first();

$title = 'Edit ' . (($type !== null) ? ucwords($type) : 'Post');

$page->auth = true;
$page->body_id = 'submit';
$page->title_h1 = $title;

$page->enqueue(['tinymce/tinymce.min', 'validate', 'dropzone', 'submission']);

if ( !$ajax )
	$page->header($title);

$errors->add('MISSING',		'The post you\'re trying to edit doesn\'t exist.');
$errors->add('OWNER',		'You don\'t have permission to edit this post.');

// Check if the post exists in the database (must not be deleted)
if ( !$post ) {
	if ( $ajax ) die(json_encode(['status' => 'error', 'details' => 'Post requested doesn\'t exist.']));
	else $errors->force('MISSING');
}

else {

	// Check if the post author matches the current user
	if ( $post['author_id'] !== $u->id ) {
		if ( $ajax ) die(json_encode(['status' => 'error', 'details' => 'Current user doesn\'t have permission to edit this post.']));
		else $errors->force('OWNER');
	}

	else {

		// If AJAX request is being made, die with the results for the post.
		if ( $ajax ) {

			$post['images'] = explode(',', $post['images']);

			foreach( $post['images'] as $id => $img )
				$post['images'][$id] = ['name' => $img, 'size' => 1];

			die(json_encode( ['status' => 'success', 'images' => json_encode($post['images'])] ));

		}

		$editable = true;

		// Show message letting users know if they edit the post, changes have to be approved
		$errors->add('EDITING',	'Note: Any changes made to this '.$type.' won\'t be shown to the public until a moderator approves them.', 'warning')->force();

		// Switch for setting post type-specific settings
		switch( $type ) {
			case 'map':
				$inputs = ['link', 'category', 'version'];
			break;
			case 'seed':
				$inputs = ['seed', 'category', 'version'];
			break;
			case 'texture':
				$inputs = ['link', 'category', 'version', 'resolution'];
			break;
			case 'skin':
				$inputs = ['category'];
			break;
			case 'mod':
				$inputs = ['link', 'category', 'version', 'platform'];
			break;
			case 'server':
				$inputs = ['ip', 'port', 'category', 'version'];
			break;
		}

		$categories = [];
		$categories_db = get_categories($type);

		// Convert categories to correct format for field verification
		foreach ( $categories_db as $category => $data ) {
			$categories[ $category ] = $data['name'];
		}

		// Add default inputs to input array
		$inputs = array_merge(['title', 'description'], $inputs);

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
				'options'		=> get_versions()
			],
			'platform' => [
				'type'			=> 'select',
				'label'			=> '<i class="icon-platform"></i> Compatible Platform',
				'placeholder'	=> 'Click to select platforms',
				'options'		=> [1 => 'iOS', 2 => 'Android']
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

		$values = [];

		// Setting HTML values of inputs from database/form submission
		foreach ( $inputs as $input ) {

			// Display submitted form values over database values
			if ( !empty( input_POST($input) ) )
				$val = $db->escape(input_POST($input));
			else
				$val = $post[$input];

			// Convert post category id's into real values
			if ( $input === 'category' )
				$val = get_category_by_id($val, $type)['key'];

			// Add value to $form array, if needed
			if ( isset($form[$input]) )
				$form[$input]['value'] = $val;

			$values[$input] = $val;

		}

		// Form submitted
		if ( submit_POST() ) {



			// Array for holding submitted values
			$submit = [];


			foreach ( $inputs as $input )
				$submit[$input] = input_POST($input);





		}

	} // END: Check if the post author matches the current user

} // END: Check if the post exists in the database (must not be deleted)

?>
	<?php $errors->display(); ?>
<?php if ( isset($editable) ) { // If post is editable/valid ?>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script>

$(function() {

	$.ajax({
		url: '/edit?post=<?php echo $id; ?>&type=<?php echo $type; ?>&fetch_thumbs=1',
		dataType: 'json',
    	success: function(data, textStatus, jqXHR) {

			// Successful return from AJAX
			if ( data.status === 'success' ) {

				data.images = JSON.parse(data.images);

				$.each(data.images, function(key, value) {

					var mockFile = {
						name: value.name,
						size: value.size,
						type: 'image/jpeg',
						status: Dropzone.ADDED,
						accepted: true,
						url: 'uploads/posts/<?php echo $type; ?>/'+value.name
					};

					dropzone.emit("addedfile", mockFile);
					dropzone.emit("thumbnail", mockFile, 'uploads/posts/<?php echo $type; ?>/'+value.name);
					dropzone.emit("complete", mockFile);
					dropzone.files.push(mockFile);

				});

			}

			// AJAX completed, but server returned error
			else {
				$('.dz-error').html('<div class="alert error">Error while fetching thumbnails: '+ data.details +'</div>');
			}

		},
    	error: function(jqXHR, textStatus, errorThrown) {
			$('.dz-error').html('<div class="alert error">Error while fetching thumbnails: Invalid server response.</div>');
		}

	});

});

	</script>



	<form action="/edit?type=<?php echo $type; ?>&post=<?php echo $id; ?>" method="POST" id="submission">
		<section>
			<input type="text" name="title" id="title" placeholder="<?php echo ucwords($type); ?> Title" autocomplete="off" value="<?php echo htmlspecialchars($values['title']); ?>">
			<textarea name="description" id="description" class="tinymce-submission tinymce" placeholder="Enter some text describing your <?php echo $type; ?> here"><?php echo $values['description']; ?></textarea>
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
			</div>
		</section>
		<section class="inputs">
			<?php Form::show_inputs($form, $inputs); ?>
		</section>
		<div class="bttn-center"><button type="submit">Submit <?php echo ucwords($type); ?></button></div>
		<input type="hidden" name="uploaded" id="uploaded" value="">
	</form>
<?php

// Type in $_GET is invalid or not owned by the user
} else {

?>



<?php } $page->footer(); ?>
<?php

/**
  * Moderator Manual Uploads
**/

require_once('core.php');

// Redirect if user not admin/mod.
if ( !$user->is_admin() && !$user->is_mod() ) redirect('/');

show_header('Moderator Map Upload', TRUE, ['body_id' => 'dashboard', 'title_main' => 'Upload Map', 'title_sub' => 'Moderator Panel']);

if ( !empty($_FILES) ) {
	
	$error->reset();
	
	$error->add('Z_MISSING',	'You didn\'t upload a ZIP.');
	$error->add('Z_MAX',		'The ZIP uploaded exceeded the maximum upload file size. Please upload a smaller file.');
	$error->add('Z_INVALID',	'The ZIP provided isn\'t in a valid format.');
	
	// Check if at least one ZIP uploaded.
	$upload = gather_files($_FILES['zip']);
	if ( empty( $_FILES['zip'] ) || count($upload) == 1 && $upload[0]['error'] == 4 ) $error->append('Z_MISSING');
	else {
		
		$i = 0;
		$images_confirmed = FALSE;
		$upload = array_slice( $upload, 0, 5 );
		foreach( $upload as $image ) {
			
			// No ZIP uploaded, unset and ignore.
			if ( $image['error'] == 4 ) {
				unset( $upload[$i] );
			}
			elseif ( $image['error'] == 1 ) {
				$error->append('Z_MAX');
				break;
			}
			
			$i++;
			
		} // End: Uploads foreach loop.
		
	} // End: Check if at least one image uploaded.
	
	if ( !$error->exists() ) {
		
		// Process uploaded ZIP.
		$images = '';
		$upload_dir = ABS . 'uploads/maps/';
		
		foreach( $upload as $i => $image ) {
			
			$f_ext = '.' . strtolower( end( explode( '.', $image['name'] ) ) );
			$f_name = 'map-' . uniqid() . strtolower(random_str(5));

			if ( $f_ext == '.zip' ) @move_uploaded_file( $_FILES['zip']['tmp_name'][$i], $upload_dir . $f_name . $f_ext );
			else $notZip = TRUE;
			
			$uploadedFile = $f_name . $f_ext;
			
		}
		
		if ( !isset($notZip) ) {
			$error->add('SUCCESS', '<b>The map has been uploaded to the server!</b><br><br>URL: <a href="http://mcpehub.com/uploads/maps/'.$uploadedFile.'" target="_blank">http://mcpehub.com/uploads/maps/'.$uploadedFile.'</a>', 'success');
			$error->set('SUCCESS');

			$completed = TRUE;
		}
		else {
			$error->set('Z_INVALID');
		}
		
	} // End: No errors found in input.
	
} // End: Form submitted.

?>
<div id="p-title">
    <h1>Manual Map Upload</h1>
    <div class="tabs">
        <a href="/moderate" class="bttn mid"><i class="fa fa-long-arrow-left"></i> Back to Moderation</a>
    </div>
</div>

<form action="/moderate-upload" method="POST" enctype="multipart/form-data">

	<?php $error->display(); ?>
	<div class="input-rules" style="padding-bottom:15px;"><p>Upload a file below to have it uploaded to the server as a map. This will <b>not</b> create a new post, but will generate a URL that you will have to insert into the post of your choice.</p></div>

<?php if (!isset($completed)) { ?>
	<div class="input-uploads">
        <input type="file" name="zip[]" id="image" class="zip-upload" />
    </div>
    <div class="submit">
        <button type="submit" class="bttn big green"><i class="fa fa-upload"></i> Upload ZIP</button>
    </div>
<?php } else { ?>
	<div class="submit">
        <a href="/moderate-upload" class="bttn big green"><i class="fa fa-upload"></i> Upload Another</a>
    </div>
<?php } ?>

</form>

<?php show_footer(); ?>
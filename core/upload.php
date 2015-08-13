<?php

/**
 * Uploader
 *
 * Handles any uploads sent from any forms for posting, avatar
 * changes and anything else that involves file uploads.
**/

require_once( '../loader.php' );

function response( $status, $msg='', $code=200 ) {
	http_response_code($code);
	echo json_encode(['status' => $status, 'msg' => $msg]);
}

// User not logged in, return 403 code and error JSON object
if ( !logged_in() )
	response('auth_failed', 'Uploads are only allowed for authorized users.', 403);

// User is logged in, allow the upload
else {

	// $_POST request recieved from server
	if ( submit_POST() ) {

		// Check if file is missing from $_POST
		if ( isset($_FILES['file']) && !empty($_FILES['file']) && $_FILES['file']['error'] === 0 ) {

			// Autoloader for Uploader class
			require_once( './uploader/Autoloader.php' );
			$loader = new Upload\Autoloader;

			$loader->autoload('Exception');
			$loader->autoload('StorageInterface');
			$loader->autoload('ValidationInterface');
			$loader->autoload('File');
			$loader->autoload('FileInfoInterface');
			$loader->autoload('FileInfo');
			$loader->autoload('Storage\FileSystem');
			$loader->autoload('Validation\Extension');
			$loader->autoload('Validation\Mimetype');
			$loader->autoload('Validation\Size');

			// Upload all new files to a temporary dir on the server
			$storage	= new \Upload\Storage\FileSystem('../uploads/temp');
			$file		= new \Upload\File('file', $storage);

			// Set a new, unique filename (40 random characters for security)
			$file->setName( $u->username .'-'. random_string(40) );

			$file->addValidations([
				new \Upload\Validation\MimeType(['image/png', 'image/jpeg']),
				new \Upload\Validation\Size('5M')
			]);

			// Attempt the file upload
			try {
				$file->upload();
				response('success', $file->getNameWithExtension(), 201);
			}
			catch(\Exception $e) {
				response('error', $file->getErrors()[0], 400);
			}

		} else response('input_missing', 'No file was submitted for upload.', 400); // Error for missing $_POST['file'] input value

	} else response('post_missing', 'Missing POST request.', 400); // Error for missing $_POST submission

}
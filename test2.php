<?php

require_once( 'core.php' );

if ( $_POST ) {
	
	
	
	$server_id = $_POST['id'];
	
	//$ip = 'test';
	//$port = '12334';
	
	$query = $db->from( 'content_servers' )->where( array( 'id' => $server_id ) )->fetch();
	$num = $db->affected_rows;
	
	// todo: send error if post not found.
	
	//if ( $num != 0 ) // Look if any posts found.
	
	if ( $num != 0 ) {
		
		$post = $query[0];
		
		if ( $post['active'] != 0 ) {
			
			
			
			$connect = new mcQuery();
			
			$ip = $post['ip'];
			$port = $post['port'];
			
			if ( $connect->connect( $ip, $port ) ) {
				$response = 'Online';
			}
			else {
				$response = 'Offline';
			}
			
			
			
		}
		
		else $response = 'Disabled';
		
		
		
		//return $_POST['id'];
		
		
		//$id = rand(1,10000);
	
	}
	else $response = 'Invalid';
	
	echo json_encode( array( 'response' => $response ) );
	
	
}
?>
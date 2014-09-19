<?php

// $_POST submission required for usage.
if ( !empty( $_POST ) ) {
	
	$status = 'error';
	$statuses = array(
		
		'error'	=> array(
			'badge'		=> '<i class="fa fa-times"></i> Error',
			'text'		=> 'Error',
			'class'		=> 'offline'
		),
		
		'online' => array(
			'badge'		=> '<i class="fa fa-check-circle"></i> Online',
			'text'		=> 'Online',
			'class'		=> 'online'
		),
		
		'offline' => array(
			'badge'		=> '<i class="fa fa-times-circle"></i> Offline',
			'text'		=> 'Offline',
			'class'		=> 'offline'
		)
		
	);
	
	// Server ID must be present in form submission.
	if ( !empty( $_POST['server_id'] ) ) {
		
		// Manually include config + db, including core.php didn't work (?).
		require_once( '../config.php' );
		
		require_once( './classes/sql.php' );
		$db = new Database( DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT );
		
		$server_id = $db->escape( $_POST['server_id'] );
		
		// Grab IP + Port from database.
		$query = $db->select('ip,port')->from( 'content_servers' )->where( array( 'id' => $server_id ) )->fetch();
		$num = $db->affected_rows;
		
		// Check if server exists.
		if ( $num != 0 ) {
			
			$server = $query[0];
			
			require_once( './classes/ping.php' );
			$connect = new mcQuery();
			
			// Connected! :D
			if ( $connect->connect( $server['ip'], $server['port'], 2 ) ) {
				
				$status = 'online';
				
				$mc = new MinecraftQuery();
				$mc->Connect( $server['ip'], $server['port'], 2 );
				
				$server_info = $mc->GetInfo();
				
				$statuses[$status]['players'] = ' - ' . $server_info['Players'] . '/' . $server_info['MaxPlayers'] . ' Players';
				
			}
			
			else
				$status = 'offline';
		
		// Server ID invalid.
		} else $status = 'error';
	
	// Server ID is missing.
	} else $status = 'error';
	
	echo json_encode( $statuses[$status] );
	
}

?>
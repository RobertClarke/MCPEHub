<?php

if ( empty($_GET['k']) || empty($_GET['server']) || empty($_GET['port']) ) $return['status'] = 'error';
else {
	
	if ( $_GET['k'] != 'yqKebthN2n4326dsbd38Nd7s' ) $return['status'] = 'error';
	else {
		
		$ip = $_GET['server'];
		$port = $_GET['port'];
		
		require ('./src/MinecraftQuery.php');
		require ('./src/MinecraftQueryException.php');
		
		$Query = new MinecraftQuery();
		
		try {
	        $Query->Connect( $ip, $port, 2 );
	
	        $grab = $Query->GetInfo();
	        
	        $return['status'] = 'online';
	        
	        $return['online'] = $grab['Players'];
	        $return['max'] = $grab['MaxPlayers'];
	        
	    }
	    catch( MinecraftQueryException $e ) { $return['status'] = 'error'; }
		
	}
	
}

echo json_encode($return);

?>
<?php

/**
  
  * Ping Action
  *
  * This action will ping a server, given a server id from the
  * database, and return status + players online (if online).
  *
  * Accepts server id from GET variable server.
  
**/

// Return value will be a JSON-encoded array.
$return = [];

// Check if servers are set in URL.
if ( !isset($_GET['s']) ) $return['status'] = 'error';
else {	
	
	require_once('../config.php');
	
	require_once('../core/classes/sql.php');
	$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
	
	// Split and mass escape the servers into an array.
	$servers = explode(',', $db->escape($_GET['s']));
	$db_ids	= '';
	
	// Where we'll store all status info.
	$servs = [];
	
	// Run through every server id in GET request and verify if its numeric.
	foreach( $servers as $i => $server ) {
		if ( is_numeric($server) ) $db_ids .= $server.',';
		else $servs[$server]['status'] = 'error';
	}
	
	// Trim trailing comma.
	$db_ids = rtrim($db_ids, ',');
	
	// If there's at least one valid server id to check.
	if ( !empty( $db_ids ) ) {
		
		$grab = $db->query('SELECT id, ip, port FROM content_servers WHERE `id` IN ('.$db_ids.') AND `active` = 1')->fetch();
		
		// If at least one valid server found in database.
		if ( !empty($grab) ) {
			
			$return['status'] = 'success';
			
			// Set up multiple CURL parallel requests.
			$multi = curl_multi_init();
			$ping = array();
			
			foreach( $grab as $i => $server ) {
				
				//$url = 'http://api.minetools.eu/ping/'.$server['ip'].'/'.$server['port'];
				$url = 'http://mcpehub.com/core/ping/pingServer?server='.$server['ip'].'&port='.$server['port'].'&k=yqKebthN2n4326dsbd38Nd7s';
				
				$ch = curl_init();
				
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				
				curl_multi_add_handle($multi, $ch);
				
				$ping[$server['id']] = $ch;
				
			}
			
			// While we're still active, execute CURL.
			$active = null;
			do {
				$mrc = curl_multi_exec($multi, $active);
			} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			
			while ($active && $mrc == CURLM_OK) {
				
				// Wait for activity on any CURL connection
				if (curl_multi_select($multi) == -1) {
					continue;
				}
				
				// Continue to exec until CURL ready to send more data.
				do {
					$mrc = curl_multi_exec($multi, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
				
			}
			
			// Loop through CURL channels and retrieve content.
			foreach ( $ping as $id => $result ) {
				
				$s = json_decode(curl_multi_getcontent($result), TRUE);
				
				$servs[$id]['id'] = $id;
				
				if ( $s['status'] != 'error' && !empty($s) ) {
					
					$servs[$id]['status']		= 'online';
					$servs[$id]['status_html']	= 'Online';
					$servs[$id]['players_html']	= '<i class="fa fa-group"></i> <strong>'.$s['online'].'/'.$s['max'].'</strong> players';
					$servs[$id]['players']		= $s['online'].'/'.$s['max'];
					
				} else {
					$servs[$id]['status']		= 'offline';
					$servs[$id]['status_html']	= 'Offline';
				}
				
				curl_multi_remove_handle($multi, $result);
				
			}
			
			// Close CURL.
			curl_multi_close($multi);
			
			$return['servs'] = $servs;
			
		} // End: If at least one valid server found in database.
		else $return['status'] = 'error';
		
	} // End: If there's at least one valid server id to check.
	else $return['status'] = 'error';
	
} // End: Check if server id found in request URL.

// Return JSON-encoded array.
echo json_encode($return);

?>
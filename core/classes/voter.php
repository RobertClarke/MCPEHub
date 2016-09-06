<?php

class Voter {
	public $vote_user;
	public $db;
	public $server;
	public $vote;
	
	function __construct($db, $user){
		$this->db = $db;
		$this->user = $user;
	}
	
	public function set_server($server){
		$this->server = $server;
		$this->get_vote_user();
	}
	
	public function get_vote_user(){
		$db = $this->db;
		if(isset($this->vote_user)){
			return $this->vote_user;
		}
		$is_anon = '0';
		if(!$this->user->logged_in()){
			$is_anon = '1';
			
			if(isset($_COOKIE['vote_track'])){
				$track_id = $db->escape($_COOKIE['vote_track']);
				$ip = $db->escape($_SERVER['REMOTE_ADDR']);
				
				$anon = $db->query("SELECT * FROM users_anon WHERE track_id = '$track_id' OR ip = '$ip'")->fetch();
				$anon_user = isset($anon[0]) ? $anon[0] : null;
			}else{
				$anon_user = null;
			}
			
			if($anon_user == null){
				$track_id = uniqid();
				$uid = $db->insert('users_anon', array('track_id'=>$track_id,'ip'=>$ip));
			}else{
				$uid = $anon_user['id'];
				$track_id = $anon_user['track_id'];
			}
			setcookie('vote_track',$track_id,time()+86400*30);
		}else{
			$uid = $this->user->info('id');
		}
		return $this->vote_user = array('id'=>$uid, 'anon'=>$is_anon);
	}
		
	public function votereward_vrc(){
		$db = $this->db;
		$sid = $this->server['id'];
		
		$server = $db->query("SELECT * FROM content_servers s LEFT JOIN server_vote_config svc ON svc.server_id = s.id AND svc.plugin = 'VoteReward' WHERE s.id = $sid")->fetch();
		$server = isset($server[0]) ? $server[0] : null;
		
		
		$pkey = ($server['public_key'] != '' ? $server['public_key'] : uniqid());
		
		if($server['public_key'] != ''){
			$pkey = $server['public_key'];
		}else{
			$pkey = uniqid();
			$db->insert('server_vote_config',
				['server_id' => $server['id'],
				'plugin' => 'VoteReward',
				'public_key' => $pkey]
			);
		}
		
		return json_encode(array(
			'website' => 'http://mcpehub.com',
			'check' => 'http://mcpehub.com/votereward/check/?key='.$pkey.'&user={USERNAME}',
			'claim' => 'http://mcpehub.com/votereward/claim/?key='.$pkey.'&user={USERNAME}'
		), JSON_UNESCAPED_SLASHES);;
	}
	
	public function votereward_process($do_claim = false){
		$db = $this->db;
		
		$mcuser = $db->escape($_GET['user']);
		$key = $db->escape($_GET['key']);
		
		$server = $db->query("SELECT * FROM server_vote_config svc LEFT JOIN server_votes sv ON sv.server_id = svc.server_id AND sv.mc_user = '$mcuser' AND sv.timestamp > now() - INTERVAL 1 DAY WHERE svc.public_key = '$key'")->fetch();
		$server = isset($server[0]) ? $server[0] : null;
		if($server == null){
			$json = array('error' => 'Invalid public key');
		}elseif($mcuser == ''){
			$json = array('error' => 'Minecraft username missing');
		}else{
			$voted = ($server['mc_user'] == $mcuser ? 1 : 0);
			$claimed = ($voted ? ($server['reward_claimed'] != null ? $server['reward_claimed'] : 0) : 0);
			
			if($voted && !$claimed && $do_claim){
				$db->where(['id'=>$server['id']])->update('server_votes', ['reward_claimed' => 1]);
				$claimed = 1;
			}
			$json = array('voted' => (int)$voted, 'claimed' => (int)$claimed);
		}
		
		return json_encode($json, JSON_UNESCAPED_SLASHES);
	}
	
	public function get_existing_vote() {	
		if(isset($this->vote))return $this->vote;
		$db = $this->db;
		
		$user = $this->get_vote_user();
		$server_id = $this->server['id'];
		$vote = $db->query("SELECT * FROM server_votes WHERE server_id = '$server_id' AND user_id = '$user[id]' AND is_anon = '$user[anon]' AND timestamp > now() - INTERVAL 1 DAY")->fetch();
		$vote = isset($vote[0]) ? $vote[0] : null;
		if($vote != null){
			$vote['remain'] = ceil(24 - (time() - strtotime($vote['timestamp']))/3600);
		}
		
		return $this->vote = $vote;
	}
	
	public function vote($mcuser){
		$db = $this->db;
		$sid = $this->server['id'];
		$mcuser = $db->escape($mcuser);
		
		$user = $this->get_vote_user();
		$vote = $this->get_existing_vote();
		
		if($vote != null){
			$emsg = 'VOTE_FAIL';
		}else{
			$emsg = 'VOTE_SUCCESS';
			$claimed = 0;
			/* votifier support
		
			$config = $db->query("SELECT * FROM server_vote_config WHERE server_id = '$sid'")->fetch();
			if(($config = $config[0]) != null){
				if($config['plugin'] == 'Votifier'){
					$votifier = new MinecraftVotifier($config['public_key'], $config['ip'], $config['port'], 'MinecraftHub.com');
					$votifier->sendVote($mcuser);
					$claimed = 1;
				}
			}*/
			$db->insert('server_votes',
				array(
					'user_id' => $user['id'],
					'mc_user' => $mcuser,
					'is_anon' => $user['anon'],
					'server_id' => $sid,
					'reward_claimed' => $claimed,
					'timestamp' => 'now()',
				));
			$db->where(['id'=>$sid])->update('content_servers', ['votes'=> $this->server['votes']+1]);
		}
		
		return $emsg;
	}
}

?>

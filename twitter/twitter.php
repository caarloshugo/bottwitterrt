<?php

require_once("api.twitter.php");

class Twitter extends API_Twitter {
	
	public function __construct($key) {
		parent::__construct($key["consumer_key"], $key["consumer_secret"], $key["oauth_token"], $key["oauth_token_secret"]);
	}
	
	public function login($redirect = NULL) {
		if(!SESSION("ZanUser")) {
			SESSION("redirect", $redirect);
			$this->getRequest();
		} else {
			redirect(SESSION("redirect"));
		}
	}
	
	public function logout($redirect = NULL) {
		if($redirect === NULL) {
			unsetSessions();
		} else {
			unsetSessions($redirect);
		}
	}
	
	public function getAccess() {
		if(isset($_GET["oauth_token"])) {
			$token = parent::getAccessToken();
			
			if(isset($token["oauth_token"])) {
				SESSION("ZanUser",          $token["screen_name"]);
				SESSION("ZanUserMethod",    "twitter");
				
				SESSION("oauthtoken",    array ("oauth_token" => $token["oauth_token"], "oauth_token_secret" => $token["oauth_token_secret"]));
				
				$this->Twitter_Model = $this->model("Twitter_Model");
				
				$user = $this->Twitter_Model->saveUser($this->account());
				
				SESSION("ZanUserID",        $user["ID_User"]);
				SESSION("ZanUserPrivilege", $user["Privilege"]);
					
				redirect(_webBase . _sh . _webLang . _sh . "twitter");
			} else {
				return FALSE;
			}
		} else {
			redirect(SESSION("redirect"));
		}
	}
	
	private function getRequest() {
		$request = parent::getRequestToken();
		
		if($this->http_code === 200) {
			if(isset($request["oauth_token"])) {
				createSession("oauthtoken", $request);
				
				$this->getURL($request["oauth_token"]);
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	public function welcome() {
		#return $this->tweet("I've registered at ".  _webName ." ". SESSION("redirect"));
	}
	
	public function getURL($token) {
		redirect(parent::getAuthorizeURL($token));
	}
	
	public function tweet($tweet = NULL) {
		if(!is_null($tweet)) {
			$response = $this->post("statuses/update", array("status" => $tweet));
			
			return $response;
		}
		
		return FALSE;
	}
	
	public function updateImage($file = NULL, $mime = NULL) {
		if(!is_null($file) and !is_null($mime)) {
			$response = $this->post("account/update_profile_image", array("image" => $file));
			
			return $response;
		}
		
		return FALSE;
	}
	
	public function retweet($id = FALSE) {
		if($id) {
			$response = $this->post("statuses/retweet/" . $id);
		}
	}
	
	public function showRetweet($id = FALSE) {
		return $this->get("statuses/retweets/" . $id);
	}
	
	public function tweets($params = array("count" => 2)) {
		return $this->get("statuses/user_timeline", $params);
	}
	
	public function mentions($params = array("count" => 20)) {
		return $this->get("statuses/mentions", $params);
	}
	
	public function timeline($params = array("count" => 20)) {
		return $this->get("statuses/home_timeline", $params);
	}
	
	public function messages($params = array("count" => 20)) {
		return $this->get("direct_messages", $params);
	}
	
	public function followers($params = array("screen_name" => "caarloshugo")) {
		return $this->get("friendships/lookup");
	}
	
	public function setMessage($params = array()) {
		if((isset($params["text"])) and (isset($params["screen_name"]) or isset($params["user_id"]))) {
			return $this->post("direct_messages/new", $params);
		}
	}
	
	public function  account() {
		return $this->get("account/verify_credentials");
	}
	
	public function getAcount($username) {
		return $this->get();
	}
	
	public function getAvatar($username) {
		$data = $this->get("users/profile_image/:" . $username);
		
		if($data) {
			
		} else {
			return FALSE;
		}
	}
	
	public function search($username = NULL, $id = NULL) {
		return $this->json_request("http://search.twitter.com/search.json?callback=?&since_id=" . $id . "&q=from:" . $username);
	}
	
	public function serachText($text = NULL, $limit = 3) {
		return $this->json_request("http://search.twitter.com/search.json?q=" . $text . "&rpp=" . $limit);
	}
	
	public function json_request($url) {
		$ch = curl_init(); 
		
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		
		$response = curl_exec($ch); 
		
		curl_close($ch);
		
		return json_decode($response);
	}
	
	public function post($method, $params = array()) {
		$response = parent::post($method, $params);
		
		if(isset($response->error)) {
			return $this->errors($response);
		}
		
		return $response;
	}
	
	public function get($method, $params = array()) {
		$response =  parent::get($method, $params);
		
		if(isset($response->error)) {
			return $this->errors($response);
		}
		
		return $response;
	}
	
	private function errors($error) {
		return  FALSE;
	}
}

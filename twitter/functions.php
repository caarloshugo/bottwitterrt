<?php
function json_request($url) {
    $ch = curl_init(); 
    
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    
    $response = curl_exec($ch); 
    
    curl_close($ch);
    
    return json_decode($response);
}

function RT($api) {	
	/*Read file json ID tweet*/
	$filename = dirname(__FILE__) . "/../json/" . $api["username"] . "_" . $api["userRT"] . "_id.json";
	
	print_r("Filaname: " . $filename . "\n");
	
	$data 	  = fopen($filename, "r");
	$contents = json_decode(fread($data, filesize($filename)));

	fclose($data);

	$twitter = new Twitter($api);
	$tweets  = $twitter->search($api["userRT"], $contents->id);
	
	if($tweets and !empty($tweets) and isset($tweets->results)) {
		if(is_array($tweets->results) and count($tweets->results) > 0) {
			foreach($tweets->results as $key => $tweet) {
				$str = substr((string) $tweet->text, 0, 2);
				
				if($str != "RT" and $tweet->to_user_id == "") {
					$retweeted = $twitter->showRetweet($tweet->id_str);
					
					if(isset($retweeted->retweeted) and $retweeted->retweeted == FALSE) {
						print_r(date('l jS \of F Y h:i:s A') . " id: " . $tweet->id_str . " - " . $tweet->text . " \n");
						$twitter->retweet($tweet->id_str);
					} elseif(!isset($retweeted->retweeted)) {
						print_r(date('l jS \of F Y h:i:s A') . " id: " . $tweet->id_str . " - " . $tweet->text . " \n");
						$twitter->retweet($tweet->id_str);
					}
				}
				
				if($key == 0) {
					$data = fopen($filename, "w");
					fwrite($data, '{"id" : "' . $tweet->id_str . '"}');
					fclose($data);
				}
				
				sleep(40);
			}
		}
	}
}

function RT_E($api) {	
	/*Read file json ID tweet*/
	$filename = dirname(__FILE__) . "/../json/E_" . $api["username"] . "_" . $api["userRT"] . "_id.json";
	
	print_r("Filaname: " . $filename . "\n");
	
	$data 	  = fopen($filename, "r");
	$contents = json_decode(fread($data, filesize($filename)));

	fclose($data);

	$twitter = new Twitter($api);
	$tweets  = $twitter->search($api["userRT"], $contents->id);
	
	if($tweets and !empty($tweets) and isset($tweets->results)) {
		if(is_array($tweets->results) and count($tweets->results) > 0) {
			foreach($tweets->results as $key => $tweet) {
				$str = substr((string) $tweet->text, 0, 2);
				
				if($str != "RT" and $tweet->to_user_id == "") {
					if(strlen($tweet->text) <= 127) {
						print_r(date('l jS \of F Y h:i:s A') . " id: " . $tweet->id_str . " - " . $tweet->text . " \n");
						$twitter->tweet('RT @codejobs ' . $tweet->text);
					}
				}
				
				if($key == 0) {
					$data = fopen($filename, "w");
					fwrite($data, '{"id" : "' . $tweet->id_str . '"}');
					fclose($data);
				}
				
				sleep(30);
			}
		} else {
			print_r("No data \n");
		}
	} else {
		print_r("No data \n");
	}
}

function searchRT($api) {
	$twitter = new Twitter($api);
	$tweets  = $twitter->serachText($api["text"]);
	
	if($tweets and !empty($tweets) and isset($tweets->results)) {
		if(is_array($tweets->results) and count($tweets->results) > 0) {
			foreach($tweets->results as $key => $tweet) {
				
				if(strpos($tweet->text, $api["text"])) {
					$str = substr((string) $tweet->text, 0, 2);
					
					if($tweet->to_user_id == "" and $tweet->from_user != "yosoy8bits") {
						$count = 140 - (strlen($tweet->from_user) + 5);
						
						if(strlen($tweet->text) <= $count) {
							print_r("tweet => " . date('l jS \of F Y h:i:s A') . " id: " . $tweet->id_str . " user: " . $tweet->from_user . " - " . $tweet->text . " \n");
							$twitter->tweet('RT @' . $tweet->from_user . ' ' . $tweet->text);
						} else {
							$retweeted = $twitter->showRetweet($tweet->id_str);
						
							if(isset($retweeted->retweeted) and $retweeted->retweeted == FALSE) {
								print_r("retweet => " . date('l jS \of F Y h:i:s A') . " id: " . $tweet->id_str . " user: " . $tweet->from_user . " - " . $tweet->text . " \n");
								$twitter->retweet($tweet->id_str);
							} elseif(!isset($retweeted->retweeted)) {
								print_r("retweet => " . date('l jS \of F Y h:i:s A') . " id: " . $tweet->id_str . " user: " . $tweet->from_user . " - " . $tweet->text . " \n");
								$twitter->retweet($tweet->id_str);
							}
						}
					}
				}
				
				sleep(30);
			}
		} else {
			print_r("No data \n");
		}
	} else {
		print_r("No data \n");
	}
}

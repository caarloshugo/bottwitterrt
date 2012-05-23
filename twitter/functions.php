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
	$filename = "/opt/lampp/htdocs/bots/json/" . $api["username"] . "_" . $api["userRT"] . "_id.json";
	
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
				
				sleep(90);
			}
		}
	}
}

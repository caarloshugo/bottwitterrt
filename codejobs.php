#!/usr/bin/php
<?php
require_once("twitter/functions.php");
require_once("twitter/twitter.php");
require_once("config/codejobs.php");

foreach($keys as $key) {
	print_r("\n\nUser: " . $key["username"]  . "-" . $key["userRT"] . "\n");
	RT($key);
	sleep(360);
}

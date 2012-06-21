#!/usr/bin/php
<?php
require_once("twitter/functions.php");
require_once("twitter/twitter.php");
require_once("config/yosoy8bits.php");

foreach($keys as $key) {
	print_r("\n\nUser: " . $key["username"]  . "-" . $key["text"] . "\n");
	searchRT($key);
}

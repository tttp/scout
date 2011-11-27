<?php
class message {
	function write($text, $extras=array()){
		echo $text;
		if(!array_key_exists('sameline', $extras))
		echo "\n";
	}

	function debug($text, $extras=array()){
		print_r($text);
	}

	function fatal($text, $extras=array()){
		echo "Fatal error: $text\n";
		exit;
		
	}	
	
	function error($text, $extras=array()){
		echo "Error: $text\n";		
	}	
	
	
}

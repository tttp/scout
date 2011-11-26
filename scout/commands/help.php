<?php
class help{

	
	var $description = 'Shows this help message.';

	var $usage = '';
	
	function run(){
		global $commands;
		$m = new message;
		foreach($commands as $name => $command){
			$help = new $command;
			$m->write("{$name}: {$help->description}");
		}
	}

}

<?php
class aws {
	
	var $settings=array(
		'eu-west-1'=>array(
			'az'=>'eu-west-1a',
			'image'=>'ami-65b28011', //ubuntu 11.10, see http://alestic.com/

			),
		'us-east-1'=>array(
			'az'=>'us-east-1a',
			'image'=>'ami-a7f539ce', //ubuntu 11.10, see http://alestic.com/
			),
		);
	
	var $region = NULL;

	function __construct(){
		$this->region = SCOUT_AWS_REGION;
		if(!array_key_exists($this->region, $this->settings)){
			message::fatal("No AWS settings found for region: {$this->region}.");
		}
	}
	
	function init(){
		require_once SCOUT_PATH_AWS_SDK_FOR_PHP;	
	}

	function get($value){
		if($value=='key'){
			return SCOUT_EC2_KEY_PREFIX.$this->region;
		}
		if($value=='region'){
			return $this->region;
		}
		return $this->settings[$this->region][$value];
	}
	
}

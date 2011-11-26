<?php
class aws {
	
	var $defaults=array(
		'eu-west-1'=>array(
			'az'=>'eu-west-1a',
			'image'=>'ami-65b28011', //ubuntu 11.10, see http://alestic.com/

			),
		'us-east-1'=>array(
			'az'=>'us-east-1a',
			'image'=>'ami-a7f539ce', //ubuntu 11.10, see http://alestic.com/
			),
		);
	
	var $region = 'us-east-1';

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
		return $this->defaults[$this->region][$value];
	}
	
	function get_userdata($template = 'agent'){
		$user_data = new template;
		$user_data->load_template('userdata.'.$template);
		$translation=array(
			'domain' => SCOUT_DOMAIN,
			'hostname' => $this->args[3],
		);
//		print_r($user_data->parse($translation));exit;
		return base64_encode($user_data->parse($translation));
	}
}

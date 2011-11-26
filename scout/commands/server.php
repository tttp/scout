<?php
class server extends command {

	var $description = 'Creates a server using puppet configurations.';
	var $usage = <<<EOD
	
scout server create <hostname> [-t|--type]=<type>

	<type>
	specify the type of server you want to create (defaults to agent)
	can be one of: master, agent

EOD;
	var $available_options = array(
		't' => array(
			'name'=>'type',
			'set_if_empty'=>true,
			'value'=>'required',
			'options'=>array('master', 'agent'),
			'default'=>'agent'
		)
	);
	
	function run(){		
		$subcommands=array('create');
		$subcommand=$this->args[1];
		if(in_array($subcommand, $subcommands)){
			call_user_func(array($this, $subcommand));
		}
	}

	function create(){

		$aws = new aws;
		$aws->init();	

		$this->m->write('Initialising EC2');
			
//		$ec2 = new AmazonEC2();
//		$ec2->set_region($aws->get('region'));

		//add puppet yaml user data
		
		// if(isset($this->opts['t']['value'])){
		// 	echo $this->opts['t']['d'];exit;
		// }
		
		$opt = array(
			'KeyName'=>$aws->get('key'),
			'InstanceType'=>'t1.micro',
			'Placement'=>array(
				'AvailabilityZone'=>$aws->get('az')
			),
			'UserData' => $aws->get_userdata($this->opt('type'))
		);
				
		print_r($opt);exit;
		
		$this->m->write('Creating instance...');
		$instance = $ec2->run_instances($aws->get('image'),1,1,$opt);
		$instanceId=$instance->body->instancesSet->item->instanceId;
		if(!strlen($instanceId)){
			$this->m->debug($instance);
			stop('Could not create instance.');
		}
		$this->m->write('Created instance with ID '.$instanceId);
		$dnsName='';
				
		$this->m->write('Waiting for external IP address...', array('sameline'));
		while($dnsName==''){
			sleep(1);
			$instance=$ec2->describe_instances(array('InstanceId'=>$instanceId));
			$dnsName=$instance->body->reservationSet->item->instancesSet->item->dnsName;
			echo '.';
		}
		echo "\n";
		$this->m->write("You can connect to this instance with\nssh -i ~/.ssh/{$aws->get('key')}.pem ubuntu@{$dnsName}\n(you might have to wait a few moments though).");
		
		//create a DNS record for this instance		
	}
	
}

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
			'options'=>array('master', 'agent', 'no-user-data'),
			'default'=>'agent'
		),
	);
	
	function run(){		
		$subcommands=array('create');
		$subcommand=$this->args[1];
		if(in_array($subcommand, $subcommands)){
			call_user_func(array($this, $subcommand));
		}
	}

	function create(){

		//initialise AWS
		$aws = new aws;
		$aws->init();	
		$this->m->write('Initialising AWS');

		//initialise EC2
		$ec2 = new AmazonEC2();
		$ec2->set_region($aws->get('region'));

		//define instance
		$this->hostname=$this->args[2];
		$this->name=$this->hostname.' (scout '.$this->opt('type').')';
		$this->domain=SCOUT_DOMAIN;		
		$this->instance_options = array(
			'KeyName'=>$aws->get('key'),
			'InstanceType'=>'t1.micro',
			'Placement'=>array(
				'AvailabilityZone'=>$aws->get('az')
			)
		);
		if($this->opt('type')!='no-user-data'){
			$this->instance_options['UserData']=$this->get_userdata();
		}
		
		//create instance
		$this->m->write("Creating instance with name: '$this->name'...");
		$instance = $ec2->run_instances($aws->get('image'),1,1,$this->instance_options);
		$instance_id=$instance->body->instancesSet->item->instanceId;
		if(!strlen($instance_id)){
			$this->m->debug($instance);
			stop('Could not create instance.');
		}
		$this->m->write("Created '{$this->hostname}' with instance id $instance_id");
		$ec2->create_tags($instance_id, array('Key'=>'Name', 'Value'=>$this->name));

		//get connection details
		$ec2_public_hostname='';
		$this->m->write('Waiting for EC2 public host name...', array('sameline'));
		while($ec2_public_hostname==''){
			sleep(1);
			echo '.';
			$instance=$ec2->describe_instances(array('InstanceId'=>$instance_id));
			$ec2_public_hostname=$instance->body->reservationSet->item->instancesSet->item->dnsName;
			$ec2_private_ip=$instance->body->reservationSet->item->instancesSet->item->privateIpAddress;
		}
		echo "\n";
		
		//output connection details
		$this->m->write("You can connect to this instance with\nssh -i ~/.ssh/{$aws->get('key')}.pem ubuntu@{$ec2_public_hostname}\n(you might have to wait a few moments though).");
		if($this->opt('type')=='master'){
			$this->m->write("Please update your 'scout/config.inc.php' with the following:");
			$this->m->write("define('SCOUT_PUPPET_MASTER_HOSTNAME', '{$this->hostname}.{$this->domain}');");
			$this->m->write("define('SCOUT_PUPPET_MASTER_EC2_INTERNAL_IP', '{$ec2_private_ip}');");
			$this->m->write("This will ensure puppet agents can connect to the puppet master.");
		}

		//TODO: create a DNS record for this instance		
	}
	
	function get_userdata(){
		$user_data = new template;
		$user_data->load_template('userdata.'.$this->opt('type'));
		$translation=array(
			'host' => $this->hostname,
			'domain' => SCOUT_DOMAIN,
			'puppet_master_hostname' => SCOUT_PUPPET_MASTER_HOSTNAME,
			'puppet_master_ip' => SCOUT_PUPPET_MASTER_EC2_INTERNAL_IP,
		);
		$user_data->parse($translation);
		return base64_encode($user_data->parse($translation));
	}
	
	
}

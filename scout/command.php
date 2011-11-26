<?php

//scount commands consist of a number of arguments, followed by options, some of which contain values.

class command {

	var $options=array();
			
	function init_drush(){
		
	}

	function init(){
		$this->m = new message;
		$this->parse_command();
	}
		
	function parse_command(){
		global $argv;
		$this->command=$argv;
		array_shift($this->command); //remove the first argument from the list of commands
		
		//go through all command values until you find one that starts with a dash (assume that is the first option)
		foreach($this->command as $a){
			if(substr($a,0,1)!='-'){
				
				//check that this is a valid command
				if(!preg_match('/^[a-z]+$/', $a)){
					stop("Invalid argument: $a (arguments should be lower case alphanumeric).");
				}
				$this->args[] = $a;
				array_shift($this->command);
			}else{
				break;
			}
		}
	
	
		//from this point on we only accept options in the format -o or -option=value

		//create default options
		foreach($this->available_options as $k => $available_option){
			$this->option_names[$available_option['name']]=$k;
			if($available_option['set_if_empty']==true){
				$this->opts[$k]['value']=$available_option['default'];
			}
		}
		
		foreach($this->command as $k => $o){
			//there are four different valid patterns
			if(preg_match('/^--(.{2,})=(.+)$/', $o, $match)){ //long option with value
				if(array_key_exists($match[1], $this->option_names)){
					$this->opts[$this->option_names[$match[1]]]=$match[2];
				} else{
					$this->invalid_option($o);
				}
			}elseif(preg_match('/^--(.{2,})$/', $o, $match)){
				if(array_key_exists($match[1], $this->option_names)){
					if($this->available_options[$this->option_names[$match[1]]]['value']=='required'){
						$this->m->fatal("required value missing for option $o.");						
					}
					$this->opts[$this->option_names[$match[1]]]=TRUE;
				} else{
					$this->invalid_option($o);
				}
			}elseif(preg_match('/^-(.)=(.+)$/', $o, $match)){
				if(array_key_exists($match[1], $this->available_options)){
					$this->opts[$match[1]]=$match[2];
				} else{
					$this->invalid_option($o);
				}
			}elseif(preg_match('/^-(.)$/', $o, $match)){
				if(array_key_exists($match[1], $this->available_options)){
					if($this->available_options[$match[1]]['value']=='required'){
						$this->m->fatal("required value missing for option $o.");						
					}
					$this->opts[$match[1]]=TRUE;
				} else{
					$this->invalid_option($o);
				}
			}else{
				stop("Invalid option syntax: $o. Should look like -o or -o=value or --option or --option=value.");
			}
		}
		
		//validate options
		foreach($this->opts as $k => $v){

			//ensure value is a valid option (when valid options are specified in $available_options
			if(is_array($this->available_options[$k]['options']) && !in_array($v, $this->available_options[$k]['options'])){
				$this->m->fatal("$v is not a valid option for -$k / --{$this->available_options[$k]['name']}.");
			}
			
			
		}
	}
	
	function opt($option){
		return $this->opts[$this->option_names[$option]];
	}
	
	function invalid_option($o){
		fatal("Parse error: option $o does not exist for the command {$this->args[0]}");
	}
		
	
}
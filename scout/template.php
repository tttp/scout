<?php
class template {
	function load_template($template){
		$template_path=SCOUT_PATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.'.template';
		if(file_exists($template_path)){
			file_get_contents($template_path);
		}else{
			message::fatal("could not find template {$template_path}");
		}
	}
	
	function parse($translation){
		foreach($translation as $s=>$r){
			$search[$x++]='{{{'.$s.'}}}';
			$replace[$x]=$r;
		}
		$this->parsed = str_replace($search , $replace , $this->unparsed);
		return $this->parsed;
	}
}
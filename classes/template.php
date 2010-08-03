<?php

class Template {
	private $registry;
	private $vars = array();

	function __construct($registry) {
		$this->registry = $registry;
	}

	function set($varname, $value, $overwrite=false) {
		if (isset($this->vars[$varname]) == true AND $overwrite == false) {
			trigger_error ('Unable to set var `' . $varname . '`. Already set, and overwrite not allowed.', E_USER_NOTICE);
			return false;
		}

		$this->vars[$varname] = $value;
		return true;
	}

	function remove($varname) {
		unset($this->vars[$varname]);
		return true;
	}

	function show($name) {
		$path = site_path . 'templates' . DIRSEP . $name . '.php';

		if (file_exists($path) == false) {
			trigger_error ('Template `' . $name . '` does not exist.', E_USER_NOTICE);
			return false;
		}
		
		// Load variables
		$this_controller = $this->registry['router']->this_controller;
		$this_action = $this->registry['router']->this_action;
		$is_authenticated = $this->registry['Auth']->check();
		foreach ($this->vars as $key => $value) {
			$$key = $value;
		}

		include ($path);		
	}

	function renderView($controller, $action) {
		$path = site_path . 'views' . DIRSEP . $controller . DIRSEP . $action . '.php';

		if (file_exists($path) == false) {
			trigger_error ('View `' .$controller.'/'.$name . '` does not exist.', E_USER_NOTICE);
			return false;
		}
		
		// Load variables
		$this_controller = $this->registry['router']->this_controller;
		$this_action = $this->registry['router']->this_action;
		foreach ($this->vars as $key => $value) {
			$$key = $value;
		}

		ob_start();
		include ($path);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;	
	}
	
	function getVars() {
		return $vars;
	}

}

?>
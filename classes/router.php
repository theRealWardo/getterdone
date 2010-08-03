<?php

Class Router {
	private $registry;
	private $path;
	private $args = array();
	private $redirect = NULL;
	public $this_controller;
	public $this_action;

	function __construct($registry) {
		$this->registry = $registry;
	}

	function redirect($redir) {
		header("Location: http://".$_SERVER['HTTP_HOST']."/".$redir);
		exit;
	}

	function setPath($path) {
		$path = DIRSEP.trim($path, '/\\');
		$path .= DIRSEP;

		if (is_dir($path) == false) {
			throw new Exception ('Invalid controller path: `' . $path . '`');
		}

		$this->path = $path;
	}

	function getArg($key = 0) {
		if (!isset($this->args[$key])) { return null; }
		return $this->args[$key];
	}

	function delegate() {
		// Analyze route
		$this->getController($file, $controller, $action, $args);
		
		// File available?
		if (is_readable($file) == false) {
			// default route to index
			$controller = "index";
			$action = "index";
			$file = $this->path.DIRSEP.$controller."_controller.php";
			$this->args = explode("/",trim($_GET['route'],'/\\'));
			//$this->notFound('no-file');
		}
		
		// Pass Controller and Action to view for navigational purposes
		$this->this_controller = $controller;
		$this->this_action = $action;

		// Include the file
		include ($file);

		// Initiate the class
		$class = 'Controller_' . $controller;
		$controller = new $class($this->registry);

		// Action available?
		if (is_callable(array($controller, $action)) == false) {
			// default route to index
			$action = "index";
			$this->args = explode("/",trim($_GET['route'],'/\\'));
			//$this->notFound('no-action');
		}
		
		// Run action
		$controller->$action();
		if (empty($this->redirect))
			$controller->render();
	}

	private function extractArgs($args) {
		if (count($args) == 0) { return false; }
		$this->args = $args;
	}
	
	private function getController(&$file, &$controller, &$action, &$args) {
		$route = (empty($_GET['route'])) ? '' : $_GET['route'];

		if (empty($route)) { $route = 'index'; }

		// Get separate parts
		$route = trim($route, '/\\');
		$parts = explode('/', $route);

		// Find right controller
		$cmd_path = $this->path;
		foreach ($parts as $part) {
			$fullpath = $cmd_path . $part;
			
			// Is there a dir with this path?
			if (is_dir($fullpath)) {
				$cmd_path .= $part . DIRSEP;
				array_shift($parts);
				continue;
			}

			// Find the file
			if (is_file($fullpath . '_controller.php')) {
				$controller = $part;
				array_shift($parts);
				break;
			}
		}

		if (empty($controller)) { $controller = 'index'; };

		// Get action
		$action = array_shift($parts);
		if (empty($action)) { $action = 'index'; }

		$file = $cmd_path . $controller . '_controller.php';
		$args = $parts;
		$this->args = $args;
	}

	private function notFound($err) {
		die("404 Not Found ".$err);
	}

}

?>
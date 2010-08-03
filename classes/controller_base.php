<?php

abstract class Controller_Base {
	protected $registry;
	private $template;
	public $Auth;
	public $DB;
	public $Router;
	public $Session;

	function __construct($registry) {
		$this->registry = $registry;
		$this->Auth = $this->registry['Auth'];
		$this->DB = $this->registry['DB'];
		$this->Router = $this->registry['router'];
		$this->Session = $this->registry['Session'];
	}

	abstract function index();
	
	function set($varname, $value, $overwrite=false) {
		$this->registry['template']->set($varname, $value, $overwrite=false);
	}
	
	function setTemplate($name) {
		$this->template = $name;
	}
	
	function render() {
		$this->getView();
		$this->beforeRender();
		$this->registry['template']->show($this->template);
	}
	
	function beforeRender() {
		if (!in_array("message",$this->registry['template']->getVars()))
			$this->set('message',$this->Session->getFlash());
	}
	
	function getView() {
		$buffer = $this->registry['template']->renderView($this->registry['router']->this_controller,$this->registry['router']->this_action);
		$this->set('main_content', $buffer);
	}
}

?>
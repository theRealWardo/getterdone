<?
class Controller_Index extends Controller_Base {

	function index() {
		$this->set('page_title', "Home");
		$this->setTemplate('main');
	}

}
?>

<?
class Controller_Main extends Controller_Base {

	function beforeRender() {
		if ($this->Router->getArg() == "ajax") {
			$this->setTemplate('ajax');
		} else {
			$this->setTemplate('main');
		}
	}
	
	function index() {
		if ($this->Auth->isMember())
			$this->Router->redirect("members");
		$this->set('page_title', "My ToDo List");
	}

	function developers() {
		// uses form helper
		$formHelper = new Form();
		$this->set("form",$formHelper);
		// fromHelper: process
		$formHelper->validAdd('name', 'NotEmpty', "Please include your a name");
		$formHelper->validAdd('email', 'EMail', "Invaild Email");
		if (isset($_POST['CRUD'])) {
			if ($formHelper->validate()) {
				$formHelper->process();
			} else {
				$this->set('message', $formHelper->getReason());
			}
		}
		$developers = DB::get_array("SELECT * FROM `contributors`");
		$this->set('developers', $developers);
		$this->set('page_title', "Developers");
		$this->setTemplate('main');	
	}

}
?>

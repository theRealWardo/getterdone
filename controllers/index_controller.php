<?
class Controller_Index extends Controller_Base {

	function beforeRender() {
		if ($this->Router->getArg() == "ajax") {
			$this->setTemplate('ajax');
		} else {
			$this->setTemplate('main');
		}
	}

	function index() {
		$this->set('page_title', "Home");
	}

	function setup() {
		$todos = 'CREATE TABLE `todos` ('
        . ' `short` VARCHAR(255) NOT NULL, '
        . ' `long` TEXT NOT NULL, '
        . ' `date` DATETIME NOT NULL, '
        . ' `owner` INT NOT NULL, '
        . ' `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,'
        . ' INDEX (`owner`)'
        . ' )'
        . ' ENGINE = myisam;';
		DB::query($todos);
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

	function about() {
	}

}
?>

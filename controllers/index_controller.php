<?
class Controller_Index extends Controller_Base {

	function index() {
		$this->set('page_title', "Home");
		$this->setTemplate('main');
	}

	function setup() {
		$sql = 'CREATE TABLE `contributors` ('
        . ' `name` VARCHAR(255) NOT NULL, '
        . ' `email` VARCHAR(255) NOT NULL, '
        . ' `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY'
        . ' )'
        . ' ENGINE = myisam;';
		DB::query($sql);
		$this->setTemplate('main');
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

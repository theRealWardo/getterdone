<?
class Session {
	private $registry;
	
	function __construct($registry) {
		$this->registry = $registry;
		$this->authSet();
		session_start();
	}
	
	function authSet() {
		if (!isset($_SESSION['auth']['check']))
			$_SESSION['auth']['check'] = false;
		if ($_SESSION['auth']['check'])
			$auth = $_SESSION['auth'];
		else
			$auth = false;
		return $auth;
	}
	
	function authLogin($usr_array) {
		$_SESSION['auth'] = $usr_array;
		$_SESSION['auth']['check'] = true;
	}
	
	function authLogout() {
		unset($_SESSION['auth']);
		$_SESSION['auth']['check'] = false;
	}
	
	function setFlash($message) {
		$_SESSION['flash'] = $message;
	}
	
	function getFlash() {
		if (!empty($_SESSION['flash'])) {
			$message = $_SESSION['flash'];
			unset($_SESSION['flash']);
			return $message;
		}else
			return false;
	}
}

?>
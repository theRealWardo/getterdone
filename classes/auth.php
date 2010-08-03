<?
class Auth {
	private $registry;
	private $auth_table = "users";
	private $user_id;
	private $username;
	private $password;
	private $admin = false;
	private $member = false;
	private $logged_in;
	private $redirect = "members/index";
	private $Session;
	private $Router;
	
	function __construct($registry) {
		$this->registry = $registry;
		$this->Session = $this->registry['Session'];
		$this->Router = $this->registry['router'];
		$this->logged_in = false;
		$auth = $this->Session->authSet();
		if ($auth){
			$this->user_id = $auth['user_id'];
			$this->username = $auth['user'];
			$this->password = $auth['pass'];
			$this->admin = $auth['admin'];
			$this->member = $auth['member'];
			$this->logged_in = true;
		}
	}
	
	function login($check = false) {
		// This function handles login if posted at $_POST['username'] and $_POST['password']
		$user = DB::safe($_POST['username']);
		$pass = sha1($_POST['password']);
		$user_data = DB::get_row("SELECT * FROM ".$this->auth_table." WHERE `username`='".$user."'");
		if ($user_data['password']== $pass) {
			$this->username = $user;
			$this->password = $pass;
			$user_id = $user_data['id'];
			$this->user_id = $user_id;
			$this->logged_in = true;
			if ($user_data['admin'] == 1)
				$this->admin = true;
			$admin = $this->admin;
			if ($user_data['member'] == 1)
				$this->member = true;
			$member = $this->member;
			if (!$check){
				$this->Session->authLogin(compact('user','pass','admin','member','user_id'));
				$this->Router->redirect($this->redirect);
			}
		}
		return $this->logged_in;
	}
	
	function logout() {
		$this->username = NULL;
		$this->password = NULL;
		$this->logged_in = false;
		$this->Session->authLogout();
	}
	
	function check() {
		return $this->logged_in;
	}
	
	function getUsername() {
		return $this->username;
	}
	
	function getUserID() {
		return $this->user_id;
	}
	
	function getPassword() {
		return $this->password;
	}
	
	function isAdmin() {
		return $this->admin;
	}

	function isMember() {
		return $this->member;
	}
}
?>

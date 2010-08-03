<?
include (site_path.DIRSEP.'lib'.DIRSEP.'recaptcha'.DIRSEP.'recaptchalib.php');

class Form {
	private $table;
	private $id;
	private $id_field;
	public $data;
	private $valid_error;
	private $updated = false;
	private $captcha = false;
	private $captcha_privatekey = "6Lcy-AQAAAAAAGjmEOOWlP9N9UmP-xUwwaHjgt4H";
	private $captcha_publickey = "6Lcy-AQAAAAAAATghsmGPzchqJcFV7pfalcjqPWI";
	private $validate = array();
	private $fail = array();
	private $no_db = array("CRUD", "idf", "id", "tbl", "x", "y","datetime", "updated");
	
	function __construct() {
		$this->data = $_POST;
		foreach ($_POST['datetime'] as $select){
			$this->data[$select] = "";
			$this->noDB($select.'_y');
			if(!empty($this->data[$select.'_y']))
				$this->data[$select] .= $this->data[$select.'_y'];
			else
				$this->data[$select] .= "0000";
			$this->data[$select] .= "-";
			$this->noDB($select.'_m');
			if(!empty($this->data[$select.'_m']))
				$this->data[$select] .= $this->data[$select.'_m'];
			else
				$this->data[$select] .= "00";
			$this->data[$select] .= "-";
			$this->noDB($select.'_d');
			if(!empty($this->data[$select.'_d']))
				$this->data[$select] .= $this->data[$select.'_d'];
			else
				$this->data[$select] .= "00";
			$this->data[$select] .= " ";
			$this->noDB($select.'_h');
			$this->noDB($select.'_a');
			if(!empty($this->data[$select.'_h'])){
				if ($this->data[$select.'_a'] == "pm")
					$this->data[$select.'_h'] += 12;
				elseif (($this->data[$select.'_a'] == "am") && ($this->data[$select.'_h'] == 12))
					$this->data[$select.'_h'] = 0;
				$this->data[$select] .= $this->data[$select.'_h'];
			}else{
				$this->data[$select] .= "00";
			}
			$this->data[$select] .= ":";
			$this->noDB($select.'_i');
			if(!empty($this->data[$select.'_i']))
				$this->data[$select] .= $this->data[$select.'_i'];
			else
				$this->data[$select] .= "00";
			$this->data[$select] .= ":";
			$this->noDB($select.'_s');
			if(!empty($this->data[$select.'_s']))
				$this->data[$select] .= $this->data[$select.'_s'];
			else
				$this->data[$select] .= "00";
		}
		if (!empty($this->data['recaptcha_challenge_field'])) {
			$this->noDB('recaptcha_challenge_field');
			$this->noDB('recaptcha_response_field');
			$this->captcha = true;
		}
	}
	
	function create($table_name) {
		$this->table = $table_name;
		return '<form action="" method="post">
		<input type="hidden" name="CRUD" value="C" />
		<input type="hidden" name="tbl" value="'.$this->table.'" />';
	}
	
	function createMail($f) {
		return '<form action="" method="post">
		<input type="hidden" name="CRUD" value="mail" />
		<input type="hidden" name="f" value="'.$f.'" />';
	}
	
	function createFile($options = array()) {
		$optionkeys = array_keys($options);
		if (in_array("action",$optionkeys))
			$action = $options['action'];
		else
			$action = "";
		if (in_array("method",$optionkeys))
			$method = $options['method'];
		else
			$method = "post";
		$action = $options['action'];
		return '<form action="'.$action.'" method="'.$method.'" enctype="multipart/form-data">';
	}
	
	function update($table_name, $id, $id_fld = "id") {
		$this->table = $table_name;
		$this->id = $id;
		$this->id_field = $id_fld;
		$this->data = DB::get_row("SELECT * FROM `".$this->table."` WHERE `".$this->id_field."`='".$this->id."'");
		if(empty($this->data)){
			$CRUD = "C";
			if (!empty($this->id))
				$append = '
		<input type="hidden" name="'.$this->id_field.'" value="'.$this->id.'" />';
		}else{
			$CRUD = "U";
		}
		$return = '<form action="" method="post">
		<input type="hidden" name="CRUD" value="'.$CRUD.'" />
		<input type="hidden" name="tbl" value="'.$this->table.'" />
		<input type="hidden" name="idf" value="'.$this->id_field.'" />
		<input type="hidden" name="id" value="'.$this->id.'" />'.$append;
		return $return;
	}
	
	function crudForm($options = array()) {
		$optionkeys = array_keys($options);
		if (in_array("action",$optionkeys))
			$action = $options['action'];
		else
			$action = "";
		if (in_array("method",$optionkeys))
			$method = $options['method'];
		else
			$method = "post";
		$action = $options['action'];
		$return =  '<form action="'.$action.'" method="'.$method.'"';
		if (in_array("file",$optionkeys))
			$return .= ' enctype="multipart/form-data"';
		$return .= '>';
		$return .= '<input type="hidden" name="CRUD" value="'.$options['CRUD'].'" />
		<input type="hidden" name="tbl" value="'.$options['table'].'" />
		<input type="hidden" name="idf" value="'.$options['id_field'].'" />
		<input type="hidden" name="id" value="'.$options['id'].'" />'.$options['append'];
		return $return;
	}	
	function hidden($value, $field) {
		return '<input type="hidden" name="'.$field.'" value="'.$value.'" />';
	}
	
	function captcha() {
		$this->captcha = true;
		return '<div class="captcha">'.recaptcha_get_html($this->captcha_publickey).'</div>';
	}
	
	function text($label, $field, $value = null) {
		if (empty($value))
			$value = $this->data[$field];
		return '<div class="label">'.$label.'</div>
		<div class="input"><input type="text" name="'.$field.'" value="'.$value.'" /></div>';
	}
	
	function inputFile($label, $field) {
		return '<div class="label">'.$label.'</div>
		<div class="file"><input type="file" name="'.$field.'" /></div>';
	}
	
	function password($label, $field) {
		return '<div class="label">'.$label.'</div>
		<div class="input"><input type="password" name="'.$field.'" /></div>';
	}
	
	function textarea($label, $field) {
		return '<div class="label">'.$label.'</div>
		<div class="input"><textarea name="'.$field.'">'.stripslashes($this->data[$field]).'</textarea></div>';
	}
	
	function datetime($label, $field, $format, $increments = 1) {
		$return = '<div class="label">'.$label.'</div>
		<input type="hidden" name="datetime[]" value="'.$field.'" />
		<div class="input">';
		if (!empty($this->data[$field])){
			$datetime = explode(" ",$this->data[$field]);
			$date = explode("-",$datetime[0]);
			$time = explode(":",$datetime[1]);
			$this->data[$field.'_y'] = (int) $date[0];
			$this->data[$field.'_m'] = (int) $date[1];
			$this->data[$field.'_d'] = (int) $date[2];
			$this->data[$field.'_h'] = (int) $time[0];
			$this->data[$field.'_i'] = (int) $time[1];
			$this->data[$field.'_s'] = (int) $time[2];
			$this->data[$field.'_a'] = "am";
			if ($this->data[$field.'_h'] > 12) {
				$this->data[$field.'_h'] -= 12;
				$this->data[$field.'_a'] = "pm";
			}
			
		}
		if (strpos($format,'m') !== false){
			// include month
			$month = array("January","February","March","April","May","June","July","August","September","October","November","December");
			$return .= '<div class="smonth"><select name="'.$field.'_m">';
			for ($i = 1; $i <= count($month); $i++){
				$return .= '<option value="'.$i.'"';
				if ($this->data[$field.'_m'] == $i)
					$return .= ' selected="selected"';
				elseif (date('n') == $i && empty($this->data[$field.'_m']))
					$return .= ' selected="selected"';
				$return .= '>'.$month[$i-1].'</option>';
			}
			$return .= '</select></div>';
		}
		if (strpos($format,'d') !== false){
			// include day
			$return .= '<div class="sday"><select name="'.$field.'_d">';
			for ($i = 1; $i <= 31; $i++){
				$return .= '<option value="'.$i.'"';
				if ($this->data[$field.'_d'] == $i)
					$return .= ' selected="selected"';
				elseif (date('j') == $i && empty($this->data[$field.'_d']))
					$return .= ' selected="selected"';
				$return .= '>'.$i.'</option>';
			}
			$return .= '</select></div>';
		}
		if (strpos($format,"y") !== false){
			// include year
			$return .= '<div class="syear"><select name="'.$field.'_y">';
			for ($i = 1910; $i <= date('Y') + 20; $i++){
				$return .= '<option value="'.$i.'"';
				if ($this->data[$field.'_y'] == $i)
					$return .= ' selected="selected"';
				elseif (date('Y') == $i && empty($this->data[$field.'_y']))
					$return .= ' selected="selected"';
				$return .= '>'.$i.'</option>';
			}
			$return .= '</select></div>';
		}
		if (strpos($format,"t") !== false){
			// include hours
			$return .= '<div class="shour"><select name="'.$field.'_h">';
			for ($i = 0; $i <= 12; $i++){
				$return .= '<option value="'.$i.'"';
				if ($this->data[$field.'_h'] == $i)
					$return .= ' selected="selected"';
				elseif (date('g') == $i && empty($this->data[$field.'_h']))
					$return .= ' selected="selected"';
				$return .= '>'.$i.'</option>';
			}
			$return .= '</select></div>';
			// include minutes
			$return .= '<div class="smin"><select name="'.$field.'_i">';
			for ($i = 0; $i <= 59; $i++){
				if ($increments != 1){
					if ($i % $increments == 0){
						$return .= '<option value="'.$i.'"';
						if ($this->data[$field.'_i'] == $i)
							$return .= ' selected="selected"';
						elseif (date('i') == $i && empty($this->data[$field.'_i']) && ($this->data[$field.'_i'] != "0"))
							$return .= ' selected="selected"';
						if ($i < 10)
							$return .= '>0'.$i.'</option>';
						else
							$return .= '>'.$i.'</option>';
					}
				}else{
					$return .= '<option value="'.$i.'"';
					if ($this->data[$field.'_i'] == $i)
						$return .= ' selected="selected"';
					elseif (date('i') == $i && empty($this->data[$field.'_i']) && ($this->data[$field.'_i'] != "0"))
						$return .= ' selected="selected"';
					if ($i < 10)
						$return .= '>0'.$i.'</option>';
					else
						$return .= '>'.$i.'</option>';
				}
			}
			$return .= '</select></div>';
			// include am/pm
			$return .= '<div class="sante"><select name="'.$field.'_a">
				<option value="am"';
			if ($this->data[$field.'_a'] == "am")
				$return .= ' selected="selected"';
			elseif (date('a') == "am" && empty($this->data[$field.'_a']))
				$return .= ' selected="selected"';
			$return .= '>am</option>
				<option value="pm"';
			if ($this->data[$field.'_a'] == "pm")
				$return .= ' selected="selected"';
			elseif (date('a') == "pm" && empty($this->data[$field.'_a']))
				$return .= ' selected="selected"';
			$return .= '>pm</option>';
			$return .= '</select></div>';
		}
		$return .= '<div class="clear_fix"></div></div>';
		return $return;
	}
	
	function submit($label) {
		return '<div class="submit"><input type="submit" value="'.$label.'" /></div>
		</form>';
	}
	
	function submitImage($image) {
		return '<div class="submit"><input type="image" src="'.$image.'" /></div>
		</form>';
	}
	
	function process() {
		switch ($this->data['CRUD']) {
		case "C":
			// CRUD: Create
			$keys = array_keys($this->data);
			$queries = array();
			$prams = array();
			foreach ($keys as $key) {
				if ($key == "password")
					$this->data[$key] = sha1($this->data[$key]);
				if ($key == "updated")
					$this->updated = true;
				if (is_array($this->data[$key]))
					$this->data[$key] = implode(", ",$this->data[$key]);
				if (($this->data[$key] == "NULL") && (is_string($this->data[$key])))
					$queries[] = "`".DB::safe($key)."` = null";
				elseif (!in_array($key,$this->no_db))
					$queries[] = "`".DB::safe($key)."`='".DB::safe($this->data[$key])."'";
			}
			$query = "INSERT INTO `".DB::safe($this->data['tbl'])."` SET ".implode(", ",$queries);
			if ($this->updated)
				$query .= ", `updated` = NOW()";
			if (SQUERIES)
				echo $query;
			if (DB::query($query))
				return true;
			else
				return false;
				
			break;
			
		case "U":
			// CRUD: Update
			$keys = array_keys($this->data);
			$queries = array();
			$prams = array();
			foreach ($keys as $key) {
				if ($key == "password")
					$this->data[$key] = sha1($this->data[$key]);
				if ($key == "updated")
					$this->updated = true;
				if (is_array($this->data[$key]))
					$this->data[$key] = implode(", ",$this->data[$key]);
				if (($this->data[$key] == "NULL") && (is_string($this->data[$key])))
					$queries[] = "`".DB::safe($key)."` = null";
				elseif (!in_array($key,$this->no_db))
					$queries[] = "`".DB::safe($key)."`='".DB::safe($this->data[$key])."'";
			}
			$query = "UPDATE `".DB::safe($this->data['tbl'])."` SET ".implode(", ",$queries);
			if ($this->updated)
				$query .= ", `updated` = NOW()";
			$query .= " WHERE `".DB::safe($this->data['idf'])."`='".DB::safe($this->data['id'])."'";
			if (SQUERIES)
				echo $query;
			if (DB::query($query))
				return true;
			else
				return false;
				
			break;
			
		case "mail":
			// E-Mail Form
			require(site_path.'lib/phpmailer/class.phpmailer.php');
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Host = "mail.mwdesigns.com:26;mail.mwdesigns.com;mail.bergencatholic.org;mail.gmail.com;mail.optonline.net";
			$mail->From = "noreply@columbiabartending.com";
			$mail->FromName = "CBA Website";
			$mail->AddAddress($this->data['f']);
			$mail->Subject = $this->data['subject'];
			$data_keys = array_keys($this->data);
			foreach ($data_keys as $key){
				if (!in_array($key,$this->no_db))
					$mail->Body .= $key.":		".$_POST[$key]."

";
			}
			if (!$mail->send())
				return 'Unable to send e-mail. Please alert us of this issue by<br /><a href="mailto:support@mwdesigns.com">e-mailing MWDesigns Support</a><br/>Error Info: '.$mail->ErrorInfo;
			else
				return "E-Mail sent successfully.<br/>Someone from the admissions office will contact you soon.";
				
			break;
		}
		
	}
	
	// Validation methods
	function validAdd($field, $type, $fail) {
		$i = 0;
		while (array_key_exists($field."__".$i,$this->validate))
			$i++;
		$this->validate[$field."__".$i] = $type;
		$this->fail[$field."__".$i] = $fail;
	}
	
	function validCheck($field) {
		$fields = explode("__",$field);
		$datafield = $fields[0];
		$actions = explode(" ",$this->validate[$field]);
		switch ($actions[0]){
		case "EMail":
			if (eregi("^[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9]@[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$",$this->data[$datafield]))
				return true;
			else
				return false;
			break;
		case "NotEmpty":
			if (!empty($this->data[$datafield]))
				return true;
			else
				return false;
			break;
		case "NotIn":
			if (count(DB::get_array("SELECT `id` FROM `".DB::safe($actions[1])."` WHERE `".$datafield."`='".$this->data[$datafield]."'")) > 0)
				return false;
			else
				return true;
			break;
		case "Match":
			if ($this->data[$datafield] == $this->data[$actions[1]])
				return true;
			else
				return false;
			break;
		case "Partner":
			if (!empty($this->data[$datafield]))
				return true;
			elseif (empty($this->data[$datafield]) && !empty($this->data[$actions[1]]))
				return true;
			else
				return false;
			break;
		}
	}
	
	function validate() {
		$keys = array_keys($this->validate);
		foreach ($keys as $key){
			if (!$this->validCheck($key)){
				$this->valid_error = $this->fail[$key];
				return false;
			}
		}
		if ($this->captcha){
			$resp = recaptcha_check_answer ($this->captcha_privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $this->data["recaptcha_challenge_field"],
                                        $this->data["recaptcha_response_field"]);
			if (!$resp->is_valid) {
				$this->valid_error = "The reCAPTCHA wasn't entered correctly.<br/>Please try again.";
				return false;
			}
		}
		return true;
	}
	
	function getReason() {
		return $this->valid_error;
	}
	
	function noDB($nodb) {
		$this->no_db[] = $nodb;
	}
	
	function getValue($key) {
		return $this->data[$key];
	}
}

?>

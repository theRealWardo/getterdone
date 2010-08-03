<?
class DB {

	function __construct() {
		if (!($connection = mysql_connect(DB_HOST, DB_USER, DB_PASS))){
			echo 'Unable to connect to database host';
		}else{
			if (!mysql_select_db(DB_BASE)) {
				echo 'Unable to connect to database';
			}
		}
	}
	
	function safe($str_to_safe) {
		return mysql_real_escape_string($str_to_safe);
	}
	
	function value($query) {
		$result = mysql_query($query);
		$info = mysql_result($result,0);
		if (!empty($info))
			return $info;
		else
			return false;
	}
	
	function get_array($query) {
		$result = mysql_query($query);
		$results = array();
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			$results[] = $row;
		return $results;
	}
	
	function get_row($query) {
		$tdata = DB::get_array($query);
		return $tdata[0];
	}
	
	function query($query) {
		return mysql_query($query);
	}
}
?>
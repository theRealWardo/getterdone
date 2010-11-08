<?
class Cache {
	public $cachePath;
	
	function __construct() {
		$this->cachePath = 'cache'.DIRSEP;
	}
	
	function set($keyName, $value, $timeInMin) {
		$file =  site_path . $this->cachePath . $keyName . ".fcache";
		$fhandle = fopen($file, "w");
		fwrite($fhandle, (time() + ($timeInMin * 60))."##//##");
		fwrite($fhandle, $value);
		fclose($fhandle);
	}
	
	function get($keyName) {
		$file =  site_path . $this->cachePath . $keyName . ".fcache";
		if (file_exists($file)) {
			$fhandle = fopen($file, "r");
			$aValue = fread($fhandle, filesize($file));
			fclose($fhandle);
			$values = explode("##//##",$aValue);
			if ($values[0] < time())
				return false;
			else
				return $value[1];
		}
		return false;
	}
}
?>
<?
error_reporting (E_ALL);
if (version_compare(phpversion(), '5.1.0', '<') == true) { die ('PHP5.1 Only'); }

// Debug
define ('SQUERIES', false);

// Mailer Contact
define ('MAILER_TARGET_ADDRESS', 'getterdone@mwdesigns.com');

// Database Config
define ('DB_HOST', 'localhost');
define ('DB_USER', 'root');
define ('DB_PASS', 'root');
define ('DB_BASE', 'getterdone');

// Constants:
define ('DIRSEP', DIRECTORY_SEPARATOR);
define ('SITE_TITLE', "GetterDone");

// Get site path
$site_path = realpath(dirname(__FILE__) . DIRSEP . '..' . DIRSEP) . DIRSEP;
define ('site_path', $site_path);

// For loading classes
function __autoload($class_name) {
	$filename = strtolower($class_name) . '.php';
	$file = site_path . 'classes' . DIRSEP . $filename;

	if (file_exists($file) == false) { 
		return false;
	}

	include ($file);
}

$registry = new Registry;
?>

<?

# Startup tasks
require 'includes/startup.php';

# Load session object
$session = new Session($registry);
$registry->set('Session', $session);

# Load database object
$DB = new DB();
$registry->set('DB', $DB);

# Load template object
$template = new Template($registry);
$registry->set('template', $template);

# Load router
$router = new Router($registry);
$registry->set('router', $router);

# Load authentication object
$auth = new Auth($registry);
$registry->set('Auth', $auth);


$router->setPath(site_path . 'controllers');

$router->delegate();
?>
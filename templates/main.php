<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="description" content="A completely free ToDo application built as a sample application for an open source PHP MVC framework." />
<title>GetterDone - <?=$page_title?></title>
<link rel="stylesheet" href="/css/main.css" />
<link rel="shortcut icon" href="/favicon.ico" />
<script type="text/javascript" src="/js/jquery.js" /></script> 
<!--[if IE]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

</head>
<body>

<div id="container">

<header id="page-header">
	<div id="logo"><a href="/" title="GetterDone Home"><img src="/images/logo.png" alt="GetterDone"></a></div>
	<nav id="main-navigation">
		<ul>
       <li<?=($this->registry['router']->this_action == "index" && $this->registry['router']->this_controller == "index")?' class="active"':''?>><a href="/" id="home" title="GetterDone Home">Home</a></li>
       <li<?=($this->registry['router']->this_action == "developers" && $this->registry['router']->this_controller == "index")?' class="active"':''?>><a href="/developers" id="developers" title="For Developers">Developers</a></li>
       <li<?=($this->registry['router']->this_action == "about" && $this->registry['router']->this_controller == "index")?' class="active"':''?>><a href="/about" id="about" title="About GetterDone">About</a></li>
		</ul>
		<? if ($is_authenticated): ?>
		<div class="button" id="logout"><a href="/logout" title="Logout of GetterDone">Logout</a></div>
		<? else: ?>
		<div class="button" id="login"><a href="/login" title="Login to GetterDone">Login</a></div>
		<? endif ?>
	</nav>
</header>

<?=$main_content?>

</div>

</body>
</html>

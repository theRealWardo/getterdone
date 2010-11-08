<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="description" content="A completely free ToDo application built as a sample application for an open source PHP MVC framework." />
<title><?=SITE_TITLE?> - <?=$page_title?></title>
<link rel="stylesheet" href="/css/main.css" />
<link rel="shortcut icon" href="/favicon.ico" />
<script type="text/javascript" src="/js/js-mvc.js" /></script>
<script type="text/javascript" src="/js/getterdone-main.js" /></script>
<!--[if IE]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

</head>
<body onload="startupMVC()">

<div id="container">

<header id="page-header">
	<div id="logo"><a href="/index" title="<?=SITE_TITLE?> Home" onclick="navigateTo(this.href); return false;"><img src="/images/logo.png" alt="<?=SITE_TITLE?>"></a></div>
	<nav>
		<ul id="main-navigation">
       <li<?=($this->registry['router']->this_action == "index" && $this->registry['router']->this_controller == "index")?' class="active"':''?>><a href="/" id="home" title="<?=SITE_TITLE?> Home" onclick="navigateTo('/index'); return false;">Home</a></li>
       <li<?=($this->registry['router']->this_action == "developers" && $this->registry['router']->this_controller == "index")?' class="active"':''?>><a href="/index/developers" id="developers" title="For Developers" onclick="navigateTo('/index/developers'); return false;">Developers</a></li>
       <li<?=($this->registry['router']->this_action == "about" && $this->registry['router']->this_controller == "index")?' class="active"':''?>><a href="/index/about" id="about" title="About <?=SITE_TITLE?>" onclick="navigateTo('/index/about'); return false;">About</a></li>
		</ul>
		<? if ($is_authenticated): ?>
		<div class="button" id="logout"><a href="/logout" title="Sign out of <?=SITE_TITLE?>">Sign Out</a></div>
		<? else: ?>
		<div class="button" id="login"><a href="/login" title="Sign in to <?=SITE_TITLE?>">Sign In</a></div>
		<? endif ?>
	</nav>
</header>

<? if (!empty($message)): ?>
<div class="message"><?=$message?></div>
<? endif ?>

<div id="main-content">
<?=$main_content?>
</div>

</div>

</body>
</html>

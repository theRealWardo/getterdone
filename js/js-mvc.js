// the main instance of a router
var route;
// the main instance of the registry
var reg;
// the main instance of the authentication handler
var auth = new Auth();

/**
 * A way to store variables that are shared between classes.
 */
function Registry() {
	this.vars = Array();
	this.paths = Array();
	this.routes = {
		'index': indexController,
		'main': mainController,
		'folder': folderController,
		'todo': todoController
	};
	this.template = {};
}

/**
 * Actually handle the getting and setting of a var
 */
Registry.prototype.set = function(key, val) {
	this.vars[key] = val;
}

/**
 * 
 */
Registry.prototype.get = function(key) {
	return this.vars[key];
}

/**
 * The thing that actually processes each request.
 */
function Router(regi) {
	this.registry = regi;
	this.registry.firstRender = true;
}

/**
 * Delegate is the start of the whole process.
 */
Router.prototype.delegate = function() {
	var loc;
	var controller;
	var action;
	// parse the window.location
	if (String(window.location).indexOf('!', 0) >= 0) {
		loc = String(window.location).substr(String(window.location).indexOf('!', 0) + 2);
		// now load the controller and its function
		controller = loc.substr(0, loc.indexOf('/', 0));
		action = loc.substr(loc.indexOf('/', 0) + 1);
	}
	if (action && !controller) {
		// page refresh or something?
		controller = action;
		action = 'index';
	}
	if (!controller || controller.length <=0) {
		// index and index...
		controller = 'index'; 
		action = 'index';
	} else if (!action || action.length <= 0) {
		// index
		action = 'index';
	}
	// Check the an argument format /controller/view/arg
	if (action.indexOf('/', 0) >= 0) {
		this.registry.arg = action.substr(action.indexOf('/', 0) + 1);
		action = action.substr(0, action.indexOf('/', 0));
	}
	this.registry.controller = controller;
	this.registry.action = action;
	if (this.registry.routes[controller] && this.registry.routes[controller][action]) {
		activeController = this.registry.routes[controller];
		if (activeController['beforeAction']) {
			activeController['beforeAction']();
		}
		// Default the render to on, controller action may override for forwarding
		this.registry.template.render = true;
		if (typeof activeController[action] == 'function') {
			activeController[action]();
		} else {
			// For some reason our controller/action pair didnt match up.
			// Quietly handle this error by not rendering...
			this.registry.template.render = false;
		}
		if (this.registry.template.render && this.registry.template.loaded) {
			this.registry.template['update'].apply(this.registry);
		} else if (this.registry.template.render) {
			this.registry.template['load'].apply(this.registry);
		}
	} else {
		throw new Error('failed to find controller or action');
	}
	this.registry.firstRender = false;
}

/**
 * Handle the creation of a template.
 */
Router.prototype.setTemplate = function(name) {
	if (name == 'info') {
		this.registry.template = new InfoTemplate();
	} else if (name == 'list') {
		this.registry.template = new ListTemplate();
	} else if (name == 'overlay') {
		this.registry.template = new OverlayTemplate();
	}
	window.onresize = this.registry.template.resize;
}

/**
 * The handler for authentication.
 */
function Auth() {
	this.user_id = null;
	this.user_email = null;
	this.isUser = false;
	this.isNewbie = false;
}

/**
 * Login was a success with these credentials.
 */
Auth.prototype.loginSuccess = function(id, email) {
	this.user_id = id;
	this.user_email = email;
	this.isUser = true;
}

/**
 * The dark magic shorthand
 */
$ = function(id) {
	return document.getElementById(id);
}

/**
 * A way to handle simple ajax requests
 */
$.ajax = function(path, callback, scope) {
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			callback.call(scope, xmlhttp.responseText);
		}
	}
	xmlhttp.open('GET', path, true);
	xmlhttp.send();
}

/**
 * A shorthand for ation.
 */
navigateTo = function(uri) {
	// if the url starts with http, parse it off
	if (uri.substr(0, 4) == "http") {
		uri = uri.substr(uri.indexOf('/', 7));
	}
	reg.paths.push(uri);
	window.location = '#!' + uri;
	route.delegate();
	reg.paths.pop();
	return false;
}

/**
 * Called when the body finishes loading.
 */
startupMVC = function() {
	reg = new Registry();
	route = new Router(reg);
	route.delegate();
}

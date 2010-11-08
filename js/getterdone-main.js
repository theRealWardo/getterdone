// Create an array that will store the todos
var GDTodos = new Array();
// Storage of the folders data
var GDTFolders = [{'title':'All Folders', 'color':0, 'id':'ALL'}];
// A list of color themes for folders.
var GDTColors = [{'background':'#000000', 'text':'#FFFFFF'},
	{'background':'#006600', 'text':'#FFFFFF'},
	{'background':'#000066', 'text':'#FFFFFF'},
	{'background':'#660066', 'text':'#FFFFFF'},
	{'background':'#006666', 'text':'#FFFFFF'},
	{'background':'#666600', 'text':'#FFFFFF'},
	{'background':'#66CCFF', 'text':'#000000'},
	{'background':'#FFFFFF', 'text':'#000000'},
	{'background':'#666666', 'text':'#FFFFFF'}];

indexController = new IndexController();
mainController = new MainController();
folderController = new FolderController();
todoController = new TodoController();

/**
 * A quick way to access a GDTFolder by name/title
 */
getFolder = function(name) {
	for (var i=0; i<GDTFolders.length; i++) {
		if (GDTFolders[i].id == name) {
			return GDTFolders[i];
		}
	}
	return null;
}

/**
 * Return the next id for adding a new folder.
 */
nextFolderId = function() {
	var x = 0;
	for (var i in GDTFolders) {
		if (GDTFolders[i].id != 'ALL' && GDTFolders[i].id > x) {
			x = GDTFolders[i].id;
		}
	}
	return ++x;
}

/**
 * Return the selected folder's id.
 */
getSelectedFolderId = function() {
	var selected = 'ALL';
	for (var i in GDTFolders) {
		if ($('folder-selected').innerHTML == GDTFolders[i].title) {
			selected = GDTFolders[i].id;
		}
	}
	return selected;
}

/**
 * A quick way to try and get a todo
 */
getTodo = function(id) {
	for (var i in GDTodos) {
		if (GDTodos[i].id == id) {
			return GDTodos[i];
		}
	}
	return null;
}

/**
 * Return the next id for adding a new folder.
 */
nextTodoId = function() {
	var x = 0;
	for (var i in GDTodos) {
		if (GDTodos[i].id > x) {
			x = GDTodos[i].id;
		}
	}
	return ++x;
}

/**
 * Return a random next color theme.
 */
nextColor = function() {
	return Math.floor(Math.random() * GDTColors.length);
}

/**
 * The index controller routes
 */
function IndexController() {
}

/**
 * Gets called before each indexController action.
 */
IndexController.prototype.beforeAction = function() {
	// default the template to info
	route.setTemplate('info');
}

/**
 * The landing page handler.
 */
IndexController.prototype.index = function() {
	if (!reg.firstRender) {
		$.ajax('/index/index/ajax', function(data) {
			$('main-content').innerHTML = data;
		}, this);
	}
}

/**
 * The developer page handler.
 */
IndexController.prototype.developers = function() {
	$.ajax('/index/developers/ajax', function(data) {
		$('main-content').innerHTML = data;
	}, this);
}

/**
 * The about page handler.
 */
IndexController.prototype.about = function() {
	$.ajax('/index/about/ajax', function(data) {
		$('main-content').innerHTML = data;
	}, this);
}

/**
 * The main controller is the document browser.
 */
function MainController() {
}

/**
 * Set the default template and things.
 */
MainController.prototype.beforeAction = function() {
	// Default the template to list
	route.setTemplate('list');
}

/**
 * Users go right to the view of all folders
 */
MainController.prototype.index = function() {
	navigateTo('/main/view/ALL');
}

/**
 * Display the todos in a folder
 */
MainController.prototype.view = function() {
	// Set the selected value to all documents
	reg.template.selectedFolder = getFolder(reg.arg);
}

/**
 * The folder controller controls the folder objects.
 */
function FolderController() {
}

/**
 * Set the template.
 */
FolderController.prototype.beforeAction = function() {
	// Default the template to overlay
	route.setTemplate('overlay');
}

/**
 * The prompt to add a new folder
 */
FolderController.prototype.new = function() {
	// Folder selector finder
	var selectedId = getSelectedFolderId();
	reg.template.content = '<h3>New Folder</h3><input type="text" id="new-folder-name" />' +
		'<div class="buttons">' +
		'<div class="button" style="float:left;"><a href="/folder/add" onclick="folderController.add($(\'new-folder-name\').value); return false;">OK</a></div>' +
		'<div class="button" style="float:right;"><a href="/main/view/'+selectedId+'" onclick="return navigateTo(this.href)">Cancel</a></div>' +
		'</div>';
	reg.template.contentHeight = 130;
}

/**
 * The handler for adding a new folder
 */
FolderController.prototype.add = function(name) {
	// TODO: add the ability to change the folder color
	var nextId = nextFolderId();
	var color = nextColor();
	GDTFolders.push({'title':name, 'color':color, 'id':nextId});
	// Go to the new folder.
	navigateTo('/main/view/' + nextId);
}

/**
 * The handler for deleting a folder
 */
FolderController.prototype.delete = function() {
	if (confirm('Are you sure you want to delete this folder and all its contents?')) {
		// First remove the actual items
		var dFolder = getFolder(reg.arg);
		for (var i=0; i<GDTodos.length; i) {
			if (GDTodos[i].folder == dFolder.id) {
				GDTodos.splice(i, 1);
			} else {
				i++;
			}
		}
		// Now we can remove the folder itself
		for (var i=0; i<GDTFolders.length; i++) {
			if (GDTFolders[i].id == dFolder.id) {
				GDTFolders.splice(i, 1);
				// Escape the loop
				i = GDTFolders.length;
			}
		}
		// Go to the main page
		navigateTo('/main');
	} else {
		// Prevent rendering.
		reg.template.render = false;
	}
	// A trick to prevent double event propogation.
	reg.folderAction = true;
}

/**
 * A display for the color picker.
 */
FolderController.prototype.color = function() {
	reg.template.content = '<h3>Select A Color</h3><div class="colors">';
	for (var i in GDTColors) {
		reg.template.content += '<a href="/folder/setColor/'+reg.arg+'-'+i+
			'" style="color:' +GDTColors[i].text+
			'; background:' +GDTColors[i].background+ '" onclick="return navigateTo(this.href)">ABC</a>';
	}
	var selectedId = getSelectedFolderId();
	reg.template.content += '</div><div class="buttons">' +
		// TODO: add support for custom themes
		//'<div class="button" style="float:left;"><a href="/folder/add" onclick="folderController.add($(\'new-folder-name\').value); return false;">Custom</a></div>' +
		'<div class="button" style="float:right;"><a href="/main/view/'+selectedId+'" onclick="return navigateTo(this.href)">Cancel</a></div>' +
		'</div>';
	reg.template.contentHeight = 240;
}

/**
 * Handler for updating a folder's color
 */
FolderController.prototype.setColor = function() {
	var folderId = reg.arg.substr(0, reg.arg.indexOf('-'));
	var colorId = reg.arg.substr(reg.arg.indexOf('-') + 1);
	for (var i in GDTFolders) {
		if (GDTFolders[i].id == folderId) {
			GDTFolders[i].color = colorId;
		}
	}
	navigateTo('/main/view/'+getSelectedFolderId());
}

/**
 * The todo controller controls the todo objects.
 */
function TodoController() {
}

/**
 * Set the template.
 */
TodoController.prototype.beforeAction = function() {
	// Default the template to overlay
	route.setTemplate('overlay');
}

/**
 * The prompt to add a new todo 
 */
TodoController.prototype.new = function() {
	reg.template.content = '<h3>New ToDo</h3><input type="text" id="new-todo-short" />' +
		'<select id="new-todo-folder">';
	reg.template.contentHeight = 170;
	reg.template.visible = true;
	// Folder selector
	var selectedId = 'ALL';
	for (var i in GDTFolders) {
		reg.template.content += '<option value="' + GDTFolders[i].id + '"';
		if ($('folder-selected').innerHTML == GDTFolders[i].title) {
			reg.template.content += ' selected="selected"';
			selectedId = GDTFolders[i].id;
		}
		reg.template.content += '>' + GDTFolders[i].title + '</option>';
	}
	reg.template.content += '</select><div class="buttons">' +
		'<div class="button" style="float:left;"><a href="/todo/add" onclick="todoController.add($(\'new-todo-short\').value, $(\'new-todo-folder\').value); return false;">OK</a></div>' +
		'<div class="button" style="float:right;"><a href="/main/view/'+selectedId+'" onclick="return navigateTo(this.href)">Cancel</a></div>' +
		'</div>';
}

/**
 * The handler for adding a new todo 
 */
TodoController.prototype.add = function(short, folder) {
	// TODO: add the ability to change the folder color
	var nextId = nextTodoId();
	GDTodos.push({'short':short, 'folder':folder, 'id':nextId, 'complete':false});
	// Go to the folder.
	navigateTo('/main/view/' + folder);
}

/**
 * The handler for deleting a todo.
 */
TodoController.prototype.delete = function() {
	var folder = getTodo(reg.arg).folder;
	if (confirm('Are you sure you want to delete this todo?')) {
		for (var i=0; i<GDTodos.length; i++) {
			if (GDTodos[i].id == reg.arg) {
				// Splice this todo off the array.
				GDTodos.splice(i, 1);
				// Escape the loop...
				i = GDTodos.length;
			}
		}
	}
	navigateTo('/main/view/' + folder);
}

/**
 * A way to complete the todos.
 */
TodoController.prototype.complete = function() {
	var todo = getTodo(reg.arg);
	todo.complete = !todo.complete;
	navigateTo('/main/view/' + todo.folder);
}


/**
 * The base template for things shared between templates
 */
function BaseTemplate() {
}

/**
 * A shared method to update the header
 */
BaseTemplate.prototype.updateHeader = function() {
	var headerHTML = '<li';
	if (reg.controller == 'index' && reg.action == 'index') {
		headerHTML += ' class="active"';
	}
	headerHTML += '><a onclick="return navigateTo(this.href)" title="GetterDone Home" href="/index">Home</a></li><li';
	if (reg.controller == 'index' && reg.action == 'developers') {
		headerHTML += ' class="active"';
	}
	headerHTML += '><a onclick="return navigateTo(this.href)" title="For Developers" href="/index/developers">Developers</a></li><li';
	if (reg.controller == 'index' && reg.action == 'about') {
		headerHTML += ' class="active"';
	}
	headerHTML += '><a onclick="return navigateTo(this.href)" title="About GetterDone" href="/index/about">About</a></li>';
	$('main-navigation').innerHTML = headerHTML;
}

/**
 * The base resize function is an empty function
 */
BaseTemplate.prototype.resize = function() {}

/**
 * The template for just showing basic information
 */
InfoTemplate.prototype = new BaseTemplate();
InfoTemplate.prototype.parent = BaseTemplate.prototype;
InfoTemplate.prototype.constructor = InfoTemplate;
function InfoTemplate() {
}

/**
 * Hide all the other templates and load this one
 * must call update itself after loaded... 
 */
InfoTemplate.prototype.load = function() {
	this.template.update();
}

/**
 * Update the HTML with the info template and the contents
 */
InfoTemplate.prototype.update = function() {
	this.parent.updateHeader.call(this);
}

/**
 * The template for listing documents
 */
ListTemplate.prototype = new BaseTemplate();
ListTemplate.prototype.parent = BaseTemplate.prototype;
ListTemplate.prototype.constructor = ListTemplate;
function ListTemplate() {
	this.loaded = false;
}

/**
 * Hide all the other templates and load this one
 * must call update itself after loading...
 */
ListTemplate.prototype.load = function() {
	if (!this.template.isLoading) {
		this.template.isLoading = true;
		$.ajax('/main/index/ajax', function(data) {
			this.template.isLoading = false;
			$('main-content').innerHTML = data;
			this.template.update();
		}, this);
	}
}

/**
 * Update the HTML with the latest document data
 */
ListTemplate.prototype.update = function() {
	this.parent.updateHeader.call(this);
	if (this.selectedFolder) {
		// Now update the left list of folders
		$('folder-list').innerHTML = '';
		for (var i in GDTFolders) {
			var thisFolder = '<li';
			if (GDTFolders[i].id == this.selectedFolder.id) {
				thisFolder += ' class="active"';
			}
			thisFolder += ' style="background:' + GDTColors[GDTFolders[i].color].background +
				'"><a href="/main/view/' + GDTFolders[i].id +
				'" style="color:' + GDTColors[GDTFolders[i].color].text + '"' +
				'onclick="return navigateTo(this.href)" class="title">' + GDTFolders[i].title + '</a>';
			if (GDTFolders[i].id != "ALL") {
				thisFolder += '<a href="/folder/delete/' + GDTFolders[i].id + '"' +
					'onclick="return navigateTo(this.href)" class="delete">Delete</a>' +
					'<a href="/folder/color/' + GDTFolders[i].id + '"' + 
					'onclick="return navigateTo(this.href)" class="color">Color</a> ';
			}
			thisFolder += '<div class="clear_fix"></div></li>';
			$('folder-list').innerHTML += thisFolder;
		}
		// And the header with the folder title
		$('folder-selected').innerHTML = this.selectedFolder.title;
		// Next, the actual document/todo list
		$('todo-list').innerHTML = '';
		var foundItems = 0;
		for (var i in GDTodos) {
			if (GDTodos[i].folder == this.selectedFolder.id || this.selectedFolder.id == 'ALL') {
				// TODO: You probably want to add mouse over handlers somewhere...
				var folder = getFolder(GDTodos[i].folder);
				var todoList = '<li style="background:' + GDTColors[folder.color].background +
					'"><a href="/todo/complete/' + GDTodos[i].id +
					'" onclick="return navigateTo(this.href)" style="color:'+GDTColors[folder.color].text+'" title="Click to ';
				if (GDTodos[i].complete) {
					todoList += 'make incomplete" class="complete"';
				} else {
					todoList += 'complete"';
				}
				todoList += '>' + GDTodos[i].short + '</a>' +
					'<a href="/todo/delete/'+GDTodos[i].id+'" class="delete" onclick="return navigateTo(this.href)">Delete</a><div class="clear_fix"></div></li>';
				$('todo-list').innerHTML += todoList;
				foundItems++;
			}
		}
		if (GDTodos.length == 0) {
			// No todos? give a welcome message.
			$('todo-list').innerHTML = '<li class="welcome">Welcome to GetterDone ToDos!<br/>' +
				'Get started by creating <a href="/folder/new" onclick="return navigateTo(this.href)">a new folder</a> or ' +
				'<a href="/todo/new" onclick="return navigateTo(this.href)">a new todo</a>.</li>';
		} else if (foundItems == 0) {
			// No items in this folder...
			$('todo-list').innerHTML = '<li class="welcome">Looks like there is nothing in here.<br/>' +
				'Go ahead and create <a href="/todo/new" onclick="return navigateTo(this.href)">a new todo</a> for this folder.</li>';
		}
	}
	this.resize();
}

/**
 * We could update the width/height of elements based on window size...
 */
ListTemplate.prototype.resize = function() {
}

/**
 * The template for showing an overlay window
 */
OverlayTemplate.prototype = new BaseTemplate();
OverlayTemplate.prototype.parent = BaseTemplate.prototype;
OverlayTemplate.prototype.constructor = OverlayTemplate;
function OverlayTemplate() {
}

/**
 * Ensure we have loaded the overlay divs
 * must call update itself after loaded... 
 */
OverlayTemplate.prototype.load = function() {
	if (!$('overlay')) {
		$('main-content').innerHTML += '<div id="overlay"><div id="overlay-content" style="height:' + reg.template.contentHeight + 'px"></div></div>';
	}
	this.template.update();
}

/**
 * Update the HTML with the info template and the contents
 */
OverlayTemplate.prototype.update = function() {
	this.parent.updateHeader.call(this);
	$('overlay-content').innerHTML = this.content;
	this.resize();
}

/**
 * Update the position of the overlay box
 */
OverlayTemplate.prototype.resize = function() {
	$('overlay-content').style.top = (window.innerHeight / 2) - (reg.template.contentHeight / 2) + 'px';
	$('overlay-content').style.left = window.innerWidth / 2 - 125 + 'px';
}

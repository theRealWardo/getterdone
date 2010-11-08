<div id="main-index">
	<div class="folders">
		<a href="/folder/new" onclick="return navigateTo(this.href)" class="newFolder">New Folder</a>
		<ul id="folder-list">
			<li id="folder-all" style="background:#000; color:#fff;">All Folders</li>
			<li id="folder-0" style="background:#060; color:#fff;">Sample Folder</li>
		</ul>
	</div>
	<div class="todos">
		<h3 id="folder-selected">All Folders</h3>
		<a href="/todo/new" onclick="return navigateTo(this.href)" class="newTodo">New ToDo</a>
		<ul id="todo-list">
			<li id="todo-0"><a href="/todo/complete/0" class="short">Click to complete me!</a> <a href="/todo/delete/0" class="delete">Delete</a></li>
		</ul>
	</div>
</div>

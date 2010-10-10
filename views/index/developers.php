<div class="page-content" id="developers">

<?=$form->create("contributors")?>
<?=$form->text("Name","name")?>
<?=$form->text("E-Mail Address","email")?>
<?=$form->submit('Add Developer')?>

<ul class="developers">
<? foreach ($developers as $developer): ?>
	<li><a href="mailto:<?=$developer['email']?>"><?=$developer['name']?></a></li>
<? endforeach ?>
</ul>

</div>

<ul>
	<li<?php if(!isset($_POST['v'])){ ?> class="selected"<?php } ?> onClick="document.location = 'index.php'">
    	<img src="images/icons/lock.png">
        <span>Passwords Home</span>
    </li>
    <?php
	$sql = "SELECT * FROM `passman_categories` WHERE `iParent` = 0";
	$q = db::Query($sql);
	while($parent = $q->fetch()){
	$sql = "SELECT * FROM `passman_categories` WHERE `iParent` = ".$parent['iID'];
	$q2 = db::Query($sql);
	while($child = $q2->fetch()){
		$children[$child['iID']] = security::decrypt($child['sCategory']);
		$icons[$child['iID']] = security::decrypt($child['sIcon']);
	}
	?>
	<li<?php if(isset($_POST['v']) && $_POST['v'] == $parent['iID']){ ?> class="selected"<?php }if(!isset($children)){ ?> onClick="goto('viewer.php', '<?php echo $parent['iID'] ?>', '');"<?php } ?>>
    	<img src="images/icons/<?php echo security::decrypt($parent['sIcon']) ?>">
        <span><?php echo security::decrypt($parent['sCategory']) ?></span>
		<?php
		if(isset($children)){
			echo "<ul>";
			asort($children);
			foreach($children as $id => $category){
			?>
				<li onClick="goto('viewer.php', '<?php echo $id ?>', '');"<?php if(isset($_POST['v']) && $_POST['v'] == $id){ ?> class="selected"<?php } ?>>
					<img src="images/icons/<?php echo $icons[$id] ?>">
					<span><?php echo $category ?></span>
				</li>
				<?php
			}
			echo "</ul>";
		}
		?>
    </li>
    <?php } ?>
	<li onClick="goto('admin.php', 'admin', '');"<?php if(isset($_POST['v']) && $_POST['v'] == "admin"){ ?> class="selected"<?php } ?>>
    	<img src="images/icons/lock.png">
        <span>Administrate</span>
    </li>
</ul>
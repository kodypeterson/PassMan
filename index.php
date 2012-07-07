<?php include("includes/template_top.php"); ?>
<?php
if(isset($_POST['p'])){
	include($_POST['p']);
}else{
	echo "Select a category from the left.";
}
?>
<?php include("includes/template_bot.php"); ?>
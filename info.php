<?php
if(!strstr($_SERVER['SCRIPT_NAME'], "index.php")){
	echo "Unauthorized Access!";
	exit;
}
	
$succ = 0;
$err = "";

if($_POST['vt'] != "add"){
	$sql = "SELECT * FROM `passman_storage` WHERE `iID` = :id AND `iCategory` = :cat";
	$q = db::Query($sql, array(':id'=>$_POST['vt'], ':cat'=>$_POST['v']));
	$pass = $q->fetch();
	
	if(isset($_POST['a']) && $_POST['a'] == "u"){
		if($_POST['title'] != security::decrypt($pass['sTitle'])){
			//TITLE HAS BEEN CHANGED
			security::action($_SESSION['fname']." ".$_SESSION['lname']." changed the title for the password called '".security::decrypt($pass['sTitle'])."' to '".$_POST['title']."'.");
		}
		if($_POST['description'] != security::decrypt($pass['sDescription'])){
			//CHANGED DESCRIPTION
			security::action($_SESSION['fname']." ".$_SESSION['lname']." changed the description for the password called '".$_POST['title']."'.");
		}
		if($_POST['expires'] != $pass['iExpires']){
			//CHANGED EXPIRATION DATE
			security::action($_SESSION['fname']." ".$_SESSION['lname']." changed the expiration date for the password called '".$_POST['title']."'.");
		}
		if($_POST['passwordfirstfield'] != "*************" && $_POST['passwordfirstfield'] != security::decrypt($pass['sValue'])){
			//CHANGED PASSWORD
			if($_POST['passwordfirstfield'] == $_POST['passwordsecondfield']){
				//CONFIRMED PASSWORD CHANGE
				security::action($_SESSION['fname']." ".$_SESSION['lname']." changed the password for the password called '".$_POST['title']."'.");
			}else{
				$err = "The passwords do not match.";
			}
		}
		if($err == ""){
			$sql = "UPDATE `passman_storage` SET `sTitle` = :title, `sDescription` = :desc, `iExpires` = :expires, `sValue` = :password WHERE `iID` = :id";
			db::Query($sql, array(':title'=>security::encrypt($_POST['title']), ':desc'=>security::encrypt($_POST['description']), ':expires'=>$_POST['expires'], ':password'=>security::encrypt($_POST['passwordfirstfield']), ':id'=>$pass['iID']));
			$succ = 1;
			$sql = "SELECT * FROM `passman_storage` WHERE `iID` = :id AND `iCategory` = :cat";
			$q = db::Query($sql, array(':id'=>$_POST['vt'], ':cat'=>$_POST['v']));
			$pass = $q->fetch();
		}
	}
}else{
	if(isset($_POST['a']) && $_POST['a'] == "u"){
		if($_POST['passwordfirstfield'] != $_POST['passwordsecondfield']){
			$err = "The passwords do not match.";
		}
		if($_POST['passwordfirstfield'] == ""){
			$err = "A password is required, duh.";
		}
		if($_POST['description'] == ""){
			$err = "This entry must have a description.";
		}
		if($_POST['title'] == ""){
			$err = "This entry must have a title.";
		}
		if($_POST['expires'] == "" && $err == ""){
			$_POST['expires'] = "0";
		}
		if($err == ""){
			$sql = "INSERT INTO `passman_storage` VALUES(null, :cat, :title, :desc, :expires, :value)";
			db::Query($sql, array(':cat'=>$_POST['v'], ':title'=>security::encrypt($_POST['title']), ':desc'=>security::encrypt($_POST['description']), ':expires'=>$_POST['expires'], ':value'=>security::encrypt($_POST['passwordfirstfield'])));
			security::action($_SESSION['fname']." ".$_SESSION['lname']." added the password called '".$_POST['title']."'.");
			echo "<script>goto('viewer.php', '".$_POST['v']."', '');</script>";
			exit;
		}
	}
	$pass['sTitle'] = security::encrypt("Add New");
}

$sql = "SELECT * FROM `passman_categories` WHERE `iID` = :id";
$q = db::Query($sql, array(':id'=>$_POST['v']));
$category = $q->fetch();
$hasParent = false;
if($category['iParent'] != 0){
	$hasParent = true;
	$sql = "SELECT * FROM `passman_categories` WHERE `iID` = :id";
	$q = db::Query($sql, array(':id'=>$category['iParent']));
	$parent = $q->fetch();
}
?>
<h1><?php if($hasParent){ echo security::decrypt($parent['sCategory'])." > ";} echo security::decrypt($category['sCategory'])." > ".security::decrypt($pass['sTitle']); ?></h1>
<h2><?php if($_POST['vt'] != "add"){ ?>Edit<?php }else{ ?>Add<?php } ?> Password</h2>
To <?php if($_POST['vt'] != "add"){ ?>edit<?php }else{ ?>add<?php } ?> the password for the shared list '<?php echo security::decrypt($category['sCategory']) ?>' below, please fill in the details and click on the 'Save' button.<Br>
<?php
if($succ == 1){
	?>
    <div class="success">
    	This password entry has been updated!
    </div>
    <?php	
}
?>
<?php
if($err != ""){
	?>
    <div class="error">
    	<?php echo $err ?>
    </div>
    <?php	
}
?>
<br>
<form action="" method="post">
    <table>
        <tr>
            <td style="vertical-align:middle">Title</td>
            <td colspan="2"><input type="text" name="title" value="<?php if($_POST['vt'] != "add"){echo security::decrypt($pass['sTitle']);}else{echo @$_POST['title'];} ?>" style="width:200px;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle">Description</td>
            <td colspan="2"><textarea name="description" style="height:100px; width:198px;"><?php if($_POST['vt'] != "add"){echo security::decrypt($pass['sDescription']);}else{echo @$_POST['description'];} ?></textarea></td>
        </tr>
        <tr>
            <td style="vertical-align:middle">Password</td>
            <td><input type="password" name="passwordfirstfield" value="<?php if($_POST['vt'] != "add"){echo security::decrypt($pass['sValue']);}; ?>" style="width:200px;" onclick="showPassBoxNoTimer();"></td>
            <td style="vertical-align:middle"><?php if($_POST['vt'] != "add"){ ?><a href="#" id="showLink" onClick="showPassBox(); return false;">show</a> | To edit click in password box<?php } ?></td>
        </tr>
        <tr>
            <td style="vertical-align:middle">Confirm Password</td>
            <td colspan="2"><input type="password" name="passwordsecondfield" value="<?php if($_POST['vt'] != "add"){echo security::decrypt($pass['sValue']);}; ?>" style="width:200px;"></td>
        </tr>
        <tr>
            <td style="vertical-align:middle">Expiration Date</td>
            <td<?php if($_POST['vt'] != "add"){?> colspan="2"<?php } ?>><input type="text" name="expires" value="<?php if($_POST['vt'] != "add"){echo $pass['iExpires'];}else{echo @$_POST['expires'];} ?>" style="width:200px;"></td>
            <?php if($_POST['vt'] == "add"){?><td style="vertical-align:middle">Leave blank for never</td><?php } ?>
        </tr>
        <tr>
            <td style="vertical-align:middle"></td>
            <td align="right" style="vertical-align:middle"><input type="button" value="Cancel" onClick="goto('viewer.php', '<?php echo $_POST['v'] ?>', '');"> | <input type="submit" value="Save"></td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <input type="hidden" name="a" value="u" />
    <input type="hidden" name="p" value="<?php echo $_POST['p'] ?>" />
    <input type="hidden" name="v" value="<?php echo $_POST['v'] ?>" />
    <input type="hidden" name="vt" value="<?php echo $_POST['vt'] ?>" />
</form>
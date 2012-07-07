<?php
include("includes/template_top.php");
if(!isset($_SESSION['activate'])){
	header("Location: login.php");
}
$err = 0;
if(isset($_POST['fname'])){
	$sql = "SELECT * FROM `passman_users` WHERE `sIdentifier` LIKE :v";
	$q = db::Query($sql, array(':v'=>$_SESSION['activate']."-%"));
	$r = $q->fetch();
	if($_POST['fname'] != $r['sFname']){
		$err = "First Name Incorrect";
		$_POST['fname'] = "";
	}
	if($_POST['lname'] != $r['sLname']){
		$err = "Last Name Incorrect";
		$_POST['lname'] = "";
	}
	if($_POST['password'] != $_POST['password2'] || $_POST['password'] == ""){
		$err = "Passwords Don't Match";
	}
	if($_POST['username'] == ""){
		$err = "User Name Blank";
	}
	if($err == 0){
		$sql = "UPDATE `passman_users` SET `sIdentifier` = :pass WHERE `iID` = :id";
		db::Query($sql, array(':pass'=>security::encryptStringForComparison($_POST['username'], $_POST['password']), ':id'=>$r['iID']));
		header("Location: login.php");
		exit;
	}
}
?>
<h1>Activate</h1>
<?php if($err != 0){ ?>
<br><?php echo $err ?><br>
<?php } ?>
<form action="" method="post">
	<table>
    	<tr>
        	<td style="vertical-align:middle">
            	First Name:
            </td>
            <td>
            	<input type="text" name="fname" autocomplete="off" onpaste="return false;" value="<?php echo @$_POST['fname'] ?>" />
            </td>
		</tr>
    	<tr>
        	<td style="vertical-align:middle">
            	Last Name:
            </td>
            <td>
            	<input type="text" name="lname" autocomplete="off" onpaste="return false;" value="<?php echo @$_POST['lname'] ?>" />
            </td>
		</tr>
    	<tr>
        	<td style="vertical-align:middle">
            	Create Username:
            </td>
            <td>
            	<input type="text" name="username" autocomplete="off" onpaste="return false;" value="<?php echo @$_POST['username'] ?>" />
            </td>
		</tr>
    	<tr>
        	<td style="vertical-align:middle">
            	Password:
            </td>
            <td>
            	<input type="password" name="password" autocomplete="off" onpaste="return false;" />
            </td>
		</tr>
    	<tr>
        	<td style="vertical-align:middle">
            	Password Again:
            </td>
            <td>
            	<input type="password" name="password2" autocomplete="off" onpaste="return false;" />
            </td>
		</tr>
    	<tr>
        	<td style="vertical-align:middle">&nbsp;
            	
            </td>
            <td align="center">
            	<input type="submit" value="Activate" />
            </td>
		</tr>
	</table>
</form>
<?php include("includes/template_bot.php"); ?>
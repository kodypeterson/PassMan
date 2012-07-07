<?php
include("classes/config.php");
include("classes/security.php");

if(isset($_GET['v'])){
	$sql = "SELECT * FROM `passman_users` WHERE `sIdentifier` LIKE :v";
	$q = db::Query($sql, array(':v'=>$_GET['v']."-%"));
	$r = $q->fetch();
	if(isset($r['iID'])){
		$t = explode("-", $r['sIdentifier']);
		if(time() > strtotime("+24 hours", $t[1])){
			//PAST 24 HOURS
			header("Location: index.php");
			exit;
		}else{
			$_SESSION['activate'] = $_GET['v'];
			header("Location: activate.php");
			exit;
		}
	}else{
		header("Location: index.php");
		exit;
	}
}

$fileName = explode("/", $_SERVER['SCRIPT_FILENAME']);						
$isSetup = false;
if($fileName[count($fileName) - 2] == "setup"){
	$isSetup = true;
}
if(!$isSetup){
	$sql = "SHOW TABLES LIKE 'passman_config';";
	$q = db::Query($sql);
	$r = $q->fetch();
	if(!isset($r[0])){
		header("Location: setup/");
		exit;
	}

	$setupPath = "";
	foreach($fileName as $key=>$value){
		if($key != (count($fileName) - 1) && $value != ""){
			$setupPath .= "/".$value;
		}
	}
	$setupPath = $setupPath."/setup/index.php";
	if(is_file($setupPath)){
		echo "<h1>REMOVE SETUP DIRECTORY</h1>";
		exit;
	}
}
if($fileName[count($fileName) - 1] == "login.php"){
	$_SESSION['user'] = "";
	$_SESSION['fname'] = "";
	$_SESSION['lname'] = "";
	$_SESSION['key'] = "";
	unset($_SESSION['user']);
	unset($_SESSION['fname']);
	unset($_SESSION['lname']);
	unset($_SESSION['key']);
}
if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['key'])){
	$id = security::encryptStringForComparison($_POST['username'], $_POST['password']);
	$sql = "SELECT * FROM `passman_users` WHERE `sIdentifier` = :id LIMIT 0, 1";
	$q = db::Query($sql, array(':id'=>$id));
	$user = $q->fetch();
	if(isset($user['iID'])){
		$key = security::encryptStringForComparison($_POST['key'], $_POST['key']);
		$sql = "SELECT * FROM `passman_config` WHERE `sValue` = :key LIMIT 0, 1";
		$q = db::Query($sql, array(':key'=>$key));
		$key = $q->fetch();
		if($key['sName'] == "packingKey"){
			$_SESSION['user'] = $id;
			$_SESSION['fname'] = $user['sFname'];
			$_SESSION['lname'] = $user['sLname'];
			$_SESSION['key'] = $_POST['key'];
			header("Location: index.php");
			exit;
		}
	}
}
if(!isset($_SESSION['user']) && $fileName[count($fileName) - 1] != "login.php" && $fileName[count($fileName) - 1] != "activate.php" && !$isSetup){
	header("Location: login.php");
	exit;
}
$sql = "INSERT INTO `passman_storage` VALUES (null, 2, \"".security::encrypt("root")."\", \"".security::encrypt("The root account for mysql.")."\", 0, \"".security::encrypt("TestPassword22")."\")";
//db::Query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="<?php if($isSetup){ echo "../"; } ?>css/style.css" />
<title>Password Manager</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="<?php if($isSetup){ echo "../"; } ?>includes/main.php" type="text/javascript"></script>
</head>

<body class="<?php if($isSetup){ echo "login"; }else{ echo str_replace(".php", "", $fileName[count($fileName) - 1]); } ?>">
	<div id="wrapper">
        <table id="main">
            <tr>
                <td id="tl">&nbsp;</td>
                <td id="t">
                	<div id="navTitle">
                    	PassMan v1.0
                    </div>
                	<div id="contentTitle">
                    	&nbsp;
                    </div>
                	<div id="rightInfo">
                    	<?php if(isset($_SESSION['fname'])){ ?>
	                    	Auto logout in: <span id="auto" style="cursor:pointer; text-decoration:underline" onclick="autoIncrease();">1:00</span> | Logged in as: <?php echo $_SESSION['fname']." ".$_SESSION['lname'] ?> | <a href="login.php">Logout</a>
                        <?php } ?>
                    </div>
                </td>
                <td id="tr">&nbsp;</td>
            </tr>
            <tr>
                <td id="l">&nbsp;</td>
                <td id="c">
                	<div id="leftNav">
                    	<?php
						$sidebarPath = "";
						foreach($fileName as $key=>$value){
							if($key != (count($fileName) - 1) && $value != ""){
								$sidebarPath .= "/".$value;
							}
						}
						$sidebarPath = $sidebarPath."/sidebars/".$fileName[count($fileName) - 1];
						if(is_file($sidebarPath)){
							include($sidebarPath);
						}
						?>
                    </div>
                	<div id="middleDivider">&nbsp;</div>
                    <div id="innerContent">
                    	<div id="inner">
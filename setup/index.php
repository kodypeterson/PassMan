<?php
include("../includes/template_top.php");
$err = "";
if(isset($_POST['fname'])){
	if($_POST['fname'] == ""){
		$err = "First name is required";
	}
	if($_POST['lname'] == ""){
		$err = "Last name is required";
	}
	if($_POST['email'] == ""){
		$err = "Email address is required";
	}
	if($_POST['uname'] == ""){
		$err = "User name is required";
	}
	if($_POST['password'] == ""){
		$err = "Password is required";
	}
	if($_POST['password'] != $_POST['password2']){
		$err = "Passwords don't match";
	}
	if($_POST['key'] == ""){
		$err = "Packing key is required";
	}
	if($_POST['key'] != $_POST['key2']){
		$err = "Packing keys don't match";
	}
	if($err == ""){
		$sql = "CREATE TABLE IF NOT EXISTS `passman_audit` (
			  `iID` int(11) NOT NULL AUTO_INCREMENT,
			  `iUser` int(11) NOT NULL,
			  `sAction` varchar(255) NOT NULL,
			  `iCategory` int(11) NOT NULL,
			  `iTime` int(11) NOT NULL,
			  PRIMARY KEY (`iID`),
			  KEY `iUser` (`iUser`,`iCategory`,`iTime`)
			  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		db::Query($sql);
		$sql = "CREATE TABLE IF NOT EXISTS `passman_categories` (
			    `iID` int(11) NOT NULL AUTO_INCREMENT,
			    `sCategory` varchar(255) NOT NULL,
			    `iParent` int(11) NOT NULL,
			    `sIcon` varchar(255) NOT NULL,
			    PRIMARY KEY (`iID`),
			    UNIQUE KEY `sCategory` (`sCategory`)
			    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		db::Query($sql);
		$sql = "CREATE TABLE IF NOT EXISTS `passman_config` (
			  `sName` varchar(255) NOT NULL,
			  `sValue` varchar(255) NOT NULL,
			  UNIQUE KEY `sName` (`sName`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		db::Query($sql);
		$sql = "CREATE TABLE IF NOT EXISTS `passman_storage` (
			  `iID` int(11) NOT NULL AUTO_INCREMENT,
			  `iCategory` int(11) NOT NULL,
			  `sTitle` varchar(255) NOT NULL,
			  `sDescription` longtext NOT NULL,
			  `iExpires` int(11) NOT NULL,
			  `sValue` varchar(255) NOT NULL,
			  PRIMARY KEY (`iID`),
			  UNIQUE KEY `sTitle` (`sTitle`)
			  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		db::Query($sql);
		$sql = "CREATE TABLE IF NOT EXISTS `passman_users` (
			  `iID` int(11) NOT NULL AUTO_INCREMENT,
			  `sFname` varchar(255) NOT NULL,
			  `sLname` varchar(255) NOT NULL,
			  `sEmail` varchar(255) NOT NULL,
			  `sIdentifier` varchar(255) NOT NULL,
			  PRIMARY KEY (`iID`),
			  UNIQUE KEY `sEmail` (`sEmail`)
			  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		db::Query($sql);
		$sql = "INSERT INTO `passman_config` VALUES('packingKey', :key)";
		db::Query($sql, array(':key'=>security::encryptStringForComparison($_POST['key'], $_POST['key'])));
		$sql = "INSERT INTO `passman_users` VALUES(null, :fname, :lname, :email, :q)";
		db::Query($sql, array(':fname'=>$_POST['fname'], ':lname'=>$_POST['lname'], ':email'=>$_POST['email'], ':q'=>security::encryptStringForComparison($_POST['uname'], $_POST['password'])));
		header("Location: ../login.php");
		exit;
	}
}
?>
<h1>PassMan Setup</h1>
Welcome to PassMan! To setup, please fill out the form:<br />
<br />
<form action="" method="post">
    <table>
        <tr>
            <td>First Name:</td>
            <td><input type="text" name="fname" onpaste="return false;" autocomplete="off" /></td>
        </tr>
        <tr>
            <td>Last Name:</td>
            <td><input type="text" name="lname" onpaste="return false;" autocomplete="off" /></td>
        </tr>
        <tr>
            <td>Email Address:</td>
            <td><input type="text" name="email" onpaste="return false;" autocomplete="off" /></td>
        </tr>
        <tr>
            <td>Create Username:</td>
            <td><input type="text" name="uname" onpaste="return false;" autocomplete="off" /></td>
        </tr>
        <tr>
            <td>Password:</td>
            <td><input type="password" name="password" onpaste="return false;" autocomplete="off" /></td>
        </tr>
        <tr>
            <td>Password Again:</td>
            <td><input type="password" name="password2" onpaste="return false;" autocomplete="off" /></td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>DO NOT FORGET THE PACKING KEY!!!!!!</strong><br />
                <strong>THERE IS NO WAY TO RECOVER IT</strong>
            </td>
        </tr>
        <tr>
            <td>Packing Key:</td>
            <td><input type="text" name="key" onpaste="return false;" autocomplete="off" /></td>
        </tr>
        <tr>
            <td>Packing Key Again:</td>
            <td><input type="text" name="key2" onpaste="return false;" autocomplete="off" /></td>
        </tr>
        <tr>
            <td></td>
            <td align="center"><input type="submit" value="Setup PassMan" /></td>
        </tr>
    </table>
</form>
<?php include("../includes/template_bot.php"); ?>
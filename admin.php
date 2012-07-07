<?php
if(!strstr($_SERVER['SCRIPT_NAME'], "index.php")){
	echo "Unauthorized Access!";
	exit;
}
if(isset($_POST['a']) && $_POST['a'] == "u"){
	$currentKey = $_SESSION['key'];
	if($_POST['key'] == $_POST['key2'] && $_POST['ckey'] == $currentKey){
		//KEYS CONFIRMED GOOD TO CHANGE
		//FIRST CHANGE KEY IN CONFIG
		$sql = "UPDATE `passman_config` SET `sValue` = :key WHERE `sName` = 'packingKey'";
		db::Query($sql, array(':key'=>security::encryptStringForComparison($_POST['key'], $_POST['key'])));
		//NOW UPDATE ALL OF THE ENCRYPTED STRINGS
		$sql = "SELECT * FROM `passman_audit`";
		$q = db::Query($sql);
		while($r = $q->fetch()){
			$action = security::decrypt($r['sAction']);
			$_SESSION['key'] = $_POST['key'];
			$newAction = security::encrypt($action);
			$sql = "UPDATE `passman_audit` SET `sAction` = :action WHERE `iID` = :id";
			db::Query($sql, array(':action'=>$newAction, ':id'=>$r['iID']));
			$_SESSION['key'] = $currentKey;
		}
		$sql = "SELECT * FROM `passman_categories`";
		$q = db::Query($sql);
		while($r = $q->fetch()){
			$cat = security::decrypt($r['sCategory']);
			$icon = security::decrypt($r['sIcon']);
			$_SESSION['key'] = $_POST['key'];
			$cat = security::encrypt($cat);
			$icon = security::encrypt($icon);
			$sql = "UPDATE `passman_categories` SET `sCategory` = :cat, `sIcon` = :icon WHERE `iID` = :id";
			db::Query($sql, array(':cat'=>$cat, ':icon'=>$icon, ':id'=>$r['iID']));
			$_SESSION['key'] = $currentKey;
		}
		$sql = "SELECT * FROM `passman_storage`";
		$q = db::Query($sql);
		while($r = $q->fetch()){
			$title = security::decrypt($r['sTitle']);
			$description = security::decrypt($r['sDescription']);
			$value = security::decrypt($r['sValue']);
			$_SESSION['key'] = $_POST['key'];
			$title = security::encrypt($title);
			$description = security::encrypt($description);
			$value = security::encrypt($value);
			$sql = "UPDATE `passman_storage` SET `sTitle` = :title, `sDescription` = :description, `sValue` = :value WHERE `iID` = :id";
			db::Query($sql, array(':title'=>$title, ':description'=>$description, ':value'=>$value, ':id'=>$r['iID']));
			$_SESSION['key'] = $currentKey;
		}
		header("Location: login.php");
		exit;
	}
}
if(isset($_POST['a']) && $_POST['a'] == "c"){
	if($_POST['cat'] != ""){
		//ADD THE CATEGORY
		$sql = "INSERT INTO `passman_categories` VALUES(null, :cat, :parent, :icon)";
		db::Query($sql, array(':cat'=>security::encrypt($_POST['cat']), ':parent'=>$_POST['parent'], ':icon'=>security::encrypt($_POST['icon'])));
	}
}
if(isset($_POST['a']) && $_POST['a'] == "p"){
	if($_POST['fname'] != "" && $_POST['lname'] != "" && $_POST['email'] != ""){
		//ADD THE CATEGORY
		$q = rand();
		$q = md5($q);
		$sql = "INSERT INTO `passman_users` VALUES(null, :fname, :lname, :email, :q)";
		db::Query($sql, array(':fname'=>$_POST['fname'], ':lname'=>$_POST['lname'], ':email'=>$_POST['email'], ':q'=>$q."-".time()));
		$url = "http://".$_SERVER['SERVER_NAME']."/".$_SERVER['DOCUMENT_URI']."?v=".$q;
		//mail($_POST['email'], "PassMan Request", "You have been added to the Edison State PassMan System.<br><br><a href='".$url."'>Please click here to activate your account</a><br><br>This link will expire in 24 hours.");
	}
}
if(isset($_POST['a']) && $_POST['a'] == "dp"){
	if($_POST['u'] != ""){
		//DELETE THE USER
		$sql = "DELETE FROM `passman_users` WHERE `sIdentifier` = :id";
		db::Query($sql, array(':id'=>$_POST['u']));
	}
}
if(isset($_POST['a']) && $_POST['a'] == "dc"){
	if($_POST['c'] != ""){
		//DELETE THE CATEGORY
		$sql = "DELETE FROM `passman_categories` WHERE `iID` = :id";
		db::Query($sql, array(':id'=>$_POST['c']));
		$sql = "SELECT * FROM `passman_categories` WHERE `iParent` = :id";
		$q = db::Query($sql, array(':id'=>$_POST['c']));
		$childs = array();
		while($r = $q->Fetch()){
			$childs[] = $r['iID'];
		}
		//DELETE THE SUB-CATEGORIES
		$sql = "DELETE FROM `passman_categories` WHERE `iParent` = :id";
		db::Query($sql, array(':id'=>$_POST['c']));
		//DELETE THE PASSWORDS IN THE CATEGORY
		$sql = "DELETE FROM `passman_storage` WHERE `iCategory` = :id";
		db::Query($sql, array(':id'=>$_POST['c']));
		//DELETE THE PASSWORDS IN THE SUB-CATEGORIES
		foreach($childs as $value){
			$sql = "DELETE FROM `passman_storage` WHERE `iCategory` = :id";
			db::Query($sql, array(':id'=>$value));
		}
	}
}
?>
<h1>Administrate</h1>
<h2>Users</h2>
<form action="" method="post" name="form3">
    <input type="hidden" name="u" value="" />
    <input type="hidden" name="a" value="dp" />
    <input type="hidden" name="p" value="<?php echo $_POST['p'] ?>" />
    <input type="hidden" name="v" value="<?php echo $_POST['v'] ?>" />
    <input type="hidden" name="vt" value="<?php echo $_POST['vt'] ?>" />
</form>
<table id="viewer">
	<thead>
        <tr>
            <td>First Name</td>
            <td>Last Name</td>
            <td>Status</td>
            <td>Actions</td>
        </tr>
	</thead>
    <tbody>
    	<?php
		$count = 0;
		$sql = "SELECT * FROM `passman_users`";
		$q = db::Query($sql);
		while($users = $q->fetch()){
			$count ++;
			?>
            <tr>
                <td style="width:auto"><?php echo $users['sFname'] ?></td>
                <td><?php echo $users['sLname'] ?></td>
                <td><?php if(strstr($users['sIdentifier'], "-")){ ?>Pending<?php }else{ ?>Active<?php } ?></td>
                <td><?php if($_SESSION['user'] != $users['sIdentifier']){ ?><a href="#" onclick="if(confirm('Are you sure you want to delete?')){$('input[name=u]').val('<?php echo $users['sIdentifier'] ?>');document.forms['form3'].submit(); return false;}else{return false;}">DELETE</a><?php } ?></td>
            </tr>
            <?php
		}
		if($count == 0){ ?>
        <tr>
            <td colspan="4"><center>There seems to be no users, how are you here?</center></td>
        </tr>
		<?php } ?>
    </tbody>
</table>
<form action="" method="post" name="form2">
    First Name: <input type="text" name="fname" autocomplete="off">
    Last Name: <input type="text" name="lname" autocomplete="off">
    Email Address: <input type="text" name="email" autocomplete="off">
    <input type="hidden" name="a" value="p" />
    <input type="hidden" name="p" value="<?php echo $_POST['p'] ?>" />
    <input type="hidden" name="v" value="<?php echo $_POST['v'] ?>" />
    <input type="hidden" name="vt" value="<?php echo $_POST['vt'] ?>" />
    <a href="#" id="addLink" onClick="document.forms['form2'].submit(); return false;">Add</a>
</form>
<br><br><br>
<h2>Categories</h2>
<form action="" method="post" name="form4">
    <input type="hidden" name="c" value="" />
    <input type="hidden" name="a" value="dc" />
    <input type="hidden" name="p" value="<?php echo $_POST['p'] ?>" />
    <input type="hidden" name="v" value="<?php echo $_POST['v'] ?>" />
    <input type="hidden" name="vt" value="<?php echo $_POST['vt'] ?>" />
</form>
<table id="viewer">
	<thead>
        <tr>
            <td style="width:auto">Category</td>
            <td style="width:130px ">Actions</td>
        </tr>
	</thead>
    <tbody>
    	<?php
		$count = 0;
		$parents = array();
		$sql = "SELECT * FROM `passman_categories` WHERE `iParent` = 0";
		$q = db::Query($sql);
		while($cats = $q->fetch()){
			$parents[$cats['iID']] = security::decrypt($cats['sCategory']);
			$count ++;
			?>
            <tr>
                <td style="width:auto"><?php echo security::decrypt($cats['sCategory']) ?></td>
                <td style="width:130px"><a href="#" onclick="if(confirm('Are you sure you want to delete? All passwords in category, sub-categories, and passwords in the sub-categories will be deleted as well!')){$('input[name=c]').val('<?php echo $cats['iID'] ?>');document.forms['form4'].submit(); return false;}else{return false;}">DELETE</a></td>
            </tr>
            <?php
			$sql = "SELECT * FROM `passman_categories` WHERE `iParent` = ".$cats['iID'];
			$q2 = db::Query($sql);
			while($children = $q2->fetch()){
				$count ++;
				?>
				<tr>
					<td style="width:auto">&nbsp;&nbsp;&nbsp;&nbsp;- <?php echo security::decrypt($children['sCategory']) ?></td>
                	<td style="width:130px"><a href="#" onclick="if(confirm('Are you sure you want to delete? All passwords in category, sub-categories, and passwords in the sub-categories will be deleted as well!')){$('input[name=c]').val('<?php echo $children['iID'] ?>');document.forms['form4'].submit(); return false;}else{return false;}">DELETE</a></td>
				</tr>
				<?php
			}
			
		}
		if($count == 0){ ?>
        <tr>
            <td colspan="4"><center>There seems to be no categories.</center></td>
        </tr>
		<?php } ?>
    </tbody>
</table>
<form action="" method="post" name="form1">
    Category: <input type="text" name="cat" autocomplete="off">
    Parent: <select name="parent"><option value="0">None</option><?php foreach($parents as $key=>$value){ ?><option value="<?php echo $key ?>"><?php echo $value ?></option><?php } ?></select>
    Icon: <select name="icon"><option value="server.png">Server</option><option value="database.png">Database</option><option value="application_osx_terminal.png">Terminal</option></select>
    <input type="hidden" name="a" value="c" />
    <input type="hidden" name="p" value="<?php echo $_POST['p'] ?>" />
    <input type="hidden" name="v" value="<?php echo $_POST['v'] ?>" />
    <input type="hidden" name="vt" value="<?php echo $_POST['vt'] ?>" />
    <a href="#" id="addLink" onClick="document.forms['form1'].submit(); return false;">Add</a>
</form>
<br><br><br>
<h2>Packing Key</h2>
<strong>DO NOT FORGET THE PACKING KEY!!!!!!</strong><br>
Changing the packing key will notify all users on the system.<br><br>
<form action="" method="post">
	Current Key: <input type="password" name="ckey" style="width:400px" onpaste="return false;" autocomplete="off"><br>
	New Key: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="key" style="width:400px" onpaste="return false;" autocomplete="off"><br>
	Key Again: &nbsp;&nbsp;&nbsp;<input type="text" name="key2" style="width:400px" onpaste="return false;" autocomplete="off"><br>
	<br>
	<input type="submit" value="Change Key">
    <input type="hidden" name="a" value="u" />
    <input type="hidden" name="p" value="<?php echo $_POST['p'] ?>" />
    <input type="hidden" name="v" value="<?php echo $_POST['v'] ?>" />
    <input type="hidden" name="vt" value="<?php echo $_POST['vt'] ?>" />
</form>
<?php
if(!strstr($_SERVER['SCRIPT_NAME'], "index.php")){
	echo "Unauthorized Access!";
	exit;
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
<h1><?php if($hasParent){ echo security::decrypt($parent['sCategory'])." > ";} echo security::decrypt($category['sCategory']); ?></h1>
<h2>Stored Passwords</h2>
<table id="viewer">
	<thead>
        <tr>
            <td>Actions</td>
            <td>Title</td>
            <td>Description</td>
            <td>Expiration Date</td>
        </tr>
	</thead>
    <tbody>
    	<?php
		$count = 0;
		$sql = "SELECT * FROM `passman_storage` WHERE `iCategory` = :cat";
		$q = db::Query($sql, array(':cat'=>$category['iID']));
		while($pass = $q->fetch()){
			$count ++;
			?>
            <tr>
                <td></td>
                <td><a href="#" onClick="goto('info.php', '<?php echo $category['iID'] ?>', '<?php echo $pass['iID'] ?>'); return false;"><?php echo security::decrypt($pass['sTitle']) ?></a></td>
                <td><?php echo security::decrypt($pass['sDescription']) ?></td>
                <td><?php if($pass['iExpires'] == 0){echo "Never";} ?></td>
            </tr>
            <?php
		}
		if($count == 0){ ?>
        <tr>
            <td colspan="4"><center>There seems to be no passwords in this category.</center></td>
        </tr>
		<?php } ?>
    </tbody>
</table>
<a href="" id="addLink" onClick="goto('info.php', '<?php echo $category['iID'] ?>', 'add'); return false;">Add</a>
<br><br><br>
<h2>Recent Activity</h2>
<table id="viewer">
	<thead>
        <tr>
            <td style="width:150px !important;">Date</td>
            <td>Description</td>
        </tr>
	</thead>
    <tbody>
    	<?php
		$count = 0;
		$sql = "SELECT * FROM `passman_audit` WHERE `iCategory` = :cat";
		$q = db::Query($sql, array(':cat'=>$category['iID']));
		while($audit = $q->fetch()){
			$count ++;
			?>
            <tr>
                <td><?php echo date("F j,Y g:i A", $audit['iTime']); ?></td>
                <td style="width:auto"><?php echo security::decrypt($audit['sAction']) ?></td>
            </tr>
            <?php
		}
		if($count == 0){ ?>
        <tr>
            <td colspan="2"><center>There seems to be no recent activity for this category.</center></td>
        </tr>
		<?php } ?>
    </tbody>
</table>
<?php
require("../../config.php");
session_start();

if(!$_SESSION['logedin']) {
	header("Location: login.php");
	die();
}

if(!$_SESSION['isadmin']) {
	die("You are not admin.");
}

if(isset($_GET['id']) || isset($_GET['majorid'])){
	if(isset($_GET['id']) && $_GET['id'] != ""){
		$id = $_GET['id'];
		if(!preg_match("/^\d+$/", $id)) {
		die("wrong input");
		}
	}
	if(isset($_GET['majorid']) && $_GET['majorid'] != "") {
		$majorid = $_GET['majorid'];
		if(!preg_match("/^\d+$/", $majorid)) {
			die("wrong input");
		}
	}
}

$conn = mysql_connect(db_server, db_username, db_password);
if (!$conn) {
	die('Could not connect: ' . mysql_error());
}
mysql_query("SET NAMES utf8");
$db_selected = mysql_select_db(db_database, $conn);
if (!$db_selected) {
	die ("Could not select db : " . mysql_error());
}

// list all majors
$sql = "SELECT * FROM `major`";
$result = mysql_query($sql);
$majorselection = "";
while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
	$majorselection .= "<option value=\"".$row['id']."\">";
	$majorselection .= $row['name'];
	$majorselection .= "</option>\n";
}

$tc = "";
if(isset($id) || isset($majorid)) {
	$sql = "SELECT `user`.`id`, `user`.`username`, `user`.`realname`, `user`.`sex`, `major`.`name` FROM `user`, `major` WHERE `user`.`major`=`major`.`id`";
	if(isset($id)) {
		$sql .= " AND `user`.`username` like \"%$id%\"";
	}
	if(isset($majorid) && $majorid != 0) {
		$sql .= " AND `major`.`id`=$majorid";
	}

	$result = mysql_query($sql);

	$tc = "<table><tr><td>账号</td><td>姓名</td><td>性别</td><td>专业</td><td>修改资料</td></tr>";
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$tc .= "<tr><td>".$row['username']."</td>";
		$tc .= "<td>".$row['realname']."</td>";
		$tc .= "<td>".(($row['sex']==0)?"男":"女")."</td>";
		$tc .= "<td>".$row["name"]."</td>";
		$tc .= "<td><a href=\"changeuserinfo.php?id=".$row["id"]."\">修改</a></td></tr>\n";
	}
	$tc .= "</table>";
}
?>
<html>

<body>
<a href="../">返回</a><br />
查看学生<br />
<form>
	学号<input type="text" name="id" /><br />
	专业<select name="majorid">
		<option value="0">请选择</option>
		<?php echo $majorselection; ?>
	</select><br />
	<input type="submit" value="查找" />
</form>
<?php echo $tc; ?>
</body>
</html>
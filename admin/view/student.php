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

if(isset($_GET['id'])){
	$id = $_GET['id'];
	if(!preg_match("/^\d+$/",$id)) {
		die("wrong input");
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

$sql = "SELECT `user`.`id`, `user`.`username`, `user`.`realname`, `user`.`sex`, `major`.`name` FROM `user`, `major` WHERE `user`.`major`=`major`.`id`";
if(isset($id)) {
	$sql .= "AND `user`.`username` like \"%$id%\"";
}
$result = mysql_query($sql);
$array = array();
while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
	$array[] = $row;
}

$tc = "<table><tr><td>账号</td><td>姓名</td><td>性别</td><td>专业</td><td>修改密码</td></tr>";
foreach($array as $row) {
	$tc .= "<tr><td>".$row['username']."</td>";
	$tc .= "<td>".$row['realname']."</td>";
	$tc .= "<td>".(($row['sex']==0)?"男":"女")."</td>";
	$tc .= "<td>".$row["name"]."</td>";
	$tc .= "<td><a href=\"changepassword.php?id=".$row["id"]."\">修改密码</a></td></tr>\n";
}
$tc .= "</table>";
?>
<html>

<body>
<a href="../">返回</a><br />
查看学生<br />
<form>
	学号<input type="text" name="id" />
	<input type="submit" value="查找" />
</form>
<?php echo $tc; ?>
</body>
</html>
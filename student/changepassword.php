<?php
require("../config.php");
session_start();

if(!$_SESSION['logedin']) {
	header("Location: login.php");
	die();
}

if($_SESSION['isadmin']) {
	die("You are admin.");
}

$id = $_SESSION['uid'];

if(isset($_REQUEST['password'])) {
	if($_REQUEST['password']=="") {
		die("请勿使用空密码");
	}
	$password = $_REQUEST['password'];
	$pwd = md5($password);

	$conn = mysql_connect(db_server, db_username, db_password);
	if (!$conn) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_query("SET NAMES utf8");
	$db_selected = mysql_select_db(db_database, $conn);
	if (!$db_selected) {
		die ("Could not select db : " . mysql_error());
	}

	$sql = "UPDATE `user` SET `password` = \"$pwd\" WHERE `id` = $id";
	$result = mysql_query($sql);
	if(!$result) {
		die (mysql_error());
	}
	die("<script>alert(\"密码已更改\");history.go(-2);</script>");
}
?>
<html>
<body>
<form method="post">
新密码<input type="text" name="password" /><br />
<input type="submit" value="提交" />
</form>
</body>
</html>
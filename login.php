<?php
require("config.php");
session_start();

if($_SESSION['logedin']) {
	header("Location: index.php");
	die();
}

if($_SERVER['REQUEST_METHOD']=="POST") {
	$username = $_POST['username'];
	$password = $_POST['password'];

	if(!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
		die("Something wrong.");
	}

	$hashed_password = md5($password);

	// query database.
	$conn = mysql_connect(db_server, db_username, db_password);
	if (!$conn) {
		die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db(db_database, $conn);
	if (!$db_selected) {
		die ("Could not select db : " . mysql_error());
	}

	$sql = "SELECT * FROM `user` WHERE `username`='$username' AND `password`='$hashed_password';";
	$result = mysql_query($sql, $conn);
	$count = mysql_num_rows($result);

	if($count) {
		$_SESSION['logedin'] = true;
		$row = mysql_fetch_array($result);
		$_SESSION['username'] = $row['username'];
		$_SESSION['uid'] = $row['id'];
		if($row['admin']) {
			$_SESSION['isadmin'] = true;
		}

		header("Location: index.php");
		die();
	}
}
?>
<html>
<head>
<title>登陆 - 教务管理系统</title>
</head>
<body>
	教务管理系统<br />
	<form method="post">
		<input type="text" name="username" value="<?php if(isset($username)) echo $username; ?>" /> <br />
		<input type="password" name="password" /> <br />
		<input type="submit" value="登陆" />
	</form>
</body>
</html>
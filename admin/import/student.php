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

if($_SERVER['REQUEST_METHOD']=="POST") {
	if($_FILES["file"]["error"] > 0) {
		die("Error: " . $_FILES["file"]["error"]);
	}
	if($_FILES["file"]["type"]!="text/csv") {
		die("Error: only CSV file is supported.");
	}
	$f = fopen($_FILES["file"]["tmp_name"], "r");
	if(!$f) {
		die("Error: cannot open file.");
	}

	$array = array();
	while(!feof($f)) {
		$array[] = fgetcsv($f);
	}
	fclose($f);
	//echo "<pre>";
	//print_r($array);
	//echo "</pre>";

	$conn = mysql_connect(db_server, db_username, db_password);
	if (!$conn) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_query("SET NAMES utf8");
	$db_selected = mysql_select_db(db_database, $conn);
	if (!$db_selected) {
		die ("Could not select db : " . mysql_error());
	}

	$line = 0;
	foreach($array as $row) {
		$line++;

		if(count($row)!=4) {
			die("Too few items on line $line");
		}
		$username = $row[0];
		$realname = $row[1];
		$sex = $row[2];
		$majorname = $row[3];
		if(!preg_match("/^[a-zA-Z]{0,2}[0-9]{9}$/", $username)) {
			die("Invalid username <b>$username</b> on line $line");
		}
		if(mb_strlen($realname)<=2 || preg_match("/[0-9A-Za-z\"]/", $realname)) {
			die("Wrong realname <b>$realname</b> on line $line");
		}
		if($sex!="男" && $sex!="女") {
			die("Wrong sex <b>$sex</b> on line $line");
		}
		if(preg_match("/\"/", $majorname)) {
			die("Invalid majorname <b>$majorname</b> on line $line");
		}

		// check if exist
		$sql = "SELECT * FROM `user` WHERE `username`=\"$username\"";
		$result = mysql_query($sql, $conn);
		$count = mysql_num_rows($result);
		if($count) {
			die("Username <b>$username</b> already exists on line $line");
		}

		if($majorname=="") {
			die("No major on line $line");
		}
		// find major id
		$sql = "SELECT * FROM `major` WHERE `name`=\"$majorname\"";
		$result = mysql_query($sql, $conn);
		if(!$result) {
			die("Error:" . mysql_error());
		}
		$count = mysql_num_rows($result);
		if(!$count) {
			die("Major <b>$majorname</b> not found on line $line");
		}
	}

	foreach($array as $row) {
		$line++;

		$username = $row[0];
		$realname = $row[1];
		$password = $username;
		$hashed_password = md5($password);
		$sex = $row[2]=="男" ? 0 : 1;
		$majorname = $row[3];
		// find major id
		$sql = "SELECT * FROM `major` WHERE `name`=\"$majorname\"";
		$result = mysql_query($sql, $conn);
		if(!$result) {
			die("Error:" . mysql_error());
		}
		$count = mysql_num_rows($result);
		if(!$count) {
			die("Major <b>$majorname</b> not found.");
		}
		$row = mysql_fetch_array($result);
		$major = $row['id'];

		// insert
		$sql = "INSERT INTO user(username, password, realname, sex, major)
		        VALUES(\"$username\", \"$hashed_password\", \"$realname\", $sex, $major);";
		$result = mysql_query($sql, $conn);
		if(!$result) {
			die("Error:" . mysql_error());
		}
	}
	die("导入成功！");
}
?>

<html>
<body>
导入学生列表
<form method="post" enctype="multipart/form-data">
	<input type="file" name="file" /> <br />
	<input type="submit" name="submit" value="上传" />
</form>
</body>
</html>
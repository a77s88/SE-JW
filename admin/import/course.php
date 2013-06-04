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
		$id = $row[0];
		$name = $row[1];
		$students = $row[2];
		$description = $row[3];
		if(!preg_match("/^[0-9]+$/", $id)) {
			die("Invalid id <b>$id</b> on line $line");
		}
		if($name=="") {
			die("Empty name <b>$name</b> on line $line");
		}
		if(!preg_match("/^[0-9]+$/", $students)) {
			die("Invalid students <b>$students</b> on line $line");
		}
		if($description=="") {
			die("Empty description <b>$description</b> on line $line");
		}

		// check if exist
		$sql = "SELECT * FROM `course` WHERE `id`=\"$id\"";
		$result = mysql_query($sql, $conn);
		$count = mysql_num_rows($result);
		if($count) {
			die("id <b>$id</b> already exists on line $line");
		}

	}

	foreach($array as $row) {
		$line++;

		$id = $row[0];
		$name = $row[1];
		$students = $row[2];
		$description = $row[3];

		// insert
		$sql = "INSERT INTO course(id, name, students, description)
		        VALUES($id, \"$name\", $students, \"$description\");";
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
导入课程列表
<form method="post" enctype="multipart/form-data">
	<input type="file" name="file" /> <br />
	<input type="submit" name="submit" value="上传" />
</form>
</body>
</html>
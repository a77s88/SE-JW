<?php
require("../../config.php");
session_start();

if(!$_SESSION['logedin']) {
	header("Location: login.php");
	die();
}

if($_SESSION['isadmin']) {
	die("You are not student.");
}

$id = $_REQUEST['id'];
if(!preg_match("/^[0-9]+$/", $id)) {
	die("Wrong input");
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

$uid = $_SESSION['uid'];
$sql = "SELECT * FROM `student_course` WHERE `course_id`=$id AND `user_id`=$uid";
$result = mysql_query($sql);
if (!$result) {
	die('Error: ' . mysql_error());
}

$count = mysql_num_rows($result);
if($count) {
	die("您已选过此课程");
}

$sql = "INSERT INTO `student_course`(user_id, course_id) VALUES($uid, $id)";
$result = mysql_query($sql);
if (!$result) {
	die('Error: ' . mysql_error());
}

echo "选课成功！";
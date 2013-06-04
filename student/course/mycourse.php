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
$sql = "SELECT `course`.`name` FROM `student_course`, `course` WHERE `student_course`.`user_id`=$uid AND `student_course`.`course_id`=`course`.`id`";
$result = mysql_query($sql);
if (!$result) {
	die('Error: ' . mysql_error());
}

$array = array();
while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
	$array[] = $row;
}

foreach($array as $row) {
	$str = implode(",", $row);
	echo $str."<br />";
}
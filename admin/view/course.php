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

$conn = mysql_connect(db_server, db_username, db_password);
if (!$conn) {
	die('Could not connect: ' . mysql_error());
}
mysql_query("SET NAMES utf8");
$db_selected = mysql_select_db(db_database, $conn);
if (!$db_selected) {
	die ("Could not select db : " . mysql_error());
}

$sql = "SELECT * FROM `course`";
$result = mysql_query($sql);
$array = array();
while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
	$array[] = $row;
}

foreach($array as $row) {
	$str = implode(",", $row);
	echo $str."<br />";
}
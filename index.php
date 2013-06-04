<?php
require("config.php");
session_start();

if(!$_SESSION['logedin']) {
	header("Location: login.php");
	die();
}

if($_SESSION['isadmin']) {
	header("Location: admin");
	die();
}

header("Location: student");
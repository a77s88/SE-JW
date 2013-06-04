<?php
require("../config.php");
session_start();

if(!$_SESSION['logedin']) {
	header("Location: ../login.php");
	die();
}

if(!$_SESSION['isadmin']) {
	die("You are not administrator.");
}
?>
Hello admin <?php echo $_SESSION['username']; ?>! <br /><br />
学生管理
<ul>
	<li><a href="view/student.php">学生管理</a></li>
	<li><a href="import/student.php">学生导入</a></li>
</ul>
课程管理
<ul>
	<li><a href="view/course.php">课程管理</a></li>
	<li><a href="import/course.php">课程导入</a></li>
</ul>
<br />
<a href="../logout.php">注销</a>
<?php
require("../config.php");
session_start();

if(!$_SESSION['logedin']) {
	header("Location: ../login.php");
	die();
}

if($_SESSION['isadmin']) {
	die("You are not student.");
}
?>
Hello student <?php echo $_SESSION['username']; ?>! <br />
<br />
<a href="course/mycourse.php">我的课程</a>
<a href="course/selectcourse.php">学期选课</a>
<a href="changepassword.php">修改密码</a>
<br />
<a href="../logout.php">注销</a>
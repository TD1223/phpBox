<?php
session_start();

if (isset($_COOKIE["PHPSESSID"])) {
	echo "cookie exists";
    setcookie("PHPSESSID", '', time() - 1800, '/');
}

session_destroy();

header("Location: login.php");
?>
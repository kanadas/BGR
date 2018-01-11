<?php
session_start();
if(basename($_SERVER['PHP_SELF']) != "index.php" && !isset($_SESSION['userid'])) header("Location: index.php");
?>
<html>
	<head>
		<title>Board Game Recommendations</title>
	</head>
	<body>

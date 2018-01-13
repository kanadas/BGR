<html>
	<head>
		<title>Board Game Recommendations</title>
	</head>
	<body>
<?php
	if(isset($_SESSION['userid'])) {
		echo "<ul>";
		echo "<li>Logged as: <b>".$_SESSION['username']."</b> <a href='logout.php'><i>Logout</i></a></li>";
		echo "<li><a href='usergames.php'>Browse rated games</a></li>";
	   	echo "</ul>";
	}
?>	

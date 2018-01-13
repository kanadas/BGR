<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL - E_NOTICE);
	session_start();

	if(!isset($_GET['id']) && !isset($_SESSION['userid'])) header("Location: index.php");
	$userid = $_GET['id'] ?: $_SESSION['userid'];

	include 'header.php';
	include 'table.php';
	$conn = oci_connect("tk385674", "salamandra");
	$sql = "SELECT ROWNUM as num, id, title, score, rating FROM (
			SELECT Game.id as id, 
				Game.name as title, 
				Game.BGGScore as score,
				Rating.value as rating
			FROM Game LEFT JOIN Rating 
			ON Game.id = Rating.gameid 
			WHERE Rating.userid = $userid 
			ORDER BY rating DESC)";
	
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt);
	drowTable(array("#", "Game title", "BGGScore", "Your rating"), 
	   array("NUM", "TITLE", "SCORE", "RATING"), $stmt, "game.php", "id", "ID");
	include 'footer.php';
?>

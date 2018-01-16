<?php
#ini_set('display_errors', 'On');
#error_reporting(E_ALL - E_NOTICE);

session_start();
if(!isset($_SESSION['userid'])) header("Location: index.php");

include 'header.php';
include 'table.php';
$conn = oci_connect("tk385674", "salamandra");
$sql = "SELECT Game.id as id, 
			Game.name as title, 
			Game.BGGScore as score,
			Rating.value as rating
		FROM Game LEFT JOIN Rating 
		ON Game.id = Rating.gameid 
		AND Rating.userid = ".$_SESSION['userid']."
		ORDER BY Game.BGGScore DESC";

$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
drowTable(array("Game title", "BGGScore", "Your rating"), 
   array("TITLE", "SCORE", "RATING"), $stmt, "game.php", "id", "ID");
include 'footer.php';
?>

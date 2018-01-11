<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

include 'header.php';
include 'table.php';
$conn = oci_connect("tk385674", "salamandra");
$sql = "SELECT Game.id as id, 
			Game.name as name, 
			Game.BGGScore as BGGScore,
			Rating.value as rating
		FROM Game LEFT JOIN Rating 
		ON Game.id = Rating.gameid 
		AND Rating.userid = ".$_SESSION['userid']."
		ORDER BY Game.BGGScore DESC";

$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
drowGameTable($stmt);
include 'footer.php';
?>

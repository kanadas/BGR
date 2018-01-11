<?php
include 'header.php';
include 'table.php';
$conn = oci_connect("tk385674", "salamandra");
$sql = "SELECT Game.id as id, 
			Game.name as name, 
			Game.BGGScore as BGGScore,
			Rating.value as rating
		FROM Game LEFT JOIN Rating 
		ON Game.id == Rating.gameid 
		WHERE Rating.userid = ".$_SESSION['userid'];
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
drowGameTable(oci_fetch_array($stmt));
include 'footer.php';
?>

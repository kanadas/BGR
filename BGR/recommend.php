<?php
#ini_set('display_errors', 'On');
#error_reporting(E_ALL - E_NOTICE);
session_start();
if(!isset($_SESSION['userid'])) header("Location: index.php");
$userid = $_SESSION['userid'];
$conn = oci_connect('tk385674', 'salamandra');
include 'header.php';

$mytags = "SELECT UserTagRating.tagid FROM UserTagRating WHERE userid = $userid";

$mygames = "SELECT Game.id FROM Game, Rating WHERE Game.id = Rating.gameid AND Rating.userid = $userid";

$sql = "SELECT Game.id, Game.name, Game.BGGScore, 
	SUM((SELECT utr.avgRating * TagType.weight FROM UserTagRating utr, TagType
		WHERE TagType.id = Tag.tagtype AND utr.userid = $userid AND utr.tagid = Tag.id)) as similarity
	FROM Game, GameTag, Tag 
	WHERE GameTag.gameid = Game.id AND GameTag.tagid = Tag.id AND Tag.id IN ($mytags) AND Game.id NOT IN ($mygames)
	GROUP BY Game.id, Game.name, Game.BGGScore ORDER BY similarity DESC";

$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

include 'table.php';

drowTable(array("Game title", "BGGScore"), array("NAME", "BGGSCORE"), $stmt, "game.php", "id", "ID");

include 'footer.php';
?>

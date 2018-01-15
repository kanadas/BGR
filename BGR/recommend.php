<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL - E_NOTICE);
session_start();
if(!isset($_SESSION['userid'])) header("Location: index.php");
$userid = $_SESSION['userid'];
$conn = oci_connect('tk385674', 'salamandra');
include 'header.php';

$tagtypesql = "SELECT id, name, weight FROM TagType";
$tagtypestmt = oci_parse($conn, $tagtypesql);

oci_execute($tagtypestmt);
$sql = "SELECT Game.id, Game.name, Game.BGGScore";
$tagsum = "0";
$tagnames = array();
while($tagtyperow = oci_fetch_array($tagtypestmt, OCI_BOTH))
{
	$tagtype = $tagtyperow['ID'];
	$tagname = $tagtyperow['NAME'];
	$tagweight = $tagtyperow['WEIGHT'];
	$tagsum .= " + $tagname";
	array_push($tagnames, $tagname);
	$sql .= ", SUM(case Tag.tagtype when $tagtype then (SELECT UserTagRating.avgRating FROM UserTagRating WHERE UserTagRating.userid = $userid AND UserTagRating.tagid = Tag.id) else 0 end) * $tagweight as $tagname ";
}

$mytags = "SELECT UserTagRating.tagid FROM UserTagRating WHERE userid = $userid";

$mygames = "SELECT Game.id FROM Game, Rating WHERE Game.id = Rating.gameid AND Rating.userid = $userid";

$sql .= "FROM Game, GameTag, Tag 
	WHERE GameTag.gameid = Game.id AND GameTag.tagid = Tag.id AND Tag.id IN ($mytags) AND Game.id NOT IN ($mygames)
	GROUP BY Game.id, Game.name, Game.BGGScore ORDER BY $tagsum DESC";

$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

include 'table.php';

#oci_execute($gametagsstmt);
#drowTable($tagnames, $tagnames, $gametagsstmt, "game.php", "id", "ID");

drowTable(array("Game title", "BGGScore"), array("NAME", "BGGSCORE"), $stmt, "game.php", "id", "ID");

include 'footer.php';
?>

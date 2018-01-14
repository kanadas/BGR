<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL - E_NOTICE);
session_start();
if(!isset($_SESSION['userid'])) header("Location: index.php");
$userid = $_SESSION['userid'];
include 'header.php';

$playertagssql = "SELECT Tag.id, Tag.tagtype, SUM(Rating.value) / COUNT(*) as avgRating FROM Rating, Game, GameTag, Tag WHERE
	Rating.userid = $userid AND
	Rating.gameid = Game.id AND
	Game.id = GameTag.gameid AND
	Tag.id = GameTag.tagid
	GOUP BY Tag.id";


$tagtypesql = "SELECT id, name, weight FROM TagType";
$tagtypestmt = oci_parse($conn, $tagtypesql);
oci_execute($tagtypestmt);
$gametagssql = "SELECT null";
$tagtypes = 0;
while($tagtyperow = oci_fetch_array($tagtypestmt, OCI_BOTH))
{
	++$tagtypes;
	$tagtype = $tagtyperow['ID'];
	$tagname = $tagtyperow['NAME'];
	$tagweight = $tagtyperow['WEIGHT'];
	$gametagssql .= ", SUM(case Tag.tagtype when $tagtype then 1 else 0 end) as $tagname ";
}
$gametagssql .= "FROM Game, GameTag, Tag WHERE Game.id = $id AND GameTag.gameid = Game.id AND GameTag.tagid = Tag.id GROUP BY Game.id";

$gametagsstmt = oci_parse($conn, $gametagssql);
oci_execute($gametagsstmt);
$gametagsrow = oci_fetch_array($gametagsstmt, OCI_BOTH);
oci_execute($tagtypestmt);
$sql = "SELECT Game.id, Game.name, Game.BGGScore";
$tagsum = "0";
$tagnames = array();
while($tagtyperow = oci_fetch_array($tagtypestmt, OCI_BOTH))
{
	$tagtype = $tagtyperow['ID'];
	$tagname = $tagtyperow['NAME'];
	$tagweight = $tagtyperow['WEIGHT'];
	$totalcount = $gametagsrow[strtoupper($tagname)];
	$tagsum .= " + $tagname";
	array_push($tagnames, $tagname);
	$sql .= ", SUM(case Tag.tagtype when $tagtype then 1 else 0 end) * $tagweight / $totalcount as $tagname ";
}

$mytags = "SELECT id FROM Tag, GameTag WHERE id = tagid AND gameid = $id";

$sql .= "FROM Game, GameTag, Tag 
	WHERE Game.id != $id AND GameTag.gameid = Game.id AND GameTag.tagid = Tag.id AND Tag.id IN ($mytags)
	GROUP BY Game.id, Game.name, Game.BGGScore ORDER BY $tagsum DESC";

$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

include 'table.php';

#oci_execute($gametagsstmt);
#drowTable($tagnames, $tagnames, $gametagsstmt, "game.php", "id", "ID");

drowTable(array("Game title", "BGGScore"), array("NAME", "BGGSCORE"), $stmt, "game.php", "id", "ID");

include 'footer.php';
?>

<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL - E_NOTICE);
session_start();
if(!isset($_SESSION['userid'])) header("Location: index.php");
if(!isset($_GET['id'])) header("Location: list.php");

$id = $_GET['id'];
$sql = "SELECT name,
	year,
	description,
	BGGScore,
	MinPlayers,
	MaxPlayers,
	AvgPlayTime,
	complexity,
	value as rating
	FROM Game LEFT JOIN Rating ON
	id = gameid AND userid = ".$_SESSION['userid']."
	WHERE id = $id";
$conn = oci_connect("tk385674", "salamandra");
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
$game = oci_fetch_array($stmt);
if($game == NULL) header("Location: list.php");
$name = $game['NAME'] ?: "-";
$year = $game['YEAR'] ?: "-";
$description = $game['DESCRIPTION'] ? $game['DESCRIPTION']->load() : "-";
$score = $game['BGGSCORE'] ?: "-";
$minplayers = $game['MINPLAYERS'] ?: "-";
$maxplayers = $game['MAXPLAYERS'] ?: "-";
$time = $game['AVGPLAYTIME'] ?: "-";
$complexity = $game['COMPLEXITY'] ?: "-";
$rating = $game['RATING'] ?: "-";
$designer = $game['DESIGNER'] ?: "-";
include 'header.php';
echo <<<EOT
<table width="100%">
	<tr><th width="100%" colspan="4">$name ($year)</th></tr>
	<tr>
		<td width="40%" colspan="2" align="center"><b>Board Game Geek Score: $score</b></td>
		<td width="60%" colspan="2" align="center"><b>
			Your Score: 
			<form action="rate.php" method="GET">
			<input type="hidden" name="id" value="$id" />
			<select name="rating">
EOT;
echo "<option value ".($rating == '-' ? "selected" : "").">-</option>";
for($i = 1; $i <= 10; ++$i) echo "<option value='$i' ".($rating == $i ? "selected" : "").">$i</option>";	
echo <<<EOT
		</select><input type='submit' value='Rate' /></form>
		</b></td>
	</tr>
	<tr>
		<td width="25%">Players: $minplayers - $maxplayers</td>
		<td width="25%">Average playing time: $time</td>
		<td width="25%">BGG Complexity Rating: $complexity</td>
	</tr>
</table>
<h3>Description:</h3><br>
$description
<br><br>
<h2>Similar games:</h2><br>
EOT;

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

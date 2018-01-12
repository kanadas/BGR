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
	designer,
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
echo "<option value disabled ".($rating == '-' ? "selected" : "").">-</option>";
for($i = 1; $i <= 10; ++$i) echo "<option value='$i' ".($rating == $i ? "selected" : "").">$i</option>";	
echo <<<EOT
		</select><input type='submit' value='Submit' /></form>
		</b></td>
	</tr>
	<tr>
		<td width="25%">Players: $minplayers - $maxplayers</td>
		<td width="25%">Average playing time: $time</td>
		<td width="25%">BGG Complexity Rating: $complexity</td>
		<td width="25%">Designer: $designer</td>
	</tr>
</table>
<h3>Description:</h3><br>
$description
EOT;
include 'footer.php';
?>

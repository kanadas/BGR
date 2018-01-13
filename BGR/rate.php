<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL - E_NOTICE);
session_start();
if(!isset($_SESSION['userid'])) header("Location: index.php");
if(!isset($_GET['id'])) header("Location: list.php");
if(!isset($_GET['rating'])) header("Locatin: game.php?id=".$_GET['id']);
$rating = intval($_GET['rating']);
if($rating < 1 || $rating > 10) header("Location: game.php?id=".$_GET['id']);
$conn = oci_connect("tk385674", "salamandra");
$sql = "DELETE FROM Rating WHERE gameid = ".$_GET['id']." AND userid = ".$_SESSION['userid'];
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
$sql = "INSERT INTO Rating (userid, gameid, value) VALUES ('".$_SESSION['userid']."', '".$_GET['id']."', '".$_GET['rating']."')";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);
header("Location: game.php?id=".$_GET['id']);
?>

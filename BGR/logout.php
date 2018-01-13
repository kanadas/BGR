<?php	
	ini_set('display_errors', 'On');
	error_reporting(E_ALL - E_NOTICE);
	session_start();
	
	$_SESSION['userid'] = NULL;
	$_SESSION['username'] = NULL;
	header('Location: index.php');
	die();
?>


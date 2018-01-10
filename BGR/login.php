<?php
	function ValidateLoginSubmission()
	{
		return !isset($_POST['name']) ||
			!isset($_POST['pass']) ||
			!isset($_POST['submit']);
	}

	function RegisterUser($conn, $name, $pass)
	{
		$sql = "SELECT name FROM Users WHERE name = ". $name;
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt);
		if(oci_fetch_array($stmt)) return false;
		$sql = "INSERT INTO Users (name, pass) VALUES ('". $name ."', '". md5($pass) ."')";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt);
		oci_commit();
		return true;
	}

	$conn = oci_connect("tk385674", "salamandra");

	if(!ValidateLoginSubmission() || !RegisterUser($conn, $_POST['name'], $_POST['pass']))
		header("Location: index.php?error=server");
	else header("Location: list.php");
	die();
?>	


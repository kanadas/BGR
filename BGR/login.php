<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL | E_STRICT);
	
	
	include 'loginerror.php';

	function ValidateLoginSubmission()
	{
		if (isset($_POST['name']) || 
			isset($_POST['pass']) ||
			($_POST['submit'] != 'Register' && $_POST['submit'] != 'Login'))
		{
			return LoginError::WrongInput;
		}
		return NULL; 
	}

	function RegisterUser($conn, $name, $pass)
	{
		$select = "SELECT id FROM Users WHERE name = ". $name;
		$stmt = oci_parse($conn, $select);
		if(!oci_execute($stmt)) return LoginError::DatabaseError;
		if(oci_fetch_array($stmt)) return LoginError::LoginExists;
		$sql = "INSERT INTO Users (name, pass) VALUES ('". $name ."', '". md5($pass) ."')";
		$stmt = oci_parse($conn, $sql);
		if(!oci_execute($stmt)) return LoginError::DatabaseError;
		return NULL;
	}

	function LoginUser($conn, $name, $pass)
	{
		$sql = "SELECT id FROM Users WHERE name = ". $name ." AND pass = ". md5($pass) ."')";
		$stmt = oci_parse($conn, $sql);
		if(!oci_execute($stmt)) return LoginError::DatabaseError;
		if(($id = oci_fetch_array($stmt)))
		{
			$_SESSION['userid'] = $id;
			return true;
		}
		return LoginError::UserNotExists;
	}

	function ProcessRequest($name, $pass, $submit)
	{
		$conn = oci_connect("tk385674", "salamandra");

		echo "kupa";

		if(!$conn) return LoginError::DatabaseError;
		$err = NULL;
		if($submit == 'Register') $err = RegisterUser($name, $pass);

		echo "dupa";

		if($err == NULL) $err = LoginUser($name, $pass);
		if($submit == 'Register' && $err == LoginError::UserNotExists) return LoginError::ServerError;

		echo "fajnie";

		return $err;
	}

	if($err = ValidateLoginSubmission())
	{

		header("Location: index.php?error=". $err);
	
		#echo "header sent";

		die();
	}

	echo "validated";

	if(($err = ProcessRequest($_POST['name'], $_POST['pass'], $_POST['submit'])) != NULL)
	{
		header("Location: index.php?error=". $err);
		die();
	}
	header("Location: list.php");
	die();
?>	


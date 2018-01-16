<?php
	session_start();
	#ini_set('display_errors', 'On');
	#error_reporting(E_ALL | E_STRICT);
	
	include 'loginerror.php';

	function ValidateLoginSubmission()
	{
		if (!isset($_POST['login']) || 
			!isset($_POST['pass']) || 
			($_POST['submit'] != 'Register' && $_POST['submit'] != 'Login'))
		{
			return LoginError::WrongInput;
		}
		return NULL; 
	}

	function RegisterUser($conn, $name, $pass)
	{
		$select = "SELECT id FROM Users WHERE login = '$name'";
		$stmt = oci_parse($conn, $select);
		if(!oci_execute($stmt)) return LoginError::DatabaseError;
		if(oci_fetch_array($stmt)) return LoginError::LoginExists;
		$sql = "INSERT INTO Users (login, pass) VALUES ('". $name ."', '". md5($pass) ."')";
		$stmt = oci_parse($conn, $sql);
		if(!oci_execute($stmt)) return LoginError::DatabaseError;
		return NULL;
	}

	function LoginUser($conn, $name, $pass)
	{
		$sql = "SELECT id FROM Users WHERE login = '". $name ."' AND pass = '". md5($pass) ."'";
		$stmt = oci_parse($conn, $sql);
		if(!oci_execute($stmt)) return LoginError::DatabaseError;
		if($id = oci_fetch_array($stmt))
		{
			$_SESSION['userid'] = $id[0];
			$_SESSION['username'] = $name;
			return NULL;
		}
		return LoginError::UserNotExists;
	}

	function ProcessRequest($name, $pass, $submit)
	{
		$conn = oci_connect("tk385674", "salamandra");
		if(!$conn) return LoginError::DatabaseError;
		$err = NULL;
		if($submit == 'Register') $err = RegisterUser($conn, $name, $pass);
		if($err == NULL) $err = LoginUser($conn, $name, $pass);
		if($submit == 'Register' && $err == LoginError::UserNotExists) return LoginError::ServerError;
		return $err;
	}

	if($err = ValidateLoginSubmission())
	{
		header("Location: index.php?error=". $err);
		die();
	}

	if($err = ProcessRequest($_POST['login'], $_POST['pass'], $_POST['submit']))
	{
		header("Location: index.php?error=". $err);
		die();
	}
	header("Location: list.php");
	die();
?>	


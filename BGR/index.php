<?php 
	include 'header.php'; 
	include 'loginerror.php';
	if(isset($_GET['error']))
	{
		echo "<a class='error'>";
		switch ($_GET['error'])
		{
		case LoginError::WrongInput : 
			echo "Enter login and password"; 
			break;
		case LoginError::LoginExists : 
			echo "User with this login already exists"; 
			break;
		case LoginError::UserNotExists : 
			echo "Login or password incorrect"; 
			break;
		case LoginError::ServerError :
			echo "Server error occurred";
			break;
		case LoginError::DatabaseError :
			echo "Database error occurred";
			break;
		default: 
			echo "Unknown error occured"; 
		}
		"</a>";
	}
?>
<form id='login' action='login.php' method='POST' accept-charset='ISO-8859-1'>
	<legend>Login/Register</legend>
	<label for='login'>Login: </label>
	<input type='text' name='login' id='login' maxlength=50 required/>
	<label for='pass'>Password: </label>
	<input type='password' name='pass' id='pass' maxlength=50 required/>
	<input type='submit' name='submit' value='Login'>
	<input type='submit' name='submit' value='Register'>
</form>
<?php include 'footer.php'; ?>

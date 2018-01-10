<?php include 'header.php'; 
	if(isset($_GET['error']))
	{
		echo "<a class='error'>";
		switch ($_GET['error'])
		{
			case "server": echo "Server error occured"; break;
			case "login": echo "Login or password incorrect"; break;
			case "register": echo "User with this login already exists"; break;
			default: echo "Unknown error occured"; break;
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

<?php
abstract class LoginError
{
	const WrongInput = 1;
	const LoginExists = 2;
	const UserNotExists = 3;
	const ServerError = 4;
	const DatabaseError = 5;
}
?>

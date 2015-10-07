<?php
interface UserAuthenticator
{
	function isAuthenticated($username, $password);	
}
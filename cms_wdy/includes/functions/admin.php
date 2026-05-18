<?php

function get_admin($email)
{
	global $conn;
	$where = sprintf('email = "%s"', $email);
	return table_fetch_row(TBL_ADMIN, $where);
}

function login($email, $password)
{
	$admin = get_admin($email);
	
	if ($admin == false) {
		return false;
	} else {
		if ($admin['email'] == $email && $admin['password'] == md5($password)) {
			return true;
		} else {
			return false;
		}
	}
}

function logout_admin()
{
	unset($_SESSION['admin']);
}

function admin_exists($email)
{
	$admin = get_admin($email);
	
	if ($admin == false) {
		return false;
	} else {
		return true;
	}
}

?>
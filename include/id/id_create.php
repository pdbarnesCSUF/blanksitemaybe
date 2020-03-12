<?php
/**
 *  @file id_create.php
 *  @brief Account creation
 */
/**
 *  @brief Checks if username is valid and available.
 *  
 *  @param [in] $username Username to check
 *  @return boolean True is valid and available. Otherwise, string of what error
 *  
 *  @details Checks if uses allowed characters. Must start with Alphanumeric. Can also
 *  contain period(.),dash(-),underscore(_).
 */
function id_create_check_username($username)
{
	global $iddbPDO, $sitesettings;
	id_verbose('(id_create_check_username)');
	//============================
	//check validity
	if (strlen($username) < $sitesettings['username_min_length'] && strlen($username) > 0)
	{
		id_message('Username too short');
		id_debug('Username too short; '.$sitesettings['username_min_length'].' minimum');
		return 'Username too short';
	}
	if (strlen($username) > $sitesettings['username_max_length'])
	{
		id_message('Username too long');
		id_debug('Username too long; '.$sitesettings['username_max_length'].' maximum');
		return 'Username too long';
	}
	//standard POSIX usernames + no symbol start
	$filtered_username = preg_replace("/[a-zA-Z0-9][a-zA-Z0-9-._]*/",'',$username);
	if ($filtered_username != '')
	{
		id_message('Invalid characters in username');
		id_debug('Invalid characters in username: /[a-zA-Z0-9][a-zA-Z0-9-._]*/');
		return 'Invalid characters in username';
	}
	//check db
	$PDO_id_create_check_username = $iddbPDO->prepare("	SELECT username
															FROM iduser_account
															WHERE username = :st_username;");
	$PDO_id_create_check_username->bindParam(':st_username', $username);
	id_verbose('checking db...');
	if ($PDO_id_create_check_username->execute())
	{
		if ($PDO_id_create_check_username->rowCount() == 0)
		{
			id_verbose('none-found');
			return true;
		}
		else
		{
			id_verbose('already used');
			return 'Already Used';
		}
	}
	else
	{
		id_debug('Username db check failed');
		return 'Database error';
	}
}
/**
 *  @brief Checks if password is valid
 *  
 *  @param [in] $password The password to check.
 *  @return boolean True if valid. Otherwise, string of what error
 *  
 *  @details Checks if password is valid. This is mostly a length check.
 */
function id_create_check_password($password)
{
	global $sitesettings;
	id_verbose('(id_create_check_password)');
	//============================
	//check validity
	if (strlen($password) < $sitesettings['password_min_length'] && strlen($password) > 0)
	{
		id_message('Password too short');
		id_debug('Password too short; '.$sitesettings['password_min_length'].' minimum');
		return 'Password too short';
	}
	return true;
}
/**
 *  @brief Checks if email is valid and available.
 *  
 *  @param [in] $email Email to check
 *  @return boolean true if email is ok to use. Otherwise, string of what error
 *  
 *  @details Checks if email is valid and not already taken.
 */
function id_create_check_email($email)
{
	global $iddbPDO;
	id_verbose('(id_create_check_email)');
	//============================
	//check validity
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		id_message('Email invalid');
		id_debug('Email invalid');
		return 'Email invalid';
	}
	//check db
	$PDO_id_create_check_email = $iddbPDO->prepare("	SELECT email
															FROM iduser_account
															WHERE email = :st_email;");
	$PDO_id_create_check_email->bindParam(':st_email', $email);
	id_verbose('checking db...');
	if ($PDO_id_create_check_email->execute())
	{
		if ($PDO_id_create_check_email->rowCount() == 0)
		{
			id_verbose('none-found');
			return true;
		}
		else
		{
			id_message('Email Already In Use');
			id_debug('Email Already In Use');
			return 'Email Already In Use';
		}
	}
	else
	{
		id_debug('Email db check failed');
		return 'Database Error';
	}
}
/**
 *  @brief Unconditionally creates id account. Do input checks before hand!
 *  
 *  @param [in] $email email
 *  @param [in] $username username
 *  @param [in] $password password plaintext
 *  @return boolean database commit success
 *  
 *  @details Takes info and creates the account. It will not do any checks.
 */
function id_create_account($email, $username, $password)
{
	global $iddbPDO;
	id_verbose('(id_create_create_account)');
	//============================

	//HASH VS PLAIN TEXT PASSWORD SAVING HERE!!!!!=============
	$password	= password_hash($password,PASSWORD_BCRYPT);	//TOO EASY D: (i mean that as a good thing)

	//gotta seperate it like this because prepare/execute does not support multiple statements
	$iddbPDO->beginTransaction();
	$iddbPDO->query("	INSERT INTO iduser_account (username,password,email)
				VALUES ('".$username."','".$password."','".$email."');");
	$iddbPDO->query("INSERT INTO iduser_permissions (userid) VALUES (lastval());");
	$iddbPDO->query("INSERT INTO iduser_profile (userid) VALUES (lastval());");
	$result = $iddbPDO->commit();

	if ($result)
	{
		id_message('Account Created');
		id_verbose('commit:true');
		return true;
	}
	else
	{
		id_message('Error Creating Accounts');
		id_verbose('commit:failed');
		return false;
	}
}
?>

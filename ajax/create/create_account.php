<?php
/*
 * @file create_account.php
 * @desc creates a new id account
 */
/*
	inputs
	username
	password
	password confirm
	email
	device (optional) passed to login portion on success
*/
$responsearr['action'] = 00; //unknown error
//false = nothing there to talk about/ change.
//string = error to show.
//true = success

$responsearr['data'] = array(
	'username' => array(
		'valid' => false,
		'reason' => ''
	),
	'email' => array(
		'valid' => false,
		'reason' => ''
	),
	'password' => array(
		'valid' => false,
		'reason' => ''
	),
	'password_confirm' => array(
		'valid' => false,
		'reason' => ''
	),
	'sessionid' => array(
		'valid' => false,
		'reason' => ''
	)
);
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../../include/id.php'))
{
	id_verbose('PAGE:ajax/create/create_account.php');
	id_init();
	//do something
	//validation
	$validated = true;
	//stuff.....
	//---username
	if (isset($_POST['username']))
	{
		$check_result = id_create_check_username($_POST['username']);
		if ($check_result === true)
		{
			id_verbose('username ok');
			$responsearr['data']['username']['valid'] = true;
			$responsearr['data']['username']['reason'] = "User Available";
		}
		else
		{
			$validated = false;
			id_message($check_result);
			id_debug($check_result);
			$responsearr['action'] = 7; //missing/bad input
			$responsearr['data']['username']['valid'] = false;
			$responsearr['data']['username']['reason'] = $check_result;
		}
	}//isset POST username
	else
	{
		$validated = false;
		id_message("username missing");
		id_debug("username missing");
		$responsearr['action'] = 7; //missing/bad input
		$responsearr['data']['username']['valid'] = false;
		$responsearr['data']['username']['reason'] = "Missing";
	}
	//---email
	if (isset($_POST['email']))
	{
		$check_result = id_create_check_email($_POST['email']);
		if ($check_result === true)
		{
			id_verbose('email ok');
			$responsearr['data']['email']['valid'] = true;
			$responsearr['data']['email']['reason'] = "E-mail OK";
		}
		else
		{
			$validated = false;
			id_message($check_result);
			id_debug($check_result);
			$responsearr['action'] = 7; //missing/bad input
			$responsearr['data']['email']['valid'] = false;
			$responsearr['data']['email']['reason'] = $check_result;
		}
	}//isset POST email
	else
	{
		$validated = false;
		id_message("email missing");
		id_debug("email missing");
		$responsearr['action'] = 7; //missing/bad input
		$responsearr['data']['email']['valid'] = false;
		$responsearr['data']['email']['reason'] = "Missing";
	}
	//---password
	if (isset($_POST['password']))
	{
		$check_result = id_create_check_password($_POST['password']);
		id_debug("PW:".$check_result);
		if ($check_result === true)
		{
			id_verbose('password ok');
			$responsearr['data']['password']['valid'] = true;
			$responsearr['data']['password']['reason'] = "Password OK";
		}
		else
		{
			$validated = false;
			id_message("PW:".$check_result);
			id_debug("PW:".$check_result);
			$responsearr['action'] = 7; //missing/bad input
			$responsearr['data']['password']['valid'] = false;
			$responsearr['data']['password']['reason'] = $check_result;
		}
	}//isset POST password
	else
	{
		$validated = false;
		id_message("password missing");
		id_debug("password missing");
		$responsearr['action'] = 7; //missing/bad input
		$responsearr['data']['password']['valid'] = false;
		$responsearr['data']['password']['reason'] = "Missing";
	}
	//---password_confirm
	if (isset($_POST['password_confirm']))
	{
		$check_result = ($_POST['password'] == $_POST['password_confirm']) ? true : "Does not match";
		if ($check_result === true)
		{
			id_verbose('password confirm ok');
			$responsearr['data']['password_confirm']['valid'] = true;
			$responsearr['data']['password_confirm']['reason'] = "Password Matches";
		}
		else
		{
			$validated = false;
			id_message("PWc:".$check_result);
			id_debug("PWc:".$check_result);
			$responsearr['action'] = 7; //missing/bad input
			$responsearr['data']['password_confirm']['valid'] = false;
			$responsearr['data']['password_confirm']['reason'] = $check_result;
		}
	}//isset POST password_confirm
	else
	{
		$validated = false;
		id_message("password confirm missing");
		id_debug("password confirm missing");
		$responsearr['action'] = 7; //missing/bad input
		$responsearr['data']['password_confirm']['valid'] = false;
		$responsearr['data']['password_confirm']['reason'] = "Missing";
	}
	//-----its valid, continue!-----
	if ($validated)
	{
		$responsearr['action'] = 0; //unknown error
		id_verbose("Validated");
		if (id_create_account($_POST['email'],$_POST['username'],$_POST['password']))
		{
			id_verbose("Created");
			$device = isset($_POST['device']) ? $_POST['device'] : 'web/fallback';
			if (id_session_login($_POST['username'],$_POST['password'],$device))
			{
				id_verbose("Logged In");
				//cookie set
				$id_SESSION['login']	=	true;
				$expire_date = time()+60*60*24*$sitesettings['session_expire_days'];
				setcookie('id_userid', $id_SESSION['id'], $expire_date, '/', $sitesettings['cookiesite']);
				setcookie('id_sessid', $id_SESSION['sessid'] , $expire_date, '/', $sitesettings['cookiesite']);
				id_verbose("TIME:".$expire_date);
				id_verbose("id_newEND");
				id_verbose("pU:".$id_SESSION['id']);
				id_verbose("pS:".$id_SESSION['sessid']);

				$responsearr['action'] = 1; //done
				$responsearr['data']['sessionid'] = $id_SESSION['sessid'];

				//also send an email Verification
				id_verbose("send email verification...");
				$responsearr['data']['sendemailverify'] = id_email_sendverification($id_SESSION['id']);
			}
			else
			{
				$responsearr['action'] = 8; //credentials error
			}
		}
		else
		{
			$responsearr['action'] = 0; //unknown error
		}
	}//validated
	else
	{

	}//validated else
	//-----end-----
	$responsearr['message'] = $id_SESSION['message'];
	$responsearr['debug'] = $id_SESSION['debug'];
}
else
{
	$responsearr['action'] = 02; //include error
	$responsearr['message'][] = "Server Error";
	$responsearr['debug'][] = "ID Include error";
}

echo json_encode($responsearr);
?>

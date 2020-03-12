<?php
/*
 * @file login.php
 * @desc handles login, input via post, sets cookies, returns hash
 */
 
$responsearr['action'] = 0; //unknown error
$responsearr['data'] = 0;
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../include/id.php'))
{
	id_init();
	if (id_session_loggedin())
	{
		id_message("Already logged in");
		$responsearr['action'] = 1;
		$responsearr['data'] = $id_SESSION['sessid'];
	}
	else
	{
		//start of login
		if (isset($_POST['username']) && isset($_POST['password']))
		{
			if (isset($_POST['device']))
				$login_hash = id_session_login($_POST['username'],$_POST['password'],$_POST['device']);
			else
				$login_hash = id_session_login($_POST['username'],$_POST['password']);
			if ($login_hash == false)
			{
				$responsearr['data'] = "Username or Password Incorrect";
				id_message($responsearr['data']);
				$responsearr['action'] = 8; //credentials error
			}
			else
			{
				id_verbose("new session");
				if ($id_SESSION['sessid'] === false)
				{
					//could not get session ID
					$responsearr['data'] = "Could not get session ID, Server under load or ERROR";
					id_message($responsearr['data']);
					$id_SESSION['id']	=	0;
					$responsearr['action'] = 0; // unknown error
				}
				else
				{
					$id_SESSION['login']	=	true;
					//$id_SESSION['id_current_user'] = new id_user($id_SESSION['id']);
					$expire_date = time()+60*60*24*$sitesettings['session_expire_days'];
					setcookie('id_userid', $id_SESSION['id'], $expire_date, '/', $sitesettings['cookiesite']);
					setcookie('id_sessid', $id_SESSION['sessid'] , $expire_date, '/', $sitesettings['cookiesite']);
					id_verbose("TIME:".$expire_date);
					id_verbose("id_newEND");
					id_verbose("pU:".$id_SESSION['id']);
					id_verbose("pS:".$id_SESSION['sessid']);
					$responsearr['action'] = 1; //done
					$responsearr['data'] = $id_SESSION['sessid'];
				}
			}
		}
		else
		{
			$responsearr['data'] = "Missing Info";
			id_message($responsearr['data']);
			$responsearr['action'] = 7; //bad input
		}
	}
	//-----end-----
	$responsearr['message'] = $id_SESSION['message'];
	$responsearr['debug'] = $id_SESSION['debug'];
}
else
{
	$responsearr['action'] = 2; //include error
	$responsearr['message'] = "Server Error";
	$responsearr['debug'][] = "ID Include error";
}
echo json_encode($responsearr);
?>

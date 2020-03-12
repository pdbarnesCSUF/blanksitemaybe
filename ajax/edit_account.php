<?php
/*
 * @file edit_account.php
 * @desc Edit user account info (ajax)
 */
/*
INPUT
current_password		required	required for anything to happen
userid					optional	for admins to edit other users, match against id_SESSION
username			optional	check if tolower(username) same. then set
active					optional	account active? (able to be used by user)
status					optional	status of account (note on why it is not active ex: banned)
email					optional	to change email, check valid/used. reset verify status
email_conrim			optional	to change email, confirm
new_password			optional	check valid. and == confirm
new_password_confirm	optional	confirm new pw
*/
$responsearr['action'] = 00; //unknown error
//false = nothing there to talk about/ change.
//string = error to show.
//true = success
$responsearr['data'] = array(
	'current_password' => false,
	'userid' => false,
	'username' => false,
	'active' => false,
	'status' => false,
	'email' => false,
	'new_password' => false,
	'new_password_confirm' => false
);
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../include/id.php'))
{
	id_verbose('PAGE:ajax/edit_account.php');
	id_init();
	//do something
	if (id_session_loggedin())
	{
		$responsearr['action'] = 1; //no problem - assume correct until there is an error
		//at end, if action == 1 then we know which things to change
		$changeusername = false;
		$changeactive = true; //forced true because html
		$changestatus = false;
		$changeemail = false;
		$changepassword = false;
		//check password
		if(isset($_POST['current_password']))
		{
			//check pw correct
			if (id_account_checkpassword($id_SESSION['id'], $_POST['current_password']))
			{
				//pw ok
				//-----check/do username-----
				if (isset($_POST['username']))
				{
					id_verbose("POST[username] set");
					if (strtolower($_POST['username']) == strtolower($id_SESSION['id_current_user']->username))
					{
						$changeusername = true; //yes, change username at end if all ok
					}
					else
					{
						id_message("Username doesn't match");
						id_debug("Username doesn't match");
						$responsearr['action'] = 7; //missing/bad input
						$responsearr['data']['username'] = "Must match username. Change only capitalization.";
					}
				}
				//-----check/do active-----
				id_verbose("POST[active] FORCED set");
				$changeactive = true;
				//-----check/do status-----
				if (isset($_POST['status']))
				{
					id_verbose("POST[status] set");
					$changestatus = true;
				}
				//-----check/do email-----
				if (isset($_POST['email']))
				{
					id_verbose("POST[email] set");
					//check if email malformed/formatted (standard php)
					if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
					{
						$changeemail = true; //(skipping confirmation for now)
						/*
						if (isset($_POST['email_confirm']))
						{
							if ($_POST['email'] === $_POST['email_confirm'])
							{
								$changeemail = true; //YES! change email at end if all ok
							}
							else
							{
								id_message("E-mail doesn't match");
								id_debug("E-mail === failed");
								$responsearr['action'] = 7; //missing/bad input
								$responsearr['data']['email'] = "Doesn't match";
								$responsearr['data']['email_confirm'] = "Doesn't Match";
							}
						}
						else //isset email confirm
						{
							id_message("E-mail confirmation required");
							id_debug("E-mail confirmation required");
							$responsearr['action'] = 7; //missing/bad input
							$responsearr['data']['email_confirm'] = "Required";
						}
						*/
					}
					else
					{
						id_message("E-mail invalid");
						id_debug("E-mail filter failed");
						$responsearr['action'] = 7; //missing/bad input
						$responsearr['data']['email'] = "Invalid";
					}
				}//if isset POST email
				//-----new_password-----
				if (isset($_POST['new_password']))
				{
					if ($_POST['new_password'] != '')
					{
						id_verbose("POST[new_password] set");
						if ($_POST['new_password'] === $_POST['new_password_confirm'])
						{
							if (strlen($_POST['new_password']) >= $sitesettings['password_min_length'])
							{
								$changepassword = true;
							}
							else
							{
								id_message("Password too short");
								id_debug("Password too short");
								$responsearr['action'] = 7; //missing/bad input
								$responsearr['data']['new_password'] = "Password too short";
							}
						}
						else
						{
							id_message("Passwords don't match");
							id_debug("Passwords don't match");
							$responsearr['action'] = 7; //missing/bad input
							$responsearr['data']['new_password'] = "Passwords don't match"; //<@todo should split confirm and validity to two seperate checks
							$responsearr['data']['new_password_confirm'] = "Please Confirm new password";
						}
					}
				}
				//----------DO STUFF----------
				if ($responsearr['action'] == 1)
				{
					id_verbose("action:1. time to do stuff");
					//-----username-----
					if ($changeusername)
					{
						id_verbose("change username");
						$resultusername = id_account_edit_username($id_SESSION['id'],$_POST['username']);
						if ($resultusername)
							$responsearr['data']['username'] = true;
						else
							$responsearr['data']['username'] = "An Error has occurred!";
					}
					//-----active-----
					if ($changeactive)
					{
						id_verbose("change active");
						$active = isset($_POST['active']) ? true : false;
						$resultactive = id_account_edit_active($id_SESSION['id'],$active);
						if ($resultactive)
							$responsearr['data']['active'] = true;
						else
							$responsearr['data']['active'] = "An Error has occurred!";
					}
					//-----status-----
					if ($changestatus)
					{
						id_verbose("change status");
						$resultstatus = id_account_edit_status($id_SESSION['id'],$_POST['status']);
						if ($resultstatus)
							$responsearr['data']['status'] = true;
						else
							$responsearr['data']['status'] = "An Error has occurred!";
					}
					//-----email-----
					if ($changeemail)
					{
						id_verbose("change email");
						$resultemail = id_account_edit_email($id_SESSION['id'],$_POST['email']);
						if ($resultemail)
							$responsearr['data']['email'] = true;
						else
							$responsearr['data']['email'] = "An Error has occurred!";
					}
					//-----password-----
					if ($changepassword)
					{
						id_Verbose("change password");
						$resultpassword = id_account_edit_password($id_SESSION['id'],$_POST['new_password']);
						if ($resultpassword)
							$responsearr['data']['new_password'] = true;
						else
							$responsearr['data']['new_password'] = "An Error has occurred!";
					}
				}//if action == 1
			}//if password correct
			else
			{
					id_message("Password incorrect");
					id_debug("Current password incorrect");
					$responsearr['action'] = 8; //credential failure
					$responsearr['data']['current_password'] = "Incorrect";
			}
		}//password set
		else
		{
			id_message("Current password not entered");
			id_debug("Current password not set");
			$responsearr['action'] = 7; //missing/bad input
			$responsearr['data']['current_password'] = "Missing";
		}
	}//if loggedin
	else
	{
		id_message("Not Logged In");
		id_debug("not logged in");
		$responsearr['action'] = 4; //not logged in
	}
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

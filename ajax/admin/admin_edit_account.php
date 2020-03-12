<?php
/*
 * @file admin_edit_account.php
 * @desc Admin edit user account info (ajax)
 */
/*
INPUT
userid					required	for admins to edit other users
username			optional	check if tolower(username) same. then set
active					optional	account active? (able to be used by user)
status					optional	status of account (note on why it is not active ex: banned)
email					optional	to change email, check valid/used. reset verify status
new_password			optional	check valid. and == confirm
*/
$responsearr['action'] = 00; //unknown error
//false = nothing there to talk about/ change.
//string = error to show.
//true = success
$responsearr['data'] = array(
	'userid' => false,
	'username' => false,
	'active' => false,
	'status' => false,
	'email' => false,
	'new_password' => false
);
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../../include/id.php'))
{
	id_verbose('PAGE:ajax/admin/admin_edit_account.php');
	id_init();
	//do something
	if (id_session_loggedin())
	{
		$responsearr['action'] = 1; //no problem - assume correct until there is an error
		//check admin
		if(id_account_isadmin())
		{
			//require userid
			if (isset($_POST['userid']))
			{
				//valid id
				if (id_userid_valid($_POST['userid']))
				{
					//id set
					//----------DO STUFF----------
					if ($responsearr['action'] == 1)
					{
						id_verbose("action:1. time to do stuff");
						//-----username-----
						if (isset($_POST['username']))
						{
							id_verbose("change username");
							$resultusername = id_account_edit_username($_POST['userid'],$_POST['username']);
							if ($resultusername)
								$responsearr['data']['username'] = true;
							else
								$responsearr['data']['username'] = "An Error has occurred!";
						}
						//-----active-----
							id_verbose("change active");
							$active = isset($_POST['active']) ? true : false;
							$resultactive = id_account_edit_active($_POST['userid'],$active);
							if ($resultactive)
								$responsearr['data']['active'] = true;
							else
								$responsearr['data']['active'] = "An Error has occurred!";
						//-----status-----
						if (isset($_POST['status']))
						{
							id_verbose("change status");
							$resultstatus = id_account_edit_status($_POST['userid'],$_POST['status']);
							if ($resultstatus)
								$responsearr['data']['status'] = true;
							else
								$responsearr['data']['status'] = "An Error has occurred!";
						}
						//-----email-----
						if (isset($_POST['email']))
						{
							id_verbose("change email");
							$resultemail = id_account_edit_email($_POST['userid'],$_POST['email']);
							if ($resultemail)
								$responsearr['data']['email'] = true;
							else
								$responsearr['data']['email'] = "An Error has occurred!";
						}
						//-----password-----
						if (isset($_POST['new_password']))
						{
							id_verbose("change password");
							$resultpassword = id_account_edit_password($_POST['userid'],$_POST['new_password']);
							if ($resultpassword)
								$responsearr['data']['new_password'] = true;
							else
								$responsearr['data']['new_password'] = "An Error has occurred!";
						}
					}//if action == 1
				}//if valid id
				else
				{
					id_message("invalid userid");
					id_debug("invalid userid");
					$responsearr['action'] = 7;
					$responsearr['data']['userid'] = "Invalid userid";
				}
			}//if userid set
			else
			{
					id_message("user id not set");
					id_debug("user id not set");
					$responsearr['action'] = 7; //bad input
					$responsearr['data']['userid'] = "userid not set";
			}
		}//password set
		else
		{
			id_message("Not admin");
			id_debug("Not admin");
			$responsearr['action'] = 16; //no permission
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

<?php
/*
 * @file admin_edit_account.php
 * @desc Admin edit user account info (ajax)
 */
/*
INPUT
userid				required	for admins to edit other users
id_admin			forced		set = true because html checkbox

*/
$responsearr['action'] = 00; //unknown error
//false = nothing there to talk about/ change.
//string = error to show.
//true = success
$responsearr['data'] = array(
	'userid' => false,
	'permissions' => false
);
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../../include/id.php'))
{
	id_verbose('PAGE:ajax/admin/admin_edit_permission.php');
	id_init();
	//do something
	if (id_session_loggedin())
	{
		//check admin
		if(id_account_isadmin())
		{
			//require userid
			if (isset($_POST['userid']))
			{
				//valid id and get
				if (id_userid_valid($_POST['userid']))
				{
					//id set
					//----------DO STUFF----------
						id_verbose("change permissions");
						//set array of permissions
						$perms['id_admin'] = isset($_POST['id_admin']);
						$perms['mc_admin'] = isset($_POST['mc_admin']);
						$perms['cs16_admin'] = isset($_POST['cs16_admin']);
						$perms['csgo_admin'] = isset($_POST['csgo_admin']);
						$perms['terraria_admin'] = isset($_POST['terraria_admin']);
						
						$resultpermissions = id_account_setpermission_manual(	$_POST['userid'],
																				$perms['id_admin'],
																				$perms['mc_admin'],
																				$perms['cs16_admin'],
																				$perms['csgo_admin'],
																				$perms['terraria_admin']
																				);
						$responsearr['data']['permissions'] = $resultpermissions;
						if ($resultpermissions)
							$responsearr['action'] = 1;
						else
							$responsearr['action'] = 0;
				}//if valid id
				else
				{
					id_message("invalid userid");
					id_debug("invalid userid (datetime_created is null)");
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

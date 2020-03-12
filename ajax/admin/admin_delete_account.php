<?php
/*
 * @file admin_delete_account.php
 * @desc Admin delete user (ajax)
 */
/*
INPUT
userid				required	for admins to edit other users
*/
$responsearr['action'] = 00; //unknown error
//false = nothing there to talk about/ change.
//string = error to show.
//true = success
$responsearr['data'] = false;
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../../include/id.php'))
{
	id_verbose('PAGE:ajax/admin/admin_delete_account.php');
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
				//valid id
				if (id_userid_valid($_POST['userid']))
				{
					//id set
					//----------DO STUFF----------
					id_verbose("delte user");

					$result = id_account_delete_user($_POST['userid']);
					$responsearr['data']= $result;
					if ($result)
						$responsearr['action'] = 1;
					else
						$responsearr['action'] = 0;
				}//if valid id
				else
				{
					id_message("invalid userid");
					id_debug("invalid userid (datetime_created is null)");
					$responsearr['action'] = 7;
					$responsearr['data'] = "Invalid userid";
				}
			}//if userid set
			else
			{
					id_message("user id not set");
					id_debug("user id not set");
					$responsearr['action'] = 7; //bad input
					$responsearr['data'] = "userid not set";
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

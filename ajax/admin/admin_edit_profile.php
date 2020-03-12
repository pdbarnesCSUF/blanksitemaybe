<?php
/*
 * @file admin_edit_profile.php
 * @desc Admin edit user profile info (ajax)
 */
/*
INPUT
userid		required	user to change
profilename	optional	name to show (not username)
birthday	optional	birthday
gender		optional	m,f,""
location	optional	location
public		optional	1public,0private,2id
prefername	optional	boolean
*/
$responsearr['action'] = 00; //unknown error
//false = nothing there to talk about/ change.
//string = error to show.
//true = success
$responsearr['data'] = array(
	'userid' => false,
	'profilename' => false,
	'birthday' => false,
	'gender' => false,
	'location' => false,
	'publicview' => false,
	'prefername' => false
);
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../../include/id.php'))
{
	id_verbose('PAGE:ajax/admin/admin_edit_profile.php');
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
						//-----name-----
						if (isset($_POST['profilename']))
						{
							id_verbose("change profilename");
							$resultprofilename = id_profile_edit_profilename($_POST['userid'],$_POST['profilename']);
							if ($resultprofilename)
								$responsearr['data']['profilename'] = true;
							else
								$responsearr['data']['profilename'] = "An Error has occurred!";
						}
						//-----birthday-----
						if (isset($_POST['birthday']))
						{
							id_verbose("change birthday");
							$p_birthday = $_POST['birthday'];
							if ($p_birthday == '')
								$p_birthday = NULL;
							$resultbirthday = id_profile_edit_birthday($_POST['userid'],$p_birthday);
							if ($resultbirthday)
								$responsearr['data']['birthday'] = true;
							else
								$responsearr['data']['birthday'] = "An Error has occurred!";
						}
						//-----gender-----
						if (isset($_POST['gender']))
						{
							id_Verbose("change gender");
							$resultgender = id_profile_edit_gender($_POST['userid'],$_POST['gender']);
							if ($resultgender)
								$responsearr['data']['gender'] = true;
							else
								$responsearr['data']['gender'] = "An Error has occurred!";
						}
						//-----location-----
						if (isset($_POST['location']))
						{
							id_Verbose("change location");
							$resultlocation = id_profile_edit_location($_POST['userid'],$_POST['location']);
							if ($resultlocation)
								$responsearr['data']['location'] = true;
							else
								$responsearr['data']['location'] = "An Error has occurred!";
						}
						//-----publicview-----
						if (isset($_POST['publicview']))
						{
							id_Verbose("change publicview");
							$resultpublicview = id_profile_edit_public($_POST['userid'],$_POST['publicview']);
							if ($resultpublicview)
								$responsearr['data']['publicview'] = true;
							else
								$responsearr['data']['publicview'] = "An Error has occurred!";
						}
						//-----prefername-----
							id_Verbose("change prefername");
							$resultprefername = id_profile_edit_prefername($_POST['userid'],isset($_POST['prefername']));
							if ($resultprefername)
								$responsearr['data']['prefername'] = true;
							else
								$responsearr['data']['prefername'] = "An Error has occurred!";
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

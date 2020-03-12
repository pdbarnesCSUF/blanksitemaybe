<?php
/*
 * @file status.php
 * @desc status of logged in user
 */
 
$responsearr['action'] = 00; //unknown error
$responsearr['data'] = array(
		'loggedin' => false,
		'userid' => 0,
		'username' => '',
		'profilename' => '',
		'preferredname' => '',
		'unread_messages' => 0
	);
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../include/id.php'))
{
	id_init();
	if (id_session_loggedin())
	{
		$responsearr['action'] = 1;
		$responsearr['data']['loggedin'] = true;
		$responsearr['data']['userid'] = $id_SESSION['id'];
		$responsearr['data']['username'] = $id_SESSION['id_current_user']->username;
		$responsearr['data']['profilename'] = $id_SESSION['id_current_user']->profilename;
		if ($id_SESSION['id_current_user']->prefer_profilename)
			$responsearr['data']['preferredname'] = $id_SESSION['id_current_user']->profilename;
		else
			$responsearr['data']['preferredname'] = $id_SESSION['id_current_user']->username;
		// @todo add unread message check
		
	}
	else
	{
		$responsearr['action'] = 1;
	}
	//-----end-----
	$responsearr['message'] = $id_SESSION['message'];
	$responsearr['debug'] = $id_SESSION['debug'];
}
else
{
	$responsearr['action'] = 02; //include error
	$responsearr['message'] = "Server Error";
	$responsearr['debug'][] = "ID Include error";
}
echo json_encode($responsearr);
?>

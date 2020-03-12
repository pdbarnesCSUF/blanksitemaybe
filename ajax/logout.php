<?php
/*
 * @file logout.php
 * @desc logs out, kills session
 */
 
$responsearr['action'] = 0; //unknown error
$responsearr['data'] = 0;
header('Content-Type: application/json');

if (include_once('../include/id.php'))
{
	id_init();
	if (id_session_loggedin())
	{
		id_session_logout();
		setcookie('id_userid', 0, 1, '/', $sitesettings['cookiesite']);
		setcookie('id_sessid', 0, 1, '/', $sitesettings['cookiesite']);
		$responsearr['action'] = 1;
		$responsearr['data'] = 1;
	}
	else
	{
		$responsearr['action'] = 04; //not logged in
		$responsearr['data'] = $id_SESSION['message'];
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

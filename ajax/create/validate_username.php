<?php
/*
 * @file validate_username.php
 * @desc check if username is valid or taken
 */

$responsearr['action'] = 00; //unknown error
$responsearr['data'] = false;
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../../include/id.php'))
{
	id_verbose('PAGE:api/create/validate_username');
	//do something
	//check POST
	if (isset($_POST['username']))
	{
		id_verbose('post set');
        $status = id_create_check_username($_POST['username']);
        if ($status === true)
        {
            $responsearr['data']['valid'] = true;
            $responsearr['data']['reason'] = "User Available";
        }
        else {
            $responsearr['data']['valid'] = false;
            $responsearr['data']['reason'] = $status;
        }
		$responsearr['action'] = 1;
	}
	else
	{
		id_debug('No Username');
		$responsearr['action'] = 7; //bad input
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

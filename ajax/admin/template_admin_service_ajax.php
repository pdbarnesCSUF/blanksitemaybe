<?php
/*
 * @file template_admin_service_ajax.php
 * @desc admin for service ajax
 */
 
$responsearr['action'] = 00; //unknown error
$responsearr['data'] = array();
$responsearr['message'] = array();
$responsearr['debug'] = array();
header('Content-Type: application/json');

if (include_once('../../include/id.php'))
{
	if (include_once($id_ROOT.'/config/service_settings.php'))
	{
		id_verbose('PAGE:ajax/admin/admin_service_ajax.php');
		id_init();
		//do something
		if (id_account_getpermission($id_SESSION['id'],'service_admin'))
		{
			if (isset($_GET["a"]))
			{
				$action = $_GET["a"];
				switch($action)
				{
					//============== log request ============
					case "log":
						$lines = read_log($SERVICESETTINGS['log_path'], $SERVICESETTINGS['log_lines']);
						foreach ($lines as $line) {
							$responsearr['data'][] = $line;
						}
						break;
					//============== start request ============
					case "start":
						$responsearr['data'][] = exec('sudo -u '.$SERVICESETTINGS['service_username'].' '.$SERVICESETTINGS['service'].' start');
						break;
					//============== stop request ============
					case "stop":
						exec('sudo -u '.$SERVICESETTINGS['service_username'].' '.$SERVICESETTINGS['service'].' say WEB-'.$id_SESSION['id_current_user']->username.': Stopping Server.');
						$responsearr['data'][] = exec('sudo -u '.$SERVICESETTINGS['service_username'].' '.$SERVICESETTINGS['service'].' stop');
						break;
					//============== status request ============
					case "status":
						exec('sudo -u '.$SERVICESETTINGS['service_username'].' '.$SERVICESETTINGS['service'],$exec_rtn);
						//header("Content-Type: application/json");
						$responsearr['data'][] = "--status not ready--";
						//format it into json
						foreach ($exec_rtn as $exec_line)
						{
							$responsearr['data'][] = $exec_line;
						}
						break;
					//============== update request ============
					case "update":
						$responsearr['data'][] = exec('sudo -u '.$SERVICESETTINGS['service_username'].' '.$SERVICESETTINGS['service'].' update');
						break;
					//============== say request ============
					case "say":
						if (isset($_POST['msg']))
						{
							$new_msg = preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['msg']);
							if ($new_msg != '')
							{
								exec('sudo -u '.$SERVICESETTINGS['service_username'].' '.$SERVICESETTINGS['service'].' say WEB-'.$id_SESSION['id_current_user']->username.': '.$new_msg, $exec_rtn);
								foreach ($exec_rtn as $exec_line)
								{
									$responsearr['data'][] = $exec_line;
								}
							}
							else
							{
								$responsearr['data'][] = "empty msg, no symbols!";
							}
						}
						else
						{
							$responsearr['data'][] = "empty msg";
						}
						break;
					default:
						$responsearr['data'][] = "bad:a";
						break;
				}
			}//if isset (_GET[a])
			else
			{
				$responsearr['data'][] = "no:a";
			}
		}//perms_check MC
		else
		{
			$responsearr['data'][] = "Permission Denied";
		}
	}//include mc_settings
	else
	{
		$responsearr['data'][] = "service_settings.php missing";
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

<?php
/**
 *  @file id_session.php
 *  @brief Session management
 */
/**
 *  @brief check if session is valid.
 *  
 *  @param [in] $sessid session id
 *  @return integer - 0 invalid, id of user
 *  
 *  @details checks database if the session id combo is valid. Invalid if session id has duplicates or expired
 */
function id_session_valid($sessid)
{
	global $iddbPDO, $sitesettings, $ID_SESSION;
	id_verbose("(".__FUNCTION__.")");
	//============================
	//is it there?
	$PDO_id_session_valid = $iddbPDO->prepare("SELECT * FROM iduser_active_sessions WHERE sessid=:st_sessid;");
	$PDO_id_session_valid->bindParam(':st_sessid', $sessid);
	$PDO_id_session_valid->execute();
	$result_rows = $PDO_id_session_valid->rowCount();

	if ($result_rows == 0)
	{
		id_verbose("valid-0");
		return 0;
	}
	else if ($result_rows == 1)
	{
		$result = $PDO_id_session_valid->fetch(PDO::FETCH_ASSOC);
		//==================debug================
		id_verbose("id:".$result['userid']);
		id_verbose("date:".$result['datetime_active']);
		id_verbose("sessid:".$result['sessid']);
		//=======================================
		$result_date = DateTime::createFromFormat('Y-m-j H:i:s.uT', $result['datetime_active']);
		id_verbose("valid-1: ".$result_date->format('Y-m-j H:i:s.uT'));
		$now_date = date_create();//current time
		//$now_date = new DateTime("2014-07-18 11:11:11");
		$date_diff = date_diff($result_date, $now_date);
		id_verbose("D:".$date_diff->days);
		id_verbose("N:".$date_diff->invert);
		if ($date_diff->invert == 0)//expires in future date
			return $result['userid'];
		if (($date_diff->days >= 0 )  && ($date_diff->days <= $sitesettings['session_expire_days'])) //how old is it?
			return $result['userid'];
		//expires, fall-out
		id_debug("expired");
	}
	//dupes or expired...
	return 0;
}
/**
 *  @brief refresh session in database
 *  
 *  @param [in] $userid id user number
 *  @param [in] $sessid session id
 *  
 *  @details Finds matching session id and updates the time to 'now'. Assumes the session/s is valid, does no checking, will update all with matching sessid.
 */
function id_session_resume($sessid)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	//refresh date
	$PDO_id_session_resume = $iddbPDO->prepare("UPDATE iduser_active_sessions
													SET datetime_active = 'now',
														last_ip = :st_ip
													WHERE sessid=:st_sessid;");
	$PDO_id_session_resume->bindParam(':st_ip', $_SERVER['REMOTE_ADDR']); //btw. not gaurentea that this is the real ip!
	$PDO_id_session_resume->bindParam(':st_sessid', $sessid);
	$PDO_id_session_resume->execute();
}
/**
 *  @brief Destroys session in database
 *  
 *  @param [in] $sessid session id
 *  
 *  @details Destroys all entries matching the session id given, does no checks.
 */
function id_session_kill($sessid)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_id_session_kill = $iddbPDO->prepare("DELETE FROM iduser_active_sessions WHERE sessid=:st_sessid;");
	$PDO_id_session_kill->bindParam(':st_sessid',$sessid);
	$PDO_id_session_kill->execute();
}
//==================CURRENT SESSION=================
/**
 *  @brief ID system login
 *  
 *  @param [in] $username username to login with
 *  @param [in] $password password to login with 
 *  @param [in] $device_type device description (eg. Potato, Browser, App, etc.)
 *  @return session hash, or false
 *  
 *  @details Logs into ID and sets session vars, and returns the session hash
 *  
 */
function id_session_login($username, $password, $device_type = '')
{
	global $iddbPDO, $ID_SESSION;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_login_query = $iddbPDO->prepare("SELECT userid,username,password FROM iduser_account WHERE username ILIKE :st_username;");
	$PDO_login_query->bindParam(':st_username', $username);
	$PDO_login_query->execute();
	
	$result_rows = $PDO_login_query->rowCount();

	if ($result_rows == 1)
	{
		$resultAcct = $PDO_login_query->fetch(PDO::FETCH_ASSOC);
		if ( password_verify($password, $resultAcct["password"]))
		{
			//get session
			$salt = mt_rand();
			$time_now = time();
			$ip = $_SERVER['REMOTE_ADDR']; //btw. not gaurentea that this is the real ip!
			//$resultAcct['userid'] already set in func header
			$collision_max = 10;	//how many times to try and get a new sessid
			for ($collision = 0; $collision < $collision_max; $collision++)
			{
				$new_sessid = md5("potato,$salt,$time_now,$ip,".$resultAcct['userid']);
				id_verbose($new_sessid);
				if (!id_session_valid($resultAcct['userid'],$new_sessid))
				{
					id_verbose("no dupe detected!");
					//GUD!
					$PDO_id_session_new = $iddbPDO->prepare("	INSERT INTO iduser_active_sessions 
																	(userid,sessid,datetime_active,last_ip,device_type)
																	VALUES (:st_id,:st_sessid,'now',:st_ip,:st_device_type);");
					$PDO_id_session_new->bindParam(':st_id', $resultAcct['userid']);
					$PDO_id_session_new->bindParam(':st_sessid', $new_sessid);
					$PDO_id_session_new->bindParam(':st_ip', $ip);
					$PDO_id_session_new->bindParam(':st_device_type', $device_type);
					$resultSession = $PDO_id_session_new->execute();
					if ($resultSession) //if add is gud
					{
						//set ID_SESSION
						$ID_SESSION['id'] = $resultAcct['userid'];
						$ID_SESSION['loggedin'] = true;
						$ID_SESSION['sessid'] = $new_sessid;
						$ID_SESSION['id_current_user'] = new ID_user($resultAcct['userid']);
						//return session
						return $new_sessid;
					}
				}
				id_debug("saltier!");
				$salt = mt_rand(); //saltier!
			}
			id_debug("ERROR: Collosions");
			return false;
		}
		else
		{
			id_debug("bad pass");
			return false;
		}
	}
	else
	{
		id_debug("bad name");
		return false;
	}
}

/**
 *  @brief ID System logout
 *  
 *  @return NONE
 *  
 *  @details Now, sets session log vars to false.
 *  
 */
function id_session_logout()
{
	global $ID_SESSION;
	id_verbose("(".__FUNCTION__.")");
	//============================
	id_session_kill($ID_SESSION['sessid']);
	$ID_SESSION['loggedin'] = false;
	$ID_SESSION['id'] = 0;
	$ID_SESSION['sessid'] = 	0;
	$ID_SESSION['id_current_user'] = NULL;
	id_verbose("logged out"); 
}

/**
 *  @brief Checks if currently logged in.
 *  
 *  @return boolean true-logged in, false-logged out
 *  
 *  @details Now, it checks if session variables are valid and logged in or otherwise logs out.
 */
function id_session_loggedin()
{
	global $ID_SESSION;
	id_verbose("(".__FUNCTION__.")");
	//============================
	id_verbose("id_set:".$ID_SESSION['id']);
	//check if logged in to id
	id_verbose('loggedin:'.(bool)$ID_SESSION['loggedin']);
	if ($ID_SESSION['loggedin'])
		return true;
	else
		return false;
}
/**
 *  @brief gets limited general status of login. more suited for API
 *  
 *  @return boolean true-logged in, false-logged out
 *  
 *  @details Now, it checks if session variables are valid and logged in or otherwise logs out.
 */
function id_session_status()
{
	global $ID_SESSION;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$arr['loggedin'] =		false;
	$arr['userid'] =		0;
	$arr['username'] =		'';
	$arr['profilename'] =	'';
	$arr['preferredname'] = '';
	$arr['unread_messages'] = 0;
	if ($ID_SESSION['loggedin'])
	{
		$arr['loggedin'] =		$ID_SESSION['loggedin'];
		$arr['userid'] =			$ID_SESSION['id'];
		$arr['username'] =		$ID_SESSION['id_current_user']->username;
		$arr['profilename'] =		$ID_SESSION['id_current_user']->profilename;
		if ($ID_SESSION['id_current_user']->prefer_profilename)
				$arr['preferredname'] = $ID_SESSION['id_current_user']->profilename;
			else
				$arr['preferredname'] = $ID_SESSION['id_current_user']->username;
		$arr['unread_messages'] = 0;
	}
	return $arr;
}
?>

<?php
/**
 *  @file id_profile.php
 *  @brief Profile viewing and management
 */
$ID_BLANK_PROFILE = array(
				"username"		=> '',
				"datetime_created"	=> 0,
				"active"		=> false,
				"status"		=> '',
				"profilename"			=> '',
				"prefer_profilename"	=> false,
				"public_view"	=> 0,
				"location"		=> '',
				"gender"		=> '',
				"picture"		=> false
			);
function id_profile_view_login($userid)
{
	global $ID_SESSION, $iddbPDO, $ID_BLANK_PROFILE;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$profile = id_profile_view($userid);
	//viewing yourself
	if ($userid == $ID_SESSION['id'])
	{
		return $profile;
	}
	//public profile
	else if ($profile['public_view'] == 1)
	{
		return $profile;
	}
	//logged in viewable
	else if ($profile['public_view'] == 2  && $ID_SESSION['loggedin'])
	{
		return $profile;
	}
	//not public, doesnt exist
	else
	{
		return $ID_BLANK_PROFILE;
	}
}
/**
 * @brief Gets a profile array
 * 
 * @param [in] $userid The requested profile
 * 
 * @details Specifcally profile stuff, not the all user info.
 */
function id_profile_view($userid) {
	global $ID_SESSION, $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$blankarr = array(
				"username"		=> '',
				"datetime_created"	=> 0,
				"active"		=> false,
				"status"		=> '',
				"profilename"			=> '',
				"prefer_profilename"	=> false,
				"public_view"	=> 0,
				"location"		=> '',
				"gender"		=> '',
				"picture"		=> false
			);
	$PDO_id_profile_view = $iddbPDO->prepare("	SELECT	username,
															datetime_created,
															active,
															status,
															profilename,
															prefer_profilename,
															public_view,
															location,
															gender,
															picture
													FROM	iduser_account,iduser_profile
													WHERE	iduser_account.userid = iduser_profile.userid
														AND
															iduser_account.userid = :st_userid;");
	$PDO_id_profile_view->bindParam(':st_userid', $userid);
	if ($PDO_id_profile_view->execute())
	{
		if ($PDO_id_profile_view->rowCount() == 1)
		{
			return $PDO_id_profile_view->fetch(PDO::FETCH_ASSOC);
		}
		id_message("Error: Dumb database entries");
		id_debug("ERROR:ID:PV:count");
		return $ID_BLANK_PROFILE;
	}
	id_message("Error: Database");
	id_debug("ERROR:ID:PV:DB");
	return $ID_BLANK_PROFILE;
}
/**
 *  @brief Get preferred name of user.
 *  
 *  @param [in] $userid Userid to check.
 *  @return string Preferred name to use.
 *  
 *  @details Returns either username or profile name based on user.
 */
function id_profile_getpreferredname($userid)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_id_preferredname = $iddbPDO->prepare("	SELECT username,prefer_profilename,profilename
													FROM iduser_account
														JOIN iduser_profile
														USING(userid)
													WHERE userid=:st_userid;");
	$PDO_id_preferredname->bindParam(':st_userid',$userid);
	if ($PDO_id_preferredname->execute())
	{
		if ($PDO_id_preferredname->rowCount() == 1)
		{
			$userArr = $PDO_id_preferredname->fetch(PDO::FETCH_ASSOC);
			if ($userArr['prefer_profilename'] && $userArr['profilename'] != '')
			{
				return $userArr['profilename'];
			}
			else
			{
				return $userArr['username'];
			}
		}
	}
	return $userid;
}
/**
 *  @brief Edits profilename of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $profilename profilename to change
 *  @return boolean success
 *  
 *  @details Edits profilename of user
 */
function id_profile_edit_profilename($userid, $profilename)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_edit_profile_profilename = $iddbPDO->prepare("UPDATE iduser_profile SET profilename=:profilename WHERE userid=:userid;");
	$PDO_edit_profile_profilename->bindParam(":profilename",$profilename);
	$PDO_edit_profile_profilename->bindParam(":userid",$userid);
	if ($PDO_edit_profile_profilename->execute())
		return true;
	else
	{
		id_debug("PDO_edit_profile_profilename failed:");
		id_debug($PDO_edit_profile_profilename->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits birthday of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $birthday birthday (Y-m-d) to change
 *  @return boolean success
 *  
 *  @details Edits birthday of user
 */
function id_profile_edit_birthday($userid, $birthday)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_edit_profile_birthday = $iddbPDO->prepare("UPDATE iduser_profile SET birthday=:birthday WHERE userid=:userid;");
	$PDO_edit_profile_birthday->bindParam(":birthday",$birthday);
	$PDO_edit_profile_birthday->bindParam(":userid",$userid);
	if ($PDO_edit_profile_birthday->execute())
		return true;
	else
	{
		id_debug("PDO_edit_profile_birthday failed:");
		id_debug($PDO_edit_profile_birthday->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits gender of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $gender gender to change
 *  @return boolean success
 *  
 *  @details Edits gender of user
 */
function id_profile_edit_gender($userid, $gender)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_edit_profile_gender = $iddbPDO->prepare("UPDATE iduser_profile SET gender=:gender WHERE userid=:userid;");
	$PDO_edit_profile_gender->bindParam(":gender",$gender);
	$PDO_edit_profile_gender->bindParam(":userid",$userid);
	if ($PDO_edit_profile_gender->execute())
		return true;
	else
	{
		id_debug("PDO_edit_profile_gender failed:");
		id_debug($PDO_edit_profile_gender->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits location of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $location location to change
 *  @return boolean success
 *  
 *  @details Edits location of user
 */
function id_profile_edit_location($userid, $location)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_edit_profile_location = $iddbPDO->prepare("UPDATE iduser_profile SET location=:location WHERE userid=:userid;");
	$PDO_edit_profile_location->bindParam(":location",$location);
	$PDO_edit_profile_location->bindParam(":userid",$userid);
	if ($PDO_edit_profile_location->execute())
		return true;
	else
	{
		id_debug("PDO_edit_profile_location failed:");
		id_debug($PDO_edit_profile_location->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits public_view of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $public_view public to change
 *  @return boolean success
 *  
 *  @details Edits public_view of user
 */
function id_profile_edit_public($userid, $public_view)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_edit_profile_public_view = $iddbPDO->prepare("UPDATE iduser_profile SET public_view=:public_view WHERE userid=:userid;");
	$PDO_edit_profile_public_view->bindParam(":public_view",$public_view);
	$PDO_edit_profile_public_view->bindParam(":userid",$userid);
	if ($PDO_edit_profile_public_view->execute())
		return true;
	else
	{
		id_debug("PDO_edit_profile_public_view failed:");
		id_debug($PDO_edit_profile_public_view->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits prefer_profilename of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $prefer_profilename boolean prefername to change
 *  @return boolean success
 *  
 *  @details Edits prefer_profilename of user
 */
function id_profile_edit_prefername($userid, $prefer_profilename)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$pname = toBool_psql($prefer_profilename);
	$PDO_edit_profile_prefer_profilename = $iddbPDO->prepare("UPDATE iduser_profile SET prefer_profilename=:prefer_profilename WHERE userid=:userid;");
	$PDO_edit_profile_prefer_profilename->bindParam(":prefer_profilename",$pname);
	$PDO_edit_profile_prefer_profilename->bindParam(":userid",$userid);
	if ($PDO_edit_profile_prefer_profilename->execute())
		return true;
	else
	{
		id_debug("PDO_edit_profile_prefer_profilename failed:");
		id_debug($PDO_edit_profile_prefer_profilename->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Set picture for user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $picture boolean set picture boolean. defaults to false
 *  @return boolean success
 *  
 *  @details Changes 'picture' of user
 */
 function id_profile_edit_picture($userid, $picture = false)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$pic = toBool_psql($picture);
	$PDO_edit_profile_edit_picture = $iddbPDO->prepare("UPDATE iduser_profile SET picture=:picture WHERE userid=:userid;");
	$PDO_edit_profile_edit_picture->bindParam(":picture",$pic);
	$PDO_edit_profile_edit_picture->bindParam(":userid",$userid);
	if ($PDO_edit_profile_edit_picture->execute())
		return true;
	else
	{
		id_debug("PDO_edit_profile_edit_picture failed:");
		id_debug($PDO_edit_profile_edit_picture->errorInfo()[2]);
		return false;
	}
}
?>

<?php
/**
 *  @file id_account.php
 *  @brief Account management
 */
function id_account_getnewestuser()
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$stmt = $iddbPDO->query("	SELECT userid,username,datetime_created 
										FROM iduser_account
										ORDER BY datetime_created DESC
										LIMIT 1
										;",PDO::FETCH_ASSOC);
	$result = $stmt->fetch();
	if ($result)
	{
		return $result;
	}
	else
		return false;
}
/**
*  @brief takes id and returns username
*  
*  @param [in] $userid id number
*  @return username
*  
*  @details Takes a ID number and returns the username
*/
function id_account_getusername($userid)
{
	global $iddbPDO, $sitesettings;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_id_username = $iddbPDO->prepare("SELECT username FROM iduser_account WHERE userid=:st_userid;");
	$PDO_id_username->bindParam(':st_userid',$userid);
	if ($PDO_id_username->execute())
	{
		if ($PDO_id_username->rowCount() == 1)
		{
			$userArr = $PDO_id_username->fetch(PDO::FETCH_ASSOC);
			return $userArr['username'];
		}
	}
	return false;
}
/**
 *  @brief checks if userid is valid
 *  
 *  @param [in] $userid ID user number
 *  @return boolean if id number exists
 *  
 *  @details Checks if the id is a real registered user.
 */
function id_userid_valid($userid)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	if (filter_var($userid,FILTER_VALIDATE_INT,FILTER_NULL_ON_FAILURE) > 0)
	{
		$PDO_id_valid_user = $iddbPDO->prepare("SELECT userid FROM iduser_account WHERE userid=:st_userid;");
		$PDO_id_valid_user->bindParam(':st_userid',$userid);
		$PDO_id_valid_user->execute();
		$numrows = $PDO_id_valid_user->rowCount();
		if ($numrows == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
/**
 *  @brief Checks password if correct
 *  
 *  @param [in] $userid the user's password to check
 *  @param [in] $password plaintext password to check
 *  @return boolean if valid
 *  
 *  @details Checks password if correct
 */
function id_account_checkpassword($userid, $password)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_id_checkPassword = $iddbPDO->prepare("SELECT password FROM iduser_account WHERE userid=:st_userid;");
	$PDO_id_checkPassword->bindParam(':st_userid',$userid);
	if ($PDO_id_checkPassword->execute())
	{
			if ($PDO_id_checkPassword->rowCount() == 1)
			{
					$resultAcct = $PDO_id_checkPassword->fetch(PDO::FETCH_ASSOC);
					return password_verify($password, $resultAcct["password"]);
			}
	}
	return false;
}
//================= EDIT =================
//database changes, no checks
/**
 *  @brief Edits username of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $username username to change
 *  @return boolean username
 *  
 *  @details Edits username of user
 */
function id_account_edit_username($userid, $username)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_edit_account_username = $iddbPDO->prepare("UPDATE iduser_account SET username=:username WHERE userid=:userid;");
	$PDO_edit_account_username->bindParam(":username",$username);
	$PDO_edit_account_username->bindParam(":userid",$userid);
	if ($PDO_edit_account_username->execute())
		return true;
	else
	{
		id_debug("PDO_edit_account_username failed:");
		id_debug($PDO_edit_account_username->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits active of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $active boolean active to change
 *  @return boolean active
 *  
 *  @details Edits active of user
 */
function id_account_edit_active($userid, $active)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$active = toBool_psql($active);
	$PDO_edit_account_active = $iddbPDO->prepare("UPDATE iduser_account SET active=:active WHERE userid=:userid;");
	$PDO_edit_account_active->bindParam(":active",$active);
	$PDO_edit_account_active->bindParam(":userid",$userid);
	if ($PDO_edit_account_active->execute())
		return true;
	else
	{
		id_debug("PDO_edit_account_active failed:");
		id_debug($PDO_edit_account_active->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits status of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $status boolean status to change
 *  @return boolean status
 *  
 *  @details Edits status of user
 */
function id_account_edit_status($userid, $status)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_edit_account_status = $iddbPDO->prepare("UPDATE iduser_account SET status=:status WHERE userid=:userid;");
	$PDO_edit_account_status->bindParam(":status",$status);
	$PDO_edit_account_status->bindParam(":userid",$userid);
	if ($PDO_edit_account_status->execute())
		return true;
	else
	{
		id_debug("PDO_edit_account_status failed:");
		id_debug($PDO_edit_account_status->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits email of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $email email to change
 *  @return boolean success
 *  
 *  @details Edits email of user, resets email verification to false
 */
function id_account_edit_email($userid, $email)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$PDO_edit_account_email = $iddbPDO->prepare("UPDATE iduser_account SET email=:email,email_verified='false' WHERE userid=:userid;");
	$PDO_edit_account_email->bindParam(":email",$email);
	$PDO_edit_account_email->bindParam(":userid",$userid);
	if ($PDO_edit_account_email->execute())
		return true;
	else
	{
		id_debug("PDO_edit_account_email failed:");
		id_debug($PDO_edit_account_email->errorInfo()[2]);
		return false;
	}
}
/**
 *  @brief Edits email verified of user
 *
 *  @param [in] $userid userid to change
 *  @param [in] $emailverify true or false(default)
 *  @return boolean success
 *
 *  @details Sets email verification status
 */
function id_account_edit_emailverify($userid, $emailverify = FALSE)
{
        global $iddbPDO;
        id_verbose("(".__FUNCTION__.")");
        //============================
        $emailverify_psql = toBool_psql($emailverify);
	$PDO_edit_account_emailverify = $iddbPDO->prepare("UPDATE iduser_account SET email_verified=:emailverify WHERE userid=:userid;");
        $PDO_edit_account_emailverify->bindParam(":emailverify",$emailverify_psql);
        $PDO_edit_account_emailverify->bindParam(":userid",$userid);
        if ($PDO_edit_account_emailverify->execute())
                return true;
        else
        {
                id_debug("PDO_edit_account_emailverify failed:");
                id_debug($PDO_edit_account_emailverify->errorInfo()[2]);
                return false;
        }
}

/**
 *  @brief Edits password of user
 *  
 *  @param [in] $userid userid to change
 *  @param [in] $password password to change
 *  @return boolean success
 *  
 *  @details Edits password of user
 */
function id_account_edit_password($userid, $password)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$tmp_pw = password_hash($password,PASSWORD_BCRYPT);
	$PDO_edit_account_password = $iddbPDO->prepare("UPDATE iduser_account SET password=:new_password WHERE userid=:userid;");
	$PDO_edit_account_password->bindParam(":new_password",$tmp_pw);
	$PDO_edit_account_password->bindParam(":userid",$userid);
	if ($PDO_edit_account_password->execute())
		return true;
	else
	{
		id_debug("PDO_edit_account_password failed:");
		id_debug($PDO_edit_account_password->errorInfo()[2]);
		return false;
	}
}
function id_account_forgot_password($userid)
{
	global $iddbPDO, $sitesettings;
	id_verbose("(".__FUNCTION__.")");
	//============================
	if ($sitesettings['email_enabled'])
	{
		$PDO_id_pw_get = $iddbPDO->prepare("SELECT username,email FROM iduser_account WHERE userid=:st_userid;");
		$PDO_id_pw_get->bindParam(':st_userid',$userid);
		if ($PDO_id_pw_get->execute())
		{
			$userArr = $PDO_id_pw_get->fetch(PDO::FETCH_ASSOC);
			//$userArr['username']
			//$userArr['email']
			//----------
			//generate and set a reset token and set it db
			//generate token
			//PDO_id_pw_set INSERT token,userid,datetime_now
			//----------
			//@todo need to rolll this email setup into a common function!!
			$mail = new PHPMailer;
			$mail->isSMTP();
			//$mail->SMTPDebug = 0;
			$mail->Host = $sitesettings['email_host'];
			$mail->Port = $sitesettings['email_port'];
			$mail->SMTPSecure = 'tls'; //ssl is deprecated
			$mail->SMTPAuth = $sitesettings['email_auth'];
			$mail->Username = $sitesettings['email_username'];
			$mail->Password = $sitesettings['email_password'];
			$mail->setFrom($sitesettings['email_system_from'],$sitesettings['email_system_from_name']);
			if ($sitesettings['email_system_replyto'])
				$mail->addReplyTo($sitesettings['email_system_replyto'],$sitesettings['email_system_replyto_name']);
			//@todo ssl signed email https://github.com/PHPMailer/PHPMailer/blob/master/examples/smime_signed_mail.phps
			//----------
			$mail->addAddress($userArr['email'],$userArr['username']);
			$mail->Subject = $sitesettings['email_system_subject_prefix']."Password Reset";
			$mail->AltBody = "blah blah password reset here";
			if (!$mail->send()) {
				id_message("Email error");
				id_debug( "Mailer Error: " . $mail->ErrorInfo);
				return false;
			} else {
				id_verbose("email sent");
				return true;
			}
		}
		else
		{
			id_debug('db error: couldn\'t get user info');
			return false;
		}
	}
	else
	{
		id_message('e-mail not enabled, contact admin to reset');
		id_debug('email disabled');
		return false;
	}
}
/**
 *  @brief Unconditionally deletes id account. Do input checks before hand! NOT FOR NORMAL USE
 *  
 *  @param [in] $userid userid
 *  @return boolean database commit success
 *  
 *  @details No checks. Deletes user. NOT FOR NORMAL USE! In most cases, a disable is sufficient. This is highly destructive and removes all references of the userid
 */
function id_account_delete_user($userid)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	//gotta seperate it like this because prepare/execute does not support multiple statements
	$iddbPDO->beginTransaction();
	$iddbPDO->query("DELETE FROM iduser_perm_hash WHERE userid = ".$userid.";");
	$iddbPDO->query("DELETE FROM iduser_permissions WHERE userid = ".$userid.";");
	//$iddbPDO->query("DELETE FROM iduser_messages WHERE senderid = ".$userid.";");
	//$iddbPDO->query("DELETE FROM iduser_messages WHERE receiverid = ".$userid.";");
	$iddbPDO->query("DELETE FROM iduser_profile WHERE userid = ".$userid.";");
	$iddbPDO->query("DELETE FROM iduser_email_verify WHERE userid = ".$userid.";");
	$iddbPDO->query("DELETE FROM iduser_active_sessions WHERE userid = ".$userid.";");
	$iddbPDO->query("DELETE FROM iduser_account WHERE userid = ".$userid.";"); // this line must be at end!
	$iddbPDO->commit();
	$result = !id_userid_valid($userid);

	if ($result)
	{
		id_message('Account Deleted');
		id_verbose('commit:true');
		return true;
	}
	else
	{
		id_message('Error Deleting Account');
		id_verbose('commit:failed');
		return false;
	}
}
/**
 *  
 */
function id_account_isadmin()
{
	global $ID_SESSION;
        id_verbose("(".__FUNCTION__.")");
        //============================
	if ($ID_SESSION['loggedin'])
		return $ID_SESSION['id_current_user']->id_admin;
	else
		return false;
}

function id_account_getpermissionarr($userid)
{
	global $iddbPDO;
	id_verbose("(".__FUNCTION__.")");
	//============================
	if (filter_var($userid,FILTER_VALIDATE_INT,FILTER_NULL_ON_FAILURE) > 0)
	{
		$PDO_id_permissionarr = $iddbPDO->prepare("SELECT * FROM iduser_permissions WHERE userid=:st_userid;");
		$PDO_id_permissionarr->bindParam(':st_userid',$userid);
		if ($PDO_id_permissionarr->execute())
		{
			$arr = $PDO_id_permissionarr->fetch(PDO::FETCH_ASSOC);
			return $arr;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function id_account_getpermission($userid,$permission)
{
	id_verbose("(".__METHOD__.")");
	$perms = id_account_getpermissionarr($userid);
	if(isset($perms[$permission]))
		return toBool($perms[$permission]);
	else
		return false;
}

function id_account_setpermission($userid,$permission,$value)
{
	global $iddbPDO;
	id_verbose("(".__METHOD__.")");
	$value = toBool_psql($value);
	$PDO_id_permsup = $iddbPDO->prepare("	UPDATE iduser_permissions
											SET :permission = :value
											WHERE iduser_permissions.userid = :userid;");
	$result = $PDO_id_permsup->execute( array(	':userid' => $userid,
													':value' => $value,
													':permission' => $permission)
												);
	if (!$result) id_debug($PDO_id_permsup->errorinfo());
	return $result;
}
function id_account_setpermission_manual($userid,$id = false,$mc = false,$cs16 = false,$csgo = false,$terraria = false)
{
	global $iddbPDO;
	id_verbose("(".__METHOD__.")");
	$PDO_id_permsman = $iddbPDO->prepare("	UPDATE iduser_permissions
											SET id_admin = :id,
												mc_admin = :mc,
												cs16_admin = :cs16,
												csgo_admin = :csgo,
												terraria_admin = :terraria
											WHERE iduser_permissions.userid = :userid;");
	$result = $PDO_id_permsman->execute( array(	':userid' => $userid,
													':id' => toBool_psql($id),
													':mc' => toBool_psql($mc),
													':cs16' => toBool_psql($cs16),
													':csgo' => toBool_psql($csgo),
													':terraria' => toBool_psql($terraria))
												);
	if (!$result) id_debug($PDO_id_permsman->errorinfo());
	return $result;
}
?>

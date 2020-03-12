<?php
/**
 *  @file id_email.php
 *  @brief E-Mail handling functions.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/**
 * @brief Creates email class with default settings set by sitesettings.
 *
 * @return PHPMailer class
 */
function id_email_factory()
{
	global $iddbPDO, $sitesettings;
	id_verbose("(".__FUNCTION__.")");
	//============================
	//check if email enabled
		if (isset($sitesettings['email_enabled']) && $sitesettings['email_enabled'])
		{
			id_verbose("sendemail enabled");
			$mail = new PHPMailer;
			$mail->isSMTP();
			//$mail->SMTPDebug = 2;
			// check email config TODO
			$mail->Host = $sitesettings['email_host'];
			$mail->Port = $sitesettings['email_port'];
			if ($sitesettings['email_smtpsecure'])
				$mail->SMTPSecure = $sitesettings['email_smtpsecure'];
			$mail->SMTPAuth = $sitesettings['email_auth'];
			$mail->Username = $sitesettings['email_username'];
			$mail->Password = $sitesettings['email_password'];
			// get default settings
			//from who - empty = system
			$mail->setFrom($sitesettings['email_system_from'], $sitesettings['email_system_from_name']);
			//replyto
			$mail->addReplyTo($sitesettings['email_system_replyto'], $sitesettings['email_system_replyto_name']);
			//subject
			$mail->Subject = $sitesettings['email_system_subject_prefix'];
			return $mail;
		}
		else
		{
			id_message("email not enabled");
			return false;
		}
}
/**
 * @brief Returns array of email/name combo from userids
 *
 * @param [in] $useridarr (should be) an array of userid numbers.
 * @return array array of people(which is an array of 'email' and 'name')
 *
 * @details takes userid array (single int acceptable). returns an array of
 * preferred name ('name') and email addresses ('email') combos per person.
 */
function id_email_getnameemailarr($useridarr)
{
	global $iddbPDO, $sitesettings;
	id_verbose("(".__FUNCTION__.")");
	//============================
	if ($useridarr)
	{
		//convert useridarr to array if single integer
		if (is_array($useridarr))
		{
			if (!$useridarr[0])
			{
				id_debug("blank array");
				return false;
			}
		}
		else {
			$useridarr = array($useridarr);
		}
		id_verbose('preparing stmt');
		//https://stackoverflow.com/questions/14767530/php-using-pdo-with-in-clause-array
		$in_qmarks = str_repeat('?,', count($useridarr) - 1) . '?';
		$PDO_id_email_getnameemail = $iddbPDO->prepare("	SELECT	iduser_account.userid,
																																	username,
																																	email,
																																	profilename,
																																	prefer_profilename
																FROM iduser_account,iduser_profile
																WHERE iduser_account.userid = iduser_profile.userid
																	AND iduser_account.userid IN ($in_qmarks);");
		id_verbose('checking db...');
		if ($PDO_id_email_getnameemail->execute($useridarr))
		{
			id_verbose($PDO_id_email_getnameemail->rowCount().'/'.count($useridarr).' found');
			if ($PDO_id_email_getnameemail->rowCount() == 0)
			{
				id_message('No valid IDs');
				id_debug('0 IDs/emails found');
				return false;
			}
			else if ($PDO_id_email_getnameemail->rowCount() < count($useridarr))
			{
				id_message('Not all valid IDs');
			}
			else if ($PDO_id_email_getnameemail->rowCount() > count($useridarr))
			{
				id_message('too many results');
				id_debug('wtf, more addresses than IDs');
				return false;
			}
			//---create array to return
			$data = $PDO_id_email_getnameemail->FetchAll(PDO::FETCH_ASSOC);
			for ($idx = 0; $idx < count($data); ++$idx)
			{
				//add name/email to nameemailarr[idx]
				if ($data[$idx]['prefer_profilename'])
					$nameemailarr[$idx]['name'] = $data[$idx]['profilename'];
			  else
					$nameemailarr[$idx]['name'] = $data[$idx]['username'];
				$nameemailarr[$idx]['email'] = $data[$idx]['email'];
			}
			return $nameemailarr;
		}
		else
		{
			id_debug('Unable to get addresses');
			return false;
		}
		return false;
	}//if $useridarr
	else
		return false;
}
/**
 * @brief takes in info and sends an email.
 *
 * @param [in] $to_id_arr array of userids to send to
 * @param [in] $from_id userid of the person sending the email
 * @param [in] $subject string the subject line
 * @param [in] $body string the content of the message
 * @param [in] $htmlmode boolean default=TRUE html email otherwise, plaintext
 * @return boolean true if all recepients have an email sent to them. (attempted)
 *
 * @details takes parameters, sends crafts email using template, sends email to
 * each recepient individually (prevents seeing eachother's emails).
 */
function id_email_send($to_id_arr,$from_id,$subject,$body,$htmlmode = TRUE)
{
	global $iddbPDO, $sitesettings, $sitesettings, $ID_ROOT;
	id_verbose("(".__FUNCTION__.")");
	//============================
	$html_template_file = $ID_ROOT.'/config/email_template.html';
	$html_template_file_distro = $ID_ROOT.'/config/email_template.html.template';
	$txt_template_file = $ID_ROOT.'/config/email_template.txt';
	$txt_template_file_distro = $ID_ROOT.'/config/email_template.txt.template';
	$email = id_email_factory();
	if ($email)
	{
		//=====PREP data=====
		//===get emails and names from database for TO
		//set emails and names
		$to_people = id_email_getnameemailarr($to_id_arr);
		if ($to_people === FALSE)
		{
			id_debug("no valid receipients");
			return false;
		}
		if (count($to_people) != count($to_id_arr))
		{
			id_debug("some invalid IDs?(toppl != to id arr)");
			return false;
		}
		//===get emails and names from database for FROM
		//set emails and names
		if ($from_id == $sitesettings['rootuserid'] || $from_id == $sitesettings['ghostuserid'])
			$from_person = array("name" => $sitesettings['email_system_from_name'], "email" => $sitesettings['email_system_from']);
		else
			$from_person = id_email_getnameemailarr($from_id)[0];
		if ($from_person === FALSE)
		{
			id_debug("invalid from user?");
			return false;
		}
		//=====PREP common message=====
		$email->setFrom($sitesettings['email_system_from'],'ID:'.$from_person['name']);
		//===set subject
		$email->Subject = $sitesettings['email_system_subject_prefix'].$subject.$sitesettings['email_system_subject_suffix'];
		//===message body
		//--html
		if ($htmlmode)
		{
			id_debug("html mode");

			if (file_exists($html_template_file))
			{
				$htmlTemplate = file_get_contents($html_template_file);
			}
			else
			{
				$htmlTemplate = file_get_contents($html_template_file_distro);
				id_debug("not found:".$html_template_file.". will fallback");
			}
			$htmlTemplate = str_replace('!!SENDER!!',$from_person['name'],$htmlTemplate);
			$htmlTemplate = str_replace('!!SUBJECT!!',$subject,$htmlTemplate);
			$htmlTemplate = str_replace('!!PREHEADER!!',substr(strip_tags($body),0,50),$htmlTemplate);
			$htmlTemplate = str_replace('!!BODY!!',$body,$htmlTemplate);
			$htmlTemplate = str_replace('!!TITLE_FULL!!',$sitesettings['title_full'],$htmlTemplate);
		}//if htmlmode
		//--text
		id_verbose("crafting txt version");
		if (file_exists($txt_template_file))
		{
			$txtTemplate = file_get_contents($txt_template_file);
		}
		else
		{
			$txtTemplate = file_get_contents($txt_template_file_distro);
			id_debug("not found:".$txt_template_file.". will fallback");
		}
		$txtTemplate = str_replace('!!SENDER!!',$from_person['name'],$txtTemplate);
		$txtTemplate = str_replace('!!SUBJECT!!',$subject,$txtTemplate);
		$txtTemplate = str_replace('!!BODY!!',strip_tags($body),$txtTemplate);
		$txtTemplate = str_replace('!!TITLE_FULL!!',$sitesettings['title_full'],$txtTemplate);
		//=====PER RECEIPIENT=====
		$sentcount = 0;
		for($sentcount = 0;$sentcount < count($to_people);++$sentcount)
		{
			$email->clearAddresses();
			$email->addAddress($to_people[$sentcount]['email'],$to_people[$sentcount]['name']);
			//format Message
			$finaltxttemplate = str_replace('!!RECEIVER!!',$to_people[$sentcount]['name'],$txtTemplate);
			if ($htmlmode)
			{
				$email->isHTML(true);
				$finalhtmltemplate = str_replace('!!RECEIVER!!',$to_people[$sentcount]['name'],$htmlTemplate);
				$email->Body = $finalhtmltemplate;
				$email->AltBody = $finaltxttemplate;
			}
			else //not html
			{
				$email->isHTML(false);
				$email->Body = $finaltxttemplate;
			}
			//TODO pgp signature
			//send the message, check for errors
			id_verbose("going to send...".($sentcount+1)."/".count($to_people));
			if (!$email->send()) {
				id_debug("Mailer Error: " . $email->ErrorInfo);
				id_message("Email Error Aborting!");
				break;
			} else {
				id_verbose("Message sent!");
				//Section 2: IMAP
				//Uncomment these to save your message in the 'Sent Mail' folder.
				//if (save_mail($mail)) {
				//    echo "Message saved!";
				//}
			}
		}//for
		id_message("Sent ".$sentcount."/".count($to_people));
		id_debug("Sent ".$sentcount."/".count($to_people));
		if ($sentcount == count($to_people))
			return true;
		else {
			id_debug ("sent != to_people");
			return false;
		}
	}//if $email
	else
	{
		id_debug("failed to get email obj");
		return false;
	}
}
function id_email_sendverification($id)
{
	global $iddbPDO, $sitesettings;
	id_verbose("(".__FUNCTION__.")");
	//============================
	//remove old hashes from db
	id_verbose("remove old verify hashes for user");
	/*
	//FIXME maintenance - this is something that applies to all users, should be a maintenance task instead
	$query_id_email_verify_removeold = $iddbPDO->query("	DELETE FROM iduser_email_verify
																														WHERE date_part('day',
																															age(datetime_sent)) <= ".
																															$sitesettings['email_system_verify_codeage']);
	*/
	$stmt_id_email_verify_removeolduser = $iddbPDO->query("	DELETE FROM iduser_email_verify
																														WHERE userid=".$id);
	$result_id_email_verify_removeolduser = $stmt_id_email_verify_removeolduser->fetch();
	if ($result_id_email_verify_removeolduser)
	{
		id_debug("Removed hash:".$result_id_email_verify_removeolduser);
	}
	else {
		id_debug("failed to remove old hash");
		//ok to continue, maybe wasnt a hash?
		//TODO check what happens if no hash
	}
	//calculate new hash
	$newhash = hash("md4","potato".$id.time());
	id_verbose("new hash:".$newhash);
	//store new hash
	id_verbose("inserting new hash");
	$stmt_id_email_verify_newhash = $iddbPDO->prepare('INSERT INTO iduser_email_verify (userid,hash)
																											VALUES (:userid,:newhash);');
	if ($stmt_id_email_verify_newhash)
	{
		$stmt_id_email_verify_newhash->bindParam(":userid",$id);
		$stmt_id_email_verify_newhash->bindParam(":newhash",$newhash);
		if ($stmt_id_email_verify_newhash->execute())
		{
			id_verbose("new hash inserted");
		}
		else {
			id_debug("error with new hash");
			return false;
		}
	}
	else {
		id_debug("db error with new hash");
		return false;
	}
	//create email
	$verifyurl = $sitesettings['home_address'].'/verify_email.php?hash='.$newhash;
	$subject='Email Verification';
	$body=	'Please verify your e-mail address by visiting <a href="'.$verifyurl.'">this link</a>.<br>'.PHP_EOL.
					'Address: <pre>'.$verifyurl.'</pre>'.PHP_EOL;
	//send email
	return id_email_send(array($id),$sitesettings['rootuserid'],$subject,$body);
	//set verified to false in account? prob not
	//TODO decide on this later, kinda?
	//id_account_edit_emailverify($id,FALSE);
}
function id_email_verify($hash)
{
	global $iddbPDO, $sitesettings;
	id_verbose("(".__FUNCTION__.")");
	//============================
	id_verbose("find/remove hash");
	$query_id_email_verify_find = $iddbPDO->query('	SELECT * FROM iduser_email_verify
																														WHERE hash='.$iddbPDO->quote($hash).';');
	if ($query_id_email_verify_find)
	{
		if ($query_id_email_verify_find->rowCount() == 1)
		{
			$result = $query_id_email_verify_find->fetch();
			//check age
			id_verbose("age:".$result['datetime_sent']);
			$age_hash = ( new DateTime($result['datetime_sent']) )->diff(new DateTime());
			$age_setting = new DateInterval('P'.$sitesettings['email_system_verify_codeage'].'D');
			if ($age_hash <= $age_setting)
			{
				//proper age, continue
			}
			else {
				//OLD!
				id_debug("hash expired");
				id_debug("old:"+$age_hash);
				id_debug("setting:"+$age_setting);
				return "Hash Expired - Request verify again";
			}
			//success - mark as verified
			$userid = $result['userid'];
			//delete hash
			$query_id_email_verify_delete = $iddbPDO->query('	DELETE FROM iduser_email_verify
																																WHERE userid='.$iddbPDO->quote($userid).';');
			if ($query_id_email_verify_delete)
			{
				//mark verified
				if (id_account_edit_emailverify($userid,TRUE))
				{
					return true;
				}
				else {
					id_debug("func failed");
					return "database error";
				}
			}//if delete
			else {
				id_debug("failed to delete old hash");
				return "Database Error";
			}//else delete
		}//if found
		else {
			//fail
			id_debug("failed: rows affected:".$query_id_email_verify_find->rowCount());
			return "Invalid Hash";
		}//else found
	}//if found executed
	else {
		id_debug("failed: failed to search");
		return "Database Error";
	}
}//function id_email_verify
?>

<?php
/**
 *  @file class_id_user.inc.php
 *  @brief class definition for ID_User class
 */
 /**
  *  @class ID_User
  *  @brief Class of a ID user. Include options, information, profile
  */
class ID_User
{
	//account
	public $userid;					///<ID unique id number
	public $username;				///<Username
	public $email;					///<Email
	public $email_verified;			///<If the email has been verified
	public $datetime_created;			///<When the account was created
	public $active;					///<Whether the account is active (ex: banned)
	public $status;					///<A flag or reason of active or other note
	//permissions
	public $id_admin;			///<Permissions - Admin access
	//profile
	public $profilename;			///<Profile name
	public $prefer_profilename;		///<Prefer profile name over username
	public $public_view;			///<0-private profile, 1-public profile, 2-only visible to id users
	public $birthday;				///<Birthdate
	public $location;				///<Location
	public $gender;					///<Gender
	public $picture;				///<saved picture boolean
	
	//functions
	/**
	 *  @brief Contsructor - initalizes variables from database using id number
	 *  
	 *  @param [in] $id ID ID number
	 *  
	 *  @details defines id variable and calls reinitialize() to retrieve information
	 */
	function __construct($userid)
	{
		id_verbose("(".__METHOD__.")");
		$this->userid	=			$userid;
		$this->reinitialize();
	}
	/**
	 *  @brief Retrieves information from the database and updates data
	 *  
	 *  @details Retrieves data from database using id number and sets varaiables.
	 */
	function reinitialize()
	{
		global $iddbPDO, $sitesettings;
		id_verbose("(".__METHOD__.")");
		//PDO
		$PDO_id_user = $iddbPDO->prepare("	SELECT COUNT(*)
											FROM iduser_account
											WHERE iduser_account.userid = ?;");
		$PDO_id_user->execute(array($this->userid));
		//if (pg_num_rows($result) == 1)
		if ($PDO_id_user->fetchColumn() == 1)
		{
			$PDO_id_user = $iddbPDO->prepare("	SELECT *
											FROM iduser_account,iduser_profile,iduser_permissions
											WHERE iduser_account.userid = iduser_profile.userid
												AND iduser_account.userid = iduser_permissions.userid
												AND iduser_account.userid = ?;");
			$PDO_id_user->execute(array($this->userid));
			$arr = $PDO_id_user->fetch(PDO::FETCH_ASSOC);
			$this->username =		$arr['username'];
			$this->email = 			$arr['email'];
			$this->email_verified =	toBool($arr['email_verified']);
			$this->datetime_created =	$arr['datetime_created'];
			$this->active =			toBool($arr['active']);
			$this->status = 		$arr['status'];
			$this->id_admin =	toBool($arr['id_admin']);
			$this->profilename =			$arr['profilename'];
			$this->prefer_profilename =	toBool($arr['prefer_profilename']);
			$this->public_view =	$arr['public_view'];	///<@todo make a toInt() and use it
			$this->birthday =		$arr['birthday'];
			$this->location =		$arr['location'];
			$this->gender =			$arr['gender'];
			$this->picture = 		toBool($arr['picture']);
		}
		else
		{
			$this->username =		"(ERROR)";
			$this->email = 			"(ERROR)";
			$this->email_verified =	false;
			$this->datetime_created =	NULL;
			$this->active =			false;
			$this->status = 		"(ERROR)";
			$this->id_admin =	false;
			$this->profilename =			"(ERROR)";
			$this->prefer_profilename =	false;
			$this->public_view =	0;
			$this->birthday =		NULL;
			$this->location =		"(ERROR)";
			$this->gender =			"E";
			$this->picture = 		false;
		}
	}
	/**
	 *  @brief Gets and returns prefered name
	 *  
	 *  @return User-name or Profile name based on profile options
	 *  
	 *  @details Returns user-name or profile name based on profile options
	 */
	function getpreferredname()
	{
		id_verbose("(".__METHOD__.")");
		if ($this->prefer_profilename && $this->profilename != '')
		{
			return $this->profilename;
		}
		else
		{
			return $this->username;
		}
	}
	
	function acp_perms_update($id_admin)
	{
		global $iddbPDO;
		id_verbose("(".__METHOD__.")");
		$id_admin = toBool_psql($id_admin);
		$PDO_id_permsup = $iddbPDO->prepare("	UPDATE iduser_permissions
												SET id_admin = :admin
												WHERE iduser_permissions.userid = :id;");
		$result = $PDO_id_permsup->execute( array(	':id' => $this->userid,
														':admin' => $id_admin)
													);
		if (!$result) id_debug($PDO_id_permsup->errorinfo());
		return $result;
	}
}
?>

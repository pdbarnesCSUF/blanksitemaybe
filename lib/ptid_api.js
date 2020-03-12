/*!
 * ID JavaScript Library v0.0.1
 * https://present.pt23.net/isds454
 *
 * Copyright Patrick Barnes
 * Please use with permission only.
 * Use at your own risk.
 * Requires jQuery - tested with 3.2.1
 * Date: 2017-09-08T01:28Z
 */
var ID = {
	var localoptions = 
	{
		contextid:0,		//what website am I? (if id will even understand)
		contexttoken:0,		//proof of who I am (if id thinks he knows you)
		retrytimer:0,	//time between reconnect
		retrylimit:0 	//times to retry
	}
	var idoptions = false; //false = didn't load
	var URL_api = "https://present.pt23.net/isds454/api";
	var currentsession = {
			hash:'',
			activedate:'',
			loggedin:false
		};
	var currentuser = {
			id:0,
			username:'',
			email:'',
			email_verified:false,
			datetime_created:'',
			name:'',
			prefer_name:false,
			birthday:'',
			location:'',
			gender:'',
			picture:false
		};
	function initialize()
	{
		//get current session cookie
		//sync idoptions
		//validate session
			//load currentsession
		//if logged in
			//load currentuser
		//if not
			//nothing
	}
	function sync_idoptions()
	{
		var URL_options = URL_api+"/options.php";
		//get json of options
			//load idoptions
		//fail
			//id disabled
	}
	function login(username,password)
	{
		var URL_login = URL_api+"/login.php";
		//send username and password via POST
			//success
				//gets success and hash
					//load currentsession
					//makes the cookie
			//wrong cred 08
				//returns wrong cred error
			//maintenance 10
				//returns maintenance mode error
		//failed to connect
			//returns failed to connect error
	}
	function logout()
	{
		var URL_logout = URL_api+"/logout.php";
		//send logout command
			//on server, deletes session hash
			//locally, delete cookie
			//return if server deleted successfully
				//"loggedout"
				//console - if logged out with errors
		//fail
			//console - "unable to communicate"
				//console -"forcing local logout"
				//delete cookie locally
	}
	function sync_user()
	{
		//currentuser = get_profile(id);
	}
	function validate_session()
	{
		var URL_validatesession = URL_api+"/validate_session.php";
		//send hash info
			//valid
				//update active date locally and on server
			//expired 11
				//delete session locally and on server
				//logged out
			//invalid 12
				//delete session locally
				//logged out
		//fail
			//unable to communicate
			//do nothing
	}
	function get_profile(userid)
	{
		var URL_profile = URL_api+"/profile.php";
		//get profile
			//if success
				//return profile
			//if fail 06
				//user does not exist or private
		//fail
	}
	function message_send(destination,msg)
	{
		var URL_messagesend = URL_api+"/message_send.php";
		//send message, with context
			//if success
				//"message sent"
			//if user does not exist 09
				//user does not exist
			//if.... fail?
				//an error has occurred
		//fail
			//no connection
	}
	function message_check()
	{
		var URL_messagecheck = URL_api+"/message_check.php";
		//get message numbers
			//returns number of unread within context
			//returns number of unread total
		//fail
			//no connection
	}
	function message_read(messageid)
	{
		var URL_messageread = URL_api+"/message_read.php";
		//read specific message
			//if found
				//return message
			//invalid message or wrong user 13
				//invalid message
		//fail
			//no connection
	}
};

<?php
/**
 *  @file id_box.inc.php
 *  @brief A 200x75px box for sites using ID login.
 *  
 *  @todo FIXME not for outside references (API)
 */
?>
<div id="id_box">
<?php
require_once($id_ROOT.'/config/sitesettings.php');
require_once($id_ROOT.'/lib/local/loginmodal.php');

if (isset($id_ROOT)&& isset($sitesettings['username_max_length'])) //is ID included?
{
	if (isset ($sitesettings['maintenance']) && !isset($id_maintenance_bypass))
	{
		?>
		<div id="maintenance" >
			<b>
				ID Maintenance!
			</b>
		</div>
		<?php
	}
	else//NOT maintenance mode
	{
	if (id_session_loggedin()) //is logged in?
	{
	?>
	<!-- logged in -->
		
<span id="box_left">
	<?php
		if ($id_SESSION['id_current_user']->picture)
		{?>
			<img class="id_profpic" src="<?php echo $sitesettings['profile_pic_location'];?>/<?php echo $id_SESSION['id_current_user']->userid;?>.png" alt="Your profile picture" />
	<?php  }
		else
		{
	?>
			<img class="id_profpic" src="<?php echo $sitesettings['images_address'];?>/PTU_anon.png" alt="No profile picture" />
	<?php  }?>
</span>
	<span id="box_right">
		<div id="username">
			<a href="<?php echo $sitesettings['home_address'];?>/user_profile.php"><?php echo id_account_getusername($id_SESSION['id']);?></a>
		</div>
		<div id="messages">
			0 messages ever
		</div>
		<div id="logout">
			<button id="id_btnlogout" class="id_button">Logout</button>
			<script>
			$(document).ready(function () {
				$("#id_btnlogout").click(function(e) {
					var url = "<?php echo $sitesettings['api_address'];?>/logout.php"; // the script where you handle the form input.

					$.ajax({
						   type: "POST",
						   url: url,
						   //data: '',
						   success: function(rtndata)
						   {
								//alert(rtndata); // show response from the php script.
								if (rtndata.action == 1)
								{
									//alert("success");
									location.pathname = "";
									//location.reload(); //fallback
								}
								else
								{
									alert("Error:"+rtndata.action);
									$("#id_modal_message").text('' + rtndata.data);
									$("#id_modal_message").show();
								}
						   }
						 });
				});
			});
			</script>
		</div>
	</span>
	
	<?php
	}
	else
	{
	?>
<!-- not logged in -->
<span id="box_left">
	<a href="<?php echo $sitesettings['home_address'];?>" target="_blank">
		TMS
	</a>
</span>
<span id="box_right">
	<button id="id_btnshowmodallogin" class="id_button id_showmodallogin">Login with ID</button>
	<script>
	$(".id_showmodallogin").click( function (){
		$("#id_modal_login").show();
		$("#id_modal_login_username").focus();
	});
	</script>
</span>
	<?php
	}
	}//end of NOT maintenance (else)
}
else
{
	echo "(ノಠ益ಠ)ノ彡pıʇd bad developer decided to NOT include the proper id file";
}
?>
</div>

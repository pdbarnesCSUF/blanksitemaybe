<?php
/**
 *  @file idbox_bootstrap3_navbar.php
 *  @brief A ID item for inclusion inside a bootstrap3 navbar.
 *  
 */
?>
<!--idbox_bootstrap3_navbar-->
<?php
require_once($id_ROOT.'/config/sitesettings.php');
require_once($id_INCLUDE.'/loginmodal.php');

if (isset($id_ROOT)&& isset($sitesettings['username_max_length'])) //is ID included?
{
?><ul id="idbox_bootstrap3_navbar" class="nav navbar-nav navbar-right"><?php
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
		{?>
		<!-- logged in -->
			<li id="idbox_bs3nav_username">
			<a id="idbox_bs3nav_proflink" href="<?php echo $sitesettings['home_address'];?>/user_profile.php">
	<?php	if ($id_SESSION['id_current_user']->picture)
			{?>
				<img id="idbox_bs3nav_profpic" src="<?php echo $sitesettings['profile_pic_location'];?>/<?php echo $id_SESSION['id_current_user']->userid;?>.png" alt="Your profile picture" width="256" height="256" />
	<?php	}
			else
			{?>
				<img id="idbox_bs3nav_profpic" src="<?php echo $sitesettings['images_address'];?>/PTU_anon.png" alt="No profile picture" />
	<?php	}?>
				<?php echo id_account_getusername($id_SESSION['id']);?>
			</a>
			</li>
			<li id="idbox_bs3nav_messages">
				<a>
				<del>0 <span class="glyphicon glyphicon-envelope"></span></del>
				</a>
			</li>
			<button id="idbox_bs3nav_btnlogout" class="btn navbar-btn">Logout</button>
			<script>
			$(document).ready(function () {
				$("#idbox_bs3nav_btnlogout").click(function(e) {
					$.ajax({
						type: "POST",
						url: "<?php echo $sitesettings['api_address']?>/logout.php",
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
								console.log('' + rtndata.data);
							}
						},
						fail: function (rtndata) {
							$("#id_modal_login_status").text("Connection Error");
						}
					});
				});
			});
			</script>
<?php } else {?>
	<!-- not logged in -->
		<li>
		<a id="idbox_bs3nav_proflink" href="<?php echo $sitesettings['home_address']?>" target="_blank">
			<img id="idbox_bs3nav_profpic" src="<?php echo $sitesettings['images_address']?>/id.png" alt="ID" />ID
		</a>
		</li>
		<button class="btn navbar-btn" id="idbox_bs3nav_btnshowmodallogin">Login/Create</button>
		<script>
			$("#idbox_bs3nav_btnshowmodallogin").click( function (){
				$("#id_modal_login").show();
				$("#id_modal_login_username").focus();
			});
		</script>
<?php	}
	}//end of NOT maintenance (else)
?> </ul>
<?php
}
else
{
	echo "(ノಠ益ಠ)ノ彡pıʇd bad developer decided to NOT include the proper id file";
}
?>
<!--end idbox_bootstrap3_navbar-->

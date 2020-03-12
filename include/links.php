<?php
/**
 *  @file links.php
 *  @brief Included - Links on the top of the page for all pages
 */
?>
<div id="id_modal_login" class="modal fade" role="dialog">
	<script>
	$(document).ready(function () {
		$("#id_modal_login_close").click( function() {
			$("#id_modal_login_username").blur();
			$("#id_modal_login_password").blur();
		});
		//focus textbox
		$('#id_modal_login').on('shown.bs.modal', function () {
			$('#id_modal_login_username').focus();
		})
		// https://stackoverflow.com/questions/1960240/jquery-ajax-submit-form
		// this is the id of the form
		$("#id_modal_login_submit").click(function(e) {
			$("#id_modal_login_submit").prop('value',"Logging in...");
			$("#id_modal_login_submit").prop('disabled',true);
			$.ajax({
					type: "POST",
					url: "<?php echo $sitesettings['home_address'];?>/api/login.php",
					data: $("#id_modal_login_form").serialize(), // serializes the form's elements.
					xhrFields: { withCredentials: true },
					success: function(rtndata)
					{
						//console.log(data); // show response from the php script.
						if (rtndata.action == 1)
						{
							//console.log("success");
							location.reload();
						}
						else
						{
							console.log("Error:"+rtndata.action);
							$("#id_modal_login_status").text('' + rtndata.data);
						}
					},
					fail: function (rtndata) {
						$("#id_modal_login_status").text("Connection Error");
					}
				});
				$("#id_modal_login_submit").prop('value',"Login");
				$("#id_modal_login_submit").prop('disabled',false);
			e.preventDefault(); // avoid to execute the actual submit of the form.
		});
	});
	</script>
	<div class="modal-dialog">
		<div id="id_modal_login_content" class="modal-content">

			<div class="modal-header">
				<input type="button" id="id_modal_login_close" class="btn btn-default close" data-dismiss="modal" value="Close">
				<a href="<?php echo $sitesettings['home_address']?>" target="_blank">
					<img style="max-width: 50px;" alt="ID" src="<?php echo $sitesettings['images_address'];?>/id.png">
					<h4 class="modal-title">ID</h4>
				</a>
			</div>
			<form id="id_modal_login_form" role="form">
				<div class="form-group">
					<label for="id_modal_login_username"><span class="glyphicon glyphicon-user"></span> Username</label>
					<input id="id_modal_login_username" class="form-control" name="username" placeholder="Username" required autofocus>
				</div>
				<div class="form-group">
					<label for="id_modal_login_password"><span class="glyphicon glyphicon-eye-open"></span> Password</label>
					<input id="id_modal_login_password" class="form-control" name="password" placeholder="Password" type="password" required>
				</div>
				<input type="hidden" name="device" value="web">
				<div id="id_modal_login_buttons_row">
					<input type="submit" id="id_modal_login_submit" class="btn btn-default" value="Login">
					<del><a href="<?php echo $sitesettings['home_address']?>/forgotpass.php" target="_blank" alt="Password Recovery (new window)">Forgot Password</a></del>
					<a href="<?php echo $sitesettings['home_address']?>/create.php" target="_blank" alt="Create an account (new window)">Create</a>
				</div>
			</form><!--id_modal_login_form-->
			<div id="id_modal_login_status" class="id_modal_message"></div>
		</div><!--id_modal_login_content-->
	</div><!--modal-dialog-->
</div><!--id_modal_login-->
<nav id="sitenav" class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a id="header-img" class="navbar-left"  href="<?php echo $sitesettings['address']?>"><img style="max-width:50px" alt="ID" src="<?php echo $sitesettings['images_address'];?>/id.png"></a>
			<a class="navbar-brand" href="<?php echo $sitesettings['address']?>">ID</a>
		</div>
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li id="sitenav-viewprofile"><a href="<?php echo $sitesettings['address']?>/view_userlist.php">View Profiles</a></li>
				<li id="sitenav-about"><a href="<?php echo $sitesettings['address']?>/about.php">About</a></li>
			</ul>
			<ul id="navbar_login" class="nav navbar-nav navbar-right">
			<?php
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
						<li id="navbar_login_username">
						<a id="navbar_login_proflink" href="<?php echo $sitesettings['home_address'];?>/user_profile.php">
				<?php	if ($ID_SESSION['id_current_user']->picture)
						{?>
							<img id="navbar_login_profpic" src="<?php echo $sitesettings['profile_pic_location'];?>/<?php echo $ID_SESSION['id_current_user']->userid;?>.png" alt="Your profile picture" width="256" height="256" />
				<?php	}
						else
						{?>
							<img id="navbar_login_profpic" src="<?php echo $sitesettings['images_address'];?>/PTU_anon.png" alt="No profile picture" />
				<?php	}?>
							<?php echo id_account_getusername($ID_SESSION['id']);?>
						</a>
						</li>
						<li id="navbar_login_messages">
							<a>
							<del>0 <span class="glyphicon glyphicon-envelope"></span></del>
							</a>
						</li>
						<button id="navbar_login_btnlogout" class="btn navbar-btn">Logout</button>
						<script>
						$(document).ready(function () {
							$("#navbar_login_btnlogout").click(function(e) {
								$.ajax({
									type: "POST",
									url: "<?php echo $sitesettings['home_address'];?>/api/logout.php",
									//data: '',
									success: function(rtndata)
									{
										//alert(rtndata); // show response from the php script.
										if (rtndata.action == 1)
										{
											//alert("success");
											window.location.href = "<?php echo $sitesettings['home_address'];?>";
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
				<!--
					<li>
					<a id="navbar_login_proflink" href="<?php echo $sitesettings['home_address']?>" target="_blank">
						<img id="navbar_login_profpic" src="<?php echo $sitesettings['images_address']?>/id.png" alt="ID" />ID
					</a>
					</li>
					-->
					<button class="btn navbar-btn" id="navbar_login_btnshowmodallogin" data-toggle="modal" data-target="#id_modal_login">Login/Create</button>
			<?php	}
				}//end of NOT maintenance (else)
			?> </ul><!--navbar_login-->
			<ul class="nav navbar-nav navbar-right">
				<?php if (id_account_isadmin()) { ?>
					<li class="downdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php if (id_account_getpermission($ID_SESSION['id'],'id_admin')) { ?><li id="sitenav-id_admin"><a href="<?php echo $sitesettings['address']?>/admin/id_admin.php">ID/Dash</a></li><?php } ?>
							<?php if (id_account_getpermission($ID_SESSION['id'],'mc_admin')) { ?><li id="sitenav-mc_admin"><a href="<?php echo $sitesettings['address']?>/admin/mc_admin.php">Minecraft</a></li><?php } ?>
							<?php if (id_account_getpermission($ID_SESSION['id'],'cs16_admin')) { ?><li id="sitenav-cs16_admin"><a href="<?php echo $sitesettings['address']?>/admin/cs16_admin.php">CS 1.6</a></li><?php } ?>
							<?php if (id_account_getpermission($ID_SESSION['id'],'csgo_admin')) { ?><li id="sitenav-csgo_admin"><a href="<?php echo $sitesettings['address']?>/admin/csgo_admin.php">CS GO</a></li><?php } ?>
							<?php if (id_account_getpermission($ID_SESSION['id'],'terraria_admin')) { ?><li id="sitenav-terraria_admin"><a href="<?php echo $sitesettings['address']?>/admin/terraria_admin.php">Terraria</a></li><?php } ?>
						</ul>
					</li>
				<?php } ?>
				<?php if (id_session_loggedin()) { ?>
					<li id="sitenav-user-account"><a href="<?php echo $sitesettings['address']?>/user_account.php">ID Account</a></li>
					<li id="sitenav-user-profile"><a href="<?php echo $sitesettings['address']?>/user_profile.php">Edit Profile</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</nav>

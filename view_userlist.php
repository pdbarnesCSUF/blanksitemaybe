<?php
/**
 *  @file view_userlist.php
 *  @brief View list of users
 *  
 */
use Tracy\Debugger;
require_once ('vendor/tracy.php');
require_once('include/include.php');
if ($sitesettings['debug'])
	Debugger::enable(Debugger::DEVELOPMENT);

//comment to use defaults
//opengraph and twitter meta info
$pageinfo['title'] = "Users - ".$sitesettings['meta']['og:title'];
$pageinfo['description'] = "ID user list.";
//$pageinfo['type'] = $sitesettings['meta']['og:type'];
//$pageinfo['url'] = $sitesettings['meta']['og:url'];
//$pageinfo['image'] = $sitesettings['meta']['og:image'];

//--------

$numusers = 0;
if (id_session_loggedin())
{
	$sql = "	SELECT	iduser_account.userid,username,profilename,datetime_created,active,status
				FROM iduser_account,iduser_profile
				WHERE iduser_account.userid=iduser_profile.userid
					AND iduser_profile.public_view >= 1
				ORDER BY userid;";
}
else
{
	$sql = "	SELECT	iduser_account.userid,username,profilename,prefer_profilename
				FROM iduser_account,iduser_profile
				WHERE iduser_account.userid=iduser_profile.userid
					AND iduser_profile.public_view = 1
				ORDER BY userid;";
}
$resultusers = $iddbPDO->query($sql);
if ($resultusers != false)
{
	$numusers = $resultusers->rowCount();
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
        <?php include($INCLUDE.'/head.php'); ?>
        <title>Users - <?php echo $sitesettings['title']?></title>
        <script>$(function(){$("#sitenav-viewprofile").addClass("active");});</script>
</head>
<body>
	<?php include ($INCLUDE.'/links.php');?>
<div id="main" class="container-fluid">
	<h1><strong>Users</strong></h1>
	<div id="mainwell" class="well">
		<div id="userlist" class="well">
			<h2>Users</h2>
			<table class="table table-hover table-condensed">
				<thead>
					<tr>
						<th>ID</th>
						<th>Username</th>
						<th>Profile Name</th>
					</tr>
				</thead>
					<?php
		if ($resultusers != false)
		{
			if ($numusers > 0)
			{
				echo "<tbody id=\"userlist_body\">".PHP_EOL;
				for ($i=0; $i < $numusers; $i++)
				{
					//get data
					$arrusers = $resultusers->fetch(PDO::FETCH_ASSOC); ///@todo fetchObject..or maybe even combined with a foreach loop to get rid of the $numrows var 
					echo "<tr>".PHP_EOL;
						echo "<td>".$arrusers['userid']."</td>".PHP_EOL;
						echo "<td><a href=\"view_user.php?userid=".$arrusers['userid']."\">".$arrusers['username']."</a></td>".PHP_EOL;
						echo "<td>".$arrusers['profilename']."</td>".PHP_EOL;
					echo "</tr>".PHP_EOL;
				}
				echo "</tbody>".PHP_EOL;
			}
			else
			{
				echo "<tr><td>No users or none public</td><td></td><td></td><td></td></tr>".PHP_EOL;
			}
		}
		else
		{
			echo "<tr><td>DB error:VPL</td></tr>";
		}
		?>
			</table>
		</div><!--userlist-->
	</div><!--mainwell-->
</div><!--main-->
<?php require($INCLUDE.'/copy.php'); ?>
</body>
</html>

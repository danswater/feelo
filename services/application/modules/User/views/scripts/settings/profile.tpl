<style type="text/css">
	.profile-iframe{
		width: 100%;
		overflow:hidden; 		
	}
	.form-divider{
		padding-top: 30px;
		border-top: 1px solid #c4c4c4;
	}
	.profile-menu li{
		padding: 10px;
		background: #eee4e4;
		margin: 4px 0px;
		font-size: 16px;
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		border-radius: 10px; /* future proofing */
		-khtml-border-radius: 10px; /* for old Konqueror browsers */
	}
	.profile-table td{
		padding: 10px;
	}
</style>
<?php
	$thisUrl = $this->url(array('module' => 'members', 'controller' => 'settings', 'action' => 'profile'), 'default', true);
?>

<table cellpadding="0" cellspacing="0" width="100%" class="profile-table">
	<tr>
		<td valign="top" width="20%">
			<ul class="profile-menu">
				<li>
					<a href="<?php echo $thisUrl; ?>#generalForm">General</a>
				</li>
				<li>
					<a href="<?php echo $thisUrl; ?>#profileForm">Profile Info</a>
				</li>
				<li>
					<a href="<?php echo $thisUrl; ?>#pictureForm">Picture</a>
				</li>
				<li>
					<a href="<?php echo $thisUrl; ?>#privacyForm">Privacy</a>
				</li>
				<li>
					<a href="<?php echo $thisUrl; ?>#notificationForm">Notification</a>
				</li>
				<li>
					<a href="<?php echo $thisUrl; ?>#emailsNotificationForm">Email Notification</a>
				</li>
				<li>
					<a href="<?php echo $thisUrl; ?>#changePasswordForm">Change Password</a>
				</li>
				<li>
					<a href="<?php echo $thisUrl; ?>#deleteAccountForm">Delete Account</a>
				</li>
			</ul>
		</td>
		<td valign="top">
			<div id="generalForm" class="form-divider">
				<iframe scrolling="no" onload="iframeLoaded(this)" id="general-iframe" class="profile-iframe" src="<?php echo $this->url(array('module' => 'members', 'controller' => 'settings', 'action' => 'general', 'format' => 'smoothbox'), 'default', true) ?>"></iframe>
			</div>

			<div id="profileForm" class="form-divider">
				<iframe scrolling="no" onload="iframeLoaded(this)" id="general-iframe" class="profile-iframe" src="<?php echo $this->url(array('module' => 'members', 'controller' => 'edit', 'action' => 'profile', 'format' => 'smoothbox'), 'default', true) ?>"></iframe>
			</div>

			<div id="pictureForm" class="form-divider">
				<iframe scrolling="no" onload="iframeLoaded(this)" id="general-iframe" class="profile-iframe" src="<?php echo $this->url(array('module' => 'members', 'controller' => 'edit', 'action' => 'photo', 'format' => 'smoothbox'), 'default', true) ?>"></iframe>
			</div>

			<div id="privacyForm" class="form-divider"> 
				<iframe scrolling="no" onload="iframeLoaded(this)" id="privacy-iframe" class="profile-iframe" src="<?php echo $this->url(array('module' => 'members', 'controller' => 'settings', 'action' => 'privacy', 'format' => 'smoothbox'), 'default', true) ?>"></iframe>
			</div>

			<div id="notificationForm" class="form-divider"> 
				<iframe scrolling="no" onload="iframeLoaded(this)" id="notification-iframe" class="profile-iframe" src="<?php echo $this->url(array('module' => 'members', 'controller' => 'settings', 'action' => 'notifications', 'format' => 'smoothbox'), 'default', true) ?>"></iframe>
			</div>
			
			<div id="emailsNotificationForm" class="form-divider"> 
				<iframe scrolling="no" onload="iframeLoaded(this)" id="notification-iframe" class="profile-iframe" src="<?php echo $this->url(array('module' => 'members', 'controller' => 'settings', 'action' => 'emails', 'format' => 'smoothbox'), 'default', true) ?>"></iframe>
			</div>			

			<div id="changePasswordForm" class="form-divider"> 
				<iframe scrolling="no" onload="iframeLoaded(this)" id="password-iframe" class="profile-iframe" src="<?php echo $this->url(array('module' => 'members', 'controller' => 'settings', 'action' => 'password', 'format' => 'smoothbox'), 'default', true) ?>"></iframe>
			</div>

			<div id="deleteAccountForm" class="form-divider"> 
				<iframe scrolling="no" onload="iframeLoaded(this)" id="account-iframe" class="profile-iframe" src="<?php echo $this->url(array('module' => 'members', 'controller' => 'settings', 'action' => 'delete', 'format' => 'smoothbox'), 'default', true) ?>"></iframe>
			</div>

		</td>
	</tr>
</table>
<script type="text/javascript">
	function iframeLoaded( elem ) {
	    if(elem) {
	    	var height = elem.contentWindow.document.body.scrollHeight + 50;
	    	elem.height = "";
	    	elem.height = height + "px";
	    }   
	}
</script>
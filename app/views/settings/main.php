<div id="container" style="display: block; padding-top:50px;">
	<div class="width settings animated fadeInUp">
		<header style="text-align:center; margin-bottom: 40px;">
			<h1>Settings</h1>
		</header>

		<section>
			<h2>General Settings</h2>

			<div class="settingsItem">
				<h3>Your Email</h3>
				<input type="text" ng-model="current_user.email" ng-disabled="input_disabled.email">
				<span ng-show="errors.email != ''" class="error">{{ errors.email  }}</span>
				<div class="settingsButtons">
					<a href="javascript:void(0)" class="buttonOutlines" ng-click="input_disabled.email = false" ng-hide="input_disabled.email == false">Edit</a>
					<a href="javascript:void(0)" class="buttonOutlines green" ng-click="updateGeneralInfo( 'email' )" ng-hide="input_disabled.email">Save</a>
					<a href="javascript:void(0)" class="buttonOutlines" ng-hide="input_disabled.email" ng-click="input_disabled.email = true">Cancel</a>
				</div>
			</div>

			<div class="settingsItem">
				<h3>Username</h3>
				<input type="text" ng-model="current_user.username" ng-disabled="input_disabled.username">
				<span ng-show="errors.username != ''" class="error">{{ errors.username  }}</span>
				<div class="settingsButtons">
					<a href="javascript:void(0)" class="buttonOutlines" ng-click="input_disabled.username = false" ng-hide="input_disabled.username == false">Edit</a>
					<a href="javascript:void(0)" class="buttonOutlines green" ng-click="updateGeneralInfo( 'username' )" ng-hide="input_disabled.username">Save</a>
					<a href="javascript:void(0)" class="buttonOutlines" ng-hide="input_disabled.username" ng-click="input_disabled.username = true">Cancel</a>
				</div>
			</div>

			<div class="settingsItem">
				<h3>Full name</h3>
				<input type="text" ng-model="current_user.displayname" ng-disabled="input_disabled.displayname">
				<span ng-show="errors.displayname != ''" class="error">{{ errors.displayname  }}</span>
				<div class="settingsButtons">
					<a href="javascript:void(0)" class="buttonOutlines" ng-click="input_disabled.displayname = false" ng-hide="input_disabled.displayname == false">Edit</a>
					<a href="javascript:void(0)" class="buttonOutlines green" ng-click="updateGeneralInfo( 'displayname' )" ng-hide="input_disabled.displayname">Save</a>
					<a href="javascript:void(0)" class="buttonOutlines" ng-hide="input_disabled.displayname" ng-click="input_disabled.displayname = true">Cancel</a>
				</div>
			</div>

			<div class="settingsItem">
				<h3>Password</h3>
				<div style="float:left; width: 400px; display:block;">
					<input type="password" ng-model="pass.password" placeholder="old password" ng-disabled="input_disabled.password">
					<input type="password" ng-model="pass.new_password" placeholder="new password" ng-disabled="input_disabled.password">
					<input type="password" ng-model="pass.confirm_password" placeholder="re-enter new password" ng-disabled="input_disabled.password">
					<span ng-show="errors.password != ''" class="error">{{ errors.password  }}</span>
				</div>

				<div class="settingsButtons">
					<a href="javascript:void(0)" class="buttonOutlines" ng-click="input_disabled.password = false" ng-hide="input_disabled.password == false">Change Password</a>
					<a href="javascript:void(0)" class="buttonOutlines green" ng-hide="input_disabled.password" ng-click="changePassword()">Save Password</a>
					<a href="javascript:void(0)" class="buttonOutlines" ng-hide="input_disabled.password" ng-click="input_disabled.password = true">Cancel</a>
				</div>
			</div>

		   	<div class="settingsItem">
				<h3>Timezone </h3>
				<select ng-model="current_user.timezone" ng-disabled="input_disabled.timezone" 
					ng-options="timezone.name as timezone.utc for timezone in settings.timezones"
				/>


				<div class="settingsButtons">
					<a href="javascript:void(0)" class="buttonOutlines" ng-click="input_disabled.timezone = false" ng-hide="input_disabled.timezone == false">Edit</a>
					<a href="javascript:void(0)" class="buttonOutlines green" ng-hide="input_disabled.timezone" ng-click="updateGeneralInfo( 'timezone', 'name' )">Save</a>
					<a href="javascript:void(0)" class="buttonOutlines" ng-hide="input_disabled.timezone" ng-click="input_disabled.timezone = true">Cancel</a>
				</div>
			</div>

		</section>

		<section>
			<h2>Profile Settings</h2>

			<div class="settingsItem">
				<h3>Profile Description</h3>
				<textarea></textarea>

				<div class="settingsButtons">
					<a href="javascript:void(0)" class="buttonOutlines">Edit</a>
					<a href="javascript:void(0)" class="buttonOutlines green">Save</a>
					<a href="javascript:void(0)" class="buttonOutlines">Cancel</a>
				</div>
			</div>

			<div class="settingsItem">
				<h3>Profile Image</h3>
				<span>Input image</span>

				<div class="settingsButtons">
					<a href="javascript:void(0)" class="buttonOutlines">Edit</a>
					<a href="javascript:void(0)" class="buttonOutlines green">Save</a>
					<a href="javascript:void(0)" class="buttonOutlines red">Remove Photo</a>
					<a href="javascript:void(0)" class="buttonOutlines">Cancel</a>
				</div>
			</div>
		</section>

		<section>
			<h2>Privacy Settings</h2>

			<div class="settingsItem">
				<h3>Blocked Users</h3>
				<input type="text" value="See a list of all the users you've blocked.">

				<div class="settingsButtons">
					<a href="#" class="buttonOutlines">Manage</a>
					<a href="#" class="buttonOutlines green">Save</a>
					<a href="#" class="buttonOutlines">Cancel</a>
				</div>
			</div>

			<div class="settingsItem">
				<h3>Profile Privacy</h3>
				<input type="text" value="Private, Public">

				<div class="settingsButtons">
					<a href="#" class="buttonOutlines">Edit</a>
					<a href="#" class="buttonOutlines green">Save</a>
					<a href="#" class="buttonOutlines">Cancel</a>
					</div>
			</div>
		</section>

		<section>
			<h2>Notification Settings</h2>

			<div class="settingsItem">
				<h3>In App Notifications</h3>
				<div style="float:left; width: 400px; display:block;">
					<input type="text" value="When people comment on my post">
					<input type="text" value="When someone follow my collection">
					<input type="text" value="When new content are added to the favo I follow ">
				</div>

				<div class="settingsButtons">
					<a href="#" class="buttonOutlines">Edit</a>
					<a href="#" class="buttonOutlines green">Save</a>
					<a href="#" class="buttonOutlines">Cancel</a>
				</div>
			</div>

			<div class="settingsItem">
				<h3>Email Notifications</h3>
				<div style="float:left; width: 400px; display:block;">
					<input type="text" value="When people comment on my post">
					<input type="text" value="When someone follow my collection">
					<input type="text" value="When new content are added to the favo I follow ">
				</div>

				<div class="settingsButtons">
					<a href="#" class="buttonOutlines">Edit</a>
					<a href="#" class="buttonOutlines green">Save</a>
					<a href="#" class="buttonOutlines">Cancel</a>
				</div>
			</div>
		</section>

		<section>
			<h2>Deactivate Account</h2>

			<div class="settingsItem">
				<h3>Delete Account</h3>
				<div style="float:left; width: 500px; display:block;">
					<span>Are you sure you want to delete your account? Any content you've uploaded 
					in the past will be permanently deleted. You will be immediately signed out 
					and will no longer be able to sign in with this account.</span>
				</div>

				<div class="settingsButtons">
					<a href="#" class="buttonOutlines">Edit</a>
					<a href="#" class="buttonOutlines red">Delete Account</a>
					<a href="#" class="buttonOutlines">Cancel</a>
				</div>
			</div>

			<div class="settingsItem">
				<h3>Export Data</h3>
				<input type="text" value="linus@copygr.am">

				<div class="settingsButtons">
					<a href="#" class="buttonOutlines">Export Now</a>
					<a href="#" class="buttonOutlines green">Send .zip</a>
					<a href="#" class="buttonOutlines">Cancel</a>
				</div>
			</div>
		</section>
	</div>
</div>
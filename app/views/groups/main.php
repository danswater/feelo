<div id="container" class="previewLeftNavOut">
	<div class="groupEditHeader">
		<h1>{{ box_name }}</h1><br />
	 	<a href="javascript:void(0)" class="buttonOutlines active" ng-show="edit_group == false" ng-click="edit_group=true">Edit</a>
	 	<a href="javascript:void(0)" class="buttonOutlines active red" ng-show="edit_group == false" ng-click="edit_group=true">Remove group</a>

	 	<input type="text" ng-model="box_name" ng-show="edit_group == true" />
	 	<a href="javascript:void(0)" class="buttonOutlines green active" ng-click="saveGroupName()" ng-show="edit_group == true">Save</a>
	</div>

	<ul class="followList">
		<li ng-repeat="user_box in box_users" ng-if="user_box.User != null">
			<a ui-sref="main.profile({ username: user_box.User.username })">
				<img class="userImage circle" ng-src="{{ user_box.User.storage_path | ybImagePath }}" />
			</a>
			<h3>
				<a ui-sref="main.profile({ username: user_box.User.username })" class="followUsername">
					{{ user_box.User.username }}
				</a>
			</h3>
		
			<div class="listUserActions" style="float: right;">
				<h3><a href="javascript:void(0)" ng-click="addRemoveUserInBox( user_box.User.user_id, $index )" class="addAction">Remove</a></h3>
			</div>
		</li>
	</ul>
</div>
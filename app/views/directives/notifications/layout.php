<notification ng-if="message.type.toString().toLowerCase() == 'commented'">
	<a ui-sref="main.profile({ username: message.data.sender.username })">
    	<img class="userImage circle" ng-src="{{ message.data.sender.image_storage_path | ybImagePath }}" />
    </a>
	<h3>
		<a ui-sref="main.profile({ username: message.data.User.username })" class="followUsername">{{ message.data.User.username }}</a> 
		{{ message.data.description }} 
	</h3>

	<a ui-sref="main.post({ project_id: message.data.post.project_id })" >
		<img class="userImage"  style="float: right; margin-right:20px;" ng-src="{{ message.data.post.image_storage_path | ybImagePath }}" /></a>
	</a>

	<div class="listUserActions" style="float: right; margin-right:20px;">
        <h3><a ui-sref="main.post({ project_id: message.data.post.project_id })" class="addAction">Reply </a></h3>
	</div>
</notification>



<notification ng-if="message.type.toString().toLowerCase() == 'liked' || message.type.toString().toLowerCase() == 'tagged' || message.type.toString().toLowerCase() == 'whmedia_processed_failed' ">
	<a ui-sref="main.profile({ username: message.data.sender.username })">
    	<img class="userImage circle" ng-src="{{ message.data.sender.image_storage_path | ybImagePath }}" />
    </a>
	<h3>
		<a ui-sref="main.profile({ username: message.data.User.username })" class="followUsername">{{ message.data.User.username }}</a> 
		{{ message.data.description }} 
	</h3>
	<a ui-sref="main.post({ project_id: message.data.post.project_id })">
		<img class="userImage"  style="float: right; margin-right:20px;" ng-src="{{ message.data.post.image_storage_path | ybImagePath }}" /></a>
	</a>
</notification>


<notification ng-if="message.type.toString().toLowerCase() == 'friend_follow' || message.type.toString().toLowerCase() == 'friend_follow_accepted'" >
	<a ui-sref="main.profile({ username: message.data.sender.username })">
    	<img class="userImage circle" ng-src="{{ message.data.sender.image_storage_path | ybImagePath }}" />
    </a>
	<h3>
		<a ui-sref="main.profile({ username: message.data.User.username })" class="followUsername">{{ message.data.User.username }}</a> 
		{{ message.data.description }} 
	</h3>

	<a ui-sref="main.profile({ username: message.data.sender.username })">
		<img class="userImage circle" style="float: right; margin-right:20px;" ng-src="{{ message.data.receiver.image_storage_path | ybImagePath }}" /></a>
	</a>
</notification>


<notification ng-if="message.type.toString().toLowerCase() == 'friend_follow_request'">
	<a ui-sref="main.profile({ username: message.data.sender.username })">
    <img class="userImage circle" ng-src="{{ message.data.sender.image_storage_path | ybImagePath }}" /></a>
	<h3>
		<a ui-sref="main.profile({ username: message.data.User.username })" class="followUsername">{{ message.data.User.username }}</a> 
		{{ message.data.description }} 
	</h3>

	<div class="listUserActions" style="float: right;">
        <h3><a href="javascript:void(0)" ng-click="confirmFollowRequest( true, message.data.sender.user_id )" class="addAction"> Yes </a></h3> 
        <h3>|</h3>
        <h3><a href="javascript:void(0)" ng-click="confirmFollowRequest( false, message.data.sender.user_id )" class="addAction"> No </a></h3>
	</div>
</notification>
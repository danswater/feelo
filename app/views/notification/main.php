<div id="container" style="margin: 0 auto 20px; width: 100%; display: block; padding-top:100px;" class="animated fadeInUp">
	<div class="notificationButtonsCanvas">
		<a ui-sref="main.notification( { type : 'general' } )" class="buttonOutlines" ng-class="{ 'active': notification_type == 'general' }">General</a>  
		<a ui-sref="main.notification( { type : 'request' } )" class="buttonOutlines" ng-class="{ 'active': notification_type == 'request' }">Request</a>
	</div>
	<div style="margin: 0 auto; width: 100%; display: block;">
		<ul class="followList">
			<li ng-repeat="notification in notifications" yb-notification-message-builder index="$index" layout="{{ notification.type.toString().toLowerCase() }}" message="notification"> </li>
		</ul>
	</div>
</div>
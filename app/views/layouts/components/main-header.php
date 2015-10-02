<div class="mobileTopNav"> </div>
<div class="">
    <div id="topNavContainer" ng-controller="TopNavController">
        <nav class="width">
            <ul>
                <li><a href="javascript:void(0)" ng-click="showLeftMenuCanvas()" id="logo"><img src="assets/custom/img/png/yambalogo.png" width="80" height="50" style="width:80px; height=50px; display:block; float: left;"></a></li>
                <li><a ui-sref="main.activity_feed">Feed</a></li>
                <li><a ui-sref="main.featured">Featured</a></li>
                <!-- <li><a ui-sref="main.trending( { creation_date : 'week' } )">Trending</a></li> -->
                <li><a href="javascript:void(0)" ng-click="showTrendingModal()">Trending</a></li>
                <li><a href="javascript:void(0)" ng-click="showSearchForm()" >Search</a></li>
                <li><a href="javascript:void(0)" ng-click="showNewPostModal()" >Post</a></li>

                <li style="float: right;"><a href="javascript:void(0)" ng-click="logout()">Logout</a></li>
                <li style="float: right;"><a ui-sref="main.settings">Settings</a></li>
                <li style="float: right;"><a ui-sref="main.profile({ username: App.user.username })">Profile</a></li>
                <li style="float: right;"><a ui-sref="main.notification( { type : 'general' } )">Notifications</a></li>
            </ul>
        </nav>
    </div>
</div>

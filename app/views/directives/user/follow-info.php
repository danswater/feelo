<li>
    <a ui-sref="main.profile({ username: data.User.username })"><img class="userImage circle" src="http://yamba.rocks/services/{{ data.User.storage_path }}" /></a>
    
    <h3><a ui-sref="main.profile({ username: data.User.username })" class="followUsername">{{ data.User.username }}</a></h3>
    
    <div class="listUserActions" style="float: right;">
        <h3 ng-if="data.is_followed == 0"><a href="#" class="followAction follow{{data.User.user_id}}" ng-click="follow(data.User.user_id)">Follow</a></h3>
        <h3 ng-if="data.is_followed == 1"><a href="#" class="followAction unfollow follow{{data.User.user_id}}" ng-click="unfollow(data.User.user_id)">Unfollow</a></h3>
        <h3><a href="#" class="addAction ng-hide">Add</a></h3>
    </div>

    <!-- <div ng-if="method == 'fetchFollowing'" class="listUserActions" style="float: right;">
        <h3><a href="#" class="followAction unfollow">Unfollow</a></h3>
        <h3><a href="#" class="addAction">Add</a></h3>
    </div> -->
</li>




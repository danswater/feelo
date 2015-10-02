<div id="profileHeaderBackground" style="background-image: url({{ cover | ybImagePath }})" class="animated fadeIn"></div>

<div class="profileHeaderCanvas">  
    <div class="width">
        <div class="profileImage">
            <img src="{{ data.storage_path | ybImagePath }}" />
            <!-- <a href="" class="button" ng-if="follow == 0">Follow</a>
            <a href="" class="button" ng-if="follow == 1">Unfollow</a>
            <a href="" class="button" ng-if="follow != 2" ng-click="group()">Add</a>
            <a href="" class="button" ng-if="follow == 2" style="width:100%;">Edit</a> -->

            <a href="" class="button" id="follow_status" ng-if="follow.my_profile == false" ng-click="followButtonFunction(data.user_id)">{{follow.follow_status}}</a>
            <a href="" class="button" ng-if="follow.my_profile == false" ng-click="group(data)">Add</a>
            <a ui-sref="main.settings" class="button" ng-if="follow.my_profile == true" style="width:100%;">Edit</a>
        </div>

        <div class="profileHeaderMeta animated fadeIn">
            <h1>{{ data.displayname }}</h1>
            <span class="username">@{{ data.username }}</span>
            <span class="description">Barcelona, Spain - Urban explorer and avid traveler.</span>

            <ul class="animated fadeInDown">
                <li>
                    <a href="#" ui-sref="main.profile({ username: data.username })">
                        <span class="numbers">{{ data.posts }}</span><br />
                        <span class="lable">Posts</span>
                    </a>
                </li>
                <li>
                    <a href="#" ui-sref="main.followers({ username: data.username })">
                        <span class="numbers">{{ data.followers }}</span><br />
                        <span class="lable">Followers</span>
                    </a>
                </li>
                <li>
                    <a href="#" ui-sref="main.following({ username: data.username })">
                        <span class="numbers">{{ data.following }}</span><br />
                        <span class="lable">Following</span>
                    </a>
                </li>
                <li>
                    <a href="#" ui-sref="main.hashtags({ username: data.username })">
                        <span class="numbers">11</span><br />
                        <span class="lable">Hashtags</span>
                    </a>
                </li>
                <li>
                    <a href="#" ui-sref="main.collections({ username: data.username })">
                        <span class="numbers">23</span><br />
                        <span class="lable">Collections</span>
                    </a>
                </li>
                <li ng-if="follow.my_profile == true">
                    <a href="#" ui-sref="main.likes({ username: data.username })">
                        <span class="numbers">321</span><br />
                        <span class="lable">Likes</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>    
</div>

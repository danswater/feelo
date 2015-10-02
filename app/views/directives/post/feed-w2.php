<article>
    <header>
        <a ng-repeat="hashtag in data.Hashtag" ng-href="#!/main/hashtag/{{ hashtag.text }}">
            <span class="itemHashtag" style="background-color: {{ data.hashtag_color }};" >#{{ hashtag.text }}</span>
        </a>

        <a href="javascript:void(0)" class="itemMoreButton" ng-click="feed_options(data.User)"></a>

        <a href="javascript:void(0)" ui-sref="main.post({ project_id: data.project_id })">
            <div class="itemImage" style="background-image:URL('{{ data.Media.storage_path | ybImagePath }}');">
                <div class="itemImageShade">
                    <span class="itemTimestamp">{{ data.creation_date }} ago</span>
                </div>
            </div>
        </a>
        <div class="itemTitle"><h2><a href="" ui-sref="main.post({ project_id: data.project_id })" yb-html-unsafe="data.title">{{ data.title }}</a></h2></div>      
        <div class="itemDevider"></div>
    </header>

    <div class="itemDescription"><h4>{{ data.description | ybStringLimit:160 }}</h4></div>

    <footer>
        <div class="itemAuthor">
            <div class="itemAuthorImage circle" style="background-image:URL('{{ data.User.storage_path | ybImagePath }}');"></div>
            <a href="javascript:void(0)" ui-sref="main.profile({ username: data.User.username })" class="username">@{{ data.User.username }}</a>
            <div class="itemMeta"> 
                <a ng-click="likeFeed(data)" ng-if="data.is_liked == 0" class="itemLikes">{{ data.like_count_int }} </a>
                <a ng-click="likeFeed(data)" ng-if="data.is_liked == 1" class="itemLikes liked">{{ data.like_count_int }} </a>
                <span class="itemComments" ng-click="commentModal(data.project_id,data.scope_ctr)">{{ data.comment_count_int }} </span>
                <span class="itemMediaType {{ data.Media.type | ybMediaType }}"></span>
                <a href="javascript:void(0)" ng-click="addCollection( data )" class="itemAddButton"></a>
            </div>
        </div>
    </footer>
</article>

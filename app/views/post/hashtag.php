<div id="hashtagHeaderBackground" style="background-image: url( {{ headerBg | ybImagePath }} )"></div>

<div class="hashtagHeaderCanvas">  
    <div class="width">
        <div class="hashtagHeaderMeta">

        	<div class="hashtagHeaderContainer">
			 	<h2>{{ hashtag }}</h2>
				<h5>{{ hashtag_info.result_count }} posts</h5>
        		<a href="" ng-click="hashtagFollow(hashtag_info.tag_id)" id="follow-hashtag">{{hashtag_info.message}}</a>
			</div>

        	 <!-- <ul>
                <li>
                    <a href="#" ui-sref="main.profile({ username: data.username })">
                        <span class="numbers">{{ data.posts }}</span><br />
                        <span class="lable">Posts</span>
                    </a>
                </li>
            <ul> -->
        </div>
    </div>    
</div>

<div id="profileContentCanvas"></div>

<div id="container" class="grid js-masonry masonry" style="margin: 0 auto; width: 100%; display: block; padding-top: 40px !important;">
    <div style="margin: 0 auto; width: 100%; display: block;">
    	<yb-feeds type="hashtag" filter="filterHashtag" callback="afterFinish( feeds )"></yb-feeds>
    </div>  
</div>

<span id="thespinner" style="display:block; width:100px; position: relative; margin: 80px auto 0;"></span>
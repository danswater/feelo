<div id="darkroom" class="clearfix">
	<div class="width animated fadeIn">

		<div yb-post-media project="post_data"></div>
		
		<div class="singleViewMeta starwars">

			<div class="authorMeta">
				<div class="authorImage circle" style="background-image: url('{{ post_data.User.storage_path | ybImagePath }}');"></div>
				<span class="authorName">{{ post_data.User.displayname }}</span>
				<span class="authorHandle"><a href="#" ui-sref="main.profile({ username: data.User.username })">@{{ post_data.User.username }}</a></span>
				<span class="AuthorFollow" ng-if="!isOwner"><a href="javascript:void(0)" class="buttonWhiteSmall" ng-click="unfollowFollow()">{{ follow.status_string }}</a></span>
			</div>

			<div class="mediaAction">
				<a href="javascript:void(0)" class="buttonWhite likes" ng-class="{ 1 : 'liked' }[post_data.is_liked]" style="margin-right: 20px;" ng-click="likeUnliked()"> 
					<span ng-if="post_data.is_liked == 0">Like</span>
					<span ng-if="post_data.is_liked == 1">Unlike</span>
				</a>
				<a href="javascript:void(0)" class="buttonWhite add" style="margin-right: 20px;" ng-click="addCollection()">Add</a>
				<a href="javascript:void(0)" class="buttonWhite repost" style="margin-right: 20px;" ng-click="sharePost()">Re-post</a>
				<a href="javascript:void(0)" class="buttonWhite share" style="margin-right: 20px;" ng-click="sharePost()">Share</a>
			</div>

			<div class="mediaMeta">
				<!--
				<div class="mediaSaves">
					<span>{{ post_data.project_saves }}</span>
					<p class="mediaMetaTitle">Saves</p>
				</div>
				-->
				<div class="mediaLikes">
					<span>{{ post_data.like_count }}</span>
					<p class="mediaMetaTitle">Likes</p>
				</div>
				<div class="mediaViews">
					<span>{{ post_data.project_views }}</span><br />
					<p class="mediaMetaTitle">Views</p>
				</div>
			</div>

		</div>

	</div>
</div>


<div id="container" class="clearfix animated fadeIn">

	<div class="twoCol">

		<div class="articleCanvas">

			<span class="timestamp">Posted {{ post_data.creation_date }} ago</span>

			<span class="hashtags"><a ng-href="#!/main/hashtag/{{ hashtag.text }}" class="itemHashtag" ng-repeat="hashtag in post_data.Hashtag" style="background-color: {{ data.hashtag_color }};">#{{ hashtag.text }}</a> </span>
			
			<h1>{{ post_data.title }}</h1>

			<h5 class="hashtag">Via <a href="{{ post_data.Media.url }}" target="_new">website</a></h5>

			<!-- <h4 class="hashtag">Hashtags <a href="#">#{{ post_data.Hashtag[0].text }},</a> <a href="#">#{{ post_data.Hashtag[1].text }},</a> <a href="#">#{{ post_data.Hashtag[2].text }}</a></h4> -->
			<p yb-html-unsafe="post_data.description">{{ post_data.description }}</p>

		</div>

		<!-- button for load more here -->
		<div ng-if="hasMoreComment">
			<a href="javascript:void(0)" ng-click="loadMoreComment()">Load More Comment</a>
		</div>
		<section class="commentsCanvas">
			<ul>
				<li ng-repeat="comment in comments">
					<div class="commentCanvas">
						<div class="commentAuthorImage">
							<img ng-src="{{ comment.User.storage_path | ybImagePath }}" class="circle"/>
						</div>	
						<div class="commentMetaCanvas">
							<span class="commentUsername">@{{ comment.User.displayname }}</span>
							<span class="commentTimestamp">{{ comment.time_diff | ybDisplayTimeDiff:true }} ago</span>
							<p class="commentContent"> {{ comment.body }} </p>
						</div>
					</div>
				</li>
			</ul>
		</section>
		<div class="commentPostCanvas">
			<form ng-submit="submitComment( commentForm )" method="post" name="commentForm">
				<textarea class="commentPost" ng-model="form.comment.body" required placeholder="Type your comment" yb-text-box-limit="512" yb-textbox-counter-container="comment_counter"></textarea>
				<input type="submit" class="button right" value="Post Comment">
			</form>
			<div id="comment_counter"></div>
		</div>

	</div>


	<div class="relatedItems">
		<section class="related yb-related-post" projects="related_projects" ></section>
		<section class="related yb-related-favos" favos="related_favos"> </section>
	</div>		

</div>



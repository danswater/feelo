<div id="overlay" ng-show="display">
    <!-- button for load more here -->
	<div ng-if="hasMoreComment">
		<a href="javascript:void(0)" ng-click="loadMoreComment()">Load More Comment</a>
	</div>
	<section class="commentsCanvas" style="overflow: hidden; height:250px;">
		<ul style="margin-top:0px;">
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
<div id="fade" ng-show="display" ng-click="close()"></div>
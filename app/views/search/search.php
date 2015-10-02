<div id="container" class="grid js-masonry masonry" style="margin: 0 auto 20px; width: 100%; display: block; padding-top:100px;">
	<div style="margin: 0 auto; width: 100%; display: block;">
		
		<!-- HASHTAH IS HERE -->
		<figure class="itemSearch" ng-repeat="hashtag in search.hashtags">
			<article>
				<a ng-href="#!/main/hashtag/{{ hashtag.text }}">
					<div class="media" style="background-image: url('{{ hashtag.Post.image.storage_path | ybImagePath }}')">
						<div class="hashtagCount">
							<span>{{ hashtag.result_count }}</span>
							<small>posts</small>
						</div>
					</div>
				</a>
				
				<div class="mediaMedia">
					<a ng-href="#!/main/hashtag/{{ hashtag.text }}">#{{ hashtag.text }}</a><br />
				</div>
			</article>
		</figure>
		
		<!-- MEDIA IS HERE -->
		<figure class="itemSearch" ng-repeat="feed in search.feeds">
			<article>
				<a ui-sref="main.post({ project_id: feed.project_id })">
					<div class="media" style="background-image: url('{{ feed.Media.storage_path | ybImagePath }}')">
						<span class="itemMediaType {{ data.Media.type | ybMediaType }}"></span>
					</div>
				</a>

				<div class="mediaMedia">
					<a ui-sref="main.post({ project_id: feed.project_id })"> {{ feed.title }}</a>
				</div>
			</article>
		</figure>

		<!-- USER IS HERE -->
		<figure class="itemSearch" ng-repeat="user in search.users">
			<article>
				<a ng-href="#!/main/profile/{{ user.User.displayname }}">
					<div class="media" style="background-image: url('{{ user.User.storage_path | ybImagePath }}')">
					</div>
				</a>
				<div class="mediaMedia">
					<a ng-href="#!/main/profile/{{ user.User.displayname }}">{{ user.User.displayname }}</a><br />
					<a ng-href="#!/main/profile/{{ user.User.displayname }}">@{{ user.User.username }}</a>
				</div>
			</article>
		</figure>

		<!-- COLLECTION IS HERE -->
		<figure class="itemSearch" ng-repeat="favo in search.favos">
			<article>
				<a ng-href="#!/main/favo/{{ favo.favcircle_id }}">
					<div class="media" style="background-image: url('{{ favo.storage_path | ybImagePath }}')"> </div>
				</a>
				<div class="mediaMedia">
					<a ng-href="#!/main/favo/{{ favo.favcircle_id }}"> {{ favo.title }} </a>
				</div>
			</article>
		</figure>
	</div>
</div>


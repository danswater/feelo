<div id="trendingContainerForm" style="display:none;" class="instantAnimation fadeInDown" ng-controller="TrendingModalController">
	<a href="javascript:void(0)" ng-click="closeTrendingModal()" class="close"></a>
	<div class="trendingWidth overlayTrending">
		<div class="trendingOverlayMenucol">
			<h2>Filters</h2>
			<ul>
				<li><a href="javascript:void(0)" ng-click="changeTrending('today')" ng-class="{ 'today':'active' }[current_filter]" title="Todays trending posts">Today</a></li>
				<li><a href="javascript:void(0)" ng-click="changeTrending('week')" ng-class="{ 'week':'active' }[current_filter]" title="This weeks trending posts">This Week</a></li>
				<li><a href="javascript:void(0)" ng-click="changeTrending('month')" ng-class="{ 'month':'active' }[current_filter]" title="This months trending posts">This Month</a></li>
			</ul>

			<h2>Interests</h2>
			<ul>
				<li><a href="#" target="" class="" title="">Business</a></li>
				<li><a href="#" target="" class="" title="">Technology</a></li>
				<li><a href="#" target="" class="" title="">Science</a></li>
				<li><a href="#" target="" class="" title="">History</a></li>
				<li><a href="#" target="" class="" title="">Arts</a></li>
				<li><a href="#" target="" class="" title="">Photography</a></li>
				<li><a href="#" target="" class="" title="">Politics</a></li>
				<li><a href="#" target="" class="" title="">Travel</a></li>
				<li><a href="#" target="" class="" title="">Food</a></li>
				<li><a href="#" target="" class="" title="">Culture</a></li>
				<li><a href="#" target="" class="" title="">Music</a></li>
			</ul>
		</div>

		<div class="trendingOverlayMaincol">
			<h2>Trending Posts</h2>
			<ul>
				<li ng-repeat="trend in trending" >
					<article>
					<a ui-sref="main.post({ project_id: trend.project_id })"><img src="{{ trend.Media.storage_path | ybImagePath }}" /></a>
					<h6 class="toplist">{{$index + 1}}.</h6>
					<a ng-href="#!/main/hashtag/{{ hashtag.text }}" target="" class="" title="" ng-repeat="hashtag in trend.Hashtag"> {{ hashtag.text | ybStringLimit:25 }} </a>
					<h3><a ui-sref="main.post({ project_id: trend.project_id })" class="postlink"> {{ trend.title }} </a></h3>
					</article>
				</li>

				<!--li>
					<article>
					<a href="#"><img src="http://yamba.rocks/services/public/whmedia_media/0e/50/4fbe_c3e1.jpg" /></a>
					<h6 class="toplist">2.</h6>
					<a href="#" target="" class="" title="">Apps</a>, <a href="#" target="" class="" title="">Technology</a>
					<h3><a href="#" class="postlink">A Sweet App That Helps You Visualize Complex Rhythms</a></h3>
					</article>
				</li>

				<li>
					<article>
					<a href="#"><img src="http://yamba.rocks/services/public/whmedia_media/af/4f/4f60_3718.jpg" /></a>
					<h6 class="toplist">3.</h6>
					<a href="#" target="" class="" title="">Design</a>, <a href="#" target="" class="" title="">Art</a>
					<h3><a href="#" class="postlink">The Age of Drone Vandalism Begins With an Epic NYC Tag</a></h3>
					</article>
				</li>

				<li>
					<article>
					<a href="#"><img src="http://yamba.rocks/services/public/whmedia_media/08/50/4fb8_c55a.jpg" /></a>
					<h6 class="toplist">4.</h6>
					<a href="#" target="" class="" title="">Art</a>, <a href="#" target="" class="" title="">Fine Art Photography</a>
					<h3><a href="#" class="postlink">Real-Life Soldiers Masquerade as Toys in the African Desert</a></h3>
					</article>
				</li>

				<li>
					<article>
					<a href="#"><img src="http://yamba.rocks/services/public/whmedia_media/7f/4f/4f30_4de7.jpeg" /></a>
					<h6 class="toplist">5.</h6>
					<a href="#" target="" class="" title="">Instagram</a>, <a href="#" target="" class="" title="">Star Wars</a>, <a href="#" target="" class="" title="">Entertainment</a>
					<h3><a href="#" class="postlink">You Really Need to Be Following the Star Wars Instagram</a></h3>
					</article>
				</li>

				<li>
					<article>
					<a href="#"><img src="http://yamba.rocks/services/public/whmedia_media/dd/4f/4f8e_7e32.jpg" /></a>
					<h6 class="toplist">6.</h6>
					<a href="#" target="" class="" title="">Autopia</a>, <a href="#" target="" class="" title="">NASA</a>, <a href="#" target="" class="" title="">Space</a>
					<h3><a href="#" class="postlink">NASA's New 10-Engine Drone Is Half Chopper, Half Plane</a></h3>
					</article>
				</li -->
			</ul>
			<div ng-show="loadingTrend">
				<h6 class="toplist">Loading... Please wait.</h6>
			</div>	
			<a href="javascript:void(0)" class="buttonWhite" ng-click="showMoreTrending()">Show more trending</a>
		</div>
	</div>
</div>
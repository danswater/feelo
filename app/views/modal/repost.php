<div id="overlay" class="yambaForm" ng-show="display" style="background:#eee; width:20%; left: 40%;">
  <div class="repost">
 	<img ng-src="{{ post.Media.storage_path | ybImagePath }}" style="max-width:100%;"/>
  	<input type="button" value="Repost" ng-click="repostPost()" class="submitButton" style="margin-top:10px;"/>
  </div>
</div>

<div id="fade" ng-show="display" ng-click="close()"></div>
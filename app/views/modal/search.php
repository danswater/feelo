<div id="searchContainerForm" ng-controller="SearchFormController" style="display:none">
	<form ng-submit="searchKeyword( searchForm )" name="searchForm" style="background-color: #fff;">
		<a href="javascript:void(0)" ng-click="closeSearchModal()" class="close" style="color: #333 !important; top: 10px; left: 8px;"></a>
		<h5 style="padding-left:40px; padding-top:20px;color: #777;">Here you can search for anything, users, hashtags, collections and media</h5>
		<input type="text" class="searchInput" id="searchFormInput" ng-model="search.keyword" required />
	</form>
	<div class="searchCanvas" ng-click="closeSearchModal()"> </div>
</div>
<div id="notification" style="display:none" class="animated infinite pulse"> </div>


<div ng-include="'app/views/layouts/components/main-leftsidemenu.php'"></div>

<div id="mainAppCanvas">
	
	<header ng-include="'app/views/layouts/components/main-header.php'"></header>

	<div ui-view></div>

	<footer ng-include="'app/views/layouts/components/main-footer.php'"></footer>

</div>

<div ng-include="'app/views/modal/trending.php'"></div>
<div ng-include="'app/views/modal/search.php'"></div>
<div ng-include="'app/views/modal/post.php'"></div>


<!-- for images -->
<div class="singleViewMedia animated fadeInLeft" ng-if="render.image">
	<img src="{{ project.Media.storage_path | ybImagePath }}" />	
</div> 

<!-- for videos -->
<div class="singleViewMedia animated fadeInLeft" ng-if="render.embedded">
	<div class="videoWrapper" ng-bind-html="embed"> </div>
</div>

<!--div>

	<div ng-repeat="embed in embedded">
		<div ></div>
	</div>	

</div-->
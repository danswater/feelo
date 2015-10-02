<div ng-if="favos.length > 0">
	<h4>Related Collections</h4>
	<ul>
		<li ng-repeat="favo in favos">
			<div class="realatedItemImage" style="background-image: url('{{ favo.Favo.photos.thumb | ybImagePath }}')"></div>
			<h5>{{ favo.Favo.title }}</h5>
		</li>
	</ul>
</div>
<profile:header data="user_data" cover="random_cover" follow="follow"></profile:header>
<div id="profileContentCanvas"></div>

<div id="container" class="grid js-masonry masonry" style="margin: 0 auto 20px; width: 100%; display: block; padding-top:100px;">
	<div style="margin: 0 auto; width: 100%; display: block;" infinite-scroll="favos.nextPage()" infinite-scroll-disabled='favos.busy' infinite-scroll-distance='2'>

		<figure class="itemSearch" ng-repeat="favo in favos.items">
			<article>
				<a ng-href="#!/main/favo/{{ favo.favcircle_id }}">
					<div class="media" style="background-image: url('{{ favo.storage_path | ybImagePath }}')">
					</div>
				</a>
				<div class="mediaMedia">
					<a ng-href="#!/main/favo/{{ favo.favcircle_id }}"> {{ favo.title }} </a>
				</div>
			</article>
		</figure>

	</div>
</div>

<span id="thespinner"></span>
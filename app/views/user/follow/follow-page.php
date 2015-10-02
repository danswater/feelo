<profile:header data="user_data" cover="random_cover" follow="follow"></profile:header>

<div id="profileContentCanvas"></div>
<div id="container" style="padding-top:70px;">
	<!-- <ul class="followList" infinite-scroll="follow_service.nextPage(fetch_method,user_data.user_id)" infinite-scroll-disabled='follow_service.busy' infinite-scroll-distance='2'> -->
	<ul class="followList" infinite-scroll="follow_service.nextPage(fetch_method)" infinite-scroll-disabled='follow_service.busy' infinite-scroll-distance='2'>
		 <follow:info ng-repeat="follow in follow_service.items" data="follow" method="fetch_method"> </follow:info>
	</ul>
	<!-- <ul class="followList">
		<follow:info filter="fetchFollowers" user="user_data.user_id"/>
	</ul> -->
</div>
<span id="thespinner"></span>

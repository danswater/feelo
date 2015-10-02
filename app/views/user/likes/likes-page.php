<profile:header data="user_data" cover="random_cover" follow="follow"></profile:header>

<div id="profileContentCanvas"></div>

<div id="container" class="grid js-masonry masonry" style="margin: 0 auto; width: 100%; display: block; padding-top:100px;">
    <div style="margin: 0 auto; width: 100%; display: block;">
    	<yb-feeds type="mylikepost" filter="filterUser" userid="{{user_data.user_id}}"></yb-feeds>
    </div>        
</div>

<span id="thespinner"></span>
<div class="width" style="padding-top: 50px; ">
	<ul style="list-style-type:none;">
		<li><a ui-sref="main.trending( { creation_date : 'day' } )" class="button" style="margin-right:5px;">Today</a></li>
		<li><a ui-sref="main.trending( { creation_date : 'week' } )" class="button" style="margin-right:5px;">This Week</a></li>
		<li><a ui-sref="main.trending( { creation_date : 'month' } )" class="button">This Month</a></li>
	</ul>
</div>  

<div id="container" class="grid js-masonry masonry" style="margin: 0 auto; width: 100%; display: block; padding-top:20px;">
    <div style="margin: 0 auto; width: 100%; display: block;">
    	<yb-feeds type="trending" filter="trending.filter"></yb-feeds>
    </div>        
</div>

<span id="thespinner" style="display:block; width:100px; position: relative; margin: 80px auto 0;"></span>
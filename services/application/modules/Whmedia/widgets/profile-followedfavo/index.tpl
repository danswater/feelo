<div id="followedfavcirclewrapper">

	<div id="followedfavcircletable">
	<table cellpadding="0" cellspacing="0" border="" width="100%" id="favCircle">
		<?php $ctr = 1; $cntCtr = 1; $total = count($this->favcircle); ?>
		<?php foreach ($this->favcircle as $circle): ?>
			<?php echo ($ctr == 1) ? "<tr>" : ""; ?>
			<td align="center">
				<div id="favcircle_<?php echo $circle["favcircle_id"]; ?>">
					<div>
						<a href="<?php echo $this->url(array('controller' => 'favboxes', 'action' => 'menprojectlist', 'favcircle_id' => $circle["favcircle_id"]), 'default', true) ?>">
							<img src="<?php echo $circle["photos"]["thumb"]; ?>" alt="cover_photo"/>
						</a>
					</div>
					<div class="category">
						<?php echo $circle["title"]; ?>
					</div>
					<?php if(isset($this->following) && $this->following == true){ ?> 
					<div>
						<a href="javascript:void(0)" onclick="followedFavo(this)" favid="<?php echo $circle["favcircle_id"]; ?>" uid="<?php echo $circle["user_id"]; ?>" id="favcircle_followed_<?php echo $circle["favcircle_id"]; ?>" class="followed-favo follower_button_5 media-follow-btn unfollow">
							unfollow
						</a>
					</div>
					<?php } ?>
				</div>
			</td>
			<?php if($ctr == 4 || $cntCtr == $total){ echo "</tr>"; $ctr = 0; }; ?>
		<?php $ctr++; $cntCtr++ ?>
		<?php endforeach; ?>
	</table>

	<?php if( $this->itempercount < $this->totalitem ){ ?>  
		<div style="text-align:center; padding-bottom:20px; font-size:18px;">
			<a href="javascript:void(0)" onclick="loadmyfollowedfav(<?php echo $this->page; ?>)" style="color:#000; font-weight:bold;">
				Load my followed fav 
			</a>
		</div>
		<?php } ?>
	</div>
</div>

<script type="text/javascript">
	var cReq = "";

	function followedFavo(eElem){
		if(cReq != "") alert("Please wait the previous request is not yet finished.");
		var favid = $(eElem).get("favid");
		var uid = $(eElem).get("uid");
		cReq = new Request({
			url : "<?php echo $this->url(array('controller'=>'favboxes','action'=>'followfav'),'default', true) ?>",
			data : {
				favcircle_id: favid,
				favuser: uid
			},
			onSuccess: function(responseText, responseXML){
				var elem = $("favcircle_followed_" + favid);
				if(elem.hasClass('unfollow')){
					elem.removeClass('unfollow');
					elem.set('html', 'follow');
				}else{
					elem.addClass('unfollow');
					elem.set('html', 'unfollow');
				}

				cReq = "";
			}	
		}).send();

	}
  
	function loadmyfollowedfav(page){
				if(cReq != "") return;

				cReq = new Request({
					url : "<?php echo $this->url(array('controller'=>'widget','action'=>'index', 'content_id' => '707'),'default', true) ?>",
					data : {
						format: 'html',
						page: page,
						subject : 'user_<?php echo $this->current_user_id; ?>'
					},
					onSuccess: function(responseText, responseXML){
						var el = Elements.from(responseText);
						var elemToUpdate = el.getElements("#followedfavcircletable");
						$("followedfavcirclewrapper").set('html', elemToUpdate[0].get( 'html' ) );
						cReq = "";
					}	
				}).send();


			}
</script>

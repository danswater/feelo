<div id="favcirclewrapper">

	<div id="favcircletable">
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
							<a href="javascript:void(0)" favid="<?php echo $circle["favcircle_id"]; ?>" 
								onclick="followingFavo(this)" id="favcircle_link_<?php echo $circle["favcircle_id"]; ?>" class="following-favo follower_button_5 media-follow-btn <?php echo $circle['isFollow'] != true ? 'unfollow' : ''; ?> ">
								<?php echo $circle['isFollow'] != true ? 'unfollow' : 'follow'; ?>
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
			<a href="javascript:void(0)" onclick="loadmyfav(<?php echo $this->page; ?>)" style="color:#000; font-weight:bold;">
				Load my fav 
			</a>
		</div>
		<?php } ?>

	</div>

</div>

<script type="text/javascript">
			var cReq = "";

			function followingFavo( thisElem ){
				if(cReq != "") alert("Please wait the previous request is not yet finished.");
				var favid = $(thisElem).get("favid");
				cReq = new Request({
					url : "<?php echo $this->url(array('controller'=>'favboxes','action'=>'followfav'),'default', true) ?>",
					data : {
						favcircle_id: favid,
						favuser: <?php echo $this->current_user_id; ?>
					},
					onSuccess: function(responseText, responseXML){
						var elem = $("favcircle_link_" + favid);
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

			function loadmyfav(page){
				if(cReq != "") return;

				cReq = new Request({
					url : "<?php echo $this->url(array('controller'=>'widget','action'=>'index', 'content_id' => '706'),'default', true) ?>",
					data : {
						format: 'html',
						page: page,
						subject : 'user_<?php echo $this->current_user_id; ?>'
					},
					onSuccess: function(responseText, responseXML){
						var el = Elements.from(responseText);
						var elemToUpdate = el.getElements("#favcircletable");
						console.info(elemToUpdate[0].get( 'html' ));
						$("favcirclewrapper").set('html', elemToUpdate[0].get( 'html' ) );
						cReq = "";
					}	
				}).send();


			}

		</script>

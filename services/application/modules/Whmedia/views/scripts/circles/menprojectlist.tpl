<div class="headline" style="height:30px;">
	<div style="position:relative;height:30px;" >
		 <?php echo $this->itemPhoto($this->userCircle, 'thumb.icon1'); ?>
			<div style="float:left; font-size: 14px; padding: 8px 10px; color : #abaaaa;"> <?php echo $this->userCircle->getTitle(); ?> </div>
	</div>
	<div style="position:relative;background-color:transparent;;height:30px;margin-top:-30px;">
		<center><h2><?php echo $this->favcircle->title; ?></h2></center>
	</div>
	<div style="position:relative;background-color:transparent;height:30px;margin-top:-30px;text-align:right;">
		<?php if(isset($this->hasFollowed)): ?>

			<a href="javascript:void(0)" id="favcircle_link" class="following-favo follower_button_5 media-follow-btn <?php echo $this->followed != true ? 'unfollow' : ''; ?> ">
				<?php echo $this->followed != true ? 'unfollow' : 'follow'; ?>
			</a>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
	var cReq = "";
	$$(".following-favo").addEvent('click', function(e){
		if(cReq != "") alert("Please wait the previous request is not yet finished.");
		cReq = new Request({
			url : "<?php echo $this->url(array('controller'=>'favboxes','action'=>'followfav'),'default', true) ?>",
			data : {
				favcircle_id: <?php echo $this->favcircle->favcircle_id; ?>,
				favuser: <?php echo $this->userCircle->getIdentity(); ?>
			},
			onSuccess: function(responseText, responseXML){
				var elem = $("favcircle_link");
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
	});
</script>

<?php echo $this->render('application/modules/Mediamasonry/widgets/featured-project-lazy/index.tpl') ?>
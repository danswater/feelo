<style type="text/css">
	.checked{
		background-image: url('application/modules/Whmedia/externals/images/check_icon.png');
		background-repeat: no-repeat;
    	background-attachment: scroll;
    	background-color: transparent;
		width: 17px;
		height: 17px;
		float: right; 
		
	}
</style>

<div id="follow_tags_rapper">
	<div> <center> <h2> More like this? </h2> </center> </div>
	<div> <center> Please Select hashtags To follow </center> </div>
	<br/>
	<center>
		<table cellpadding="0" cellspacing="0" border="0">
			<?php $ctr = 0; ?>
			<?php foreach ($this->tags as $tag) { ?>
			<?php if($ctr == 0){ ?> <tr> <?php } ?>
				<td align="center" style="padding: 10px;">
					<?php 
						$addedClass = "unfollow"; 
						$iconStyle = "none";
						if(isset($this->followed[$tag["tag_id"]])){
							$addedClass = "";
							$iconStyle = "block";
						}
					?>
					<a href="javascript:void(0)" style="width: 200px; padding:10px;" tid="<?php echo $tag["tag_id"]; ?>" class="following follower_button_2 media-follow-btn <?php echo $addedClass; ?>"> 
						<?php echo $tag["text"];?>
						<span class="checked check_<?php echo $tag["tag_id"]; ?>" style="display: <?php echo $iconStyle; ?>;"></span>	
					</a>
				</td>
			<?php $ctr++; ?>
			<?php if($ctr > 2 || $ctr == count($this->tags)){ $ctr = 0; ?> </tr> <?php } ?>
			<?php } ?>
		</table>
	</center>
	<br/>
	<div style="text-align:right">
		<a href="javascript:void(0)" class="media-follow-btn save_follow">Save</a>
		<a href="javascript:void(0)" class="media-follow-btn cancel_follow unfollow">Cancel</a>
	</div>
</div>
<script type="text/javascript">
	var tag_follow = [];
	var tag_unfollow = [];

	var arrayFn = {

		addToArray : function(arr, val){
			arr.push(val);
		},
		removeToArray : function(arr, val){
			var index = arr.indexOf(val);
			if(index > -1){
				arr.splice(index, 1)
			}
		}
	}
	$$(".following").addEvent("click", function(event){
		event.preventDefault()
		event.stop();
		var tid = $(this).get("tid");
		if($(this).hasClass("unfollow")){
			$(this).removeClass('unfollow');
			$$(".check_"+tid).setStyle("display", "block");	

			arrayFn.addToArray(tag_follow, tid);
			arrayFn.removeToArray(tag_unfollow, tid);
		}else{
			$(this).addClass('unfollow');
			$$(".check_"+tid).setStyle("display", "none");	

			arrayFn.removeToArray(tag_follow, tid);
			arrayFn.addToArray(tag_unfollow, tid);
		}
	})
	var saving_follow = function(event){
		event.preventDefault()
		event.stop();

		if(tag_follow.length <= 0 && tag_unfollow.length <= 0) return;

		var data_filter = {};
		$$('.save_follow').removeEvent("click", saving_follow);
		for(var i = tag_follow.length; i--;){
			data_filter["tag_follow_" + tag_follow[i]] = tag_follow[i];
			
			try {
				var objProject = parent.document.getElementById( 'project-' + tag_follow[ i ] );
				var strClass = objProject.getAttribute( 'class' );
				var arrClass = strClass.split( ' ' );
				arrClass[ 1 ] = 'followed-icon';
				objProject.setAttribute( 'class', arrClass.join( ' ' ) );
			} catch( e ) {}

		}
		for(var i = tag_unfollow.length; i--;){
			data_filter["tag_unfollow_" + tag_unfollow[i]] = tag_unfollow[i];

			try {
				var objProject = parent.document.getElementById( 'project-' + tag_unfollow[ i ] );
				var strClass = objProject.getAttribute( 'class' );
				var arrClass = strClass.split( ' ' );
				arrClass[ 1 ] = 'follow-icon';
				objProject.setAttribute( 'class', arrClass.join( ' ' ) );
			} catch( e ) {}

		}
		new Request({
			url : "<?php echo $this->url(array('controller' => 'index', 'action'=>'following-hashtag', 'module' => 'whmedia'), 'default', true) ?>",
			data : data_filter,
			onRequest: function(){
				$$(".save_follow").set("text", "Saving...");	
			},
			onSuccess: function(){
				parent.Smoothbox.close();
			}	
		}).send();
	}
	$$('.save_follow').addEvent('click', saving_follow)
	$$('.cancel_follow').addEvent('click', function(){
		parent.Smoothbox.close();	
	})
</script>


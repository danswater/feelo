<style type="text/css">
	#hash-tag-rapper{
		margin-top: 20px;
		margin-bottom: 20px;
	}
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

<div id="hash-tag-rapper">
	<div id="notification" style="text-align:center; font-size: 18px;"> </div>
	<br/>
	<center>
		<table cellpadding="0" cellspacing="0" border="0">
			<?php $ctr = 0; ?>
			<?php foreach ($this->tags as $tag) { ?>
			<?php if($ctr == 0){ ?> <tr> <?php } ?>
				<td align="center" style="padding: 10px;">
					<?php /* ?>
					<a href="<?php echo $this->url(array('controller' => 'tag', 'action'=>'hashtagpost', 'module' => 'whmedia', 'tid' => $tag["hashtag_id"]), 'default', true) ?>" style="width: 200px; padding:10px;" ttext="<?php echo $tag["text"]; ?>" tid="<?php echo $tag["hashtag_id"]; ?>" class=" follower_button_2 media-follow-btn"> 
						<?php */ ?>
					<a href="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>?query=<?php echo $tag["text"]; ?>&amp;type=tags" style="width: 200px; padding:10px;" ttext="<?php echo $tag["text"]; ?>" tid="<?php echo $tag["hashtag_id"]; ?>" class=" follower_button_2 media-follow-btn"> 
						<?php echo $tag["text"]; ?>
						<span class="checked check_<?php echo $tag["hashtag_id"]; ?>"></span>	
					</a>
				</td>
			<?php $ctr++; ?>
			<?php if($ctr > 2 || $ctr == count($this->tags)){ $ctr = 0; ?> </tr> <?php } ?>
			<?php } ?>	
		</table>
	</center>
</div>

<?php if(!isset($this->notUser)){ ?>
<script type="text/javascript">
	var cReq = "";

	$$('.following').addEvent('click', function(event){
		event.preventDefault()
		event.stop();

		if(cReq != "") alert("Please wait the previous request is not yet finished.");

		var telem = $(this);
		var tid = telem.get("tid");
		var data_filter = {};	
		var cb = function(){};
		var ok = "";
		if($(this).hasClass("unfollow")){
			ok = confirm("Are you sure you want to follow this tag [" + telem.get('ttext') + "] ?");
			data_filter["tag_follow_" + tid] = tid;
			$(this).removeClass('unfollow');
			$$(".check_"+tid).setStyle("display", "block");		
		}else{
			ok = confirm("Are you sure you want to unfollow this tag [" + telem.get('ttext') + "] ?");
			data_filter["tag_unfollow_" + tid] = tid;
			cb = function(){
				telem.addClass('unfollow');
				$$(".check_"+tid).setStyle("display", "none");	
			}
		}
		
		if(ok){

			cReq = new Request({
				url : "<?php echo $this->url(array('controller' => 'index', 'action'=>'following-hashtag', 'module' => 'whmedia'), 'default', true) ?>",
				data : data_filter,
				onRequest: function(){
					$("notification").set("html", "Processing.. Please wait")
				},
				onSuccess: function(){
					cb();
					cReq = "";
					$("notification").set("html", "Processing.. Done")
					setTimeout(function(){
						$("notification").set("html", "")
					}, 1000)
				}	
			}).send();
		}
	})

</script>
<?php } ?>
<style type="text/css">
	.hp-border{
		border-bottom: 1px solid #ccc;
		margin: 5px 0px 20px 0px;
	}
	.hp-border h2,
	.hp-border a{
		position: relative;
	}
	.hp-border a{
		display: block;
		margin-top: -30px;
		float: right;
		font-size: 16px;
		font-weight: bold;
	}
</style>

<div class="hp-border">
	<h2><?php echo $this->hashtag["text"]; ?></h2>
	<a href="javascript:void(0)" class="hpfollow"  ttext="<?php echo $this->hashtag["text"]; ?>" tid="<?php echo $this->hashtag["tag_id"]; ?>">Unfollow</a>
</div>

<script type="text/javascript">
	var cReq = "";

	$$('.hpfollow').addEvent('click', function(event){
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
			if(ok){
				telem.set("html", "Unfollow");
			}

			data_filter["tag_follow_" + tid] = tid;
			$(this).removeClass('unfollow');
			$$(".check_"+tid).setStyle("display", "block");		
		}else{
			ok = confirm("Are you sure you want to unfollow this tag [" + telem.get('ttext') + "] ?");
			if(ok){
				telem.set("html", "Follow");
			}
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
				},
				onSuccess: function(){
					cb();
					cReq = "";
				}	
			}).send();
		}
	})
</script>

<div>
	<?php echo $this->render('application/modules/Mediamasonry/widgets/featured-project-lazy/index.tpl') ?>
</div>



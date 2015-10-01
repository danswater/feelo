<?php echo $this->partial('etc/head.tpl', array('pageTitle' => $this->pageTitle)) ?>
<div id="infobar">
    <div id="add_circles_wrapper">
        <button name="add_circles" id="add_circles" onclick="Smoothbox.open('<?php echo ($this->baseurl()=='/'?'':$this->baseurl()); ?>/boxes/createfavbox/rel/yes')"><?php echo $this->translate("add favo box"); ?></button>
		<div style="float:right;">
			<?php echo $this->pagination; ?> 
		</div>
	</div>
</div>
<div class="clear"></div>
<div id="favcirclewrapper">
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
					<div>
						<a href="<?php echo $this->url(array('controller' => 'favboxes', 'action' => 'favprojectlist', 'favcircle_id' => $circle["favcircle_id"]), 'default', true) ?>"> View </a> 
						|
						<a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo ($this->baseurl()=='/'?'':$this->baseurl()); ?>/boxes/createfavbox/favid/<?php echo $circle["favcircle_id"]; ?>')"> Edit </a> 
						|
						<a href="javascript:void(0)" onclick="deleteFavCircle(<?php echo $circle["favcircle_id"]; ?>, '<?php echo $circle["category"]; ?>')"> Delete </a>
					</div>
				</div>
			</td>
			<?php if($ctr == 4 || $cntCtr == $total){ echo "</tr>"; $ctr = 0; }; ?>
		<?php $ctr++; $cntCtr++ ?>
		<?php endforeach; ?>
	</table>
</div>
<div style="float:right;">
	<?php echo $this->pagination; ?> 
</div>
<script type="text/javascript">
	function deleteFavCircle(id, category){
		if(confirm("Are you sure you want to delete this ["+category+"] category?")){
			window.location.href = '<?php echo ($this->baseurl()=='/'?'':$this->baseurl()); ?>/boxes/deletefavbox/fid/' + id;
		}
	}
	
</script>
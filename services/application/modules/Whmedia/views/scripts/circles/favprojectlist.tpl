<table width="100%" id="tablefavboxlist">
	<?php foreach( $this->paginator as $whmedia ):?>
		<tr>
			<td valign="center" width="<?php echo $this->thumb_width; ?>">
				<?php echo $this->htmlLink($whmedia->getHref(), 
			    $this->htmlImage($whmedia->getPhotoUrl($this->thumb_width, false, false), array('alt' => $this->translate('Project Thumb'))), 
			    array()); ?> 
			</td>
			<td valign="center">
				 <?php echo $whmedia->getTitle() ?>
			</td>
			<td valign="center" class="btndeleterow">
				<a href="javascript:void(0)" onclick="deleteRow(<?php echo $whmedia->getIdentity(); ?>)">Delete</a>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<script type="text/javascript">

	function deleteRow(pid){
		var href = "<?php echo $this->url(array('controller' => 'favboxes', 'action' => 'delfavproject', 'favcircle_id' => $this->favcircle_id), 'default', true) ?>";
		href += "/project_id/" + pid;

		if(confirm("Are you sure you want to delete this project in your fav circle?")){
			window.location.href = href;
		}
	}
	

</script>
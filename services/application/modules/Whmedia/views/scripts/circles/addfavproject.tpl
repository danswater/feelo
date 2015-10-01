<?php 
	$appendStr = "";
	if(isset($this->project_id)){
		$appendStr = "project_id/" . $this->project_id;
	}
?>

<form action="<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format' => 'smoothbox')); ?>" method="post">
	<div id="createfavbox">
		<?php if(isset($this->errorMsg)): ?>
			<div class="errorMsg"> <?php echo $this->errorMsg; ?> </div>
		<?php endif; ?>

		<div class="row">
			<center>
				<h4>Pick a Favo Box</h4>
			</center>

			<table width="100%" id="addfavbox">
				<tr>
					<td width="40%" align="center">add to favo box</td>
					<td width="60%" align="center">
						<select name="favcircle_id" style="width: 100%;">
							<option value=""></option>
							<?php foreach($this->favcircle as $circle): ?>
							<option value="<?php echo $circle->favcircle_id; ?>"><?php echo $circle->title; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40%" align="center">make favo box</td>
					<td width="60%" align="center">
						<input type="button" name="makefavo" class="makefavo" value="go" onclick="window.location.href='<?php echo ($this->baseurl()=='/'?'':$this->baseurl()); ?>/boxes/createfavbox/format/smoothbox/<?php echo $appendStr; ?>'"/>
					</td>
				</tr>
			</table>
		</div>
		<div class="row">
			<table width="100%">
				<tr>
					<td>
						<input type="submit" name="saving" class="save" value="save" />
					</td>
					<td>
						<input type="button" class="cancel" value="cancel" onclick="javascript:parent.Smoothbox.close()" />
					</td>
				</tr>
			</table>
		</div>
	</div>
</form>
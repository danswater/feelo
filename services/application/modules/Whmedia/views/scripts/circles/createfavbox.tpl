<form action="<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format' => 'smoothbox')); ?>" method="post">
	<div id="createfavbox">
		<?php if(isset($this->errorMsg)): ?>
			<div class="errorMsg"> <?php echo $this->errorMsg; ?> </div>
		<?php endif; ?>
		<div class="row">
			<input type="text" name="boxname" value="<?php echo isset($this->boxname) ? $this->boxname : "" ?>"/>
			<label>name</lable>
		</div>
		<div class="row">
			<table width="100%">
				<tr>
					<td width="90px" valign="middle">
						<div class="imgsrc" id="coverPhotoImgSrc" style="min-height:50px; width:80px;">
							<?php if(isset($this->photo)): ?>
								<img src="<?php echo $this->photo["cover"]; ?>" alt="" />
								<input type="hidden" name="file_id" value="<?php echo $this->photo["file_id"]; ?> "/>
							<?php endif; ?>
						</div>
						<label>cover photo</label>
					</td>
					<td valign="middle">
						<label>
							<a href="javascript:void(0)" onclick="show_cover_photo()">upload</a>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="row">
			<table width="100%">
				<tr>
					<td>
						<label>privacy</label>
					</td>
					<td>
						<input type="radio" name="privacy" value="0" <?php if(!isset($this->isprivate)|| $this->isprivate != 1 ){ echo "checked"; } ?> />
						<label>public</label>
					</td>
					<td>
						<input type="radio" name="privacy" value="1" <?php if(isset($this->isprivate) && $this->isprivate == 1){ echo "checked"; } ?>  />
						<label>private</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="row">
			<input type="text" name="category" value="<?php echo isset($this->category) ? $this->category : "" ?>"/>
			<label>category</lable>
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
<?php echo $this->form; ?>
<iframe id="submit_cover_photo_here" name="submit_cover_photo_here" style="width:1px; height:1px" ></iframe>
<script type="text/javascript">
	function show_cover_photo(){
		$("cover_photo").click();
	}
	function upload_cover_photo(){
		$("submit_cover_photo_here").addEvent("load", function(){
			var iframe = document.getElementById("submit_cover_photo_here");
			var iframe_contents = iframe.contentDocument.body.innerHTML
			var data = typeof iframe_contents == "string" ? JSON.parse(iframe_contents) : {};
			
			if(typeof data.cover != "undefined"){
				var html = '<input type="hidden" name="file_id" value="' + data.file_id + '"/>';
					html += '<img src="' + data.cover + '" alt="" />';
				$("coverPhotoImgSrc").innerHTML = html;
			}
		})
		$("submit_cover_photo").submit();
	}
</script>
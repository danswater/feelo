
<?php /* ?>
<div id="add_block_<?php echo $this->media_id; ?>" class="add_block">
    <div style="margin-top:-10px; text-align:center;">
        <a href="javascript:void(0);" class="add_media" onclick="javascript:wh_project.addVideo(<?php echo $this->media_id; ?>);"><?php echo $this->translate("Add Video") ?></a>
        <a href="javascript:void(0);" class="add_media" onclick="javascript:wh_project.addImage(<?php echo $this->media_id; ?>);"><?php echo $this->translate("Add Image") ?></a>
        <a href="javascript:void(0);" class="embed_media" onclick="javascript:wh_project.addVideoServices(<?php echo $this->media_id; ?>);"><?php echo $this->translate("Embed Media") ?></a>    
        <a href="javascript:void(0);" class="add_url" onclick="javascript:wh_project.addURL(<?php echo $this->media_id; ?>);"><?php echo $this->translate("Add URL") ?></a>    
    </div>    
</div>
<?php */ ?>


<style type="text/css">
	.add_block{background: #f1eeee; height: 188px; border-radius:5px; }
	.add_block ul{margin-top: 30px; }
	.add_block ul li{float: left; margin: 30px; }
	.add_block ul li a:hover{ text-decoration: none !important;}
	.add_block ul li span{font-weight: bold; color: #78c6d7; }
	.swiff-uploader-box{
		margin-top: 0px !important;
	}
</style>


<div id="add_block_<?php echo $this->media_id; ?>" class="add_block">
	<div style="margin-top:-10px; text-align:center;">
		<ul>
			<li>
				<a href="javascript:void(0);" onclick="javascript:wh_project.addImage(<?php echo $this->media_id; ?>);">
					<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Whmedia/externals/images/camera.png" />
					<br/>
					<span>photos</span>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" onclick="javascript:wh_project.addVideo(<?php echo $this->media_id; ?>);">
					<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Whmedia/externals/images/video.png" />
					<br/>
					<span>videos</span>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" onclick="javascript:wh_project.addVideoServices(<?php echo $this->media_id; ?>);">
					<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Whmedia/externals/images/link.png" />
					<br/>
					<span>media</span>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" onclick="javascript:wh_project.addURL(<?php echo $this->media_id; ?>);">
					<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Whmedia/externals/images/globe.png" />
					<br/>
					<span>url</span>
				</a>
			</li>
		</ul>
	</div>
</div>

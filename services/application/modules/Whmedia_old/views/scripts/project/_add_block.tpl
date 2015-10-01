<div id="add_block_<?php echo $this->media_id; ?>" class="add_block">
    <div style="margin-top:-10px; text-align:center;">
    	<a href="javascript:void(0);" class="add_media" onclick="javascript:wh_project.addMedia(<?php echo $this->media_id; ?>);"><?php echo $this->translate("Add Media")?></a>
        <a href="javascript:void(0);" class="add_text" onclick="javascript:wh_project.addTextBlock(<?php echo $this->media_id; ?>);"><?php echo $this->translate("Add Text")?></a>
        <a href="javascript:void(0);" class="embed_media" onclick="javascript:wh_project.addVideoServices(<?php echo $this->media_id; ?>);"><?php echo $this->translate("Embed Media")?></a>    
        <a href="javascript:void(0);" class="add_url" onclick="javascript:wh_project.addURL(<?php echo $this->media_id; ?>);"><?php echo $this->translate("Add URL")?></a>    
    </div>    
</div>
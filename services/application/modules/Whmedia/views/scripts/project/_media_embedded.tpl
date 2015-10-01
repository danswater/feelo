<div id="whmedia_<?php echo $this->media->media_id; ?>" class="media_div media_div_par" style="background:#EFEFEF;">

<div style="position:relative; min-height:150px">
    <div class="wh_edit_media_controls_old" style="display:none">
       <div class="wh_edit_media_control-block">
        <?php if (!in_array($this->media->getMediaType(), array('text', 'url')) ): ?>
            <a class="link_edit_lable" href="javascript:void(0);" onclick="javascript:wh_project.edit_media_title(<?php echo $this->media->media_id; ?>);"><?php echo $this->translate("Edit label")?></a>
        <?php endif; ?>
        <?php if ($this->media->is_text): ?>
            <a class="link_edit" href="javascript:void(0);" onclick="javascript:wh_project.editTextBlock(<?php echo $this->media->media_id; ?>);"><?php echo $this->translate("Edit")?></a>
        <?php endif; ?>    
        <?php if (!$this->media->encode and in_array($this->media->getMediaType(), array('image', 'video', 'pdf', 'youtube', 'vimeo', 'embed_ly'))):?>
            <a class="set_as_cover Tips" <?php if ($this->subject()->cover_file_id == $this->media->media_id):?>style="display: none;"<?php endif;?> href="javascript:void(0);" onclick="javascript:wh_project.set_cover(<?php echo $this->media->media_id ?>)" rel="<?php echo $this->translate('Set Post Cover')?>"><?php echo $this->translate("Set as cover")?></a>                              
        <?php endif;?>    
        <?php if ($this->media->getMediaType() == 'video' and $this->media->encode == 0 and $this->media->duration !== 0): ?>
            <?php echo $this->htmlLink(array('route' => 'whmedia_video_edit_cover', 'video_id' => $this->media->media_id), $this->translate("Change Thumbnail"), array('class' => 'smoothbox Tips editcover',
                                                                                                                                                                       'rel' => $this->translate("Edit Cover"))) ?>    
        <?php endif;?>    
        <?php if ($this->media->getMediaType() == 'image' and $this->filters):?>
            <?php echo $this->htmlLink(array('route' => 'whfilters_image_filters', 'media_id' => $this->media->media_id), $this->translate("Apply Filters"), array('class' => 'Tips apply-filters',
                                                                                                                                                                   'rel' => $this->translate("Process this image with graphic filters"))) ?>
        <?php endif;?>    
        <a class="link_delete" href="javascript:void(0);" onclick="javascript:wh_project.confirm_delete(<?php echo $this->media->media_id; ?>, <?php echo ($this->media->is_text) ? 'true' : 'false' ?>);"><?php echo $this->translate("Delete")?></a>
       </div>
    </div>    
    
    <div class="media_content <?php if ($this->subject()->cover_file_id == $this->media->media_id):?>project-cover<?php endif;?>">
        <center>
           <?php echo $this->media->Embedded(); ?>
        </center>   
        <span class="project-cover-caption Tips" title="<?php echo $this->translate("This is post cover.")?>" <?php if ($this->subject()->cover_file_id != $this->media->media_id):?>style="display: none;"<?php endif;?>></span>        
        
    </div>
    <?php if (!in_array($this->media->getMediaType(), array('text', 'url')) ): ?>
        <div class="div_title">
            <?php echo nl2br($this->media->getTitle()); ?>        
        </div>
    <?php endif; ?>
</div>

    <?php echo $this->form; ?>
</div>
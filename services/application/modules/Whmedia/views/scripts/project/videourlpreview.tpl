<p><?php echo $this->translate('Input URL:') ?> <a href="<?php echo $this->url; ?>" target="_blank"><?php echo $this->url; ?></a></p>
<?php if ($this->video_info['thumbnail']):?>
    <img alt="Media thumb" id="wh_video_thumb" osrc="<?php echo $this->video_info['thumbnail']?>" style="display: none;"/>
    <p alt="Thumb Loading" id="wh_thumb_loading" ></p>
<?php endif;?>
<br/>
<?php echo $this->translate('Title: ') ?> <input type="text" id="video_title" name="video_title" value="<?php echo $this->video_info['information']['title']?>" />
<div id="buttons_video">
    <button onclick='javascript:parent.wh_project.saveVideoServices("<?php echo $this->video_info['type']?>", "<?php echo $this->video_info['code']?>", <?php echo $this->block_id?>);'><?php echo $this->translate('Add media') ?></button>
     <?php echo $this->translate('or') ?>
    <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close();'><?php echo $this->translate('Cancel') ?></a>
</div>
<span id="saving_video" style="display: none;"><?php echo $this->translate('Please wait. Media is saving...') ?></span>

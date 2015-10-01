<h3><?php echo $this->translate('Link URL') ?></h3>

<label class="label-text"><?php echo $this->translate('You can send this URL to your friends.') ?></label>
<textarea readonly="readonly" onclick="javascript:this.select();">
<?php echo $this->media->getFullHref(); ?>
</textarea>
<div style="clear:both; height:25px;"></div>
<?php if (!in_array($this->media->getMediaType(), array('text', 'url')) ): ?>
    <h3><?php echo $this->translate('Embedded Code') ?></h3>

    <label class="label-text"><?php echo $this->translate('Copy and paste this code to show selected media on other web page.') ?></label>
    <textarea readonly="readonly" onclick="javascript:this.select();">
    <?php echo $this->media->getEmbeddedCode(); ?>
    </textarea>
<?php endif;?>
<div style="clear:both; height:10px;"></div>
<button onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Close");?></button>
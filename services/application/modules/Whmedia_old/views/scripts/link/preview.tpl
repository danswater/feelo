<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>
<?php if (!empty($this->title)):?>
    <h4><?php echo $this->htmlLink($this->url, $this->title)?></h4>
<?php endif;?>
<?php if (!empty($this->description)):?>
    <p><?php echo $this->description?></p>
<?php endif;?>
    <?php
        if (!empty ($this->images)):
        $images = '';
    ?>
        <?php foreach ($this->images as $value): ?>
            <?php $images .= "'{$value}',"; ?>
        <?php
            endforeach;
            $images = rtrim($images, ',');
        ?>
        <img id="loading_thumbs" alt="Loading" src="application/modules/Core/externals/images/loading.gif" />
        <div id="thumbs_preview" style="display: none;">
            <div id="manage_thumbs">
                <div id="thumbs_show">
                    
                </div>
                <a href='javascript:void(0);' onclick="javascript:wh_link.prev();" id="thumb_prev"><?php echo $this->translate('Prev') ?></a>
                <?php echo $this->translate('%1$s of %2$s', '<span id="current_images">1</span>', '<span id="count_images"></span>') ?>
                <a href='javascript:void(0);' onclick="javascript:wh_link.next();" id="thumb_next"><?php echo $this->translate('Next') ?></a>
            </div>
            <input type="checkbox" onclick="wh_link.isShow(this);" id="isShow_thumb"/> <label for="isShow_thumb"><?php echo $this->translate("Don't show an image") ?></label>
        </div>
    <?php endif;?>

<script type="text/javascript">
    window.addEvent('domready', function() {
                      wh_link = new URL_content ({
                                                    title: "<?php echo addslashes($this->title) ?>",  
                                                    url: "<?php echo addslashes($this->url) ?>",
                                                    <?php if (!empty ($this->images)): ?>
                                                        images:[<?php echo $images ?>],
                                                    <?php endif;?>      
                                                    description: "<?php echo addslashes(htmlspecialchars_decode($this->description))  ?>"
                                                  });
                                                 });
</script>
<div id="buttons_video">
    <button onclick='javascript:parent.wh_project.saveURL(wh_link.getData(), <?php echo $this->block_id?>)'><?php echo $this->translate('Save URL') ?></button>
     <?php echo $this->translate('or') ?>
    <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close();'><?php echo $this->translate('Cancel') ?></a>
</div>
<span id="saving_video" style="display: none;"><?php echo $this->translate('Please wait. URL is saving...') ?></span>
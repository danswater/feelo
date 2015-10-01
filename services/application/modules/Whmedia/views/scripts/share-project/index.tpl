<h2>
    <?php echo $this->translate("Share: %s", $this->project->toString(array('target' => '_parent')) ) ?>
</h2>
<?php echo $this->htmlLink($this->project->getHref(), 
                                       $this->htmlImage($this->project->getPhotoUrl(350, false, false), array('alt' => $this->translate('Project Thumb'))), 
                                       array('class' => 'media-browse-img',
                                             'target' => '_parent')); ?>
<?php echo $this->content()->renderWidget('whmedia.share-social') ?>
<a onclick="parent.Smoothbox.close();" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('Close'); ?></a>
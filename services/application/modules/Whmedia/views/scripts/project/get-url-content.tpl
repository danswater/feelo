<p><?php echo $this->translate('Input URL:') ?> <a href="<?php echo $this->url; ?>" target="_blank"><?php echo $this->url; ?></a></p>
<div>
  <div class="sharebox">
    <?php echo $this->action("preview", "link", "core", array("uri"=>$this->url)) ?>
  </div>
</div>
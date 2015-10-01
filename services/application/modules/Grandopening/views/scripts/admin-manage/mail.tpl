<h2><?php echo $this->translate("Grand Opening Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="tip">
    <?php echo $this->translate("Your message has been queued for sending.") ?>
</div>
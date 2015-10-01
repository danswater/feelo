<div class="headline">
  <h2>
    <?php echo $this->translate('Media');?>
    <?php if (isset($this->owner)): ?>
      >> <?php echo $this->translate('%1$s\'s Projects', $this->owner)?>
    <?php endif; ?>    
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

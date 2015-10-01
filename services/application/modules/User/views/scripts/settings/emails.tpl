<?php $addClass = ""; ?>
<?php if( Zend_Controller_Front::getInstance()->getRequest()->getParam('format') != 'smoothbox' ): ?>
<div class="headline">
  <h2>
    <?php echo $this->translate('My Settings');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<?php else: ?>
  <?php $addClass = "global_iframe_form"; ?>
<?php endif; ?>

<div class="<?php echo $addClass ?>">
  <?php echo $this->form->render($this) ?>
</div>

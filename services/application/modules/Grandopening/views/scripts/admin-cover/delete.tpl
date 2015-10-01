<?php if( !$this->status ): ?>
  <?php echo $this->form->render($this) ?>
<?php else: ?>
  <?php echo $this->translate('Deleted') ?>
  <script type="text/javascript">
    var fileindex = '<?php echo sprintf('%d', $this->fileIndex) ?>';
    setTimeout(function() {
      //parent.$('admin_file_' + fileindex).destroy();
      parent.Smoothbox.close();
      setTimeout(function() {
        parent.window.location.replace( parent.window.location.href );
      }, 250);
    }, 1000);
  </script>
<?php endif; ?>
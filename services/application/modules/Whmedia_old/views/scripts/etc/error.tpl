<div style="margin: 10px;">

  <h2><?php echo $this->translate('Whoops!') ?></h2>

  <?php echo $this->translate('An error has occurred.') ?>

  <?php if( isset($this->error) ): ?>
    <br />
    <br />
    <pre><?php echo $this->error; ?></pre>
  <?php endif; ?>

</div>
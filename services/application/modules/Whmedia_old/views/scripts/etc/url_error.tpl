<div style="margin: 10px;">

  <h2><?php echo $this->translate('URL check ERROR!') ?></h2>

  <?php echo $this->translate('During URL checking an error occurred.') ?>
  <?php if( isset($this->url) ): ?>
    <br />
    <br />
    <pre><?php echo $this->translate('Input URL:') ?> <?php echo $this->url; ?></pre>
  <?php endif; ?>
  <?php if( isset($this->error) ): ?>
    <br />
    <br />
    <pre><?php echo $this->error; ?></pre>
  <?php endif; ?>
  <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close();'>Close</a>
</div>
<?php if( @$this->messages ): ?>
<p><?php echo $this->messages ?></p>
 <?php else:?>
<form method="post" class="global_form_popup" <?php if (isset($this->action)) echo 'action="' . $this->action . '"'; else echo ''; ?>>
    <div>
      <h3><?php echo $this->translate($this->delete_title) ?></h3>
      <p>
         <?php echo $this->translate($this->delete_description) ?>
      </p>
      <br />
      <p>
        <?php if (isset($this->info)): ?>
            <input type="hidden" name="info" value='<?php echo $this->info ?>'/>
        <?php endif;?>
        <input type="hidden" name="confirm" value="1"/>
        <button type='submit'><?php if (isset($this->button)) echo $this->translate($this->button); else echo $this->translate('Delete'); ?></button>
        <?php echo $this->translate("or") ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
<?php endif; ?>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>

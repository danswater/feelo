<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>
	<div class="clcache">    
        <p><?php echo $this->translate("You can clean Media Plugin image cache. This operation will free disk space from obsolete thumbnails. Note, system will need to create new cache, so thumbnails may load slower for a first time.")?>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'whmedia', 'controller' => 'settings', 'action' => 'del-cache'), $this->translate('Clear Cache'), array('class' => 'smoothbox buttonlink')) ?></p>
     </div>    
  </div>

</div>

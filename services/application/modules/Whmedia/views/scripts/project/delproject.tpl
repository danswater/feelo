<?php $project = Engine_Api::_()->core()->getSubject() ?>
<div class="headline">
<h2>
  <?php echo $this->pageTitle ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php 
    $tmp = $this->navigation()->menu()->setContainer($this->navigation);
    echo $tmp->render() ?>
  </div>
<?php endif; ?>
</div>
<h4>
  "<?php echo $project->getTitle() ?>"
</h4>

    <?php echo $this->form->render($this) ?>

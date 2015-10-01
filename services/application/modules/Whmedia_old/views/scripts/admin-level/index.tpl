<?php
    $script = "function allow_create(allow) {
                    if (allow.get('value') == 1)
                        \$('project_element').setStyle('display', 'block');
                    else
                        \$('project_element').setStyle('display', 'none');
               }";
    $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
?>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php

      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>
<script type="text/javascript">
//<![CDATA[
function updateFields() {
  $$('input[name=wh_facebook_appid]').set('disabled', true);
  var new_value = $$('input[name=wh_facebook_type]:checked')[0].get('value');
  if ('0' == new_value)
    $$('input[name=wh_facebook_appid]')[0].set('disabled', false);
  else if ('1' == new_value)
    $$('input[name=wh_facebook_appid]').set('disabled', true);
}
window.addEvent('load', function(){
  updateFields();
});
//]]>
</script>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

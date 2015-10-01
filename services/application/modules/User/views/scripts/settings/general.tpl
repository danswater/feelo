<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: general.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
$addClass = "";
?>
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

<div class="global_form <?php echo $addClass ?> ">
  <?php if ($this->form->saveSuccessful): ?>
    <h3><?php echo $this->translate('Settings were successfully saved.');?></h3>
  <?php endif; ?>
  <?php echo $this->form->render($this) ?>
</div>

<?php if( Zend_Controller_Front::getInstance()->getRequest()->getParam('format') == 'html' ): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      var req = new Form.Request($$('.global_form')[0], $('global_content'), {
        requestOptions : {
          url : '<?php echo $this->url(array()) ?>'
        },
        extraData : {
          format : 'html'
        }
      });
    });
  </script>
<?php endif; ?>
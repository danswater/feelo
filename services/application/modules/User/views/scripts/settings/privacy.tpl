<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: privacy.tpl 9747 2012-07-26 02:08:08Z john $
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

<div class=" <?php echo $addClass ?> ">
  <?php echo $this->form->render($this) ?>
</div>

<div id="blockedUserList" style="display:none;">
  <ul>
    <?php foreach ($this->blockedUsers as $user): ?>
      <?php if($user instanceof User_Model_User && $user->getIdentity()) :?>
        <li>[
          <?php echo $this->htmlLink(array('controller' => 'block', 'action' => 'remove', 'user_id' => $user->getIdentity()), 'Unblock', array('class'=>'smoothbox')) ?>
          ] <?php echo $user->getTitle() ?></li>
      <?php endif;?>
    <?php endforeach; ?>
  </ul>
</div>

<script type="text/javascript">
<!--
window.addEvent('load', function(){
  $$('#blockedUserList ul')[0].inject($('blockList-element'));
});
// -->
</script>

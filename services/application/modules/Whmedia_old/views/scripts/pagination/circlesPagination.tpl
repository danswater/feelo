<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: pagination.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<?php if ($this->pageCount > 1): ?>
  <ul class="paginationControl">

    <?php if (isset($this->previous)): ?>
      <li><a href="javascript:void(0)" onclick="$('page').value = '<?php echo $this->previous;?>';friends_in_circle.searchMembers();"><?php echo $this->translate("&#171; Previous") ?></a></li>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
      <?php if ($page != $this->current): ?>
          <li><a href="javascript:void(0)" onclick="$('page').value = '<?php echo $page;?>';friends_in_circle.searchMembers();"><?php echo $page;?></a></li>
      <?php else: ?>
          <li class="selected"><a href="javascript:void(0)" ><?php echo $page; ?></a></li>
      <?php endif; ?>
    <?php endforeach; ?>

    <?php if (isset($this->next)): ?>
      <li><a href="javascript:void(0)" onclick="$('page').value = '<?php echo $this->next;?>';friends_in_circle.searchMembers();"><?php echo $this->translate("Next &#187;") ?></a></li>
    <?php endif; ?>

  </ul>
<?php endif; ?>
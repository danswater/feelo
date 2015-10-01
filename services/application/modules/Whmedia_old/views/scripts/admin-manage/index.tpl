<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
 <?php echo $this->translate('This page lists all of the media projects your users have created. You can use this page to monitor these entries and delete offensive material if necessary.');?>'

   </p>
<br/>

    <?php if( count($this->paginator) ): ?>

<table class='admin_table' style="float:left;">
<thead>

  <tr>
    <th class='admin_table_short'>ID</th>
    <th>Title</th>
    <th>Owner</th>
    <th>Views</th>
    <th>Media</th>
    <th>Date</th>
    <th>Options</th>
  </tr>

</thead>
<tbody>
        <?php foreach ($this->paginator as $item): ?>

          <tr>           
            <td><?php echo $item->getIdentity() ?></td>
            <td><?php echo $item->getTitle(); ?></td>
            <td><?php echo $item->getOwner()->toString() ?></td>
            <td><?php echo $item->project_views ?></td>
            <td><?php echo $item->count_media ?></td>
            <td><?php echo $item->creation_date ?></td>
            <td style="white-space:nowrap!important;">
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'whmedia', 'controller' => 'manage', 'action' => 'delete', 'id' => $item->getIdentity()), 'delete', array('class' => 'smoothbox')) ?>
                  |
                  <?php echo $this->htmlLink($item->getHref(), 'view', array('target' => '_blank')); ?>
            </td>
          </tr>

            <?php endforeach; ?>
</tbody>
</table>
<br/>

<div class='browse_nextlast'>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<?php else:?>
  There are no entries by your members yet.
<?php endif; ?>

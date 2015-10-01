<script type="text/javascript">
  var SortablesInstance;

  window.addEvent('load', function() {
    SortablesInstance = new Sortables('step_list', {
      constrain: false,
      handle: 'qcat_sortable',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

 var reorder = function(e) {
     var steps = e.parentNode.childNodes;
     var ordering = {};
     var i = 1;
     for (var step in steps)
     {
       var child_id = steps[step].id;
       if ((child_id != undefined) && (child_id.substr(0, 5) == 'step_'))
       {
         ordering[child_id] = i;
         i++;
       }
     }
    ordering['format'] = 'json';
    // Send request
    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
    var request = new Request.JSON({
      'url' : url,
      'data' : ordering,
      onSuccess : function(responseJSON) {
      }
    });

    request.send();
  }

  function ignoreDrag(event)
  {
    event.stopPropagation();
    return false;
  }
</script>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
      <h3><?php echo $this->translate('Media Entry Categories')?></h3>
      <p class="description">
        <?php echo $this->translate('If you want to allow your users to categorize their media, create the categories below.')?>
      </p>

      <table class='admin_table'>
        <thead>

          <tr>
            <th style="width: 17px;"></th>
            <th>Category Name</th>
            <th>URL</th>
            <th>Options</th>
          </tr>

        </thead>
        <tbody id="step_list">
          <?php foreach ($this->categories as $category): ?>

              <tr class='qcat_sortable' id='step_<?php echo $category->category_id;?>'>
                <td style="cursor: move;"><img alt="Sort Categories"  src="application/modules/Core/externals/images/admin/sortable.png"/></td>
                <td onmousedown="ignoreDrag(event);"><?php echo $category->category_name?></td>
                <td onmousedown="ignoreDrag(event);"><?php echo $category->url?></td>
                <td onmousedown="ignoreDrag(event);">
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'whmedia', 'controller' => 'settings', 'action' => 'edit-category', 'id' =>$category->category_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'whmedia', 'controller' => 'settings', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                </td>
              </tr>

          <?php endforeach; ?>
        </tbody>
      </table>
      <br/>
      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'whmedia', 'controller' => 'settings', 'action' => 'add-category'), $this->translate('Add New Category'), array(
      'class' => 'smoothbox buttonlink')) ?>
      </div>
    </form>
  </div>
</div>

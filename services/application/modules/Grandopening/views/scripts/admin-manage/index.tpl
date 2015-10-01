<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Grandopening/externals/scripts/go.js') ?>
<script type="text/javascript">
    function selectAll(checked_element) {
        $('collections_form').getElements('input[name="collection_id[]"]').set('checked', checked_element.checked);
    }
    window.addEvent('domready', function() {
        $('collections_form').addEvent('submit', function(event) {
            event.stop();
            Smoothbox.open('', {mode: "whGO_Confirm",
                                title: '<?php echo $this->translate("Confirm send e-mails."); ?>', 
                                description: '<?php echo $this->translate("Are you sure want to send e-mails?"); ?>', 
                                button_ok: '<?php echo $this->translate("Yes, do that."); ?>',
                                onDoAction: function() {                                                          
                                                        event.target.submit();
                                            }                                
                               });
            
            });
    });
</script>
<h2><?php echo $this->translate('Grand Opening Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<p>
    <?php echo $this->translate("This page lists all users who added their emails. You can use this page to monitor these emails and send announcements.") ?>
</p>

<?php if( !empty($this->message ) ): ?>
    <br/>
    <div class="tip">
      <span>
        <?php echo $this->translate($this->message); ?>
      </span>
    </div>
<?php endif; ?>
<br />
    <?php if( count($this->paginator) ): ?>
<p>
    <?php echo $this->translate("User requests (%d)", $this->paginator->getTotalItemCount()) ?>
</p><? echo $this->form ?>
<form id='collections_form' method="post" action="<?php echo $this->url(array('action'=>'mail'));?>" target="_parent">
    <table class='admin_table'>
    <thead>
      <tr>
        <th><input onclick="selectAll(this)" type='checkbox' class='checkbox'></th>  
        <th><?php echo $this->translate("Name") ?></th>
        <th><?php echo $this->translate("Email") ?></th>
        <th><?php echo $this->translate("Date") ?></th>
        <th><?php echo $this->translate("Emails Sent") ?></th>    
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
            <?php foreach ($this->paginator as $item): ?>

              <tr>         
                <td><input type='checkbox' class='checkbox' name="collection_id[]" value="<?php echo $item->collection_id ?>"></td>
                <td><?php echo trim($item->username); ?></td>
                <td><a href="mailto:<?php echo $item->email ?>"><?php echo $item->email ?></a></td>
                <td><?php echo $item->creation_date ?></td>
                <td><?php echo (empty($item->invites)) ? 0 : $item->invites ?></td>
                <td>
                      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'grandopening', 'controller' => 'manage', 'action' => 'delete', 'id' => $item->collection_id), 'delete', array('class' => 'smoothbox')) ?>
                </td>
              </tr>

                <?php endforeach; ?>
    </tbody>
    </table>

    <br/>
    <?php if( $this->paginator->count() >= 1): ?>
        <div>
          <?php echo $this->paginationControl($this->paginator); ?>
        </div>
    <?php endif; ?>
    <input type="hidden" name="task" value="send"/>
    <br/>
    <div class="go-quicklinks">
        <ul>          
            <li>
                <button type='submit'>
                    <?php if ($this->user_signup_inviteonly ): ?>
                        <?php echo $this->translate("Send Invites") ?>
                    <?php else: ?>  
                        <?php echo $this->translate("Send Emails") ?>
                    <?php endif;?>    
                </button>              
            </li>                         
            <li>
              <?php echo $this->htmlLink(array('action' => 'export', 'reset' => false), $this->translate('Export all to CSV'), array('class' => 'buttonlink go_table_export')) ?>
            </li>
        </ul>
    </div>
</form>
<br/>
<?php if ($this->user_signup_inviteonly ): ?>
    <p>
        <?php echo $this->translate("If you want to change 'Send Invites' template text go by the following %s", $this->htmlLink(array("route" => "admin_default", "controller" => "mail", "action" => "templates", "template" => $this->template_invite, 'reset' => true), $this->translate('link'))) ?>
    </p>
<?php else: ?>      
    <p>
        <?php echo $this->translate("Edit template for 'Send Emails' action %s", $this->htmlLink(array("route" => "admin_default", "controller" => "mail", "action" => "templates", "template" => $this->template_message, 'reset' => true), $this->translate('link'))) ?>
    </p>
<?php endif;?>

<?php else:?>
  <?php echo $this->translate("No emails were founded.") ?>
<?php endif; ?>

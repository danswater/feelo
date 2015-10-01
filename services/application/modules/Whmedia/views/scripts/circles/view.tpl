<div class="browsemembers_results">
    <h2>
        <?php echo $this->list->title; ?>
    </h2>
    <h3>
      <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
    </h3>

    <ul id="browsemembers_ul">
      <?php foreach( $this->users as $user ): ?>
        <li>
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
          <?php if( $this->viewer()->getIdentity() ): ?>
            <div class='browsemembers_results_links'>
              <p><?php echo $this->htmlLink($this->url(array('action' => 'remove', 'user_id' => $user->getIdentity(), 'box_id' => $this->list->getIdentity()), 'whmedia_circles_action', true), 'Remove from Box', array('class' => 'buttonlink smoothbox ')); ?></p>
            </div>
          <?php endif; ?>

            <div class='browsemembers_results_info'>
              <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
              <?php echo $user->status; ?>
              <?php if( $user->status != "" ): ?>
                <div>
                  <?php echo $this->timestamp($user->status_date) ?>
                </div>
              <?php endif; ?>
            </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php if( $this->users ): ?>
      <div class='browsemembers_viewmore' id="browsemembers_viewmore">
        <?php echo $this->paginationControl($this->users); ?>
      </div>
    <?php endif; ?>

    <script type="text/javascript">
      page = '<?php echo sprintf('%d', $this->page) ?>';
      totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
      userCount = '<?php echo sprintf('%d', $this->userCount) ?>';
    </script>
</div>
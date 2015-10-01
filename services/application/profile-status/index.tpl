<div id='profile_status'>
  <h2>
    <?php echo $this->subject()->getTitle() ?>
	<br>
  </h2>
  <?php if( $this->auth && isset($this->aboutMe)): ?>
    <span class="profile_status_text" id="user_profile_status_container">
        <?php echo $this->aboutMe ?>
    </span>
  <?php endif; ?>
</div>


<?php if( !$this->auth ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('This profile is private - only friends of this member may view it.');?>
    </span>
  </div>
  <br />
<?php endif; ?>
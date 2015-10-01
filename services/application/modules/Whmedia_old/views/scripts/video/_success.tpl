<div>
    <script type="text/javascript">
      en4.core.runonce.add(function() {
          parent.wh_project.updateMediaBlock(<?php echo $this->video->media_id ?>);
      });  
      setTimeout(function()
      {
        parent.Smoothbox.close();
      }, 1000);
    </script>

    <div class="global_form_popup_message">
      <?php echo $this->translate("Video cover saved."); ?>
    </div>

</div>
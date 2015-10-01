<?php   
    $this->headScript()->appendFile($this->baseUrl() . '/externals/tinymce/tiny_mce.js'); 
    $this->headScript()
         ->appendFile($this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.js')
         ->appendFile($this->baseUrl() . '/externals/fancyupload/Fx.ProgressBar.js')
         ->appendFile($this->baseUrl() . '/externals/fancyupload/FancyUpload2.js');
    $this->headLink()
         ->appendStylesheet($this->baseUrl() . '/externals/fancyupload/fancyupload.css');
    $this->headTranslate(array(
                                'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
                                'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
                                'Remove', 'Click to remove this entry.', 'Upload failed',
                                '{name} already added.',
                                '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
                                '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
                                '{name} file can not be uploaded. It is over the limit.',
                                '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
                                'Server returned HTTP-Status <code>#{code}</code>',
                                'Security error occurred ({text})',
                                'Error caused a send or load operation to fail ({text})',
                                "WHMEDIA_VIEWS_SCRIPTS_FANCYUPLOAD_DESCRIPTION",
                                "WHMEDIA_VIEWS_SCRIPTS_FANCYUPLOAD_TYPES",
                                "Cancel",
                                "To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).",
                                "To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).",
                                "A required file was not found, please be patient and we'll fix this.",
                                "To enable the embedded uploader, install the latest Adobe Flash plugin."
                            ));
?>
<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>

<?php $project = Engine_Api::_()->core()->getSubject();
      $this->headTranslate(array("Save", 
                                 "Cancel", 
                                 "Saving...", 
                                 "Delete?", 
                                 "Delete",
                                 "Are you sure that you want to delete it? It will not be recoverable after being deleted.",
                                 "Media deleted.",
                                 "You can add video link from next resources:",
                                 "Get Video",
                                 "Publish",
                                 "Unpublish",
                                 "Video cover saved.",
                                 "Add a link to the website:",
                                 "Get Content"));  ?>
<?php
    $foreach = '';
    foreach($this->file_types_array as $file_type) {
        $foreach .= '<li style="list-style:disc;">' . $this->translate($file_type) . '</li>';
    }
    $script = <<<EOF
<fieldset id="demo-fallback"><p>{$this->translate("WHMEDIA_VIEWS_SCRIPTS_FANCYUPLOAD_DESCRIPTION")}</p></fieldset><div id="demo-status" class="hide"><div>{$this->translate("WHMEDIA_VIEWS_SCRIPTS_FANCYUPLOAD_TYPES")}<br/><ul>{$foreach}</ul></div><div><a class="buttonlink icon_whmedia_new_upload" href="javascript:void(0);" id="demo-browse">{$this->translate('Select Media')}</a></div><div class="demo-status-overall" id="demo-status-overall" style="display: none"><div class="overall-title"></div><img src="{$this->baseUrl()}/externals/fancyupload/assets/progress-bar/bar.gif" class="progress overall-progress" /></div><div class="demo-status-current" id="demo-status-current" style="display: none"><div class="current-title"></div><img src="{$this->baseUrl()}/externals/fancyupload/assets/progress-bar/bar.gif" class="progress current-progress" /></div><div class="current-text"></div></div><ul id="demo-list"></ul>
EOF;

?>
<script type="text/javascript">
    en4.core.runonce.add(function()
    {
      new Tips($$('.Tips'));
      try {
          
            var UploaderTemplate = Elements.from('<?php echo addslashes(str_replace('\r\n', '', $script)) ?>');
            wh_project = new whmedia.edit_layout({
                                          project_id: <?php echo $project->project_id?>,
                                          max_files: <?php echo $this->max_files ?>,
                                          count_files: <?php echo $this->medias_count ?>,
                                          module:'<?php echo WHMEDIA_URL_WORLD ?>',
                                          uploaderTemplate: UploaderTemplate,                                          
                                          fileSizeMax: <?php echo $this->fileSizeMax ?>,
                                          typeFilter: <?php echo $this->file_types ?>,
                                          language: '<?php echo $this->language ?>',
                                          is_published: <?php echo $project->is_published ?>
                                      });
      }
      catch(e) {
            alert(e);
      }
    });

  
</script>
<style>
	.media_div_par{width: <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('image_width', '600')+90?>px;}
</style>
<div class="generic_layout_container layout_middle">
  <div class="headline">
  
  <div class="manage_proj_btns">
    <button id="button_publish" onclick="javascript:wh_project.publishToggle()"><?php echo $this->translate(($this->project->is_published) ? 'Unpublish' : "Publish"); ?></button>
    <span><?php echo $this->translate('or'); ?></span>
    <?php echo $this->htmlLink($this->project, $this->translate('Preview'), array('target' => '_blank','class' => 'preview_link')); ?>
  </div> 

  <?php if( count($this->navigation) ): ?>

  <div class='tabs manage_proj_nav'>
      <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
  <?php endif; ?>
  </div>
  

  <div id="media_container">
      <div id="whmedia_0" class="first_media_div media_div_par active-buttons">
          <?php echo $this->partial('project/_add_block.tpl', array('media_id' => 0)) ?>
      </div>
      <?php foreach ($this->medias as $this->media): ?>                
          <?php echo $this->partial('project/_media_embedded.tpl', array('media' => $this->media,
                                                                         'filters' => $this->filters)) ?>        
      <?php endforeach; ?>
  </div>

  <div class="media_details">

      <div class="media_info">
          <h3><?php echo $this->project->getTitle()?></h3>
          <p>
              <?php
              $tmp_categories = $this->categories->getRowMatching('category_id', $this->project->category_id);
              if (is_object($tmp_categories)):
              ?>
              <?php echo $this->translate("Category: "); ?>
              <?php echo $tmp_categories->category_name ?>
              <?php endif; ?>
          </p>
          <div>
              <p><?php echo $this->translate("Created:") . ' ' . $this->timestamp($this->project->creation_date) ?></p>
              <p><?php echo $this->translate("Views: %d", $this->project->project_views) ?></p>
              <p><?php echo $this->translate("Comments: %s", $this->project->comments()->getCommentCount()) ?></p>
              <p><?php echo $this->translate("Likes: %d", $this->project->likes()->getLikeCount()) ?></p>
          </div>
      </div>

      <?php
      $tags = $this->project->gettags();
      if ($tags !== null and count($tags)):
      ?>
      <div class="whmedia_browse_info_tag">
          <div class="whmedia_browse_info_tag_title"><?php echo $this->translate('Tags: '); ?></div>
          <?php foreach ($tags as $tag): ?>
          <a href='javascript:void(0);'><span class="fp_tag">&bull;</span><?php echo $tag->getTag()->text ?></a>
          <?php endforeach; ?>
      </div>
      <?php endif; ?>

  </div>


  <div class="media_stats" id="project_media_stats">
    <div class="media_stats_wrapper">       
          <?php if ($this->max_files > 0): ?>
              <p><?php echo $this->translate("Max files:") . ' ' . $this->max_files ?></p>
          <?php endif;?>
          <p><?php echo $this->translate("Media added:")?> <span id="medias_count"><?php echo $this->medias_count ?></span></p>
          <?php if ($this->max_files > 0): ?>
              <p><?php echo $this->translate("You can add:") ?> <span id="max_files"><?php echo (($this->max_files) - ($this->medias_count)) ?></span></p>
          <?php endif;?>
          <div class="tip" id="not_more_add" style="display: none;">
            <span>
              <?php echo $this->translate("You can't upload more media files."); ?>        
            </span>
        </div>           
      </div>    
  </div>

</div>
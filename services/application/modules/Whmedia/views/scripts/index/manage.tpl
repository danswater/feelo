<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>
<?php
    if ($this->isMobile) {
        $script = <<<EOF
                en4.core.runonce.add(function() {                                                    
                    $$("div.m_proj_settings_mobile").addEvent('click', function(i) {
                        i.target.toggleClass('active');
                    });
                });
EOF;
    }
    $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
?>

  <?php if( $this->paginator->getTotalItemCount() > 0 ):
          $thumb_block_height = Whmedia_Model_Media::getThumbDimension('height') + 70;?>
    <ul class="media_browse">
    <?php foreach( $this->paginator as $whmedia ): ?>
      <li style="height:<?php echo $thumb_block_height ?>px">
        <?php if (!$whmedia->is_published): ?>
            <div class="media-proj-draft"><?php echo $this->translate('Draft')?></div>
        <?php endif; ?>  
      	<div class="pulldown <?php echo ($this->isMobile) ? 'm_proj_settings_mobile' : 'm_proj_settings' ?>">
        	<div class="pulldown_contents_wrapper">
            	<div class="pulldown_contents">
                	<ul>
                    	<li><?php if (!$this->isApple)
                    echo $this->htmlLink(array('route' => 'whmedia_project', 'project_id' => $whmedia->getIdentity()), $this->translate('Manage Project'), array('class' => 'manage' ))?>
                    	</li>
                        <li>
                            <?php echo $this->htmlLink(array('route' => 'whmedia_project', 'action' => 'delproject', 'project_id' => $whmedia->getIdentity()), $this->translate("Delete Project"), array('class' => 'smoothbox buttonlink icon_delete_media')) ?>
                        </li>
                    </ul>
                </div>
             </div>
          </div>
          <div class="media_thumb_wrapper"><?php echo $this->htmlLink($whmedia->getHref(), "<img alt='Project Thumb' src=\"{$whmedia->getPhotoUrl()}\" />"); ?></div>
          
          <div class="media_browse_title">
              <?php echo $this->htmlLink($whmedia->getHref(), $this->whtruncate($whmedia->getTitle())); ?>
          </div>
         
		 <span class="media_files">
              <?php $MediasCount = $whmedia->count_media;
                    echo $this->translate(array("%d file", "%d files",$MediasCount), $MediasCount);
              ?>
          </span> |
          <span class="media_views"><?php echo $this->translate('Views: ') . $whmedia->project_views ?></span> |
          <span class="media_likes"><?php echo $whmedia->likes()->getLikeCount(); ?></span>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php if( $this->paginator->count() >= 1): ?>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, 'pagination/whmediapagination.tpl'); ?>
    </div>
  <?php endif; ?>

<?php elseif( $this->category || $this->show == 2 || $this->search  ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Projects were not posted in this criterias yet.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have not created projects yet.'); ?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('You can %1$spost%2$s a new one!', '<a href="'.$this->url(array('controller' => 'project', 'action' => 'create'), 'whmedia_default', true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
<?php if ($this->only_items === false): ?>
    <?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js')
                             ->appendFile($this->baseUrl() . '/application/modules/Mediamasonry/externals/scripts/mooMasonry.js')?>
    <?php
        $script = <<<EOF
                window.addEvent('load', function(){
                    $('media-browse_{$this->identity}').addEvent('masoned', function (){\$('media-browse_{$this->identity}').setStyle('opacity', 1);}).masonry({
                            singleMode: true,
                            itemSelector: '.media-browse-box'
                    });

                });
                var max_projects_page_{$this->identity} = {$this->paginator->count()};
                var current_projects_page_{$this->identity} = 1;
EOF;
        $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
    ?>
    <div id="media-browse_<?php echo $this->identity ?>" style="opacity:0;">
<?php endif;?>
    <?php foreach( $this->paginator as $whmedia ):?>
        <div class="media-browse-box" style="width:<?php echo $this->thumb_width ?>px;">
            <?php echo $this->htmlLink($this->url(array('widget_id' => $this->identity, 'id' => $whmedia->getIdentity()), 'mediamasonry_show', true),
                                       $this->htmlImage($whmedia->getPhotoUrl($this->thumb_width, false, false), array('alt' => $this->translate('Project Thumb'))),
                                       array('class' => 'media-browse-img smoothbox')); ?>
            <?php echo $this->htmlLink($whmedia->getHref(), $this->whtruncate($whmedia->getTitle(), $this->thumb_width), array('class' => 'media-browse-title')); ?>
            <p class="media-author">
                <?php echo $this->translate('by %s', $whmedia->getOwner()->toString()) ?>
            </p>
            <div class="media-info">
                <span class="media-files">
                    <?php $MediasCount = $whmedia->count_media;
                          echo $this->translate(array("%d file", "%d files",$MediasCount), $MediasCount);
                    ?>
                </span>
                |
                <span class="media-views">
                    <?php echo $this->translate('whViews: %d', $whmedia->project_views)?>
                </span>
                |
                <span class="media-likes"><?php echo $whmedia->likes()->getLikeCount(); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
<?php if ($this->only_items === false): ?>
    </div>
    <div style="clear: both;" ></div>
    <?php if( $this->paginator->count() > 1): ?>
        <div class="projects_viewmore project_loadmore" id="projects_viewmore_<?php echo $this->identity ?>">
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('show more'), array(
                                                                                                    'id' => 'projects_viewmore_link',
                                                                                                    'class' => 'buttonlink',
                                                                                                    'onclick' => "javascript:projects_viewmore({$this->identity});"
                                                                                                    )) ?>
        </div>

        <div class="projects_viewmore" id="projects_loading_<?php echo $this->identity ?>" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='vertical-align: middle; margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
    <?php endif; ?>

<?php endif; ?>
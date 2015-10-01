<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>
<?php
    $tmp_url_world = WHMEDIA_URL_WORLD;
    $script = <<<EOF
            window.addEvent('load', function() {
                parent.Smoothbox.instance.showWindow();
                wh_media = new whmedia.project.media({project_id:{$this->project->getIdentity()},
                                                      module:'{$tmp_url_world}',
                                                      lang : {loading: '{$this->translate('Loading...')}'}
                                                     });

            });
EOF;
    $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
?>
<?php
    if ($this->hot_keys_enable) {
        $script = <<<EOF
                window.addEvent('load', function() {
                    new parent.wh_leafing({prev: $('prev_media'),
                                           next: $('next_media')
                                           });

                });
EOF;
        $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
    }
?>
<div class="media_file_content">

    <?php echo ($this->media !== null) ? $this->media->Embedded() : $this->media->getPhotoUrl(200, false, false) ; ?>
    <div class="media_desc">
        <?php /* if (Engine_Api::_()->authorization()->context->isAllowed($this->media->getProject(), 'everyone', 'allow_d_orig') and $this->media->issetOriginal() ): ?>
            <div class="wh_media_download_button">
                <?php echo $this->htmlLink(array('route' => 'whmedia_default', 'controller' => 'share', 'action' => 'download', 'media_id' =>$this->media->getIdentity()), '', array('class' => 'buttonlink Tips wh_media_download_btn', 'rel' => $this->translate("Download original file"))) ?>
            </div>
        <?php endif ?>
        <?php  if ($this->media->getMediaType() != 'text'): ?>
             <div id="media_like_<?php echo $this->media->getIdentity() ?>" class="likeunlike">
                <?php echo $this->render('likes/_like.tpl') ?>
             </div>
        <?php endif */ ?>
        <p>
            <?php echo $this->translate("Project: %s", $this->htmlLink($this->project->getHref(), $this->project->getTitle(), array('target' => '_blank',
                                                                                                                                     'rel' => $this->translate("Go to this project")))) ?>
        </p>
        <p>
            <?php echo $this->translate("Author: %s", $this->project->getOwner()->toString(array('target' => '_blank'))) ?>
        </p>
        <?php
            if ($this->media instanceof Whmedia_Model_Media and !in_array($this->media->getMediaType(), array('text', 'url'))):
                $title = $this->media->getTitle();
                if (trim($title)):
            ?>
                    <p><?php echo nl2br($title); ?></p>
            <?php   endif;
                endif;?>
    </div>
    <?php if ($this->previous !== false or $this->next !== false): ?>
        <div>
            <?php if ($this->previous !== false): ?>
                <div class="btn-holder prev-media">
                    <?php echo $this->htmlLink($this->previous, '', array('onclick' => 'javascript:open_smooth(this.href);',
                                                                                             'id' => 'prev_media')); ?>
                </div>
            <?php endif;?>
            <?php   if ($this->next !== false): ?>
                <div class="btn-holder next-media">
                    <?php echo $this->htmlLink($this->next, '', array('onclick' => 'javascript:open_smooth(this.href);',
                                                                                        'id' => 'next_media')); ?>
                </div>
            <?php endif;?>
        </div>
    <?php endif;?>    
</div>
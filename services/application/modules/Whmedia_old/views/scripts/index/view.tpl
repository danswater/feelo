<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>
<?php
$tmp_url_world = WHMEDIA_URL_WORLD;
$script = <<<EOF
            en4.core.runonce.add(function() {
                wh_project_follow = new whmedia.project_follow();   
                new Tips($$('.Tips'));
                wh_search_project = new whmedia.search();
                wh_media = new whmedia.project.media({project_id:{$this->project->getIdentity()},
                                                      module:'{$tmp_url_world}',
                                                      lang : {loading: '{$this->translate('Loading...')}'}
                                                     });
                                                     
            });
EOF;
if ($this->isMobile) {
    $script .= <<<EOF
                en4.core.runonce.add(function() {                                                    
                    $$("div.m_proj_settings_mobile").addEvent('click', function(i) {
                        i.target.toggleClass('active');
                    });
                });
EOF;
}
$this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
?>

<h3>
    <?php echo $this->project->getTitle() ?>
</h3>
<?php if ($this->isOwner or Engine_Api::_()->whmedia()->isAdmin($this->viewer())): ?>
    <div class="pulldown <?php echo ($this->isMobile) ? 'm_proj_settings_mobile' : 'm_proj_settings' ?>">
        <div class="pulldown_contents_wrapper">
            <div class="pulldown_contents">
                <ul>
                    <?php if ($this->isOwner): ?>
                        <li>
                            <?php echo $this->htmlLink(array('route' => 'whmedia_project', 'action' => 'index', 'project_id' => $this->project->getIdentity(), 'reset' => true), $this->translate("Manage Project"), array('class' => 'icon_manage_media')) ?>
                        </li>
                        <li>
                            <?php echo $this->htmlLink(array('route' => 'whmedia_project', 'action' => 'edit', 'project_id' => $this->project->getIdentity(), 'reset' => true), $this->translate("Edit Details"), array('class' => 'editcover')) ?>
                        </li>
                        <li>
                            <?php echo $this->htmlLink(array('route' => 'add_whmedia', 'project_id' => $this->project->getIdentity(), 'reset' => true), $this->translate("Add Media"), array('class' => 'icon_whmedia_new')) ?>
                        </li>
                        <li>
                            <?php echo $this->htmlLink(array('route' => 'whmedia_project', 'action' => 'delproject', 'project_id' => $this->project->getIdentity(), 'forward' => 'index'), $this->translate("Delete Project"), array('class' => 'smoothbox icon_delete_media')) ?>
                        </li>
                    <?php else: ?>
                        <li>
                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'whmedia', 'controller' => 'manage', 'action' => 'delete', 'id' => $this->project->getIdentity(), 'forward' => 'index'), $this->translate("Delete Project"), array('class' => 'smoothbox icon_delete_media')) ?>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>


<div class="media_details">

    <div class="media_owner_details">
        <div class="media_owner_photo"><?php echo $this->htmlLink($this->project->getOwner()->getHref(), $this->itemPhoto($this->project->getOwner(), 'thumb.icon')) ?></div>

        <div class="media_owner_name"><?php echo $this->project->getOwner()->toString() ?>                	
            <div class="media_owner_status"><?php echo $this->viewMore($this->project->getOwner()->status, 85) ?></div>
        </div>
        <ul class="media_comments_info">
            <li class="media-proj-views Tips" title="<?php echo $this->translate('Views'); ?>"><?php echo $this->project->project_views ?></li>
            <li class="media-proj-likes Tips" title="<?php echo $this->translate('Likes'); ?>"><?php echo $this->project->likes()->getLikeCount() ?></li>
            <li class="media-proj-comments Tips" title="<?php echo $this->translate('Comments'); ?>"><?php echo $this->project->comments()->getCommentCount() ?></li>
            <li class="media-proj-time Tips" title="<?php echo $this->translate('Created'); ?>"><?php echo $this->timestamp($this->project->creation_date) ?></li>
        </ul>
        <div class="follow-buttons">
            <?php if (!$this->project->getOwner()->isOwner($this->viewer())): ?>
                <?php if ($this->viewer()->getIdentity()): ?>
                    <a href="javascript:void(0);" onclick="javascript:wh_project_follow.togglefollow(<?php echo $this->project->getOwner()->getIdentity() ?>)" class="follower_button_<?php echo $this->project->getOwner()->getIdentity() ?> media-follow-btn <?php if (($isFollow = Engine_Api::_()->getDbtable('follow', 'whmedia')->isFollow($this->project->getOwner(), $this->viewer()))): ?>unfollow<?php endif; ?>"><?php echo $this->translate(($isFollow) ? 'unFollow' : 'Follow') ?></a>
                <?php else: ?>    
                    <?php echo $this->htmlLink(array('route' => 'whmedia_user_login'), $this->translate('Follow'), array('class' => 'media-follow-btn smoothbox')) ?>
                <?php endif; ?>
            <?php endif; ?> 
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'whmedia', 'controller' => 'share-project', 'project_id' => $this->project->getIdentity(), 'reset' => true), '&nbsp;', array('class' => 'media-repost-btn media-follow-btn smoothbox')); ?>
        </div>

    </div>	
    <div style="clear:both;"></div>
</div>
<?php $medias = $this->medias; ?>
<?php if (($medias_count = $medias->count())) : ?>
    <?php foreach ($medias as $this->media): ?>

        <div class="mediaslides" style="width: <?php echo (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('image_width', '600') + 90 ?>px;">

            <div class="media_file" id="whmedia_<?php echo $this->media->media_id; ?>">
                <?php if (!in_array($this->media->getMediaType(), array('text', 'url'))): ?>
                    <p class="media_file_title"><?php echo nl2br($this->media->getTitle()); ?></p>
                <?php endif ?>
                <div style="width: <?php echo $this->img_width ?>px;">
                    <?php
                    if ($this->media->getMediaType() == 'image')
                        echo $this->htmlLink(array('route' => 'whmedia_default', 'action' => 'show-media', 'media' => $this->media->getIdentity()), $this->media->Embedded(), array('class' => 'smoothbox'));
                    else
                        echo $this->media->Embedded();
                    ?>
                    <div class="media_desc" style="width: <?php echo (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('image_width', '600') - 90 ?>px;">    
                        <?php if ($this->allow_d_orig and $this->media->issetOriginal()): ?>
                            <div class="wh_media_download_button">
                                <?php echo $this->htmlLink(array('route' => 'whmedia_default', 'controller' => 'share', 'action' => 'download', 'media_id' => $this->media->getIdentity()), $this->translate(""), array('class' => 'buttonlink Tips wh_media_download_btn', 'rel' => $this->translate("Download original file"))) ?>
                            </div>    
                        <?php endif ?>    

                        <?php if ($this->isOwner and !in_array($this->media->getMediaType(), array('youtube', 'vimeo')) and $this->media->getMediaType() != 'text'): ?>
                            <div class="wh_media_share_button">
                                <?php
                                echo $this->htmlLink(array('route' => 'whmedia_default', 'controller' => 'share', 'action' => 'get-code', 'media_id' => $this->media->getIdentity()), $this->translate("whmedia_Share"), array('class' => 'buttonlink smoothbox Tips',
                                    'rel' => $this->translate("whmedia_Share")))
                                ?>
                            </div>
                        <?php endif ?>
                            <?php if ($this->media->getMediaType() != 'text'): /*?>
                            <div id="media_like_<?php echo $this->media->getIdentity() ?>" class="likeunlike">
                            <?php echo $this->render('likes/_like.tpl') ?>
                            </div> <?php */?>
        <?php endif ?>

                    </div>
                </div>

            </div>
        </div>
    <?php endforeach; ?>

    <div class="media_info">
        <p>
            <?php
            $tmp_categories = $this->categories->getRowMatching('category_id', $this->project->category_id);
            if (is_object($tmp_categories)):
                ?>
                <?php echo $this->translate("Category: "); ?>
                <?php echo $this->htmlLink(array('route' => 'whmedia_category', 'category' => $tmp_categories->url), $tmp_categories->category_name) ?>                       
        <?php endif; ?>
        </p>	
        <?php
        $tags = $this->project->gettags();
        if ($tags !== null and count($tags)):
            ?>
            <div class="whmedia_browse_info_tag">
                <div class="whmedia_browse_info_tag_title"><?php echo $this->translate('Tags: '); ?></div>
                <?php foreach ($tags as $tag): ?>
                    <a href='javascript:void(0);' onclick='javascript:wh_search_project.tagAction("<?php echo $tag->getTag()->text; ?>");'><span class="fp_tag">&bull;</span><?php echo $tag->getTag()->text ?></a>
            <?php endforeach; ?>
            </div>
    <?php endif; ?>
    </div>
    <?php if ($this->viewer()->getIdentity()): ?>
        <div class="abuse-report">
            <?php echo $this->htmlLink($this->url(array('controller' => 'report', 'action' => 'create', 'subject' => $this->subject()->getGuid()), 'default', true), $this->translate('Report of Abuse'), array('class' => 'smoothbox')) ?>
        </div>
    <?php endif ?>

<?php else : ?>


    <div class="tip">
        <span>
    <?php echo $this->translate('No media was found.'); ?>
        </span>
    </div>
<?php endif; ?>	
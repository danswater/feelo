<?php if ($this->only_items === false): ?>
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
        <?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js')
                                 ->appendFile($this->baseUrl() . '/application/modules/Mediamasonry/externals/scripts/mooMasonry.js')?>
        <?php
            $script = <<<EOF
                    window.addEvent('load', function(){
                        $('media-browse').addEvent('masoned', function (){
                                                                            \$('media-browse').setStyle('opacity', 1);
                                                                            \$('big-loader').setStyle('display', 'none');
                                                                            }).masonry({
                                                                                        singleMode: true,
                                                                                        itemSelector: '.media-browse-box'
                                                                            });
                         wh_project_likes = new whmedia.project_likes();         
                         wh_project_follow = new whmedia.project_follow();         
                         new ToTopScroller($('media-browse'),$('media-scroll2top'));   

                    });
                    var max_projects_page = {$this->paginator->count()};
                    var current_projects_page = 1;
EOF;
            $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
        ?>
        <?php $this->headTranslate(array("whLikes: %d", "unFollow"));  ?>
        <?php if( $this->paginator->count() > 1): ?>
            <script type="text/javascript">
            //<![CDATA[
                  var scroller_count = 0;
                  var ScrollLoaderVar = null;
                  window.addEvent('domready', function() {
                    ScrollLoaderVar = new ScrollLoader({           
                        onScroll: function(){
                            projects_viewmore(null);            
                        }
                    });
                  });
            //]]>
            </script>
        <?php endif; ?>
    <?php endif; ?>
            
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
        <div id="big-loader" class="big-loader">
            <img src="<?php echo $this->baseUrl() ?>/application/modules/Whmedia/externals/images/big-loader.gif" alt="<?php echo $this->translate('loader') ?>" />
        </div>
    <?php endif; ?>
    <div id="media-browse" <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>style="opacity:0;"<?php endif; ?>>
<?php endif;?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <?php foreach( $this->paginator as $whmedia ):?>
        <div class="media-browse-box" style="width:<?php echo $this->thumb_width ?>px;" id="project_<?php echo $whmedia->getIdentity() ?>">
            <div class="media-proj-img">
                <?php if ($this->viewer()->getIdentity()): ?>
                    <a href="javascript:void(0);" class="media-like-icon <?php if ($whmedia->likes()->isLike($this->viewer())):?>media-unlike<?php endif;?>" onclick="javascript:wh_project_likes.togglelike(<?php echo $whmedia->getIdentity() ?>)"></a>
                <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => 'whmedia_user_login'), '', array('class' => 'media-like-icon media-unlike smoothbox')) ?>
                <?php endif; ?>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'whmedia', 'controller' => 'share-project', 'project_id' => $whmedia->getIdentity(), 'reset' => true), '', array('class' => 'media-share-icon smoothbox')); ?>       
                <?php echo $this->htmlLink($whmedia->getHref(), 
                                           $this->htmlImage($whmedia->getPhotoUrl($this->thumb_width, false, false), array('alt' => $this->translate('Project Thumb'))), 
                                           array('class' => 'media-browse-img')); ?>
            </div>
            <div class="media-proj-title"><?php echo $this->htmlLink($whmedia->getHref(), $whmedia->getTitle(), array('class' => 'media-browse-title')); ?></div>
            <div class="proj-auth-info">
                <div class="media-author-thumb">
                        <?php echo $this->htmlLink($whmedia->getOwner()->getHref(), $this->itemPhoto($whmedia->getOwner(), 'thumb.icon')) ?>
                    <div class="media-about-author">
                        <div class="media-author-thumb">
                            <?php echo $this->htmlLink($whmedia->getOwner()->getHref(), $this->itemPhoto($whmedia->getOwner(), 'thumb.icon')) ?>
                            <?php if ($whmedia->getOwner()->getIdentity() != $this->viewer()->getIdentity()): ?>
                                <?php if ($this->viewer()->getIdentity()): ?>
                                    <a href="javascript:void(0);" onclick="javascript:wh_project_follow.togglefollow(<?php echo $whmedia->getOwner()->getIdentity() ?>)" class="follower_button_<?php echo $whmedia->getOwner()->getIdentity() ?> media-follow-btn <?php if (($isFollow = $this->followApi->isFollow($whmedia->getOwner(), $this->viewer()))):?>unfollow<?php endif;?>"><?php echo $this->translate(($isFollow) ? 'unFollow' : 'Follow')?></a>
                                <?php else: ?>    
                                    <?php echo $this->htmlLink(array('route' => 'whmedia_user_login'), $this->translate('Follow'), array('class' => 'media-follow-btn smoothbox')) ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="media-author-info">
                                <?php echo $whmedia->getOwner()->toString() ?>
                            <span>founder, Celebrity</span>
                            <p class="followers-count"><strong class="count_follower_<?php echo $whmedia->getOwner()->getIdentity() ?>"><?php echo $this->followApi->getFollowersCount($whmedia->getOwner()) ?></strong> <?php echo $this->translate("Follower")?></p>
                            <p class="followers-count"><strong><?php echo $this->followApi->getFollowingCount($whmedia->getOwner()) ?></strong> <?php echo $this->translate("Following")?></p>
                        </div>
                    </div>
                </div>
                <div class="media-descr-info">
                        <div class="media-author-name"><?php echo $whmedia->getOwner()->toString() ?></div>   
                    <div class="media-info">
                        <span class="media-views">
                            <?php echo $this->translate('whViews: %d', $whmedia->project_views)?>
                        </span>
                        <span class="media-likes"><?php echo $this->translate('whLikes: %d', $whmedia->likes()->getLikeCount())?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
<?php elseif( $this->category || $this->show == 2 || $this->search  ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Projects were not posted in this criterias yet.');?>
      </span>
    </div>
<?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have not create projects yet.'); ?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('You can %1$spost%2$s a new one!', '<a href="'.$this->url(array('controller' => 'project', 'action' => 'create'), 'whmedia_default', true).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
<?php endif; ?>
<?php if ($this->only_items === false): ?>
    </div>
    <div style="clear: both;" ></div>
    <a href="javascript:void(0);" class="media-scroll2top" style="display: none;" id="media-scroll2top"><?php echo $this->translate('up') ?></a>    
    <?php if( $this->paginator->count() > 1): ?>
        <div class="projects_viewmore project_loadmore" id="projects_viewmore">
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('show more'), array(
                                                                                                    'id' => 'projects_viewmore_link',
                                                                                                    'class' => 'buttonlink',
                                                                                                    'onclick' => "javascript:projects_viewmore(null);"
                                                                                                    )) ?>
        </div>

        <div class="projects_viewmore" id="projects_loading" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='vertical-align: middle; margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

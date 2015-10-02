<script type="text/javascript">
    en4.core.runonce.add(function() {
        var tips = new Tips($$('.Tips'), {
            text: '',
            className:'tip-wrap media-icon-tip'
        });
    });

    function changeTip(el) {
        if (el.hasClass('media-unlike') == true)
            el.store('tip:title', 'Like This Post');
        else
            el.store('tip:title', 'Unlike This Post');
    }
</script>
<?php if ($this->sendScript):?>
    <script type="text/javascript">
    //<![CDATA[
      var scroller_count_<?php echo $this->identity; ?> = 0;
      var ScrollLoaderVar = null;
	  
      window.addEvent('domready', function() {	  
		var isPasswordSet = <?php echo $this->viewer()->isPasswordSet() ?>;
		if ( !isPasswordSet ) {		
			//Smoothbox.open( '/members/settings/password/type/1/format/smoothbox' );
        }
		ScrollLoaderVar = new ScrollLoader({    
            onScroll: function(){
                projects_viewmore(<?php echo $this->identity; ?> <?php if (isset($this->addition_data)) echo ',' . $this->addition_data?>);            
            }
        });
      });
    //]]>
    </script>
<?php endif;?>
<?php if ($this->only_items === false): ?>
    <?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js')
                             ->appendFile($this->baseUrl() . '/application/modules/Mediamasonry/externals/scripts/mooMasonry.js')?>
    <?php
        $script = <<<EOF
                    window.addEvent('load', function(){
                        $('media-browse_{$this->identity}').addEvent('masoned', function (){
                                                                            \$('media-browse_{$this->identity}').setStyle('opacity', 1);
                                                                            \$('big-loader').setStyle('display', 'none');
                                                                            }).masonry({
                                                                                        singleMode: true,
                                                                                        itemSelector: '.media-browse-box'
                                                                            });
                         wh_project_likes = new whmedia.project_likes();         
                         wh_project_follow = new whmedia.project_follow();         
                         new ToTopScroller($('media-browse_{$this->identity}'),$('media-scroll2top'));   
                    });
                    var max_projects_page_{$this->identity} = {$this->paginator->count()};
                    var current_projects_page_{$this->identity} = 1;
EOF;
        $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
    ?>
    <?php $this->headTranslate(array("whLikes: %d", "unFollow"));  ?>
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
        <div id="big-loader" class="big-loader">
            <img src="<?php echo $this->baseUrl() ?>/application/modules/Whmedia/externals/images/big-loader2.gif" alt="<?php echo $this->translate('loader') ?>" />
        </div>
    <?php endif; ?>
    <div id="media-browse_<?php echo $this->identity ?>" style="opacity:0;">
<?php endif;?>
    <?php foreach( $this->paginator as $whmedia ):?>
	
		<?php /*** added by me if block ***/ ?>
		<?php /* start of the isblock */ ?>
		<?php if( !$this->viewer()->isBlockedBy( $whmedia->getOwner() ) ): ?>
	
	
        <div class="media-browse-box" style="width:<?php echo $this->thumb_width ?>px;" id="project_<?php echo $whmedia->getIdentity() ?>">
            <div class="media-proj-img">
                <?php if ($this->viewer()->getIdentity()): ?>
                    <?php if (!$whmedia->isOwner($this->viewer()))
                            echo $this->htmlLink(array('route' => 'default', 'module' => 'whmedia', 'controller' => 'share-project', 'action' => 'repost', 'project_id' => $whmedia->getIdentity(), 'reset' => true), '', array('class' => 'Tips media-repost-icon smoothbox', 'title' => 'Repost This Post')); ?>       
                    <a href="javascript:void(0);" class="Tips media-like-icon <?php if ($whmedia->likes()->isLike($this->viewer())):?>media-unlike<?php endif;?>" title="<?php if ($whmedia->likes()->isLike($this->viewer())):?>Unlike This Post<?php else :?>Like This Post<?php endif;?>" onclick="javascript:wh_project_likes.togglelike(<?php echo $whmedia->getIdentity() ?>);changeTip(this);"></a>
                <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => 'whmedia_user_login'), '', array('class' => 'Tips media-like-icon media-unlike smoothbox', 'title' => 'Unlike This Post')) ?>
                <?php endif; ?>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'whmedia', 'controller' => 'tag', 'project_id' => $whmedia->getIdentity(), 'reset' => true), '', array('class' => 'Tips media-plus-icon smoothbox', 'title' => 'Follow Hashtag')); ?> 
                <?php if(!isset($this->removeFav)){ ?>
                <?php echo $this->htmlLink(array('route' => 'default', 'controller' => 'boxes', 'action' => 'addfavproject', 'project_id' => $whmedia->getIdentity(), 'reset' => true), '', array('class' => 'Tips media-favo-icon smoothbox', 'title' => 'Add Favo')); ?>      
                <?php } ?>
                <?php echo $this->htmlLink($whmedia->getHref(), 
                                           $this->htmlImage($whmedia->getPhotoUrl($this->thumb_width, false, false), array('alt' => $this->translate('Project Thumb')), array('class' => "curve-top-image")), 
                                           array('class' => 'media-browse-img')); ?>
            
            </div>
            <div class="media-proj-title"><?php echo $this->htmlLink($whmedia->getHref(), $whmedia->getTitle(), array('class' => 'media-browse-title')); ?></div>
            <div class="proj-auth-info">
                <div class="media-author-thumb">
                        <?php echo $this->htmlLink($whmedia->getOwner()->getHref(), $this->itemPhoto($whmedia->getOwner(), 'thumb.icon circular-mini')) ?>
                    <div class="media-about-author">
                        <div class="media-author-thumb">
                            <?php echo $this->htmlLink($whmedia->getOwner()->getHref(), $this->itemPhoto($whmedia->getOwner(), 'thumb.icon circular-mini')) ?>
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
                            <span><?php echo $whmedia->getOwner()->getAboutMe(); ?></span>
                            <p class="followers-count"><strong class="count_follower_<?php echo $whmedia->getOwner()->getIdentity() ?>"><?php echo $this->followApi->getFollowersCount($whmedia->getOwner()) ?></strong> <?php echo $this->translate("Follower")?></p>
                            <p class="followers-count"><strong><?php echo $this->followApi->getFollowingCount($whmedia->getOwner()) ?></strong> <?php echo $this->translate("Following")?></p>
                        </div>
                    </div>
                </div>
                <div class="media-descr-info">
                        <div class="media-author-name"><?php echo $whmedia->getOwner()->toString() ?></div>  
                    <div class="media-info">
						<ul>
							<li class="media-icons">
								<a href="#" title="Views" style="text-decoration: none">
									<div class="media-views-icon">
										<span class="media-views">
											<?php echo $whmedia->project_views ?>
										</span>								
									</div>
								</a>
							</li>
							<li class="media-icons">
								<a href="#" title="Likes" style="text-decoration: none">
									<div class="media-likes-icon">
										<span class="media-likes">
											<?php echo $whmedia->likes()->getLikeCount()?>
										</span>										
									</div>
								</a>
							</li>
                            <li class="media-icons">
                                <?php echo $whmedia->getCoverMedia()->getMediaTypeLabel(); ?>
                            </li>
						</ul>

                    </div>
                </div>
        	</div>
        </div>
		
		
		<?php endif; ?> <?php /* end of the isblock */ ?>
		
    <?php endforeach; ?>
<?php if ($this->only_items === false): ?>
    </div>
    <div style="clear: both;" ></div>
    <a href="javascript:void(0);" class="media-scroll2top" style="display: none;" id="media-scroll2top"><?php echo $this->translate('up') ?></a>    
    <?php if( $this->paginator->count() > 1): ?>
        <div class="projects_viewmore project_loadmore" id="projects_viewmore_<?php echo $this->identity ?>">
              <?php if (isset($this->addition_data)) 
                        $addition_data = ',' . $this->addition_data;
                    else
                        $addition_data = '';
                    echo $this->htmlLink('javascript:void(0);', $this->translate('show more'), array(
                                                                                                    'id' => 'projects_viewmore_link',
                                                                                                    'class' => 'buttonlink',
                                                                                                    'onclick' => "javascript:projects_viewmore({$this->identity} {$addition_data});"
                                                                                                    )) ?>
        </div>

        <div class="projects_viewmore" id="projects_loading_<?php echo $this->identity ?>" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='vertical-align: middle; margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
    <?php endif; ?>

<?php endif; ?>
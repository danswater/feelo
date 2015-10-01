<script type="text/javascript">
    en4.core.runonce.add(function() {
        new Tips($$('.Tips'), {
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

<div class="generic_layout_container layout_middle">
  
	<div class="wazzup-header">
		<div id="wazzup-tag-top">
			<ul class="wazzup-ul-float">
				<li><a class="hash-tag" data-id="37" href="#"><span>#news</span></a></li>
				<li><a class="hash-tag" data-id="67" href="#"><span>#technology</span></a></li>
				<li><a class="hash-tag" data-id="53" href="#"><span>#sports</span></a></li>
				<li><a class="hash-tag" data-id="83" href="#"><span>#fashion</span></a></li>
				<li><a class="hash-tag" data-id="1"  href="#"><span>#garden</span></a></li>
				<li><a class="hash-tag" data-id="58" href="#"><span>#arts</span></a></li>
			</ul>
		</div>
		<div id="wazzup-tag-middle">
			<ul class="wazzup-ul-float">
				<li><a class="hash-tag" data-id="3"  href="#"><span>#decor</span></span></a></li>
				<li><a class="hash-tag" data-id="76" href="#"><span>#travel</span></a></li>
				<li><a class="hash-tag" data-id="5"  href="#"><span>#home</span></a></li>
				<li><a class="hash-tag" data-id="88" href="#"><span>#gadgets</span></a></li>
				<li><a class="hash-tag" data-id="110" href="#"><span>#filmmaking</span></a></li>
				<li><a class="hash-tag" data-id="6"  href="#"><span>#wedding</span></a></li>
				<li><a class="hash-tag" data-id="8" href="#"><span>#rocks</span></a></li>
				<li><a class="hash-tag" data-id="17" href="#"><span>#space</span></a></li>
			</ul>
		</div>
		<div id="wazzup-tag-bottom">
			<ul class="wazzup-ul-float">
				<li><a class="hash-tag" data-id="41" href="#"><span>#photography</span></a></li>
				<li><a class="hash-tag" data-id="130" href="#"><span>#dogs</span></a></li>
				<li><a class="hash-tag" data-id="11" href="#"><span>#test</span></a></li>
				<li><a class="hash-tag" data-id="42" href="#"><span>#animals</span></a></li>
				<li><a class="hash-tag" data-id="25" href="#"><span>#project</span></a></li>
				<li><a class="hash-tag" data-id="28" href="#"><span>#science</span></a></li>
			</ul>
		</div>
		
		
		<div style="clear:both"></div>
		
		<div id="giant_search_box">
			<form id="giant_search_form" class="" action="/zzupWeb/search" method="get">
				<input type="text" name="query" id="query" value="" placeholder="Search">
				<div style="margin: 0 auto; width: 312px; padding-top: 15px">
				  <ul id="filter-search">
					<li style="float: left; padding-left: 21px;">
					  <a id="filter-item1" href="search?query=" <?php echo ( $_GET[ 'type' ] == '' ) ? 'style="text-decoration: underline"' : '' ?>> All </a>
					</li>
					<li style="float: left; padding-left: 21px;">
					  <a id="filter-item2" href="search?query=&tags=whmedia_project" <?php echo ( $_GET[ 'type' ] == 'whmedia_project' ) ? 'style="text-decoration: underline"' : '' ?>> Post </a>
					</li>
					<li style="float: left; padding-left: 21px;">
					  <a id="filter-item3" href="search?query=&tags=tags" <?php echo ( $_GET[ 'type' ] == 'tags' ) ? 'style="text-decoration: underline"' : '' ?>> #Tags </a>
					</li>
					<li style="float: left; padding-left: 21px;">
					  <a id="filter-item4" href="#"> Stories </a>
					</li>
				  </ul>
				</div>
			</form>	
		</div>
		
<br />
<br />

</div>

<div class="generic_layout_container layout_middle">
  
    <ul id="filter-list">

        <li><button id="follow-tag" data-tagid="<?php echo $this->tag_id ?>" type="button" class="btn btn-default"> <?php echo ( $this->followed ) ? 'unfollow' : 'follow' ?> </button></li>

        <li><div id="bytime-element" class="form-element">
            <select name="bytime" id="bytime" data-tag="<?php echo $this->tags ?>">
                <option value="0" label="All Time">All Time</option>
                <option value="today" label="Today">Today</option>
                <option value="week" label="This Week">This Week</option>
                <option value="month" label="This Month">This Month</option>
                <option value="featured" label="Featured">Featured</option>
            </select>
        </div></li>

</div>
    <div id="media-browse" <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>style="opacity:0;"<?php endif; ?>>
<?php endif;?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <?php foreach( $this->paginator as $whmedia ):?>
        <div class="media-browse-box" style="width:<?php echo $this->thumb_width ?>px;" id="project_<?php echo $whmedia->getIdentity() ?>">
            <div class="media-proj-img">
                <?php if ($this->viewer()->getIdentity()): ?>
                    <?php if (!$whmedia->isOwner($this->viewer()))
                            echo $this->htmlLink(array('route' => 'default', 'module' => 'whmedia', 'controller' => 'share-project', 'action' => 'repost', 'project_id' => $whmedia->getIdentity(), 'reset' => true), '', array('class' => 'Tips media-repost-icon smoothbox', 'title' => 'Repost This Post')); ?>       
                    <a href="javascript:void(0);" class="Tips media-like-icon <?php if ($whmedia->likes()->isLike($this->viewer())):?>media-unlike<?php endif;?>" title="<?php if ($whmedia->likes()->isLike($this->viewer())):?>Unlike This Post<?php else :?>Like This Post<?php endif;?>" onclick="javascript:wh_project_likes.togglelike(<?php echo $whmedia->getIdentity() ?>);changeTip(this)"></a>
                <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => 'whmedia_user_login'), '', array('class' => 'Tips media-like-icon media-unlike smoothbox','title' => 'Unlike This Post')) ?>
                <?php endif; ?>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'whmedia', 'controller' => 'share-project', 'project_id' => $whmedia->getIdentity(), 'reset' => true), '', array('class' => 'Tips media-share-icon smoothbox', 'title' => 'Share This Post')); ?>       
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
        <?php echo $this->translate('Nobody has posted a project in this criteria yet.');?>
      </span>
    </div>
<?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has written a projects yet.'); ?>
        <?php if ($this->can_create): // @todo check if user is allowed to create a poll ?>
          <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('controller' => 'project', 'action' => 'create'), 'whmedia_default', true).'">', '</a>'); ?>
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

<?php 
    if ( $this->only_items === false ) :
        echo $this->inlineScript()
            ->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js')
            ->appendFile($this->baseUrl() . '/application/modules/Mediamasonry/externals/scripts/mooMasonry.js')
            ->appendFile($this->baseUrl() . '/application/modules/Mediamasonry/externals/scripts/index.js' );
    endif;

?>

<script>

window.addEvent('domready', function() {
        var indexApp = new IndexApp();
        indexApp.start();
});
</script>

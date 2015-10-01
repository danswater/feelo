<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>
<script type="text/javascript">
    window.addEvent('domready', function(){        
         wh_project_follow = new whmedia.project_follow();         
    });
</script>
<ul>
    <?php foreach ($this->follow_suggestion as $item) : ?>
        <li>

            <div class="">
                <div class="follow_user_thumb">
                    <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile')) ?>
                    <a href="javascript:void(0);" onclick="javascript:wh_project_follow.togglefollow(<?php echo $item->getIdentity() ?>)" class="follower_button_<?php echo $item->getIdentity() ?> media-follow-btn <?php if (($isFollow = $this->followApi->isFollow($item, $this->viewer()))):?>unfollow<?php endif;?>"><?php echo $this->translate(($isFollow) ? 'unFollow' : 'Follow')?></a>                    
                </div>
                <div class="follow_user_info">
                    <?php echo $item->toString() ?>
                    <div class="followers-count">
                    	<p><strong class="count_follower_<?php echo $item->getIdentity() ?>"><?php echo $this->followApi->getFollowersCount($item) ?></strong> <?php echo $this->translate("Follower")?></p>
                    	<p><strong><?php echo $this->followApi->getFollowingCount($item) ?></strong> <?php echo $this->translate("Following")?></p>
                	</div>
                </div>
                
            </div>

        </li>
    <?php endforeach;?>
</ul>
<?php echo $this->htmlLink(array('route' => 'whmedia_members', 'action' => 'follow-suggestion', 'reset' => true), $this->translate("View More"), array('class' => 'buttonlink icon_viewmore')) ?>

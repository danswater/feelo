<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>
<?php
            $script = <<<EOF
                    window.addEvent('domready', function(){      
                         wh_project_follow = new whmedia.project_follow();         
                    });

EOF;
            $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
        ?>
    <div class="profile-follow-btn">
        <?php if ($this->subject()->getIdentity() != $this->viewer()->getIdentity()): ?>
            <?php if ($this->viewer()->getIdentity()): ?>
                <a href="javascript:void(0);" onclick="javascript:wh_project_follow.togglefollow(<?php echo $this->subject()->getIdentity() ?>)" class="follower_button_<?php echo $this->subject()->getIdentity() ?> media-follow-btn <?php if (($isFollow = $this->followApi->isFollow($this->subject(), $this->viewer()))):?>unfollow<?php endif;?>"><?php echo $this->translate(($isFollow) ? 'unFollow' : 'Follow')?></a>
            <?php else: ?>    
                <?php echo $this->htmlLink(array('route' => 'whmedia_user_login'), $this->translate('Follow'), array('class' => 'media-follow-btn smoothbox')) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<ul>
    <li><span><strong><?php echo $this->followApi->getFollowingCount($this->subject()) ?></strong></span><span><?php echo $this->translate("Following :")?></span></li>
    <li><span><strong class="count_follower_<?php echo $this->subject()->getIdentity() ?>"><?php echo $this->followApi->getFollowersCount($this->subject()) ?></strong></span><span><?php echo $this->translate("Follower")?></span></li>
    
</ul>

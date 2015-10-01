<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>
    <?php
        $isAdmin = $this->viewer()->isAdmin();
        $script = <<<EOF
                    window.addEvent('load', function(){       
                         wh_project_follow = new whmedia.project_follow();   
                    });
EOF;
        $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
        if ($isAdmin) {
            $script = <<<EOF
                        window.addEvent('load', function(){        
                             new Tips($$('.Tips'));
                        });
EOF;
            $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
        }
        $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');     
        $featuredApi = Engine_Api::_()->getDbtable('featured', 'whmedia');     
    ?>
    <ul class="follow-members-list">
        <?php foreach( $this->paginator as $item ): ?>
            <li>
                <div class="follow-member-photo">
                	<div class="follow-member-photo-link"><?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile')) ?></div>
                    <div class="follow-member-follow-btn">
                    <?php if (!$item->isOwner($this->viewer())): ?>
                        <?php if ($this->viewer()->getIdentity()): ?>
                            <a href="javascript:void(0);" onclick="javascript:wh_project_follow.togglefollow(<?php echo $item->getIdentity() ?>)" class="follower_button_<?php echo $item->getIdentity() ?> media-follow-btn <?php if (($isFollow = $followApi->isFollow($item, $this->viewer()))):?>unfollow<?php endif;?>"><?php echo $this->translate(($isFollow) ? 'unFollow' : 'Follow')?></a>
                        <?php else: ?>    
                            <?php echo $this->htmlLink(array('route' => 'whmedia_user_login'), $this->translate('Follow'), array('class' => 'media-follow-btn smoothbox')) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="follow-member-name"><?php echo $item ?></div>
                <?php if ($isAdmin): ?>
                    <a id="featured_user_<?php echo $item->getIdentity() ?>" href="javascript:void(0);" onclick="javascript:wh_project_follow.togglefeatured(<?php echo $item->getIdentity() ?>)" class="<?php if ($featuredApi->isFeatured($item)): ?>unfeatured-member<?php else: ?>featured-member<?php endif; ?> Tips" rel="Featured"></a>
                <?php endif; ?>
            </li>
        <?php endforeach;?>
    </ul>
    <?php echo $this->paginationControl($this->paginator, null, NULL, array('query' => $this->params)); ?>
<?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Sorry, no members were found.');?>
      </span>
    </div>
<?php endif; ?>

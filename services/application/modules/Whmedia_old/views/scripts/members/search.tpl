<?php if (!$this->ajax) : ?>
    <script type="text/javascript">
        en4.core.runonce.add(function() {
            var scroller_count = 0;
            var ScrollLoaderVar = null;
            var current_projects_page = 1;
            var max_projects_page = <?php echo $this->paginator->count() ?>;
            ScrollLoaderVar = new ScrollLoader({           
                onScroll: function(){
                    users_viewmore();            
                }
            });
            window.users_viewmore = users_viewmore = function () {
                var projects_viewmore = $('projects_viewmore');
                var projects_loading = $('projects_loading');
                var media_browse = $('users_browse');
                var scroller_count_label = 'scroller_count';
                current_projects_page = current_projects_page + 1;
                var fun_current_projects_page = current_projects_page;
                var fun_max_projects_page = max_projects_page;
                var url = window.location.href + '/search/' + fun_current_projects_page;
                try {
                    var formObjects=$$('field_search_criteria')[0].toQueryString().parseQueryString();
                    var data = $extend(formObjects, {
                        format : 'html', 
                        page: fun_current_projects_page
                    });
                }
                catch (e) {
                    var data = {
                        format : 'html', 
                        page: fun_current_projects_page
                    };
                }
                        
                projects_viewmore.setStyle('display', 'none');
                projects_loading.setStyle('display');
            
                if (typeof ScrollLoaderVar != 'undefined' && ScrollLoaderVar != null) {
                    ScrollLoaderVar.detach();
                }
            
                new Request.HTML({
                    url : url,
                    evalScripts : true,
                    data : data,
                    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        var new_elements = Elements.from(responseHTML);
                        if (new_elements[0].hasClass('add2boxes'))
                            new_elements.erase(new_elements[0]);
                        var imgs = new Array();
                        try {
                            new_elements.each(function(item) {
                                imgs.push(item.getElement('img').get('src'));
                            });
                        }
                        catch(e) {
                            alert(e);
                        }
                        new Asset.images(imgs, {
                            'onComplete' : function() {
                                new_elements.inject(media_browse);
                                Smoothbox.bind(this);
                                if (fun_current_projects_page < fun_max_projects_page) {
                                    projects_viewmore.setStyle('display');
                                }
                                projects_loading.setStyle('display', 'none');
                                if (typeof ScrollLoaderVar != 'undefined' && ScrollLoaderVar != null && (fun_current_projects_page < fun_max_projects_page) && window[scroller_count_label] < 7) {
                                    ScrollLoaderVar.attach();
                                }    
                                if (typeof ScrollLoaderVar != 'undefined' && ScrollLoaderVar != null) {
                                    window[scroller_count_label]++;
                                } 
                            }
                        });
                    }
                }).send();
            }
        });
    </script>
    <?php echo $this->form ?>
<?php endif ?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
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
    <ul class="follow-members-list" id="users_browse">
        <?php foreach ($this->paginator as $item): ?>
            <li>
                <div class="follow-member-photo">
                    <div class="follow-member-photo-link"><?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile')) ?></div>
                    <div class="follow-member-follow-btn">
                        <?php if (!$item->isOwner($this->viewer())): ?>
                            <?php if ($this->viewer()->getIdentity()): ?>
                                <a href="javascript:void(0);" onclick="javascript:wh_project_follow.togglefollow(<?php echo $item->getIdentity() ?>)" class="follower_button_<?php echo $item->getIdentity() ?> media-follow-btn <?php if (($isFollow = $followApi->isFollow($item, $this->viewer()))): ?>unfollow<?php endif; ?>"><?php echo $this->translate(($isFollow) ? 'unFollow' : 'Follow') ?></a>
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
        <?php endforeach; ?>
    </ul>
    <?php if (!$this->ajax) : ?>
        <div class="projects_viewmore project_loadmore" id="projects_viewmore">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('+ Load More'), array(
                'id' => 'projects_viewmore_link',
                'class' => 'buttonlink',
                'onclick' => "javascript:users_viewmore();"
            ))
            ?>
        </div>
        <div class="projects_viewmore" id="projects_loading" style="display: none;">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='vertical-align: middle; margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
        </div>
    <?php endif ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('Sorry, no members were found.'); ?>
        </span>
    </div>
<?php endif; ?>
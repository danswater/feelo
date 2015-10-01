<script type="text/javascript">
    en4.core.runonce.add(function() {
        
        var content = '<div class="current-user"><div class="user-photo"><?php echo $this->htmlLink($this->viewer()->getHref(), $this->itemPhoto($this->viewer(), "thumb.icon", $this->translate("Member Thumb"))) ?></div><?php echo $this->viewer()->toString(array("class" => "user-name")) ?></div>';

        var moreEl = new Element('li', {'class': 'more'});
        moreEl.set('html', content);
        //var moreLink = new Element('a', {'class': 'menu_core_mini core_main_more', html: 'More', href: 'javascript:void(0);'});
        //moreLink.inject(moreEl);
        moreEl.inject($$('.core_mini_store')[0].getParent('li'), 'after');
        
        var ul = new Element('ul', {'class': 'submenu'});
        
        $$('.core_mini_profile')[0].getParent('li').inject(ul);
        $$('.core_mini_messages')[0].getParent('li').inject(ul);
        <?php if ($this->viewer()->isAdmin()) : ?>
            $$('.core_mini_admin')[0].getParent('li').inject(ul);
        <?php endif ?>
        $$('.core_mini_settings')[0].getParent('li').inject(ul);
        $$('.core_mini_auth')[0].getParent('li').inject(ul);


        ul.inject(moreEl, 'bottom');
        ul.hide();
        
        moreEl.addEvent('mouseenter', function(e) {
            e.stopPropagation();
            if (ul.getStyle('display') == 'none') {
                ul.show();
            }
        });
        
        moreEl.addEvent('mouseleave', function() {
            if (ul.getStyle('display') == 'block') {
                ul.hide();
            }
        }); 
    });
</script>
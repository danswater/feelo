<?php if (count($this->images) > 7) : ?>
    <script type="text/javascript">
        en4.core.runonce.add(function() {        
            var imgSwitch = function(elem) {
                var elems = $$('.img');
                var elem = elems.getRandom();
                var links = elem.getChildren('a');
                links.each(function(link){
                    if (link.getStyle('opacity') == 1) {
                        link.tween('opacity', 0);
                    } else {
                        link.tween('opacity', 1);
                    }
                });
                imgSwitch.delay(Number.random(1000, 10000));
            }
            imgSwitch().delay(Number.random(1000, 10000));
        })
    </script>

    <div id="cover-wrapper">
        <div id="first-column">
            <div id="first-block" class="img">
                <?php if (isset($this->images[0])) : ?>
                    <a href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[0]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[0]->storage_path ?>" />
                    </a>
                <?php endif ?>
                <?php if (isset($this->images[13])) : ?>
                    <a style="opacity: 0" href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[13]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[13]->storage_path ?>" />
                    </a>
                <?php endif ?>
            </div>
            <div id="second-block" class="img">
                <?php if (isset($this->images[1])) : ?>
                    <a href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[1]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[1]->storage_path ?>" />
                    </a>
                <?php endif ?>
                <?php if (isset($this->images[12])) : ?>
                    <a style="opacity: 0" href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[12]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[12]->storage_path ?>" />
                    </a>
                <?php endif ?>
            </div>
        </div>
        <div id="second-column">
            <div id="third-block" class="img">
                <?php if (isset($this->images[2])) : ?>
                    <a href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[2]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=410&h=437&src=' . $this->images[2]->storage_path ?>" />
                    </a>
                <?php endif ?>
                <?php if (isset($this->images[11])) : ?>
                    <a style="opacity: 0" href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[11]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=410&h=437&src=' . $this->images[11]->storage_path ?>" />
                    </a>
                <?php endif ?>
            </div>
        </div>
        <div id="third-column">
            <div id="fourth-block" class="img">
                <?php if (isset($this->images[3])) : ?>
                    <a href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[3]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[3]->storage_path ?>" />
                    </a>
                <?php endif ?>
                <?php if (isset($this->images[10])) : ?>
                    <a style="opacity: 0" href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[10]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[10]->storage_path ?>" />
                    </a>
                <?php endif ?>
            </div>
            <div id="fifth-block" class="img">
                <?php if (isset($this->images[4])) : ?>
                    <a href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[4]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[4]->storage_path ?>" />
                    </a>
                <?php endif ?>
                <?php if (isset($this->images[9])) : ?>
                    <a style="opacity: 0" href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[9]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[9]->storage_path ?>" />
                    </a>
                <?php endif ?>
            </div>
        </div>
        <div id="fourth-column">
            <div id="sixth-block" class="img">
                <?php if (isset($this->images[5])) : ?>
                    <a href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[5]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[5]->storage_path ?>" />
                    </a>
                <?php endif ?>
                <?php if (isset($this->images[8])) : ?>
                    <a style="opacity: 0" href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[8]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[8]->storage_path ?>" />
                    </a>
                <?php endif ?>
            </div>
            <div id="seventh-block" class="img">
                <?php if (isset($this->images[6])) : ?>
                    <a href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[6]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[6]->storage_path ?>" />
                    </a>
                <?php endif ?>
                <?php if (isset($this->images[7])) : ?>
                    <a style="opacity: 0" href="<?php echo Engine_Api::_()->getItem('whmedia_media', $this->images[7]->parent_id)->getParent()->getHref() ?>">
                        <img src="<?php echo $this->baseUrl() . '/whshow_thumb.php?cz=1&w=220&h=205&src=' . $this->images[7]->storage_path ?>" />
                    </a>
                <?php endif ?>
            </div>
        </div>       
    </div>
<?php else : ?>
    <div id="cover-wrapper" class="empty"></div>
<?php endif ?>

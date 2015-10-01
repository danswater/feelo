<?php
    if ($this->user_projects->count() > $this->count_item) {
        $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/carousel/Carousel.js');
        $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/carousel/Carousel.Extra.js');
        $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/carousel/PeriodicalExecuter.js');
        $script = <<<EOF
            window.addEvent("domready", function() {
                carousel = new Carousel.Extra({
				container: "whmedia_content_{$this->identity}",
				scroll: {$this->count_item},
				circular: true,
				previous: "whmedia_previous_{$this->identity}",
				next: "whmedia_next_{$this->identity}",
				distance:{$this->count_item},
                                autostart: false,
				fx: {
                                    duration: 300
				}
			});
            
            });
EOF;
        $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
    }

?>
<div id="whmedia_m_<?php echo $this->identity ?>" class="whmedia_m" style="width: <?php echo 133*$this->count_item+70?>px;">
    <div class="inwhframe" style="width: <?php echo 133*$this->count_item?>px;" id="whmedia_content_<?php echo $this->identity ?>">
        <?php foreach ($this->user_projects as $project) : ?>
            <?php if (!empty ($this->project) and $project->getIdentity() == $this->project->getIdentity())
                continue; ?>
            <div class="whmedia_item">
                <?php echo $this->htmlLink($project->getHref(), "<img alt='Project Thumb' src=\"{$project->getPhotoUrl(120, 120)}\" />"); ?>
                <?php echo $this->htmlLink($project->getHref(), $this->string()->chunk($this->whtruncate($project->getTitle(), 300), 10)); ?>
            </div>
        <?php endforeach; ?>
		
    </div>
    <?php if ($this->user_projects->count() > $this->count_item): ?>
        <div id="whmedia_frame_<?php echo $this->identity ?>" class="whmedia_frame">
            <div id="whmedia_previous_<?php echo $this->identity ?>" title="<?php echo $this->translate("Previous"); ?>" class="whmedia_previous">
                <a href='javascript:void(0);'></a>
            </div>
            <div id="whmedia_next_<?php echo $this->identity ?>" title="<?php echo $this->translate("Next"); ?>" class="whmedia_next">
                <a href='javascript:void(0);'></a>
            </div>

        </div>
    <?php endif; ?>
</div>
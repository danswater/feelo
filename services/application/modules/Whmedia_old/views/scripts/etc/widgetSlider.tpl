<?php
    if ($this->paginator->getTotalItemCount() > $this->count_item) {
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
    $factor = $this->thumb_width + 13;
?>

<div id="whmedia_m_<?php echo $this->identity ?>" class="whmedia_m" style="width: <?php echo $factor*$this->count_item+70?>px; height: <?php echo $this->thumb_height + 45 ?>px !important;">
    <div class="inwhframe" style="width: <?php echo $factor*$this->count_item?>px;" id="whmedia_content_<?php echo $this->identity ?>">
        <?php foreach ($this->paginator as $project) : ?>
            <div class="whmedia_slider_item">
                <?php echo $this->htmlLink($project->getHref(), "<img alt='Project Thumb' src=\"{$project->getPhotoUrl((int)$this->thumb_width, (int)$this->thumb_height)}\" />", array('style' => "width:{$this->thumb_width}px")); ?>
                <?php echo $this->htmlLink($project->getHref(), $this->string()->chunk($this->whtruncate($project->getTitle(), 300), 10)); ?>
            </div>
        <?php endforeach; ?>

    </div>
    <?php if ($this->paginator->getTotalItemCount() > $this->count_item): ?>
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
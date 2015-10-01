<div id="whmedia_m_<?php echo $this->identity ?>" class="whmedia_m">
    <div class="related-projects">
        <?php foreach ($this->user_projects as $project) : ?>
            <?php if (!empty ($this->project) and $project->getIdentity() == $this->project->getIdentity())
                continue; ?>
            <div class="whmedia_item">
                <?php echo $this->htmlLink($project->getHref(), "<div><img alt='Project Thumb' src=\"{$project->getPhotoUrl(120, 120)}\" /></div>"); ?>
                <?php echo $this->htmlLink($project->getHref(), $this->string()->chunk($this->whtruncate($project->getTitle(), 300), 10)); ?>
            </div>
        <?php endforeach; ?>
		
    </div>
</div>
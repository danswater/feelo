<ul id="profile_whmedia_fprojects" class="thumbs">
  <?php foreach( $this->paginator as $project ): ?>
    <li>
   	<div class="media_thumb_wrapper">             
      <a href="<?php echo $project->getHref(); ?>">
	        <img src="<?php echo $project->getPhotoUrl($this->thumb_width, $this->thumb_height); ?>" alt=""  />        
      </a>
    </div> 
      <div class="thumbs_info">
        <div class="thumbs_title">
          <?php echo $this->htmlLink($project->getHref(), $this->string()->chunk($this->whtruncate($project->getTitle(), $this->thumb_width), 10)) ?>
        </div>
        <div class="media_author">
        	<?php echo $this->translate('by %s', $project->getOwner()->toString()) ?>
        </div>        
        
		<span class="media_files">        
				<?php echo $project->count_media; ?>
		</span> |
		<span class="media_views"><?php echo $this->translate('Views: %d', $project->project_views) ?></span> | <span class="media_likes"><?php echo $project->likes()->getLikeCount(); ?></span>
        
      </div>
    </li>
  <?php endforeach;?>
</ul>

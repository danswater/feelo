<ul class="thumbs">
  <?php foreach( $this->paginator as $media ): ?>
    <li>
   	<div class="media_thumb_wrapper">         
      <a href="<?php echo $media->getHref(); ?>">
        <img src="<?php echo $media->getThumb($this->thumb_width, $this->thumb_height); ?>" alt=""  />        
      </a>
    </div>  
      <div class="thumbs_info">
        <div class="thumbs_title">
          <?php echo $this->htmlLink($media->getHref(), $this->string()->chunk($this->whtruncate($media->getTitle(), $this->thumb_width), 10)) ?>
        </div>
             <div class="media_project">
                <?php echo $this->translate('in'); ?> <?php echo $this->htmlLink($media->getProject()->getHref(), $this->string()->chunk($this->whtruncate($media->getProject()->getTitle(), $this->thumb_width), 10)); ?>
             </div>   
        	<?php echo $this->translate('by %s', $media->getOwner()->toString()) ?>
        <span class="media_likes"><?php echo $media->likes()->getLikeCount(); ?></span>
      </div>
    </li>
  <?php endforeach;?>
</ul>

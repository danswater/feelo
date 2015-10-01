<?php $isLiked = $this->media->likes()->isLike($this->viewer()); ?>
<?php  if ($this->viewer()->getIdentity()): ?>
    <?php if( !$isLiked ): ?>
      <a href="javascript:void(0)" class="like" onclick="wh_media.like(<?php echo "{$this->media->getIdentity()}" ?>)">

      </a>
    <?php else: ?>
      <a href="javascript:void(0)" class="unlike" onclick="wh_media.unlike(<?php echo  "{$this->media->getIdentity()}" ?>)">

      </a>
    <?php endif ?>
<?php else :?>
    <span class="disablelikeunlike"></span>
<?php endif ?>
<?php if( $this->media->likes()->getLikeCount() > 0 ): ?>

<span id="whmedia_likes_<?php echo $this->media->getIdentity() ?>" class="whmedia_media_likes" title="<?php echo $this->translate('Loading...') ?>">
  <?php echo $this->translate(array('%s', '%s ', $this->media->likes()->getLikeCount()), $this->locale()->toNumber($this->media->likes()->getLikeCount())) ?>
</span>

<?php endif;?>
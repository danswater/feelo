<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_whmedia').getParent();
    $('profile_whmedia_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_whmedia_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_whmedia_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_whmedia_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<ul id="profile_whmedia" class="thumbs">
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
        <div class="media_author">
             <div class="media_project">
                <?php echo $this->translate('in'); ?> <?php echo $this->htmlLink($media->getProject()->getHref(), $this->string()->chunk($this->whtruncate($media->getProject()->getTitle(), $this->thumb_width), 10)); ?>
             </div>   
        	<?php echo $this->translate('by %s', $media->getOwner()->toString()) ?>
            <span class="media_likes"><?php echo $media->likes()->getLikeCount(); ?></span>
        </div>          
      </div>
    </li>
  <?php endforeach;?>
</ul>

<div>
  <div id="profile_whmedia_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_whmedia_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
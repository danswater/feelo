<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_whmedia_projects').getParent();
    $('profile_whmedia_projects_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_whmedia_projects_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_whmedia_projects_previous').removeEvents('click').addEvent('click', function(){
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

    $('profile_whmedia_projects_next').removeEvents('click').addEvent('click', function(){
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

<ul id="profile_whmedia_projects" class="thumbs">
  <?php foreach( $this->paginator as $project ): ?>
    <li>
        <?php if (!$project->is_published): ?>
            <div class="media-proj-draft"><?php echo $this->translate('Draft')?></div>
        <?php endif; ?>  
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
				<?php $MediasCount = $project->count_media;
							echo $this->translate(array("%d file", "%d files",$MediasCount), $MediasCount);
					  ?>
		</span> |
		<span class="media_views"><?php echo $this->translate('whViews: %d', $project->project_views) ?></span> | <span class="media_likes"><?php echo $project->likes()->getLikeCount(); ?></span>        
      </div>
    </li>
  <?php endforeach;?>
</ul>

<div>
  <div id="profile_whmedia_projects_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_whmedia_projects_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
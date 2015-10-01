<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core_Views_Search
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>


<?php
	/*
	 *  remove hash-tag in class and add search?query=photo&type=tags 
	 */
	$hashtagUrl = $this->url(array('controller' => 'search'), 'default', true);
?>

<div class="generic_layout_container layout_main">
	<div class="wazzup-header">
		<div id="wazzup-tag-top">
			<ul class="wazzup-ul-float">
				<li><a class="" data-id="37" href="<?php echo $hashtagUrl;?>?query=news&type=tags"><span>#news</span></a></li>
				<li><a class="" data-id="67" href="<?php echo $hashtagUrl;?>?query=technology&type=tags"><span>#technology</span></a></li>
				<li><a class="" data-id="53" href="<?php echo $hashtagUrl;?>?query=sports&type=tags"><span>#sports</span></a></li>
				<li><a class="" data-id="83" href="<?php echo $hashtagUrl;?>?query=fashion&type=tags"><span>#fashion</span></a></li>
				<li><a class="" data-id="1"  href="<?php echo $hashtagUrl;?>?query=garden&type=tags"><span>#garden</span></a></li>
				<li><a class="" data-id="58" href="<?php echo $hashtagUrl;?>?query=arts&type=tags"><span>#arts</span></a></li>
			</ul>
		</div>
		<div id="wazzup-tag-middle">
			<ul class="wazzup-ul-float">
				<li><a class="" data-id="3"  href="<?php echo $hashtagUrl;?>?query=decor&type=tags"><span>#decor</span></span></a></li>
				<li><a class="" data-id="76" href="<?php echo $hashtagUrl;?>?query=travel&type=tags"><span>#travel</span></a></li>
				<li><a class="" data-id="5"  href="<?php echo $hashtagUrl;?>?query=home&type=tags"><span>#home</span></a></li>
				<li><a class="" data-id="88" href="<?php echo $hashtagUrl;?>?query=gadgets&type=tags"><span>#gadgets</span></a></li>
				<li><a class="" data-id="110" href="<?php echo $hashtagUrl;?>?query=filmmaking&type=tags"><span>#filmmaking</span></a></li>
				<li><a class="" data-id="6"  href="<?php echo $hashtagUrl;?>?query=wedding&type=tags"><span>#wedding</span></a></li>
				<li><a class="" data-id="8" href="<?php echo $hashtagUrl;?>?query=rocks&type=tags"><span>#rocks</span></a></li>
				<li><a class="" data-id="17" href="<?php echo $hashtagUrl;?>?query=space&type=tags"><span>#space</span></a></li>
			</ul>
		</div>
		<div id="wazzup-tag-bottom">
			<ul class="wazzup-ul-float">
				<li><a class="" data-id="41" href="<?php echo $hashtagUrl;?>?query=photography&type=tags"><span>#photography</span></a></li>
				<li><a class="" data-id="130" href="<?php echo $hashtagUrl;?>?query=dogs&type=tags"><span>#dogs</span></a></li>
				<li><a class="" data-id="11" href="<?php echo $hashtagUrl;?>?query=test&type=tags"><span>#test</span></a></li>
				<li><a class="" data-id="42" href="<?php echo $hashtagUrl;?>?query=animals&type=tags"><span>#animals</span></a></li>
				<li><a class="" data-id="25" href="<?php echo $hashtagUrl;?>?query=project&type=tags"><span>#project</span></a></li>
				<li><a class="" data-id="28" href="<?php echo $hashtagUrl;?>?query=science&type=tags"><span>#science</span></a></li>
			</ul>
		</div>
		
		
		<div style="clear:both"></div>

		<div id="giant_search_box">
		  
		  <?php
		  	// echo $this->form->setAttrib('class', '')->render($this)
		  	$query = isset( $this->query) ? $this->query : '';
		  ?>
			<form id="giant_search_form" class="" action="<?php echo $_SERVER[ 'REDIRECT_URL'] ?>" method="get">
				<input type="text" name="query" id="query" value="<?php echo $query ?>" placeholder="Search">
				<input type="hidden" name="type" value="<?php echo $_GET[ 'type' ] ?>" />

			</form>
		</div>

		<div>
		  <ul id="filter-search">
			<li style="float: left; padding-left: 21px;">
			  <a id="filter-item1" href="#" <?php echo ( $_GET[ 'type' ] == '' ) ? 'style="text-decoration: underline"' : '' ?>> All </a>
			</li>
			<li style="float: left; padding-left: 21px;">
			  <a id="filter-item2" href="#" <?php echo ( $_GET[ 'type' ] == 'whmedia_project' ) ? 'style="text-decoration: underline"' : '' ?>> Post </a>
			</li>
			<li style="float: left; padding-left: 21px;">
			  <a id="filter-item3" href="#" <?php echo ( $_GET[ 'type' ] == 'tags' ) ? 'style="text-decoration: underline"' : '' ?>> #Tags </a>
			</li>
			<li style="float: left; padding-left: 21px;">
			  <a id="filter-item4" href="#" <?php echo ( $_GET[ 'type' ] == 'favo' ) ? 'style="text-decoration: underline"' : '' ?>> Favorites </a>
			</li>
		  </ul>
		</div>


<br />
<br />
<?php 
  if( empty($this->result ) ) : 
?>
	<?php /* ?>
    <div class="tip">
      <span>
        <?php echo $this->translate( 'Please enter a search query.' ) ?>
      </span>
    </div>
    <?php */ ?>
<?php 
  else :
?>
  <?php
    if( !empty( $this->result[ 'tags' ] ) ) :
  ?> 
    <h3>RELATED #TAGS</h3>
	<?php 
		if( $this->type == '' ) :
	?>

		<span style="padding-left: 32px">
		  <?php
			foreach( $this->result[ 'tags' ] as $item ) :
			$url = 'search?query='. $item[ 'text' ] . '&type=tags';
		  ?>

				  <a id="tag" class="wazzup-anchor-all" href="<?php echo $url ?>" data-id="<?php echo $item[ 'tag_id' ] ?>">
					<span id="tag-name">#<?php echo $item[ 'text' ] ?></span>
				  </a>
				  
		  <?php endforeach; ?>

		  <?php
			if( $this->result[ 'tagsCount' ] > 5 ) :
		  ?>
				<a id="tag" class="wazzup-anchor-more" style="padding-left: 20px; font-weight: bold; font-size: 15px" href="#" data-id="<?php echo $item[ 'tag_id' ] ?>">
					<span id="tag-name"><?php echo $this->result[ 'tagsMore' ] ?> more</span>
				</a>
				  
		  <?php endif ?>
	  	</span>
	
	<?php else : ?>

      <?php
        foreach( $this->result[ 'tags' ] as $item ) :
			$url = 'search?query='. $item[ 'text' ] . '&type=tags';        	
      ?>

        <div id="hashtag-result" class="hashtag_result">
          <div class="search_info" style="float: left;width: 175px;">
            <p class="search_description">
              <a id="tag" class="wazzup-anchor" href="<?php echo $url ?>" data-id="<?php echo $item[ 'tag_id' ] ?>">
                <span id="tag-name">#<?php echo $item[ 'text' ] ?></span>
              </a>
			  <span id="follow-icon-box">
			  	<?php 
			  		$class = "follow-icon";
			  		if( $this->result[ 'isFollowed' ] ) {
			  			$class = "followed-icon";
			  		}
			  	?>
				<a id="project-<?php echo $item[ 'tag_id' ] ?>" class="smoothbox <?php echo $class ?>" href="<?php echo $this->url(array('module'=>'whmedia', 'controller' => 'tag', 'hash_id' => $item[ 'tag_id' ]), 'default', true) ?>">
				</a>
			  </span>
			</p>
          </div>

		  <div class="search_info" style="float: right;width: 175px;position: relative;top: 6px">
		    <p class="search_filter">
		      <select name="bytime" id="bytime" data-tag="cebu">
		        <option value="<?php echo $item[ 'text' ] ?>,<?php echo $this->type ?>,0" label="All Time" <?php echo ($this->filter == "0" ) ? 'selected' : '' ?> >All Time</option>
		        <option value="<?php echo $item[ 'text' ] ?>,<?php echo $this->type ?>,today" label="Today" <?php echo ($this->filter == "today" ) ? 'selected' : '' ?> >Today</option>
		        <option value="<?php echo $item[ 'text' ] ?>,<?php echo $this->type ?>,week" label="This Week" <?php echo ($this->filter == "week" ) ? 'selected' : '' ?> >This Week</option>
		        <option value="<?php echo $item[ 'text' ] ?>,<?php echo $this->type ?>,month" label="This Month" <?php echo ($this->filter == "month" ) ? 'selected' : '' ?> >This Month</option>
		        <option value="<?php echo $item[ 'text' ] ?>,<?php echo $this->type ?>,featured" label="Featured" <?php echo ($this->filter == "featured" ) ? 'selected' : '' ?>>Featured</option>
		      </select>
		    </p>
		  </div>          
        </div>      

      <?php endforeach; ?>
	  
	 <?php endif; ?>

    <?php endif;?>
  <br />
  <br />

  <?php
    if( !empty( $this->result[ 'whmedia_project' ] ) ) :
  ?>

    <span>
		<h3>MEDIA</h3>
		<?php 
			if( $this->type == '' ) :
		?>
			<a style="float: right; margin-top: -31px; color:#666; font-weight: bold" href="search?query=<?php echo $this->query ?>&type=whmedia_project">View All</a>

		<?php elseif( ( $_GET[ 'type' ] != 'whmedia_project' ) && ( $_GET[ 'type' ] != 'tags' ) ) : ?>

			<a id="view-all-tag" style="float: right; margin-top: -31px; color:#666; font-weight: bold" href="#">View All</a>

		<?php endif; ?>
	</span>

	<div class="clear:both"></div>
	<?php if( $this->result[ 'whmedia_project' ] instanceof Zend_Paginator ) : ?>

		<?php 
			foreach( $this->result[ 'whmedia_project' ] as $item ) : 
		?>
		<div class="media-browse-box-wrapper">
			<div class="media-browse-box-search">
				<div class="media-proj-img">
						<?php
							echo $this->htmlLink( 
								$item->getHref(), 
								$this->htmlImage( 
									$item->getPhotoUrl( 204 , 204, true )
								)
							);
						?> 
				</div>
				<div class="media-proj-title-search">
				<?php
					if( strlen( $item->getTitle() ) > 23 ) {
						$item[ 'title' ] = substr( $item->getTitle(), 0, 20 ). '...';
					}
					echo $this->htmlLink( $item->getHref(),
					$item[ 'title' ]
					)
				?>					
				</div>
				<div class="media-auth-info">
					<div class="media-author-thumb"></div>
				</div>
			</div>		
		</div>		
		<?php endforeach ?>
		
	<?php else: ?>

      <?php
        foreach( $this->result[ 'whmedia_project' ] as $item ) :
          $fileItem = $this->item( $item[ 'type' ], $item[ 'id' ] );
      ?>
      <div class="media-browse-box-wrapper">
		<div class="media-browse-box-search">
			<div class="media-proj-img">
                <?php
                  echo $this->htmlLink( 
                    'whmedia/view/'. $item[ 'id' ] .'/'. $item[ 'title' ] .'', 
                    $this->itemPhoto( $fileItem, 'thumb.extralarge' ) 
                    ); 
                ?>
			</div>
			<div class="media-proj-title-search">
            <?php
				if( strlen( $item[ 'title'] ) > 25 ) {
					$item[ 'title' ] = substr( $item[ 'title'], 0, 20 ). '...';
				}
                echo $this->htmlLink(
                    'whmedia/view/'. $item[ 'id' ] .'/'. $item[ 'title' ] .'',
                    $item[ 'title' ]
                );
            ?>			
			</div>
			<div class="media-auth-info">
				<div class="media-author-thumb">
				</div>
			</div>
		</div>		
	</div>
      <?php endforeach; ?>

	<?php endif; ?>
	
<?php endif ?>
  <br />

  <?php
    if( !empty( $this->result[ 'user' ] ) ) :
  ?> 

    <h3>USER</h3>
      <?php
        foreach( $this->result[ 'user' ] as $item ) :
      ?>
  		<div class="media-browse-box-wrapper">
		<div class="media-browse-box-search-none">
			<div class="media-proj-img-search">
                <?php
				$fileItem = $this->item( 'user', $item[ 'user_id' ] );
                  echo $this->htmlLink( 
                    'profile/'. $item[ 'username' ] .'', 
                    $this->itemPhoto( $fileItem, 180 ) 
                  ); 
                ?>
			</div>
			<div class="media-proj-title-search">
                  <?php 
                    /*echo $this->htmlLink(
                      'profile/'. $item[ 'username' ] .'',
                      $item[ 'displayname' ]
                    );*/
                  ?>		
			</div>
			<div class="media-auth-info">
				<div class="media-author-thumb"></div>
			</div>
		</div>		
		</div>	

      <?php endforeach; ?>

    <?php endif; ?>

    <!-- favo -->
    <?php if( !empty( $this->result[ 'favo' ] ) ) : ?>
    <h3>Favo</h3>
    <?php foreach($this->result[ 'favo' ] as $circle): ?>
   	<div class="media-browse-box-search">
		<div class="media-proj-img">
			<a href="<?php echo $this->url(array('controller' => 'favboxes', 'action' => 'menprojectlist', 'favcircle_id' => $circle["favcircle_id"]), 'default', true) ?>">
				<img src="<?php echo $circle["photos"]["thumb"]; ?>" alt="cover_photo"/>
			</a>				
		</div>
		<div class="media-proj-title-search" style="text-align:center;">
			<h2> <?php echo $circle["title"]; ?>	</h2>
		</div>
		<div class="media-auth-info">
			<div class="media-author-thumb"></div>
		</div>
	</div> 	
    <?php endforeach; ?>
	<?php endif; ?>
    <!-- favo -->



<?php endif; ?>


<?php 
  $script[ 'core' ]  = '/application/modules/Whmedia/';
  $script[ 'core' ] .= 'externals/scripts/whmedia_core.js';

  $script[ 'index' ] = '/application/modules/Core/externals/scripts/index.js';
        
  echo $this->inlineScript()
    ->appendFile($this->baseUrl() . $script[ 'core' ] )
    ->appendFile($this->baseUrl() . $script[ 'index' ] )
?>		
	</div>	
</div>
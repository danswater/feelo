<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php
$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
$logo  = $this->logo;
$route = $this->viewer()->getIdentity()
             ? array('route'=>'user_general', 'action'=>'home')
             : array('route'=>'default');

echo ($logo) ? $this->htmlLink( array( 'route' => 'mediamasonry', 'action' => 'activity-feed' ), $this->htmlImage($logo, array('alt'=>$title)), array("style" => "float:left;")) : $this->htmlLink($route, $title);
?>
<?php if( $this->viewer()->getIdentity()) :?>
<style type="text/css">
	.layout_core_menu_mini{float: right !important;  }
	.layout_core_menu_mini #core_menu_mini_menu{ padding: 0px !important;}
	.layout_core_menu_mini #core_menu_mini_menu a{ letter-spacing: 0px !important;}
	.dropdown-box{ position: absolute; background: #fff; border: 1px solid #ccc; margin-top: 40px; border-radius:5px; z-index: 100;}
	.dropdown-box table td{ padding: 5px 100px 10px 10px; border:}
	.dropdown-box table td a{ font-size: 16px;letter-spacing: 0px !important; color: #000;}
	#custom-global-search{ position: absolute; margin-left: 260px; margin-top: 13px;}
	#custom-global-search input#global_search_field{ width: 300px; height: 25px; letter-spacing: 0px; background: #f5f4f4; border:none;}
	#custom-global-search li, #custom-global-search label { letter-spacing: 0px; color:#999999;}
	#custom-global-search .overTxtLabel{ margin-top: 2px;}
	.search-icon{margin-top: -24px; margin-left: 278px; position: absolute; width: 24px; height: 20px; display: block; }
	.static-nav{ margin:0px !important; padding:0px !important;}
	.static-nav span{ color: #999999}
	.static-nav li{ padding: 0px !important; width: 75px; text-align: center;}
	.active_head_menu{ color : #000 !important;}
</style>

<?php
$active_feed = "";
$active_featured = "";
$active_trending = "";
if($this->cur_module == "whmedia" && $this->cur_controller = "index" && $this->cur_action == "activityfeed"){
	$active_feed = "-active";
}else if($this->cur_module == "user" && $this->cur_controller = "index" && $this->cur_action == "home"){
	$active_featured = "-active";
}else if($this->cur_module == "whmedia" && $this->cur_controller = "index" && $this->cur_action == "popular"){
	$active_trending = "-active";
}

?>

<div class="layout_core_menu_mini">
	<div id="core_menu_mini_menu">
		<ul class="static-nav">
			<li> 
				<a href="<?php echo $this->url(array('module' => 'whmedia', 'controller' => 'activity-feed'), 'default', true) ?>" id="custom-activity-feed"> 
					<label class="text-label text-icon<?php echo $active_feed; ?>">feed</label>
					<?php /*
					<span class="mini_menu_icon feed-icon<?php echo $active_feed; ?>"></span>
					<span style="float:left;" class="<?php echo $active_feed; ?>">my feed</span> 
					<img src="<?php echo $this->layout()->staticBaseUrl ?>application/themes/wazzap2day/images/small-menu-arrow.png"
					style="position: absolute; margin-top:3px;" />
					*/ ?>
				</a> 
			</li>
			<li>
				
				<a href="<?php echo $this->url(array('module' => 'members', 'controller' => 'home'), 'default', true) ?>">
					<label class="text-label text-icon<?php echo $active_featured; ?>">featured</label>
					<?php /*
					<span class="mini_menu_icon featured-icon<?php echo $active_featured; ?>"></span>
					<span class="<?php echo $active_featured; ?>"> featured </span>
					*/ ?>
				</a>
				
			</li>
			<li>
				
				<a href="<?php echo $this->url(array('module' => 'whmedia', 'controller' => 'popular'), 'default', true) ?>">
					<label class="text-label text-icon<?php echo $active_trending; ?>">trending</label>
					<?php /*
					<span class="mini_menu_icon trending-icon<?php echo $active_trending; ?>"></span>
					<span class="<?php echo $active_trending; ?>"> trending </span>
					*/ ?>
				</a>
				
			</li>
		</ul>
	</div>
	<div id="custom-my-feed" class="dropdown-box" style="display:none; overflow-y:scroll; max-height:350px; "></div>
	<div id="custom-global-search">
        <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <input type='text' class='text suggested' name='query' id='global_search_field' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' />
          <!-- <span class="search-icon icon-search"></span> -->
        </form>
	</div>
	<div style="position:absolute; margin-left: 535px; margin-top:14px; height:27px; width:29px; background:#83def1; border-bottom-right-radius: 5px; border-top-right-radius: 5px;">
		<a href="javascript:void(0)" id="submit_search_form_btn">
			<img  style="padding:4px 0px 0px 4px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/themes/wazzap2day/images/magnify-icon.png" border="0"/>
		</a>
	</div>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
	var href = "<?php echo $this->url(array('module' => 'whmedia', 'controller' => 'activity-feed', 'action'=>'box_id'), 'default', true) ?>"
	new Request({
		url : "<?php echo $this->url(array('module' => 'whmedia', 'controller' => 'index', 'action'=>'circles'), 'default', true) ?>",
			onProgress : function(event, xhr){
		},
		onSuccess : function(responseText, responseXML){
			var data = JSON.decode(responseText);	
			var html ="<table>";
			for(var i = 0; i < data.length; i++){
				if((i % 2) == 0) html += "<tr>";
				html += '<td>';
					html += '<a href="'+href+'/'+data[i].circle_id+'">'
						html += data[i].title;
					html += '</a>'
				html += '</td>';
				if((i % 2) == 1) html += "</tr>";
			}
			html +="</table>";
			$("custom-my-feed").set("html", html)
		}
	}).send();	
	DropDownHover('custom-activity-feed', 'custom-my-feed');
})
</script>	

<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.Request.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Observer.js"></script>
<script type="text/javascript">
    window.addEvent('domready', function() {

        var searchFormSubmit = function(e){
          if(e.key == "enter" && e.code == 13)
            $('global_search_form').submit();
        }

        $("submit_search_form_btn").addEvent("click", function(){
        	 $('global_search_form').submit();	
        })

        new Autocompleter.Request.GET('global_search_field', "<?php echo $this->url(array('controller' => 'tag', 'action'=>'suggest'), 'default', true) ?>", {
            'postVar': 'text',
            'onSelection' : function(e, select){
                $('global_search_field').set("value", select.get('text'));
            },
            onShow : function(){
              $('global_search_field').removeEvent('keydown', searchFormSubmit)
            },
            onHide : function(){
              $('global_search_field').addEvent('keydown', searchFormSubmit);
            }
        });
    });
</script>
<?php endif; ?>
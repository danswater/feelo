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
<?php /* ?>
<style type="text/css">
	.navigation .core_main_feed{float: left; }
	#nav{list-style:none; font-weight:bold; margin-bottom:10px; float: left; }
	#nav li{float:left; margin-right:10px; }
	#nav a{display:block; padding:15px 5px 5px 5px; text-decoration:none; font-weight: normal; }
	#nav a:hover{text-decoration:underline; }
	#nav ol{list-style:none; position:absolute; display: none; background:#fff; border:2px solid #f5f4f4; border-radius:5px;; }
	#nav ol li{padding-top:1px; float:none; }
	#nav ol a{white-space:nowrap; }
	#nav li:hover ol{  display: block; }
	#nav li:hover a{text-decoration:underline; }
	#nav li:hover ol a{text-decoration:none; }
	#nav li:hover ol li a:hover{font-weight: bold; }
</style>
<?php
  echo $this->navigation()
    ->menu()
    ->setContainer($this->navigation)
    ->setPartial(null)
    ->setUlClass('navigation')
    ->render();
?>
<script type="text/javascript">
	window.addEvent('domready', function() {
		//if(tElem.length == 0) return;
		var rElement = new Element('span#circled-follow');
		var href = "<?php echo $this->url(array('module' => 'whmedia', 'controller' => 'activity-feed', 'action'=>'box_id'), 'default', true) ?>"
		new Request({
			url : "<?php echo $this->url(array('module' => 'whmedia', 'controller' => 'index', 'action'=>'circles'), 'default', true) ?>",
			onProgress : function(event, xhr){

			},
			onSuccess : function(responseText, responseXML){
				var data = JSON.decode(responseText);	

				var navCreator = function(conf){
					conf = Object.merge({data : [], ulId : 'nav', start : 0, limit : 4, more : true }, conf);
					var html = '<ol id="' + conf.ulId + '">',cutStr = function(str){
						if(str.length > 20){
							str = str.substr(0, 20) + "...";
						}
						return str;
					};
					for(var i = conf.start; i < conf.data.length; i++){
						html += '<li>';
							html += '<a href="'+href+'/'+conf.data[i].circle_id+'">'
								html += cutStr(conf.data[i].title);
							html += '</a>'
						html += '</li>';
						if(conf.more && i > conf.limit){
							html += '<li>';
								html += '<a href="javascript:void(0)"> more </a>';
								conf.start = i+1;
								conf.more = false
								conf.ulId = 'sub-nav' 
								html += navCreator(conf);
							html += '</li>';		
							break;
						}
					}
					html += '</ol>';	
					return html;
				}
				var hdone = navCreator({data : data, ulId : 'nav', start : 0, limit : 4, more : true })
				rElement.set('html', hdone);

				var tElem = $$(".navigation").getElement('a.core_main_feed');
				tElem.grab(rElement, 'after')

				//var tElem = document.getElementsByClassName( 'navigation' );
				//tElem[ 0 ].insertBefore( rElement, tElem[ 0 ].firstChild );

			}
		}).send();

	});
</script>
<?php */ ?>


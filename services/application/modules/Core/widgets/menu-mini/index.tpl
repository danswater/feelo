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
<style type="text/css">
    .autocompleter-choices{background: #fff; font-size: 12px; }
    .autocompleter-choices li{padding : 5px 10px 5px 10px; border:none;}
    .autocompleter-choices li:hover{background: #888888 !important; color: #fff !important; font-size: 15px; font-weight: bold !important; }
    #profile-info{display: none; position: absolute; background: #fff; z-index: 20; width: 300px; margin-top: 5px; margin-left: -190px; border-radius:8px; border: 1px solid #ccc; }
     #profile-info ol{list-style: none; margin: 10px 0px; }
     #profile-info ol li{ padding: 8px 10px 8px 20px; }
     #profile-info ol li a{ margin-left: 20px; color: #abaaaa; font-weight: bold; }
     .custom-user-nav ul{ margin-top: 7px; }
     .custom-user-nav ul li.minwidth{ min-width: 70px}
     .custom-user-nav ul li .pdtop{ margin-top: 6px; }
     .notificationBtn{
        padding:20px 0px 10px 0px;
        margin:0px 20px 0px 20px;
     }

     .notificationBtn .notbtn{
        float: left;
      } 
      .notificationBtn .notbtn:first-child{
        border-right: 1px solid #ccc;
      }
     .notificationBtn a{
        background: #fff;
        padding: 5px 10px 5px 10px;
        width: 100px;
        display: block;
        text-align: center;
     }
     .redDot{
        background: #F20000;
        display:block;
        width: 10px;
        height: 10px;
        position: absolute;
        margin-top: 5px;

     }
</style>
<div id='core_menu_mini_menu' class="custom-user-nav">
  <?php
    // Reverse the navigation order (they're floating right)
    $count = count($this->navigation);
    foreach( $this->navigation->getPages() as $item ) $item->setOrder(--$count);
  ?>
  
  <ul>
    <?php if( $this->viewer->getIdentity()) :?>
    <li class="minwidth">
      <span onclick="toggleUpdatesPulldown(event, this, '4');" style="display: inline-block; " id="minitop_update_pulldown" class="updates_pulldown">

        <div class="pulldown_contents_wrapper">
          <div class="pulldown_contents" id="notifier_content">
            <div class="notificationBtn">
              <div class="notbtn">
                <a href="javascript:void(0)" class="notification_update" id="notification_update_href"> 
                  <?php if( $this->hasNotifications > 0 ){ ?>
                  <span class="redDot circular-mini" id="noti"></span> 
                  <?php } ?>
                  Notification</a>
              </div>
              <div class="notbtn">
                <a href="javascript:void(0)" onclick="loadRequestUpdate()" class="request_update" id="request_update_href">
                  <?php if( $this->hasRequestNot > 0 ){ ?>
                  <span class="redDot circular-mini" id="requestnot"></span>
                  <?php } ?>
                  Request</a>
              </div>
              <div style="clear:both"></div>
            </div>
            <ul class="notifications_menu" id="notifications_menu">
              <div class="notifications_loading" id="notifications_loading">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
              </div>
            </ul>
          </div>
          <div class="pulldown_options">
            <?php /*
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
               $this->translate('View All Updates'),
               array('id' => 'notifications_viewall_link')) ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
              'id' => 'notifications_markread_link',
            )) ?>
            */ ?>
          </div>
        </div>

        <img style="padding:10px 15px 0px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/themes/wazzap2day/images/bill-icon.png">
        <a href="javascript:void(0);" id="updates_toggle" class="bill_icon_updates <?php if( $this->notificationCount ):?> new_updates<?php endif;?>">
          <?php if($this->notificationCount != 0){echo $this->notificationCount; } ?>
        </a>
      </span>
    </li>
    <?php endif; ?>
    <?php if( $this->viewer->getIdentity()) {?>
    <?php /* new version added */ ?>
    <li class="minwidth">
        <a class="pdtop" href="<?php echo $this->url(array('module'=>'whmedia', 'controller' => 'project', 'action' => 'create'), 'default', true) ?>"> 
          post
        </a>
    </li>
        <?php /*
    <li>
    
        <a href="<?php echo $this->url(array('controller' => 'boxes'), 'default', true) ?>">the box</a>
        

        <a href="<?php echo $this->url(array('controller' => 'boxes'), 'default', true) ?>" id="my-dropdown-box">
          <span style="float:left">the box</span>
          <img src="<?php echo $this->layout()->staticBaseUrl ?>application/themes/wazzap2day/images/small-menu-arrow.png"
          style="position: absolute; margin-top:3px;" />
        </a>

        <div id="my-dropdown-hover" class="dropdown-box" style="display:none; margin-top:20px;">
          <table>
            <tbody>
              <tr>
                <td>
                  <a href="<?php echo $this->url(array('controller' => 'boxes'), 'default', true) ?>"> 
                    my friend box
                  </a>
                </td>
              </tr>
              <tr>
                <td>
                  <a href="<?php echo $this->url(array('controller' => 'favboxes'), 'default', true) ?>">  
                    my favorite box
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <script type="text/javascript">
          window.addEvent('domready', function() {
            DropDownHover('my-dropdown-box', 'my-dropdown-hover');
          });
        </script>

    </li>
    */ ?>
    <li class="minwidth">
        <a href="search?type=tags" class="pdtop">
          explore
        </a>
    </li>
    <li>
      <div style="cursor: pointer;" id="profile-info-button" >
        <?php echo $this->itemPhoto($this->viewer, 'thumb.icon1 circular-mini'); ?>
        <div style="float:left; font-size: 14px; padding: 8px 10px; color : #abaaaa;"> <?php echo $this->viewer->getTitle(); ?> </div>
        <img style="padding-top:10px;" src="<?php echo $this->layout()->staticBaseUrl ?>application/themes/wazzap2day/images/darrow-white.png">
      </div>
      <div id="profile-info">
          <ol>
              <li>
                <span class="custom-icon icon-human"></span> 
                <a href="<?php echo $this->url(array('controller' => 'profile', 'action'=> $this->viewer->username), 'default', true) ?>">profile</a>
              </li>
              <li>
                <span class="custom-icon icon-gear"></span>
                <a href="<?php echo $this->url(array('module'=>'members', 'controller' => 'settings', 'action'=>'profile'), 'default', true) ?>">settings</a>
              </li>
              <?php if( $this->viewer->isAdmin()){ ?>
              <li>
                <span class="custom-icon"></span>
                <a href="<?php echo $this->url(array('controller' => 'admin'), 'default', true) ?>">admin</a>
              </li>
              <?php } ?>
              <li>
                <span class="custom-icon"></span>
                <a href="<?php echo $this->url(array('controller' => 'boxes'), 'default', true) ?>"> the box</a>
              </li>
              <li>
                <span class="custom-icon"></span>
                <a href="<?php echo $this->url(array('controller' => 'favboxes'), 'default', true) ?>"> my favo</a>
              </li>
              <li>
                <span class="custom-icon icon-shutdown"></span>
                <a href="<?php echo $this->url(array('controller' => 'logout'), 'default', true) ?>">logout</a>
              </li>
          </ol>
          <div style="border-top:1px solid #EAEAEA; padding:7px;">
            <center>
            <a href="<?php echo $this->url(array('controller' => 'help', 'action' => 'privacy'), 'default', true) ?>"> Privacy </a>  - 
            <a href="<?php echo $this->url(array('controller' => 'help', 'action' => 'terms'), 'default', true) ?>"> Terms of Service </a> - 
            <a href="<?php echo $this->url(array('controller' => 'help', 'action' => 'contact'), 'default', true) ?>"> Contact </a>
            </center>
          </div>
      </div>
    </li>

    <?php /* new version added */ ?>
    <?php }else{ ?>

    <?php foreach( $this->navigation as $item ): ?>
      <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
        'class' => ( !empty($item->class) ? $item->class : null ),
        'alt' => ( !empty($item->alt) ? $item->alt : null ),
        'target' => ( !empty($item->target) ? $item->target : null ),
      ))) ?></li>
    <?php endforeach;  ?>

    <?php } ?>

    <?php /* if($this->search_check):?>
      <li id="global_search_form_container">
        <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <input type='text' class='text suggested' name='query' id='global_search_field' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' />
        </form>
      </li>
    <?php endif; */?>
  </ul>
</div>
<?php /* ?>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.Request.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Observer.js"></script>


<script type="text/javascript">
    
    window.addEvent('domready', function() {
 
        var searchFormSubmit = function(e){
          if(e.key == "enter" && e.code == 13)
            $('global_search_form').submit();
        }

        new Autocompleter.Request.GET('global_search_field', "<?php echo $this->url(array('controller' => 'tag', 'action'=>'suggest'), 'default', true) ?>", {
            'postVar': 'text',
            'onSelection' : function(e, select){
                $('global_search_field').set("value", select.get('text'));
                console.info("onSelection");
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
<?php */ ?>
<?php if( $this->viewer->getIdentity()) :?>
<script type="text/javascript">
    


    window.addEvent('domready', function() {
      var notifyPage = 1;
      var notifierLoadMore = function() {
        notifyPage++;
        new Request.HTML({
          'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
          'data' : {
            'format' : 'html',
            'page' : notifyPage
          },
          'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('notifications_menu').innerHTML += responseHTML;
          }
        }).send();
      };


      document.getElementById('notifier_content').addEventListener('scroll', function(event){
          var obj = this;
          if( obj.scrollTop == (obj.scrollHeight - obj.offsetHeight))
          {
            notifierLoadMore();
          }                                                     
      })




      $('profile-info-button').addEvent('click', function(event){
        event.stop();
        removeAllPopupOverlay();
        var belem = $('profile-info');
        if(belem.getStyle('display') == "none"){
          belem.setStyle("display", 'block');
          $("custom-wbody-overlay").setStyle('display', 'block')
        }else{
          belem.setStyle("display", 'none');
        }
      })
    });
</script>
<?php endif; ?>
<script type='text/javascript'>
   var userRequestSend = function(action, user_id, notification_id)
    {
      var url;
      if( action == 'confirm' )
      {
        url = '<?php echo $this->url(array('controller' => 'friends', 'action' => 'confirm'), 'user_extended', true) ?>';
      }
      else if( action == 'reject' )
      {
        url = '<?php echo $this->url(array('controller' => 'friends', 'action' => 'ignore'), 'user_extended', true) ?>';
      }
      else
      {
        return false;
      }

      (new Request.JSON({
        'url' : url,
        'data' : {
          'user_id' : user_id,
          'format' : 'json',
          'token' : '<?php echo $this->token() ?>'
        },
        'onSuccess' : function(responseJSON)
        {
          if( !responseJSON.status )
          {
            alert(responseJSON.error);
          }
          else
          {
            showNotifications(); // get new pull
            alert(responseJSON.messages[1]);
          }
        }
      })).send();
    }

  var notificationUpdater;

  en4.core.runonce.add(function(){
    if($('global_search_field')){
      new OverText($('global_search_field'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }

    if($('notifications_markread_link')){
      $('notifications_markread_link').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        en4.activity.hideNotifications('');
      });
    }

    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdater = new NotificationUpdateHandler({
              'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>
  });
  
  
  var activeNotifications = function(active){
    $("notification_update_href").setStyle('font-weight', 'normal');
    $("request_update_href").setStyle('font-weight', 'normal');

    if( "request" == active ){
      $("request_update_href").setStyle('font-weight', 'bold');
    }else{
      $("notification_update_href").setStyle('font-weight', 'bold');
    }
  }

  var loadRequestUpdate = function(){
    activeNotifications("request");
    showNotifications({
      "type" : "friend_follow_request"
    })
    if( $("requestnot") != null )
      $("requestnot").setStyle('display', 'none')
  }

  var toggleUpdatesPulldown = function(event, element, user_id) {
    if( event.target.className == "request_update" ) return;
    if( $("noti") != null )
      $("noti").setStyle('display', 'none')
    // mark as read
    en4.activity.hideNotifications('');

    activeNotifications();
    removeAllPopupOverlay();
    if( element.className=='updates_pulldown' ) {
      $("custom-wbody-overlay").setStyle('display', 'block')
      element.className= 'updates_pulldown_active';
      showNotifications();
    } else {
      element.className='updates_pulldown';
    }
  }

  var showNotifications = function(params) {
    en4.activity.updateNotifications();
    params = params||{};
    var data = {
      'format' : 'html',
      'page' : 1
    };
    Object.append( data, params );

    new Request.HTML({
      'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
      'data' : data,
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

          $('notifications_menu').innerHTML = responseHTML;
          $('notifications_menu').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;

            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            }else if(current_link.getParent('a').get('href')){
              forward_link = current_link.getParent('a').get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }

            /********************* old **********************
            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;
            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }
            *******************************************/
          });
        } else {
          if($('notifications_loading'))
            $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
          else
            $("notifications_menu").innerHTML = '<div id="notifications_loading" class="notifications_loading">You have no new updates.</div>';
        }
      }
    }).send();
  };

  /*
  function focusSearch() {
    if(document.getElementById('global_search_field').value == 'Search') {
      document.getElementById('global_search_field').value = '';
      document.getElementById('global_search_field').className = 'text';
    }
  }
  function blurSearch() {
    if(document.getElementById('global_search_field').value == '') {
      document.getElementById('global_search_field').value = 'Search';
      document.getElementById('global_search_field').className = 'text suggested';
    }
  }
  */
</script>
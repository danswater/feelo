<?php echo $this->partial('etc/head.tpl', array('pageTitle' => $this->pageTitle)) ?>
<?php if(!$this->count_friends): ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("You haven't followed anyone."); ?>        
        </span>
    </div>
<?php else: ?>
    <?php
	$this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/mootools.gplus.js');
	$this->headLink()->appendStylesheet($this->baseUrl() . '/application/modules/Whmedia/externals/styles/circles/all.css');
    ?>
    <script type="text/javascript">
        window.addEvent('domready', function() {
                friends_in_circle = new GroupCircle('users', 'circles');
        });
    </script><!-- /gplus -->
    <!--[if IE]><link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl(); ?>/application/modules/Whmedia/externals/styles/circles/ie.css" media="screen"/><![endif]-->
    <!--[if lt IE 9]><link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl();?>/application/modules/Whmedia/externals/styles/circles/ielt9.css" media="screen"/><![endif]-->

    <div id="infobar">
        <div id="total_friend_wrapper">
            <p><?php echo $this->translate(array("You have %s friend", "You have %s friends", $this->count_friends), ($this->count_friends)); ?></p>
        </div>
        <div id="add_circles_wrapper">
            <button name="add_circles" id="add_circles" onclick="Smoothbox.open('<?php echo ($this->baseurl()=='/'?'':$this->baseurl()); ?>/boxes/create')" style="display: none;"><?php echo $this->translate("Add Box"); ?></button>
        </div>
    </div>

    <div class='layout_right browsemembers_criteria circle_search_wrapper' >
        <?php echo $this->form; ?>
    </div>
    <div class='layout_middle circles_page_wrapper'>
        <div class="page">
            <div id="loading">
                <img src="<?php echo $this->baseUrl(); ?>/application/modules/Whmedia/externals/images/loading.gif" alt="<?php echo $this->translate("Loading..."); ?>" />               
            </div>
            <div id="users" class="b-users"></div>
            <p class="boxes-descr">Drag Profiles to your Boxes to Follow Their Feed</p>
            <div id="circles" class="circles"></div>
        </div>
    </div>
	<!--[if IE 8]><script type="text/javascript">document.namespaces.add('v', 'urn:schemas-microsoft-com:vml');</script><![endif]-->
	<!--[if IE]><v:oval strokecolor="#b4d5f8" strokeweight="0" class="vml"></v:oval><![endif]-->
    <div id="circles_pagination"></div>
<?php endif; ?>

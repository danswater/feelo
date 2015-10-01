<?php
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $fullURL = $request->getScheme() . '://' . $request->getHttpHost();
?>


<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style whmedia_share">
<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
<a class="addthis_button_tweet"></a>
<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($fullURL . $this->subject()->getHref())?>&media=<?php echo urlencode($fullURL . $this->subject()->getPhotoUrl('thumb.normal'))?>&description=<?php echo urlencode($this->subject()->getTitle())?>" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f33a4c907e62718"></script>
<!-- AddThis Button END -->

<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
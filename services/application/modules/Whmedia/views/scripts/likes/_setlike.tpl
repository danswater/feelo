<?php echo $this->render('likes/_like.tpl') ?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        var el = $('whmedia_likes_<?php echo $this->media->getIdentity() ?>');
        wh_media.tooltips.attach(el);
        el.addEvent('mouseover', function(event) { wh_media.addhover(el); });
    });
</script>
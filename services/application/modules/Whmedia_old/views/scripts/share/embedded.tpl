<?php echo $this->doctype()->__toString() ?>
<html>
    <head>
        <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />
        <script type="text/javascript" src="<?php echo $this->baseUrl() ?>/application/modules/Whmedia/externals/scripts/mootools-core-1.4.2.js"></script>
    </head>
    <body style="margin: 0px;">
      <?php echo $this->media->Embedded(); ?>
      <?php
            $Container = $this->headScript()->getContainer();
            foreach ($Container as $key => $item) {
                if (!empty ($item->attributes['src']))
                    $str_match = $item->attributes['src'];
                elseif (!empty ($item->attributes['src']))
                    $str_match = $item->attributes['source'];
                if (strstr($str_match, 'player') === false)
                    unset($Container[$key]);
        } ?>
      <?php echo $this->headScript()->setContainer($Container)->toString()."\n" ?>
    </body>
</html>
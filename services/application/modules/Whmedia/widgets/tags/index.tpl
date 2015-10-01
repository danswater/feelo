<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whmedia/externals/scripts/whmedia_core.js') ?>
<?php
    $tmp_url_world = WHMEDIA_URL_WORLD;
    $script = <<<EOF

        en4.core.runonce.add(function() {
            wh_search_project = new whmedia.search({module:'{$tmp_url_world}'});
        });
EOF;
        $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());

?>
<ul>
  <?php foreach ($this->populartags as $ptag): ?>
      <li>
          <a href='javascript:void(0);' onclick='wh_search_project.tagAction("<?php echo $ptag['text']; ?>");'><?php echo $ptag['text']?> <span><?php echo $ptag['count_tag']?></span></a>
      </li>
  <?php endforeach; ?>
</ul>


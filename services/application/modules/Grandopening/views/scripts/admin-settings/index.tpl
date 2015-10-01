<script type="text/javascript">
    function set_date(allow) {
        if (allow.get('value') == 1)
            $('grandopening_endtime-wrapper').setStyle('display', 'block');
        else
            $('grandopening_endtime-wrapper').setStyle('display', 'none');
    }
    en4.core.runonce.add(function() {
        if ($('use_date-1').get('checked'))
            $('grandopening_endtime-wrapper').setStyle('display', 'block');
    })

</script>
<h2><?php echo $this->translate('Grand Opening Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
        <div class='go_note'>	
            Note, <?php echo $this->htmlLink('pages/grandopening', 'GrandOpening Page', array('target' => '_blank')) ?> is available in Layout editor. You can modify how it looks by using widgets.
        </div>
    </div> 
</div>

<script type="text/javascript">
    en4.core.runonce.add(function() {     
        try {
            
            $('facebook-element').getElement('a').addEvent('click', function(action) {
                                                                               parent.window.location.href = $('facebook-element').getElement('a').get('href'); 
            });
            $('twitter-element').getElement('a').addEvent('click', function(action) {
                                                                               parent.window.location.href = $('twitter-element').getElement('a').get('href'); 
            });
            $('forgot-element').getElement('a').addEvent('click', function(action) {
                                                                               parent.window.location.href = $('forgot-element').getElement('a').get('href'); 
            });
            new Element('a', {href: 'javascript:void(0);',
                          'class': 'media-close-btn'})
                          .addEvent('click', function(){parent.Smoothbox.close();})
                          .inject(parent.$('TB_iframeContent'), 'before');
        }
        catch(e) {
            alert(e);
        }
    });
</script>
<?php echo $this->form ?>

<div class="join-link">
	<p>Don't Have Account yet?</p>
    <button onclick="parent.window.location='./signup';">Join now</button>
</div>

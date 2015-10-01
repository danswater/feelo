<script type="text/javascript">
   en4.core.runonce.add(function() {

    var searchWrapper = new Fx.Slide('search-wrapper',{duration:200}).hide();
    var allowDownload = new Fx.Slide('allow_download_original-wrapper',{duration:200}).hide();

    var fx = new Fx.Slide('category_id-element', {duration:200}).hide();
    $('category_id-label').addEvent('click', function(event){
        fx.toggle();
        $(this).toggleClass('active');
        allowDownload.toggle();
        searchWrapper.toggle();
        return false;
    });
    
    var fy = new Fx.Slide('auth_view-element', {duration:200}).hide();
    $('auth_view-label').addEvent('click', function(event){
        fy.toggle();
        $(this).toggleClass('active');
        return false;
    });

    var fz = new Fx.Slide('auth_comment-element', {duration:200}).hide();
    $('auth_comment-label').addEvent('click', function(event){
        fz.toggle();
        $(this).toggleClass('active');
        return false;
    });


  });

</script>
<?php
  $this->headScript()
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js');

    $script = "en4.core.runonce.add(function()
                  {
                    new Autocompleter.Request.JSON('whtags', '{$this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true)}', {
                      'postVar' : 'text',
                      'customChoices' : true,
                      'minLength': 1,
                      'selectMode': 'pick',
                      'autocompleteType': 'tag',
                      'className': 'tag-autosuggest',
                      'filterSubset' : true,
                      'multiple' : true,
                      'injectChoice': function(token){
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
                        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
                        choice.inputValue = token;
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                      }
                    });
                  });";
    $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
?>
<?php/* echo $this->content()->renderWidget('whmedia.browse-menu')*/ ?>
<?php echo $this->form->render($this) ?>
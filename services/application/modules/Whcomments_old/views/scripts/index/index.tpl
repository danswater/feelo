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

<script type="text/javascript">
    var CommentLikesTooltips;

    var showCommentsForm = function(form) {
        var slide = new Fx.Slide(form);
        slide.toggle();

        $$('#' + form + ' #body')[0].focus();
    }

    en4.core.runonce.add(function() {
        // Scroll to comment
        if (window.location.hash != '') {
            var hel = $(window.location.hash);
            if (hel) {
                window.scrollTo(hel);
            }
        }

        Array.each($$('.comment_form'), function(form) {
            var slide = new Fx.Slide(form);
            slide.hide();
            console.log(form);
            en4.core.comments.attachCreateComment(form.getChildren()[0]);
        });

        // Add hover event to get likes
        $$('.comments_comment_likes').addEvent('mouseover', function(event) {
            var el = $(event.target);
            if (!el.retrieve('tip-loaded', false)) {
                el.store('tip-loaded', true);
                el.store('tip:title', '<?php echo $this->translate('Loading...') ?>');
                el.store('tip:text', '');
                var id = el.get('id').match(/\d+/)[0];
                // Load the likes
                var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'get-likes'), 'default', true) ?>';
                var req = new Request.JSON({
                    url: url,
                    data: {
                        format: 'json',
                        type: 'core_comment',
                        id: id
                                //type : '<?php //echo $this->subject()->getType()           ?>',
                                //id : '<?php //echo $this->subject()->getIdentity()           ?>',
                                //comment_id : id
                    },
                    onComplete: function(responseJSON) {
                        el.store('tip:title', responseJSON.body);
                        el.store('tip:text', '');
                        CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
                    }
                });
                req.send();
            }
        });
        // Add tooltips
        CommentLikesTooltips = new Tips($$('.comments_comment_likes'), {
            fixed: true,
            className: 'comments_comment_likes_tips',
            offset: {
                'x': 48,
                'y': 16
            }
        });
        // Enable links
        $$('.comments_body').enableLinks();
    });
</script>

<?php
$this->headTranslate(array(
    'Are you sure you want to delete this?',
));
?>

<?php if (!$this->page): ?>
    <div class='comments' id="comments">
    <?php endif; ?>
    <div class='comments_options'>
        <?php if (isset($this->form)): ?>
            - <a href='javascript:void(0);' onclick="$('comment-form').style.display = '';
            $('comment-form').body.focus();"><?php echo $this->translate('Post Comment') ?></a>
             <?php endif; ?>
    </div>
    <?php echo $this->comments($this->comments, $this->subject) ?>
    <script type="text/javascript">
    en4.core.runonce.add(function() {
        $($('comment-form').body).autogrow();
        en4.core.comments.attachCreateComment($('comment-form'));
    });
    </script>
    <?php if (isset($this->form)) echo $this->form->setAttribs(array('id' => 'comment-form', 'style' => 'display:none;'))->render() ?>
    <?php if (!$this->page): ?>
    </div>
<?php endif; ?>
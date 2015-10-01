<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Whcomments/externals/scripts/core.js') ?>
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

            en4.whcomments.attachCreateComment(form.getChildren()[0]);
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


<div class='comments' id="comments">
    <ul class="smiles">
        <li>
            <a href="javascript:void(0)" onclick="en4.whcomments.comment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', 'happy');">
                <img src="application/modules/Whcomments/externals/images/happy.png" alt="Happy" />    
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick="en4.whcomments.comment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', 'nice');">
                <img src="application/modules/Whcomments/externals/images/nice.png" alt="Nice" />    
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick="en4.whcomments.comment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', 'omg');">
                <img src="application/modules/Whcomments/externals/images/omg.png" alt="Omg" />
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" onclick="en4.whcomments.comment('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', 'sad');">
                <img src="application/modules/Whcomments/externals/images/sad.png" alt="Sad" />    
            </a>
        </li>
    </ul>
    <script type="text/javascript">
    en4.core.runonce.add(function() {
        en4.whcomments.attachCreateComment($('comment-form'));
    });
    </script>
    <?php if (isset($this->form)) echo $this->form->setAttribs(array('id' => 'comment-form'))->render() ?>
    <?php echo $this->comments($this->comments) ?>
    
    <?php if($this->total_comments > $this->comment_limit){ ?>
        <a href="javascript:void(0)" onclick="loadMoreComment('<?php echo $this->comment_type; ?>', <?php echo $this->comment_id; ?>, <?php echo $this->comment_page; ?>)" id="load-comments" class="follower_button_1 media-follow-btn " style="width:100%; text-align:center;"> Load More </a>
    <?php } ?>
</div>
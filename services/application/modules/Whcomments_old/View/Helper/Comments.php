<?php

class Whcomments_View_Helper_Comments extends Zend_View_Helper_Abstract {

    public function comments($comments) {
        $subject =& $this->view->subject;
        $viewer = $this->view->viewer();

        $posters = array();
        $output = '';

        $canDelete = $this->view->canDelete;

        for ($i = 0; $i < count($comments); $i++) {
            if ($comments[$i]['deleted'] == 0) {
                $poster = isset($posters[$comments[$i]['poster_type']][$comments[$i]['poster_id']]) ? $posters[$comments[$i]['poster_type']][$comments[$i]['poster_id']] : $posters[$comments[$i]['poster_type']][$comments[$i]['poster_id']] = $this->view->item($comments[$i]['poster_type'], $comments[$i]['poster_id']);

                $output .= '<div class="comment_item comment_' . $comments[$i]['comment_id'] . ($comments[$i]['tree_depth'] >= 4 ? " noindent" : "") . '">
                            <div class="comment_wrapper">
                                <div class="comments_author_photo">
                                    ' . $this->view->htmlLink($poster->getHref(), $this->view->itemPhoto($poster, 'thumb.icon circular-mini', $poster->getTitle())) . '
                                </div>
                                <div class="comments_info">
                                    <span class="comments_author">
                                        ' . $this->view->htmlLink($poster->getHref(), $poster->getTitle()) . '
                                    </span>
                                    <span class="comments_body">
                                        ' . $this->view->viewMore($comments[$i]['body']) . '
                                    </span>
                                    <div class="comments_date">
                                        ' . $this->view->timestamp($comments[$i]['creation_date']);
                if ($this->view->canComment && false)
                    $output .= '- <a href="javascript:void(0)" onclick="showCommentsForm(\'comment_form_' . $comments[$i]['comment_id'] . '\')">' . $this->view->translate('Add Comment') . '</a>';
                if ($this->view->canDelete || $poster->isSelf($viewer))
                    $output .= '- <a href="javascript:void(0)" onclick="en4.whcomments.deleteComment(\'' . $subject->getType() . '\', \'' . $subject->getIdentity() . '\', \'' . $comments[$i]['comment_id'] . '\')">' . $this->view->translate('Delete') . '</a>';

                $output .= '</div>
                                </div>
                                <div class="comment_form" id="comment_form_' . $comments[$i]['comment_id'] . '">
                                    <form method="post" action="" style="" enctype="application/x-www-form-urlencoded">
                                        <textarea rows="2" cols="45" id="body" name="body"></textarea>
                                        <button type="submit" id="submit" name="submit">Post Comment</button>
                                        <input type="hidden" id="type" value="' . $comments[$i]['resource_type'] . '" name="type">
                                        <input type="hidden" id="identity" value="' . $comments[$i]['resource_id'] . '" name="identity">
                                        <input type="hidden" id="parent_id" value="' . $comments[$i]['comment_id'] . '" name="parent_id">
                                    </form>
                                </div>
                            </div>';
                if ((isset($comments[$i + 1])))
                    $output .= str_repeat('</div>', $comments[$i]['tree_depth'] - $comments[$i + 1]['tree_depth'] + 1);
                else
                    $output .= str_repeat('</div>', $comments[$i]['tree_depth'] + 1);
            } else {
                $output .= '<div class="comment_item comment_deleted' . ($comments[$i]['tree_depth'] >= 4 ? " noindent" : "") . '">' . $this->view->translate('Comment deleted');

                if ((isset($comments[$i + 1])))
                    $output .= str_repeat('</div>', $comments[$i]['tree_depth'] - $comments[$i + 1]['tree_depth'] + 1);
                else
                    $output .= str_repeat('</div>', $comments[$i]['tree_depth'] + 1);
            }
        }
        unset($posters);

        return $output;
    }

}

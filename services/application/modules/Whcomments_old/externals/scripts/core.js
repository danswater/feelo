(function() {
    var $ = 'id' in document ? document.id : window.$;

    en4.whcomments = {
        loadComments: function(type, id, page) {
            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'whcomments/comment/list',
                data: {
                    format: 'html',
                    type: type,
                    id: id,
                    page: page
                }
            }), {
                'element': $('comments')
            });
        },
        attachCreateComment: function(formElement) {
            var bind = this;
            formElement.addEvent('submit', function(event) {
                event.stop();
                var form_values = formElement.toQueryString();
                form_values += '&format=json';
                form_values += '&id=' + formElement.identity.value;
                en4.core.request.send(new Request.JSON({
                    url: en4.core.baseUrl + 'whcomments/comment/create',
                    data: form_values
                }), {
                    'element': $('comments')
                });
            });
        },
        comment: function(type, id, body) {
            en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'whcomments/comment/create',
                data: {
                    format: 'json',
                    type: type,
                    id: id,
                    body: body
                }
            }), {
                'element': $('comments')
            });
        },
        like: function(type, id, comment_id) {
            en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'whcomments/comment/like',
                data: {
                    format: 'json',
                    type: type,
                    id: id,
                    comment_id: comment_id
                }
            }), {
                'element': $('comments')
            });
        },
        unlike: function(type, id, comment_id) {
            en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'whcomments/comment/unlike',
                data: {
                    format: 'json',
                    type: type,
                    id: id,
                    comment_id: comment_id
                }
            }), {
                'element': $('comments')
            });
        },
        showLikes: function(type, id) {
            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'whcomments/comment/list',
                data: {
                    format: 'html',
                    type: type,
                    id: id,
                    viewAllLikes: true
                }
            }), {
                'element': $('comments')
            });
        },
        deleteComment: function(type, id, comment_id) {
            if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
                return;
            }
            en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'whcomments/comment/delete',
                data: {
                    format: 'json',
                    type: type,
                    id: id,
                    comment_id: comment_id
                }
            }), {
                'element': $('comments')
            });
        }
    };

})();

function loadMoreComment(type, id, page){
    en4.whcomments.loadComments(type, id, page);
}
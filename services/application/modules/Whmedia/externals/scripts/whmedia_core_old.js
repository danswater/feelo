var whpageAction = function(page) {
    $('page').value = page;
    $('filter_form').submit();
}

var whmedia = new Class({
    Implements: [Options],
    options: {
        lang: null,
        module: 'whmedia',
        controller: 'index'
    },
    initialize: function(a) {
        this.setOptions(a);
    },
    run_JSON: function(action, data, SuccessFunction) {
        new Request.JSON({
            'url': this.getURL(action),
            'data': $extend(data, {'isajax': true,
                'format': 'json'}),
            'onSuccess': function(responseObject) {
                if ($type(responseObject) != "object") {
                    alert('ERROR occurred. Please try againe.');
                    return false;
                }
                if (!responseObject.status || responseObject.status != true) {
                    if (responseObject.reload == true)
                        window.location.reload(true);
                    if (responseObject.error && $type(responseObject.error) == 'string') {
                        alert(responseObject.error);
                    }
                    return false;
                }
                if (responseObject.status == true) {
                    delete responseObject.status;
                    if (typeOf(SuccessFunction) == 'function')
                        SuccessFunction(responseObject);
                    return false;
                }
            }.bind(this)
        }).send();
    },
    getURL: function(action) {
        return en4.core.baseUrl + this.options.module + '/' + this.options.controller + '/' + action;
    }
});
/**
 * Search
 */
whmedia.search = new Class({
    Extends: whmedia,
    form: null,
    text: null,
    initialize: function(options) {
        this.parent(options);
        this.form = new Element('form', {
            id: 'filter_form',
            action: this.getURL('index'),
            method: 'post',
            target: '_parent'
        });
        this.text = new Element('input', {type: 'text'}).inject(this.form);
    },
    tagAction: function(tag) {
        this.text.set('name', 'tags').set('value', tag);
        this.send();
    },
    byTimeAction: function( tag, byTime ) {
        this.hashtag.set( 'name', 'tags' ).set( 'value', tag );
        this.text.set( 'name', 'bytime' ).set( 'value', byTime );
        this.send();
    },
    categoryAction: function(category_id) {
        this.text.set('name', 'category').set('value', category_id);
        this.send();
    },
    send: function() {
        document.body.appendChild(this.form);
        this.form.submit();
    }
});

/**
 * Project
 */
whmedia.project = new Class({
    Extends: whmedia,
    options: {
        project_id: null,
        controller: 'project'
    },
    initialize: function(options) {
        this.parent(options);
    },
    edit_media_title: function(media_id) {
        var title = $('mediatitle_' + media_id).get('value');
        new Request.JSON({
            'url': this.getURL('editmediatitle'),
            'data': {
                'media_id': media_id,
                'project_id': this.options.project_id,
                'mediatitle': title,
                'isajax': true
            },
            'onSuccess': function(responseJSON) {
                if (responseJSON.status == true) {
                    $('span_save_result').set('text', title);
                    $('save_result').setStyle('display');
                    return false;
                }
                else {
                    alert(responseJSON.error);
                }
            }.bind(this)
        }).send();
    },
    set_cover: function(media_id) {
        new Request.JSON({
            'url': this.getURL('setcover'),
            'data': {
                'media_id': media_id,
                'project_id': this.options.project_id,
                'isajax': true
            },
            'onSuccess': function(responseJSON) {
                if (responseJSON.status == true) {
                    var cover = $('project_cover');
                    if (cover != null) {
                        var make = cover.getParent('li.media_sortable');
                        make.getElement('li.cover_it').erase('style');
                        cover.dispose();
                    }
                    var media = $('media_li_' + media_id);
                    new Element('div', {
                        text: 'Cover',
                        id: 'project_cover'
                    }).inject(media);
                    media.getElement('li.cover_it').setStyle('display', 'none');
                    return false;
                }
                else {
                    alert(responseJSON.error);
                }
            }
        }).send();
    },
    reorder: function(e) {
        var steps = e.parentNode.childNodes;
        var ordering = {};
        var i = 1;
        for (var step in steps)
        {
            var child_id = steps[step].id;
            if ((child_id != undefined) && (child_id.substr(0, 9) == 'media_li_'))
            {
                ordering[child_id] = i;
                i++;
            }
        }
        new Request.JSON({
            'url': this.getURL('order'),
            'data': {
                'order_data': ordering,
                'project_id': this.options.project_id,
                'isajax': true
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.status == true) {
                    return true;
                }
                else {
                    alert(responseJSON.error);
                }
            }
        }).send();
    },
    videoURL_preview: function(input) {
        var url = input.get('value').replace(/(^\s+)|(\s+$)/g, "");
        if (url == '')
            return;
        input.set('value', '');
        Smoothbox.open(null, {mode: 'Request',
            url: en4.core.baseUrl + this.options.module + '/project/' + this.options.project_id + '/videourlpreview',
            requestMethod: 'post',
            requestData: {
                url: url
            },
            onLoad: function() {
                var th_img = $('wh_video_thumb');
                if (th_img != null) {
                    Asset.image(th_img.get('osrc'), {
                        id: 'wh_video_thumb',
                        title: 'Thumb Loading',
                        alt: 'Thumb Loading',
                        onLoad: function(img) {
                            $('wh_thumb_loading').dispose();
                            img.replaces(th_img);
                            Smoothbox.instance.doAutoResize($('global_content_simple'));
                        }
                    });
                }
            }
        });
    },
    add_video_service: function(type, code) {
        $('buttons_video').setStyle('display', 'none');
        $('saving_video').setStyle('display', 'block');
        var bind = this;
        new Request.JSON({
            'url': this.getURL('videourladd'),
            'data': {
                'type': type,
                'code': code,
                'title': $('video_title').get('value'),
                'project_id': this.options.project_id,
                'isajax': true
            },
            'onSuccess': function(responseObject) {
                if ($type(responseObject) != "object") {
                    alert('ERROR occurred. Please try againe.');
                    return;
                }
                if (!responseObject.status || responseObject.status != true) {
                    if (responseObject.error && $type(responseObject.error) == 'string') {
                        new Element('span', {'style': 'color:red;',
                            text: 'Error: ' + responseObject.error
                        }).replaces('saving_video');
                    }
                    return;
                }
                Smoothbox.close();
                uploadCount += 1;
                var count_max_files = $('count_max_files');
                if (count_max_files != null)
                    count_max_files.set('text', count_max_files.get('text') - 1);
                var vs = new Element('div', {id: 'vs_' + responseObject.media_id});
                var img_type_src = '';
                if (type == 'youtube')
                    img_type_src = 'youtube_icon.png';
                if (type == 'vimeo')
                    img_type_src = 'vimeo_icon.png';
                new Element('img', {alt: 'Site Thumb',
                    src: en4.core.baseUrl + 'application/modules/Whmedia/externals/images/' + img_type_src
                }).inject(vs);
                new Element('span', {text: responseObject.title}).inject(vs);
                var del_link = new Element('a', {text: 'delete',
                    href: 'javascript:void(0);'});
                del_link.addEvent('click', function(e) {
                    this.fileRemove(responseObject.media_id)
                }.bind(bind));
                del_link.inject(vs);
                vs.inject($('site_video_list'));
            }
        }).send();
    },
    fileRemove: function(file_id) {
        uploadCount -= 1;

        new Request.JSON({
            'url': this.getURL('removemedia'),
            'data': {
                'media_id': file_id,
                'project_id': this.options.project_id,
                'isajax': true
            },
            'onSuccess': function(responseObject) {
                if ($type(responseObject) != "object") {
                    alert('ERROR occurred. Please try againe.');
                    return;
                }
                if (!responseObject.status || responseObject.status != true) {
                    if (responseObject.error && $type(responseObject.error) == 'string') {
                        new Element('span', {'style': 'color:red;',
                            text: 'Error: ' + responseObject.error
                        }).inject($('vs_' + file_id));
                    }
                    return;
                }

                var count_max_files = $('count_max_files');
                if (count_max_files != null)
                    count_max_files.set('text', parseInt(count_max_files.get('text')) + 1);
                $('vs_' + file_id).destroy();

            }
        }).send();
    },
    delSelected: function() {
        var selected = $('step_list').getElements("input.del_check_box:checked");
        if (selected.length > 0) {
            var media_ids = new Array();
            selected.each(function(item, index) {
                media_ids.push(item.get('value'));

            });
            Smoothbox.open(null, {mode: 'Request',
                url: en4.core.baseUrl + this.options.module + '/project/' + this.options.project_id + '/delselectedmedia',
                requestMethod: 'post',
                requestData: {
                    media_ids: JSON.encode(media_ids)
                }
            });
        }
        return false;
    }

});

/**
 * Media
 */
whmedia.project.media = new Class({
    Extends: whmedia.project,
    options: {
        type: 'whmedia_media',
        module: 'whmedia',
        controller: 'likes'
    },
    tooltips: null,
    initialize: function(options) {
        this.parent(options);
        this.tooltips = new Tips($$('.' + this.options.type + '_likes'), {
            fixed: true,
            className: this.options.type + '_likes_tips',
            offset: {
                'x': 38,
                'y': -10
            }
        });
        // Add hover event to get likes
        $$('.' + this.options.type + '_likes').addEvent('mouseover', function(event) {
            this.addhover($(event.target))
        }.bind(this));
    },
    addhover: function(el) {
        if (!el.retrieve('tip-loaded', false)) {
            el.store('tip-loaded', true);
            el.store('tip:title', this.options.lang.loading);
            el.store('tip:text', '');
            var id = el.get('id').match(/\d+/)[0];
            // Load the likes
            var url = this.getURL('get-likes');
            var req = new Request.JSON({
                url: url,
                data: {
                    format: 'json',
                    type: this.options.type,
                    id: id

                },
                onComplete: function(responseJSON) {
                    el.store('tip:title', responseJSON.body);
                    el.store('tip:text', '');
                    this.tooltips.elementEnter('mouseover', el); // Force it to update the text
                }.bind(this)
            });
            req.send();
        }
    },
    like: function(media_id) {
        en4.core.request.send(new Request.JSON({
            url: this.getURL('like'),
            data: {
                format: 'json',
                type: this.options.type,
                id: media_id
            }
        }), {
            'element': $('media_like_' + media_id)
        });
    },
    unlike: function(media_id) {
        en4.core.request.send(new Request.JSON({
            url: this.getURL('unlike'),
            data: {
                format: 'json',
                type: this.options.type,
                id: media_id
            }
        }), {
            'element': $('media_like_' + media_id)
        });
    }
});

whmedia.cover_media = new Class({
    Extends: whmedia,
    options: {
        controller: 'video',
        video_id: null
    },
    slider: null,
    initialize: function(options) {
        this.parent(options);
        this.loading_image = new Asset.image('application/modules/Core/externals/images/loading.gif', {id: 'wh_loader'});
    },
    getURL: function(action) {
        return en4.core.baseUrl + this.options.module + '/' + this.options.controller + '/' + this.options.video_id + '/' + action;
    },
    get_frame: function(time) {
        var loading_image = this.loading_image;
        var frame_img = window.$('frame_img');
        var slide_container_block = window.$('slide_container_block');
        frame_img.empty();
        loading_image.inject(frame_img);
        slide_container_block.setStyle('display', 'none');
        window.$('button_get_frame').setStyle('display', 'none');
        this.run_JSON('get-frame', {'time': time}, function(response) {
            var bind = this;
            new Asset.image(response.src, {
                onload: function(j) {
                    frame_img.empty();
                    this.erase('height');
                    this.inject(frame_img);
                    slide_container_block.setStyle('display', 'block');
                    window.$('set_cover_slider').setStyle('display', 'block').removeEvents().addEvent('click', function(e) {
                        bind.set_cover(response.file_id)
                    }.bind(this))
                }
            });
        }.bind(this));
    },
    get_time_frame: function() {
        var hour = parseInt(window.$('selected_hour').get('value'));
        var minute = parseInt(window.$('selected_minute').get('value'));
        if (minute > 59) {
            minute = 59;
            window.$('selected_minute').set('value', 59);
        }
        var second = parseInt(window.$('selected_second').get('value'));
        if (second > 59) {
            second = 59;
            window.$('selected_second').set('value', 59);
        }
        var pos = 3600 * hour + 60 * minute + second;
        this.get_frame(pos);
        this.slider.setMin(pos);
    },
    set_cover: function(id) {
        var covers = window.$('all_covers');
        covers.empty();
        this.loading_image.inject(covers);
        this.run_JSON('set-cover', {'id': id}, function(response) {
            parent.wh_project.updateMediaBlock(this.options.video_id);
            new Element('div', {'class': 'global_form_popup_message',
                'text': parent.en4.core.language.translate("Video cover saved.")}).replaces(covers);
            parent.Smoothbox.instance.doAutoResize($('global_content_simple'));
            setTimeout(function()
            {
                parent.Smoothbox.close();
            }, 1000);
        }.bind(this));
    }
});

function timeFormat(input) {
    input = parseInt(input);
    if (input < 10) {
        return '0' + input;
    }
    else
        return input;
}

function ignoreDrag(e) {
    if (e && e.stopPropagation)
        e.stopPropagation()
    else
        event.cancelBubble = true
    return false;
}

function projects_viewmore(identity, addition_data, isMobile) {
    if (identity == null) {
        var projects_viewmore = $('projects_viewmore');
        var projects_loading = $('projects_loading');
        var media_browse = $('media-browse');
        var scroller_count_label = 'scroller_count';
        current_projects_page = current_projects_page + 1;
        var fun_current_projects_page = current_projects_page;
        var fun_max_projects_page = max_projects_page;
        var url = window.location.href;
        try {
            var formObjects = $('filter_form').toQueryString().parseQueryString();
            var data = $extend(formObjects, {format: 'html', page: fun_current_projects_page});
        }
        catch (e) {
            var data = {format: 'html', page: fun_current_projects_page};
        }
    }
    else {
        var projects_viewmore = $('projects_viewmore_' + identity);
        var projects_loading = $('projects_loading_' + identity);
        var media_browse = $('media-browse_' + identity);
        var scroller_count_label = 'scroller_count_' + identity;
        window['current_projects_page_' + identity] = window['current_projects_page_' + identity] + 1;
        var fun_max_projects_page = window['max_projects_page_' + identity];
        var fun_current_projects_page = window['current_projects_page_' + identity];
        var url = en4.core.baseUrl + 'widget/index/content_id/' + identity;
        var data = {format: 'html', page: fun_current_projects_page};
    }
    if (addition_data != null && typeof addition_data == 'object') {
        data = $extend(data, addition_data);
    }
    if(projects_viewmore != null) 
        projects_viewmore.setStyle('display', 'none');
    if(projects_loading != null) 
        projects_loading.setStyle('display');

    if (typeof ScrollLoaderVar != 'undefined' && ScrollLoaderVar != null) {
        ScrollLoaderVar.detach();
    }

    new Request.HTML({
        url: url,
        evalScripts: false,
        data: data,
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var new_elements = Elements.from(responseHTML);
            try {
                if (isMobile) {
                    new_elements.each(function(item) {
                        item.getElement("div.m_proj_settings_mobile").addEvent('click', function(i) {
                            i.target.toggleClass('active');
                        });
                    });
                }
            }
            catch (e) {
            }
            var imgs = new Array();
            try {
                new_elements.each(function(item) {
                    imgs.push(item.getElement('img').get('src'));
                });
            }
            catch (e) {
                alert(e);
            }
            new Asset.images(imgs, {
                'onComplete': function() {
                    new_elements.inject(media_browse);
                    Smoothbox.bind(this.pc_div);
                    if (fun_current_projects_page < fun_max_projects_page) {
                        if(projects_viewmore != null)
                            projects_viewmore.setStyle('display');
                    }
                    if(projects_loading != null)
                        projects_loading.setStyle('display', 'none');
                    media_browse.masonry({
                        singleMode: true,
                        itemSelector: '.media-browse-box'
                    });
                    if (typeof ScrollLoaderVar != 'undefined' && ScrollLoaderVar != null && (fun_current_projects_page < fun_max_projects_page)){
                        ScrollLoaderVar.attach();
                    }
                    /*
                    if (typeof ScrollLoaderVar != 'undefined' && ScrollLoaderVar != null && (fun_current_projects_page < fun_max_projects_page) && window[scroller_count_label] < 7) {
                        ScrollLoaderVar.attach();
                    }
                    if (typeof ScrollLoaderVar != 'undefined' && ScrollLoaderVar != null) {
                        window[scroller_count_label]++;
                    }
                    */
                }
            });


        }
    }).send();
}

function open_smooth(url) {
    parent.Smoothbox.instance.hideWindow();
    parent.Smoothbox.instance.position();
    parent.Smoothbox.instance.showOverlay();
    parent.Smoothbox.instance.showLoading();
    window.location.href = url;
}
var wh_leafing_instance = false;
var current_keyboard = false;

var wh_leafing = new Class({
    Implements: [Options],
    options: {
        prev: null,
        next: null
    },
    initialize: function(a) {
        //<a onclick="javascript:parent.Smoothbox.close();" href="javascript:void(0);" class="media-close-btn" ><?php echo $this->translate("Close"); ?></a>
        new Element('a', {href: 'javascript:void(0);',
            'class': 'media-close-btn'})
                .addEvent('click', function() {
            parent.Smoothbox.close();
        })
                .inject($('TB_iframeContent'), 'before');
        this.setOptions(a);
        if (wh_leafing_instance == false) {
            wh_leafing_instance = new Keyboard({active: true});
        }
        if (current_keyboard != false) {
            wh_leafing_instance.drop(current_keyboard);
            current_keyboard = false;
        }
        current_keyboard = new Keyboard({active: true});
        current_keyboard.addShortcut('prev', {
            'keys': 'left',
            'propagate': false,
            'handler': function(e) {
                this.prev(e)
            }.bind(this)
        });
        current_keyboard.addShortcut('next_space', {
            'keys': 'space',
            'propagate': false,
            'handler': function(e) {
                this.next(e)
            }.bind(this)
        });
        current_keyboard.addShortcut('next_right', {
            'keys': 'right',
            'propagate': false,
            'handler': function(e) {
                this.next(e)
            }.bind(this)
        });
        wh_leafing_instance.manage(current_keyboard);

    },
    prev: function(e) {
        if (typeOf(this.options.prev) == 'element' && this.options.prev.getPosition().x > 0) {
            this.open_smooth(this.options.prev.get('href'));
            e.stop();
        }
        else {
            this.checkKey();
        }
    },
    next: function(e) {
        if (typeOf(this.options.next) == 'element' && this.options.next.getPosition().x > 0) {
            this.open_smooth(this.options.next.get('href'));
            e.stop();
        }
        else {
            this.checkKey();
        }
    },
    checkKey: function() {
        if ((typeOf(this.options.prev) != 'element' || this.options.prev.getPosition().x == 0) && (typeOf(this.options.next) != 'element' || this.options.next.getPosition().x == 0)) {
            wh_leafing_instance.drop(current_keyboard);
        }
    },
    open_smooth: function(url) {
        Smoothbox.instance.hideWindow();
        Smoothbox.instance.position();
        Smoothbox.instance.showOverlay();
        Smoothbox.instance.showLoading();
        $('TB_iframeContent').set('src', url);
    }
});

/*
 * Edit layout
 */
try {
    var WhFancyUpload2_File = new Class({
        Extends: FancyUpload2.File,
        onRemove: function() {
            var parent = this.element.getParent('ul');
            this.element.dispose();
            if (parent.getElements('li').length <= 0) {
                parent.setStyle('display', 'none');
            }
        }
    });
}
catch (e) {
}

whmedia.edit_layout = new Class({
    Extends: whmedia,
    options: {
        controller: 'project',
        project_id: null,
        max_files: 0,
        count_files: 0,
        uploaderTemplate: '',
        is_published: 0,
        language: 'en'
    },
    sort: null,
    window_scroll: null,
    uploader: null,
    FancyUpload: null,
    initialize: function(options) {
        this.parent(options);
        this.checkOrder();
        this.init_sort();

        this.window_scroll = new Fx.Scroll(window);
    },
    init_sort: function() {
        var bind = this;
        this.sort = new Fx.Sort($$('#media_container .media_div'), {mode: 'vertical',
            transition: Fx.Transitions.Back.easeInOut,
            duration: 1000,
            onComplete: function() {
                this.rearrangeDOM();
                bind.checkOrder();

                bind.run_JSON('order', {
                    'project_id': bind.options.project_id,
                    'order': bind.getOrder()
                });
            }
        });
    },
    edit_media_title: function(media_id) {
        var media_div = $('whmedia_' + media_id);
        var div_title = media_div.getElement('div.div_title');
        media_div.getElement('.wh_edit_media_controls').setStyle('display', 'none');
        var text = div_title.get('text').trim();
        div_title.empty();
        new Element('textarea', {
            rows: 2,
            text: text
        }).inject(div_title);
        new Element('button', {type: "button",
            text: en4.core.language.translate("Save")}).addEvent('click', function() {
            media_div.getElement('.wh_edit_media_controls').setStyle('display', '');
            this.save_media_title(media_id);
        }.bind(this)
                ).inject(div_title);
        new Element('button', {type: "button",
            text: en4.core.language.translate("Cancel")}).addEvent('click', function() {
            media_div.getElement('.wh_edit_media_controls').setStyle('display', '');
            this.show_media_title(media_id);
        }.bind(this)
                ).inject(div_title);
    },
    save_media_title: function(media_id) {
        var div_title = $('whmedia_' + media_id).getElement('div.div_title');
        var title = div_title.getElement('textarea').get('value').trim();
        div_title.getElements('button').destroy();
        new Element('span', {
            text: en4.core.language.translate("Saving...")
        }).inject(div_title);
        new Request.JSON({
            'url': this.getURL('editmediatitle'),
            'data': {
                'media_id': media_id,
                'project_id': this.options.project_id,
                'mediatitle': title,
                'isajax': true
            },
            'onSuccess': function(responseJSON) {
                if (responseJSON.status == true) {
                    div_title.empty().set('text', title);
                    return false;
                }
                else {
                    alert(responseJSON.error);
                }
            }.bind(this)
        }).send();
    },
    show_media_title: function(media_id) {
        var div_title = $('whmedia_' + media_id).getElement('div.div_title');
        var text = div_title.getElement('textarea').get('text').trim();
        div_title.empty();
        div_title.set('text', text);
    },
    confirm_delete: function(media_id, is_text) {
        Smoothbox.open('', {mode: "whmediaConfirm",
            delete_media: function(smooth) {
                this.run_JSON('delmedia',
                        {'media_id': media_id,
                            'project_id': this.options.project_id
                        },
                function(response) {
                    $('whmedia_' + media_id).dispose();
                    if (is_text == false) {
                        this.options.count_files--;
                    }
                    this.init_sort();
                    this.checkOrder();
                    smooth.content.empty();
                    new Element('p', {text: en4.core.language.translate('Media deleted.')}).inject(smooth.content);
                    smooth.hideLoading();
                    smooth.showWindow();
                    smooth.onLoad();
                    setTimeout(function() {
                        smooth.close();
                    }, 1000);
                }.bind(this));
            }.bind(this)});
    },
    order: function(media_id, order) {
        var media_div = $('whmedia_' + media_id);
        var elements = $$('div.media_div');
        if (order == 'top') {
            var element_index = elements.indexOf(media_div);
            var order_array = [element_index];
            for (var key = 0; key < elements.length; key++) {
                if (key != element_index) {
                    order_array.include(key);
                }
            }
            this.sort.sort(order_array);
        }
        else if (order == 'bottom') {
            var element_index = elements.indexOf(media_div);
            var order_array = [];
            for (var key = 0; key < elements.length; key++) {
                if (key != element_index) {
                    order_array.include(key);
                }
            }
            order_array.include(element_index);
            this.sort.sort(order_array);
        }
        else if (order == 'up') {
            var one;
            for (var key = 0; key < elements.length; key++) {
                if (elements[key].get('id') == 'whmedia_' + media_id) {
                    break;
                }
                else
                    one = key;
            }
            this.sort.swap(key, one);
        }
        else if (order == 'down') {
            for (var key = 0; key < elements.length; key++) {
                if (elements[key].get('id') == 'whmedia_' + media_id) {
                    break;
                }
            }
            this.sort.swap(key + 1, key);
        }
        else
            return false;


    },
    checkOrder: function() {
        var elements = $$('div.media_div');
        var length = elements.length - 1;
        elements.each(function(item, index) {
            var links_order = item.getElement('div div.links_order');
            if (links_order) {
                links_order.getElements('a').each(function(link_item) {
                    if (link_item.hasClass('link_top')) {
                        if (index == 0) {
                            link_item.setStyle('display', 'none');
                            return;
                        }
                    }
                    if (link_item.hasClass('link_up')) {
                        if (index == 0) {
                            link_item.setStyle('display', 'none');
                            return;
                        }
                    }
                    if (link_item.hasClass('link_down')) {
                        if (index >= length) {
                            link_item.setStyle('display', 'none');
                            return;
                        }
                    }
                    if (link_item.hasClass('link_bottom')) {
                        if (index >= length) {
                            link_item.setStyle('display', 'none');
                            return;
                        }
                    }
                    link_item.setStyle('display', '');

                });
            }
        });
    },
    blockCancel: function(block) {
        block.dispose();
        this.init_sort();
        this.checkOrder();
    },
    getOrder: function(block_id) {
        var order_out = new Array();
        var medias = $$('div.media_div');
        if (medias.length > 0) {
            while (medias.length > 0) {
                var tmp = medias.shift().get('id');
                if (typeOf(block_id) == 'number' && tmp == 'whmedia_' + block_id) {
                    order_out.push('current');
                }
                else {
                    order_out.push(tmp);
                }

            }
        }
        return order_out;
    },
    isCanAddMedia: function() {
        if (this.options.max_files == 0)
            return true;
        if ((this.options.max_files - this.options.count_files) > 0)
            return true;
        return false;
    },
    addVideoServices: function(block_id) {
        if (!this.isCanAddMedia())
            return;
        var gen_block_id = Math.floor(Math.random() * (block_id + 1) * 1000) + block_id;

        var block_div = new Element('div', {
            id: 'whmedia_' + gen_block_id,
            'class': "media_div media_div_par new_media"
        });
        new Element('div', {'text': en4.core.language.translate("You can add link from other resources. Example:")}).inject(block_div);
        var ul_list = new Element('ul').inject(block_div);
        new Element('li', {'text': 'Youtube'}).inject(ul_list);
        new Element('li', {'text': 'Vimeo'}).inject(ul_list);
        new Element('li', {'text': 'Many others'}).inject(ul_list);

        new Element('input', {'type': 'text'}).inject(block_div);

        new Element('button', {type: "button",
            text: en4.core.language.translate("Get Media")}).addEvent('click', function() {
            this.getVideoServices(gen_block_id);
        }.bind(this)
                ).inject(block_div);
        new Element('button', {type: "button",
            text: en4.core.language.translate("Cancel")}).addEvent('click', function() {
            this.blockCancel(block_div);
        }.bind(this)
                ).inject(block_div);
        block_div.inject('whmedia_' + block_id, 'after');
        this.init_sort();
        this.checkOrder();

    },
    getVideoServices: function(block_id) {
        if (!this.isCanAddMedia()) {
            $('whmedia_' + block_id).dispose();
            return;
        }
        var input_url = $('whmedia_' + block_id).getElement('input');
        var url = input_url.get('value').replace(/(^\s+)|(\s+$)/g, "");
        if (url == '')
            return;
        input_url.set('value', '');

        Smoothbox.open(null, {mode: 'Request',
            url: en4.core.baseUrl + this.options.module + '/project/' + this.options.project_id + '/videourlpreview',
            requestMethod: 'post',
            requestData: {
                url: url,
                block_id: block_id
            },
            onLoad: function() {
                var th_img = $('wh_video_thumb');
                if (th_img != null) {
                    Asset.image(th_img.get('osrc'), {
                        id: 'wh_video_thumb',
                        title: 'Thumb Loading',
                        alt: 'Thumb Loading',
                        onLoad: function(img) {
                            $('wh_thumb_loading').dispose();
                            img.replaces(th_img);
                            Smoothbox.instance.doAutoResize($('global_content_simple'));
                        }
                    });
                }
            }
        });
    },
    saveVideoServices: function(type, code, block_id) {
        $('buttons_video').setStyle('display', 'none');
        $('saving_video').setStyle('display', 'block');

        var order_out = this.getOrder(block_id);

        new Request.JSON({
            'url': this.getURL('videourladd'),
            'data': {
                'type': type,
                'code': code,
                'order': order_out,
                'title': $('video_title').get('value'),
                'project_id': this.options.project_id,
                'isajax': true
            },
            'onSuccess': function(responseObject) {
                if ($type(responseObject) != "object") {
                    alert('ERROR occurred. Please try againe.');
                    return;
                }
                if (!responseObject.status || responseObject.status != true) {
                    if (responseObject.error && $type(responseObject.error) == 'string') {
                        new Element('span', {'style': 'color:red;',
                            text: 'Error: ' + responseObject.error
                        }).replaces('saving_video');
                    }
                    return;
                }
                Smoothbox.close();
                this.options.count_files++;

                Elements.from(responseObject.html).replaces('whmedia_' + block_id);
                this.init_sort();
                this.checkOrder();
            }.bind(this)
        }).send();
    },
    hideUploader: function() {
        if (this.uploader != null) {
            this.FancyUpload = null;
            this.uploader.getElement("#demo-list").getElements('li').dispose();
            this.uploader.getElement("#demo-list").setStyle('display', 'none');
            this.uploader.dispose();
            this.uploader = null;
        }
    },
    addVideo: function(block_id) {
        if (!this.isCanAddMedia())
            return;
        this.hideUploader();
        var gen_block_id = Math.floor(Math.random() * (block_id + 1) * 1000) + block_id;

        var block_div = new Element('div', {
            id: 'whmedia_' + gen_block_id,
            'class': "media_div media_div_par new_media"
        });
        this.options.uploaderTemplate.inject(block_div);
        this.init_sort();
        this.checkOrder();
        new Element('button', {type: "button",
            text: en4.core.language.translate("Close")}).addEvent('click', function() {
            this.blockCancel(block_div);
        }.bind(this)
                ).inject(block_div);
        block_div.inject('whmedia_' + block_id, 'after');
        block_div.getElements('#demo-status ul li')[0].hide();
        block_div.getElements('#demo-status ul li')[1].show();
        var order_out = this.getOrder(gen_block_id);
        this.FancyUpload = new FancyUpload2(block_div.getElement('#demo-status'), block_div.getElement('#demo-list'), {
            fileClass: WhFancyUpload2_File,
            verbose: false,
            multiple: false,
            appendCookieData: true,
            path: en4.core.baseUrl + 'externals/fancyupload/Swiff.Uploader.swf',
            target: $('demo-browse'),
            fileSizeMax: this.options.fileSizeMax,
            typeFilter: {'videos': '*.mpeg; *.mp4; *.mkv; *.mpg; *.mpe; *.qt; *.mov; *.avi; '},
            fileListMax: this.options.max_files - this.options.count_files,
            url: this.getURL('upload'),
            data: {
                'project_id': this.options.project_id,
                'isajax': true,
                'order': JSON.encode(order_out)
            },
            onLoad: function() {
                var fallback = block_div.getElement('#demo-fallback');
                if (fallback == null)
                    return;
                block_div.getElement('#demo-status').removeClass('hide'); // we show the actual UI
                fallback.destroy(); // ... and hide the plain form

                // We relay the interactions with the overlayed flash to the link
                this.target.addEvents({
                    click: function() {
                        return false;
                    },
                    mouseenter: function() {
                        this.addClass('hover');
                    },
                    mouseleave: function() {
                        this.removeClass('hover');
                        this.blur();
                    },
                    mousedown: function() {
                        this.focus();
                    }
                });

            },
            onFail: function(error) {
                switch (error) {
                    case 'hidden': // works after enabling the movie and clicking refresh
                        alert(en4.core.language.translate('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).'));
                        break;
                    case 'blocked': // This no *full* fail, it works after the user clicks the button
                        alert(en4.core.language.translate("To enable the embedded uploader, enable the blocked Flash movie (see Flashblock)."));
                        break;
                    case 'empty': // Oh oh, wrong path
                        alert(en4.core.language.translate("A required file was not found, please be patient and we'll fix this."));
                        break;
                    case 'flash': // no flash 9+
                        alert(en4.core.language.translate("To enable the embedded uploader, install the latest Adobe Flash plugin."));
                }
            },
            // Edit the following lines, it is your custom event handling

            /**
             * Is called when files were not added, "files" is an array of invalid File classes.
             *
             * This example creates a list of error elements directly in the file list, which
             * hide on click.
             */
            onSelectFail: function(files) {
                files.each(function(file) {
                    new Element('li', {
                        'class': 'validation-error',
                        html: file.validationErrorMessage || file.validationError,
                        title: MooTools.lang.get('FancyUpload', 'removeTitle'),
                        events: {
                            click: function() {
                                this.destroy();
                                if (block_div.getElement("#demo-list").getElements('li').length <= 0) {
                                    block_div.getElement("#demo-list").setStyle('display', 'none');
                                }
                            }
                        }
                    }).inject(this.list, 'top');
                }, this);
                this.list.setStyle('display', 'block');
                block_div.getElement("#demo-status-current").setStyle('display', 'none');
                block_div.getElement("#demo-status-overall").setStyle('display', 'none');

            },
            onComplete: function hideProgress() {
                block_div.getElement("#demo-status-current").setStyle('display', 'none');
                block_div.getElement("#demo-status-overall").setStyle('display', 'none');
            },
            onFileStart: function() {
                block_div.getElement("#demo-browse").setStyle('display', 'none');
            },
            onFileRemove: function(file) {
                if (block_div.getElement("#demo-list").getElements('li').length <= 0) {
                    block_div.getElement("#demo-list").setStyle('display', 'none');
                }

            },
            onSelectSuccess: function(file) {
                block_div.getElement("#demo-list").setStyle('display', 'block');

                block_div.getElement("#demo-status-current").setStyle('display', 'block');
                block_div.getElement("#demo-status-overall").setStyle('display', 'block');
                this.start();
            },
            /**
             * This one was directly in FancyUpload2 before, the event makes it
             * easier for you, to add your own response handling (you probably want
             * to send something else than JSON or different items).
             */
            onFileSuccess: function(file, response) {
                var json = new Hash(JSON.decode(response, true) || {});
                if (json.get('status') == '1') {
                    this.options.count_files++;
                    Elements.from(json.html).inject(block_div, 'before');
                    file.onRemove();
                    this.init_sort();
                    this.checkOrder();
                    this.window_scroll.stop().toElement(block_div);
                    // show the html code element and populate with uploaded image html
                    block_div.hasClass('new_media') ? block_div.destroy() : '';
                    $$('.swiff-uploader-box').destroy();
                } else {
                    file.element.addClass('file-failed');
                    file.info.set('html', '<span>An error occurred:</span> ' + (json.get('error') ? (json.get('error')) : response));
                }
            }.bind(this)
        });
        this.uploader = block_div;
        //this.FancyUpload.reposition();
    },
    addImage: function(block_id) {
        if (!this.isCanAddMedia())
            return;
        this.hideUploader();
        var gen_block_id = Math.floor(Math.random() * (block_id + 1) * 1000) + block_id;

        var block_div = new Element('div', {
            id: 'whmedia_' + gen_block_id,
            'class': "media_div media_div_par new_media"
        });
        this.options.uploaderTemplate.inject(block_div);
        this.init_sort();
        this.checkOrder();
        new Element('button', {type: "button",
            text: en4.core.language.translate("Close")}).addEvent('click', function() {
            this.blockCancel(block_div);
        }.bind(this)
                ).inject(block_div);
        block_div.inject('whmedia_' + block_id, 'after');
        block_div.getElements('#demo-status ul li')[1].hide();
        block_div.getElements('#demo-status ul li')[0].show();
        var order_out = this.getOrder(gen_block_id);
        this.FancyUpload = new FancyUpload2(block_div.getElement('#demo-status'), block_div.getElement('#demo-list'), {
            fileClass: WhFancyUpload2_File,
            verbose: false,
            multiple: false,
            appendCookieData: true,
            path: en4.core.baseUrl + 'externals/fancyupload/Swiff.Uploader.swf',
            target: block_div.getElement('#demo-browse'),
            fileSizeMax: this.options.fileSizeMax,
            typeFilter: {'images': '*.jpg; *.jpeg; *.gif; *.png'},
            fileListMax: this.options.max_files - this.options.count_files,
            url: this.getURL('upload'),
            data: {
                'project_id': this.options.project_id,
                'isajax': true,
                'order': JSON.encode(order_out)
            },
            onLoad: function() {
                var fallback = block_div.getElement('#demo-fallback');
                if (fallback == null)
                    return;
                block_div.getElement('#demo-status').removeClass('hide'); // we show the actual UI
                fallback.destroy(); // ... and hide the plain form

                // We relay the interactions with the overlayed flash to the link
                this.target.addEvents({
                    click: function() {
                        return false;
                    },
                    mouseenter: function() {
                        this.addClass('hover');
                    },
                    mouseleave: function() {
                        this.removeClass('hover');
                        this.blur();
                    },
                    mousedown: function() {
                        this.focus();
                    }
                });

            },
            onFail: function(error) {
                switch (error) {
                    case 'hidden': // works after enabling the movie and clicking refresh
                        alert(en4.core.language.translate('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).'));
                        break;
                    case 'blocked': // This no *full* fail, it works after the user clicks the button
                        alert(en4.core.language.translate("To enable the embedded uploader, enable the blocked Flash movie (see Flashblock)."));
                        break;
                    case 'empty': // Oh oh, wrong path
                        alert(en4.core.language.translate("A required file was not found, please be patient and we'll fix this."));
                        break;
                    case 'flash': // no flash 9+
                        alert(en4.core.language.translate("To enable the embedded uploader, install the latest Adobe Flash plugin."));
                }
            },
            // Edit the following lines, it is your custom event handling

            /**
             * Is called when files were not added, "files" is an array of invalid File classes.
             *
             * This example creates a list of error elements directly in the file list, which
             * hide on click.
             */
            onSelectFail: function(files) {
                files.each(function(file) {
                    new Element('li', {
                        'class': 'validation-error',
                        html: file.validationErrorMessage || file.validationError,
                        title: MooTools.lang.get('FancyUpload', 'removeTitle'),
                        events: {
                            click: function() {
                                this.destroy();
                                if (block_div.getElement("#demo-list").getElements('li').length <= 0) {
                                    block_div.getElement("#demo-list").setStyle('display', 'none');
                                }
                            }
                        }
                    }).inject(this.list, 'top');
                }, this);
                this.list.setStyle('display', 'block');
                block_div.getElement("#demo-status-current").setStyle('display', 'none');
                block_div.getElement("#demo-status-overall").setStyle('display', 'none');

            },
            onComplete: function hideProgress() {
                block_div.getElement("#demo-status-current").setStyle('display', 'none');
                block_div.getElement("#demo-status-overall").setStyle('display', 'none');
            },
            onFileStart: function() {
                block_div.getElement("#demo-browse").setStyle('display', 'none');
            },
            onFileRemove: function(file) {
                if (block_div.getElement("#demo-list").getElements('li').length <= 0) {
                    block_div.getElement("#demo-list").setStyle('display', 'none');
                }

            },
            onSelectSuccess: function(file) {
                block_div.getElement("#demo-list").setStyle('display', 'block');

                block_div.getElement("#demo-status-current").setStyle('display', 'block');
                block_div.getElement("#demo-status-overall").setStyle('display', 'block');
                this.start();
            },
            /**
             * This one was directly in FancyUpload2 before, the event makes it
             * easier for you, to add your own response handling (you probably want
             * to send something else than JSON or different items).
             */
            onFileSuccess: function(file, response) {
                var json = new Hash(JSON.decode(response, true) || {});
                if (json.get('status') == '1') {
                    Elements.from(json.html).inject(block_div, 'before');
                    file.onRemove();
                    this.init_sort();
                    this.checkOrder();
                    this.window_scroll.stop().toElement(block_div);
                    // show the html code element and populate with uploaded image html
                    block_div.hasClass('new_media') ? block_div.destroy() : '';
                    $$('.swiff-uploader-box').destroy();
                } else {
                    file.element.addClass('file-failed');
                    file.info.set('html', '<span>An error occurred:</span> ' + (json.get('error') ? (json.get('error')) : response));
                }
            }.bind(this)
        });
        this.uploader = block_div;
    },
    publishToggle: function() {
        this.options.is_published = (this.options.is_published) ? 0 : 1;
        this.run_JSON('publish', {
            'project_id': this.options.project_id,
            'is_published': this.options.is_published
        });
        $('button_publish').set('text', en4.core.language.translate(this.options.is_published ? 'Unpublish' : 'Publish'));
    },
    set_cover: function(media_id) {
        this.run_JSON('setcover',
                {
                    'media_id': media_id,
                    'project_id': this.options.project_id
                },
        function(responseJSON) {
            var el_cover = $('media_container').getElement('div.project-cover');
            if (el_cover != null) {
                el_cover.removeClass('project-cover');
                el_cover.getElement('span.project-cover-caption').setStyle('display', 'none');
            }
            var curr_cover = $('whmedia_' + media_id).getElement('div.media_content');
            curr_cover.addClass('project-cover');
            curr_cover.getElement('span.project-cover-caption').setStyle('display', '');
            $$('a.set_as_cover').setStyle('display', '');
            $('whmedia_' + media_id).getElement('a.set_as_cover').setStyle('display', 'none')
        });
    },
    updateMediaBlock: function(block_id) {
        this.run_JSON('get-media-content',
                {
                    'project_id': this.options.project_id,
                    'media_id': block_id
                },
        function(response) {
            var elements = Elements.from(response.html);
            elements.replaces('whmedia_' + block_id);
            this.init_sort();
            this.checkOrder();
            elements.getElements('.smoothbox').each(function(item) {
                Smoothbox.bind(item);
            })

            var el = Elements.from(response.html, false).getElement('script');
            eval(el[0].get('text'));
        }.bind(this));

    },
    addURL: function(block_id) {
        if (!this.isCanAddMedia())
            return;
        var gen_block_id = Math.floor(Math.random() * (block_id + 1) * 1000) + block_id;

        var block_div = new Element('div', {
            id: 'whmedia_' + gen_block_id,
            'class': "media_div media_div_par new_media"
        });
        new Element('div', {'text': en4.core.language.translate("Add a link to the website:")}).inject(block_div);

        new Element('input', {'type': 'text'}).inject(block_div);

        new Element('button', {type: "button",
            text: en4.core.language.translate("Get Content")}).addEvent('click', function() {
            this.getURLContent(gen_block_id);
        }.bind(this)
                ).inject(block_div);
        new Element('button', {type: "button",
            text: en4.core.language.translate("Cancel")}).addEvent('click', function() {
            this.blockCancel(block_div);
        }.bind(this)
                ).inject(block_div);
        block_div.inject('whmedia_' + block_id, 'after');
        this.init_sort();
        this.checkOrder();

    },
    getURLContent: function(block_id) {
        if (!this.isCanAddMedia()) {
            $('whmedia_' + block_id).dispose();
            return;
        }
        var input_url = $('whmedia_' + block_id).getElement('input');
        var url = input_url.get('value').replace(/(^\s+)|(\s+$)/g, "");
        if (url == '')
            return;
        input_url.set('value', '');

        Smoothbox.open(null, {mode: 'Request',
            url: en4.core.baseUrl + this.options.module + '/project/' + this.options.project_id + '/get-url-content',
            requestMethod: 'post',
            requestData: {
                url: url,
                block_id: block_id,
                format: 'smoothbox'
            },
            onLoad: function() {
                wh_link.init();
            }
        });
    },
    saveURL: function(data, block_id) {
        $('buttons_video').setStyle('display', 'none');
        $('saving_video').setStyle('display', 'block');

        var order_out = this.getOrder(block_id);
        this.run_JSON('save-url',
                $extend(data, {'project_id': this.options.project_id,
            'order': order_out}),
        function(response) {
            Smoothbox.close();
            this.options.count_files++;

            Elements.from(response.html).replaces('whmedia_' + block_id);
            this.init_sort();
            this.checkOrder();
        }.bind(this));
    }
});

Smoothbox.Modal.whmediaConfirm = new Class({
    Extends: Smoothbox.Modal,
    element: false,
    load: function()
    {
        if (this.content)
        {
            return;
        }

        this.parent();

        this.content = new Element('div', {
            id: 'TB_ajaxContent'
        });
        this.content.inject(this.window);

        new Element('h3', {text: en4.core.language.translate('Delete?')}).inject(this.content);
        new Element('p', {text: en4.core.language.translate('Are you sure that you want to delete it? It will not be recoverable after being deleted.')}).inject(this.content);
        var buttons = new Element('div', {'class': 'confirm_buttons'});
        new Element('button', {type: "button",
            text: en4.core.language.translate("Delete")}).addEvent('click', function() {
            this.showLoading();
            this.hideWindow();
            this.options['delete_media'](this);
        }.bind(this)
                ).inject(buttons);
        new Element('button', {type: "button",
            text: en4.core.language.translate("Cancel")}).addEvent('click', function() {
            this.close();
        }.bind(this)
                ).inject(buttons);
        buttons.inject(this.content)

        this.hideLoading();
        this.showWindow();
        this.onLoad();
    },
    setOptions: function(options)
    {
        this.element = $(options.element);
        this.parent(options);
    }

});


// Avoiding MooTools.lang dependency
(function() {
    var phrases = {
        'progressOverall': 'Overall Progress ({total})',
        'currentTitle': 'File Progress',
        'currentFile': 'Uploading "{name}"',
        'currentProgress': 'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.',
        'fileName': '{name}',
        'remove': 'Remove',
        'removeTitle': 'Click to remove this entry.',
        'fileError': 'Upload failed',
        'validationErrors': {
            'duplicate': '{name} already added.',
            'sizeLimitMin': '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
            'sizeLimitMax': '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
            'fileListMax': '{name} file can not be uploaded. It is over the limit.',
            'fileListSizeMax': '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.'
        },
        'errors': {
            'httpStatus': 'Server returned HTTP-Status <code>#{code}</code>',
            'securityError': 'Security error occurred ({text})',
            'ioError': 'Error caused a send or load operation to fail ({text})'
        }
    };

    // en4 hack
    if (('en4' in window) && $type(en4) && $type(en4.core.language)) {
        $H(phrases).each(function(value, key) {
            if ($type(value) == 'string') {
                phrases[key] = en4.core.language.translate(value);
            } else if ($type(value) == 'object') {
                $H(value).each(function(pvalue, pkey) {
                    if ($type(value) == 'string') {
                        phrases[key][pkey] = en4.core.language.translate(pvalue);
                    }
                });
            }
        });
    }

    if (MooTools.lang) {
        MooTools.lang.set('en-US', 'FancyUpload', phrases);
    } else {
        MooTools.lang = {
            data: {
                'FancyUpload': phrases
            },
            get: function(from, key) {
                return this.data[from][key];
            },
            set: function(locale, from, data) {
                data[from] = data;
            }
        };
    }
})();

(function() {

    this.ScrollLoader = new Class({
        Implements: [Options, Events],
        options: {
            /*onScroll: $empty,*/
            area: null,
            mode: 'vertical',
            container: null
        },
        initialize: function(options) {
            this.setOptions(options);
            if (options.area == null) {
                this.options.area = this.getViewportSize().height + 100;
            }
            this.bound = {scroll: this.scroll.bind(this)};
            this.container = document.id(this.options.container) || window;
            this.attach();
        },
        attach: function() {
            this.container.addEvent('scroll', this.bound.scroll);
            return this;
        },
        detach: function() {
            this.container.removeEvent('scroll', this.bound.scroll);
            return this;
        },
        scroll: function() {
            var z = this.options.mode == 'vertical' ? 'y' : 'x';

            var size = this.container.getSize()[z],
                    scroll = this.container.getScroll()[z],
                    scrollSize = this.container.getScrollSize()[z];

            if (scroll + size < scrollSize - this.options.area)
                return;

            this.fireEvent('scroll');
        },
        getViewportSize: function() {
            var size = {};

            if (typeof window.innerWidth != 'undefined') {
                size.width = window.innerWidth,
                        size.height = window.innerHeight
            }
            else if (typeof document.documentElement != 'undefined'
                    && typeof document.documentElement.clientWidth !=
                    'undefined' && document.documentElement.clientWidth != 0) {
                size.width = document.documentElement.clientWidth,
                        size.height = document.documentElement.clientHeight
            } else {
                size.width = document.getElementsByTagName('body')[0].clientWidth,
                        size.height = document.getElementsByTagName('body')[0].clientHeight
            }

            return size;
        }

    });

})();

whmedia.project_likes = new Class({
    Extends: whmedia,
    options: {
        module: 'whmedia',
        controller: 'project-likes'
    },
    initialize: function(options) {
        this.parent(options);

    },
    togglelike: function(project_id, callback) {
        this.run_JSON('toggle-like', {'id': project_id}, function(response) {
            var project_div = $('project_' + project_id);
            if(project_div != null){
                if (response.islike){
                    project_div.getElement('a.media-like-icon').addClass('media-unlike');
                }else
                    project_div.getElement('a.media-like-icon').removeClass('media-unlike');
                project_div.getElement('span.media-likes').set('text', en4.core.language.translate("whLikes: %d", response.count_likes));
            }
            if(typeof callback != "undefined") callback(response);

        });
    }
});

whmedia.project_follow = new Class({
    Extends: whmedia,
    current_follow_user: null,
    options: {
        module: 'whmedia',
        controller: 'follow'
    },
    initialize: function(options) {
        this.parent(options);

    },
    toggle_box: function(a, box_id) {
        this.run_JSON('toggle-box', {'box_id': box_id, 'user_id': this.current_follow_user}, function(response) {
            try {
                if (response.inbox) {
                    a.addClass('a_box');
                }
                else {
                    a.removeClass('a_box');
                }

            }
            catch (e) {
            }
        });
    },
    togglefollow: function(user_id) {
        var a_follower_button = $$('a.follower_button_' + user_id).pick();
        if (!a_follower_button.hasClass('unfollow')) {
            this.current_follow_user = user_id;

            try{
                var div_boxes = $('div_boxes').clone();
                div_boxes.setStyle('display', '');
                var add2boxes_img = div_boxes.getElement('div.add2boxes_img');

                try {
                    var parent_div = a_follower_button.getParent('div.media-author-thumb');
                    parent_div.getElement('img.item_photo_user').getParent('a').clone().inject(add2boxes_img).getElement('img.item_photo_user').setStyles({'max-width': '48px'});
                    parent_div.getParent('div.media-about-author').getElement('div.media-author-info').getElement('a').clone().inject(add2boxes_img);
                }
                catch (e) {
                    try {
                        var parent_div = a_follower_button.getParent('div.follow_user_thumb');
                        parent_div.getElement('img.item_photo_user').getParent('a').clone().inject(add2boxes_img).getElement('img.item_photo_user').setStyles({'max-width': '48px'});
                        parent_div.getParent('div').getElement('div.follow_user_info').getElement('a').clone().inject(add2boxes_img);
                    }
                    catch (er) {
                        try {
                            var parent_div = a_follower_button.getParent('li');
                            parent_div.getElement('img.item_photo_user').getParent('a').clone().inject(add2boxes_img).getElement('img.item_photo_user').setStyles({'max-width': '48px'});
                            parent_div.getElement('div.follow-member-name').getElement('a').clone().inject(add2boxes_img);
                        }
                        catch (error) {
                            try {
                                $('profile_photo').getElement('img').clone().inject(add2boxes_img).setStyles({'max-width': '48px'});
                                new Element('span', {'text': $('profile_status').getElement('h2').get('text')}).inject(add2boxes_img);
                            }
                            catch (error1) {
                            }
                        }
                    }
                }
                if (typeOf(div_boxes) == 'element') {
                    Smoothbox.open(div_boxes, {mode: 'Inline'});
                }
            } catch( e ) { console.log( e ); }

        }
        this.run_JSON('toggle-follow', {'user_id': user_id}, function(response) {
            try {
                if (response.isfollow) {
                    $$('a.follower_button_' + user_id).addClass('unfollow').set('text', en4.core.language.translate("unFollow"));
                }
                else {
                    $$('a.follower_button_' + user_id).removeClass('unfollow').set('text', en4.core.language.translate("Follow"));
                }
                $$('strong.count_follower_' + user_id).set('text', response.count_following);
            }
            catch (e) {
            }
        });
    },
    togglefeatured: function(user_id) {
        this.run_JSON('toggle-featured', {'user_id': user_id}, function(response) {
            try {
                if (response.isfeatured) {
                    $('featured_user_' + user_id).removeClass('featured-member').addClass('unfeatured-member');
                }
                else {
                    $('featured_user_' + user_id).removeClass('unfeatured-member').addClass('featured-member');
                }
            }
            catch (e) {
            }
        });
    }
});

ToTopScroller = new Class({
    element: null,
    scroll_element: null,
    show_stickly: false,
    initialize: function(el, sc_el) {
        if (typeOf(el) == 'element') {
            this.element = el;
        }
        else if (typeOf(el) == 'string') {
            this.element = $(el);
        }
        else
            return;
        if (typeOf(sc_el) == 'element') {
            this.scroll_element = sc_el;
        }
        else if (typeOf(sc_el) == 'string') {
            this.scroll_element = $(sc_el);
        }
        else
            return;
        window.addEvent('scroll', function(e) {
            this.check(e);
        }.bind(this));
        this.scroll_element.addEvent('click', function() {
            new Fx.Scroll(window).toTop();
        });


    },
    check: function(e) {
        var h_start = this.element.getPosition().y;
        if (h_start < window.getScroll().y && !this.show_stickly) {
            this.show_stickly = true;
            this.showBox();
            return;
        }
        if (h_start > window.getScroll().y && this.show_stickly) {
            this.show_stickly = false;
            this.hideBox();
            return;
        }
    },
    showBox: function() {
        this.scroll_element.setStyle('display', '');
    },
    hideBox: function() {
        this.scroll_element.setStyle('display', 'none');
    }
});

var URL_content = new Class({
    Implements: [Events, Options],
    options: {
        title: null,
        description: null,
        thumb: null,
        images: null,
        url: null,
        imageMaxAspect: (10 / 3),
        imageMinAspect: (3 / 10),
        imageMinSize: 48,
        imageMaxSize: 5000,
        imageMinPixels: 2304,
        imageMaxPixels: 1000000
    },
    images: null,
    imageCount: null,
    currentIndex: null,
    thumbs_show: null,
    thumb: null,
    initialize: function(options) {
        this.setOptions(options);
    },
    init: function() {
        if (this.options.thumb == null && this.options.images != null) {
            this.thumbs_show = window.$('thumbs_show');
            this.count_images = window.$('count_images');
            this.current_images = window.$('current_images');
            this.thumb_prev = window.$('thumb_prev');
            this.thumb_next = window.$('thumb_next');
            this.thumbs_preview = window.$('thumbs_preview');
            this.manage_thumbs = window.$('manage_thumbs');
            this.images = new Asset.images(this.options.images, {
                'properties': {
                    'class': 'whmedia-link-image',
                    'style': 'max-width:350px;max-height:250px;'
                },
                'onComplete': function() {
                    this.show_thumbs();
                }.bind(this)
            });

        }
    },
    show_thumbs: function() {
        var images = this.images.filter(function(item, index) {
            return this.checkImageValid(item);
        }, this);
        if (images.length > 0) {
            this.images.each(function(item, index) {
                item.set('width', 'auto');
                item.set('height', 'auto');
            });
            this.imageCount = images.length;
            this.images = images;
            this.count_images.set('text', this.imageCount);
            this.thumb_prev.setStyle('display', 'none');
            if (this.imageCount == 1) {
                this.thumb_next.setStyle('display', 'none');
            }
            this.currentIndex = 0;
            this.show_current_image();
        }
        else {
            this.thumbs_preview.dispose();
        }
        window.$('loading_thumbs').dispose();
        this.thumbs_preview.setStyle('display', '');
    },
    prev: function() {
        this.currentIndex = this.currentIndex - 1;
        if (this.currentIndex <= 0) {
            this.currentIndex = 0;
            this.thumb_prev.setStyle('display', 'none');
        }
        if (this.imageCount > 1) {
            this.thumb_next.setStyle('display', 'inline');
        }
        this.show_current_image();
    },
    next: function() {
        this.currentIndex = this.currentIndex + 1;
        if (this.currentIndex >= (this.imageCount - 1)) {
            this.currentIndex = this.imageCount - 1;
            this.thumb_next.setStyle('display', 'none');
        }
        if (this.imageCount > 1) {
            this.thumb_prev.setStyle('display', 'inline');
        }
        this.show_current_image();
    },
    show_current_image: function() {
        this.thumbs_show.empty();
        this.images[this.currentIndex].inject(this.thumbs_show);
        this.current_images.set('text', this.currentIndex + 1);
        this.thumb = this.images[this.currentIndex].get('src');
    },
    isShow: function(checkbox) {
        if (checkbox.checked) {
            this.manage_thumbs.setStyle('display', 'none');
            this.thumb = null;
        }
        else {
            this.manage_thumbs.setStyle('display', 'block');
            this.thumb = this.images[this.currentIndex].get('src');
        }
    },
    checkImageValid: function(element) {
        var size = element.getSize();
        var sizeAlt = {x: element.width, y: element.height};
        var width = sizeAlt.x || size.x;
        var height = sizeAlt.y || size.y;
        var pixels = width * height;
        var aspect = width / height;

        // Debugging
        if (this.options.debug) {
            console.log(element.get('src'), sizeAlt, size, width, height, pixels, aspect);
        }

        // Check aspect
        if (aspect > this.options.imageMaxAspect) {
            // Debugging
            if (this.options.debug) {
                console.log('Aspect greater than max - ', element.get('src'), aspect, this.options.imageMaxAspect);
            }
            return false;
        } else if (aspect < this.options.imageMinAspect) {
            // Debugging
            if (this.options.debug) {
                console.log('Aspect less than min - ', element.get('src'), aspect, this.options.imageMinAspect);
            }
            return false;
        }
        // Check min size
        if (width < this.options.imageMinSize) {
            // Debugging
            if (this.options.debug) {
                console.log('Width less than min - ', element.get('src'), width, this.options.imageMinSize);
            }
            return false;
        } else if (height < this.options.imageMinSize) {
            // Debugging
            if (this.options.debug) {
                console.log('Height less than min - ', element.get('src'), height, this.options.imageMinSize);
            }
            return false;
        }
        // Check max size
        if (width > this.options.imageMaxSize) {
            // Debugging
            if (this.options.debug) {
                console.log('Width greater than max - ', element.get('src'), width, this.options.imageMaxSize);
            }
            return false;
        } else if (height > this.options.imageMaxSize) {
            // Debugging
            if (this.options.debug) {
                console.log('Height greater than max - ', element.get('src'), height, this.options.imageMaxSize);
            }
            return false;
        }
        // Check  pixels
        if (pixels < this.options.imageMinPixels) {
            // Debugging
            if (this.options.debug) {
                console.log('Pixel count less than min - ', element.get('src'), pixels, this.options.imageMinPixels);
            }
            return false;
        } else if (pixels > this.options.imageMaxPixels) {
            // Debugging
            if (this.options.debug) {
                console.log('Pixel count greater than max - ', element.get('src'), pixels, this.options.imageMaxPixels);
            }
            return false;
        }

        return true;
    },
    getData: function() {
        return {'title': this.options.title,
            'description': this.options.description,
            'thumb': this.thumb,
            'url': this.options.url};
    }

});
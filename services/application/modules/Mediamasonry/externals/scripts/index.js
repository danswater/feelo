function changeTip(el) {
    if (el.hasClass('media-unlike') == true)
        el.store('tip:title', 'Like This Post');
    else
        el.store('tip:title', 'Unlike This Post');
}

function WidgetApp() {}

WidgetApp.prototype.start = function() {
    window[ 'max_projects_page_' + options.identity ]   = options.count;
    window['current_projects_page_' + options.identity] = 1;  
    
    var self = this;
    window.addEvent( 'load', function() {
        self.wh_project_likes  = new whmedia.project_likes();         
        self.wh_project_follow = new whmedia.project_follow();         
        self.toTopScroller     = new ToTopScroller( $( 'media-browse_' + options.identity ),$( 'media-scroll2top' ) );

        self.likeButtonHandler();
        self.followUserHandler();
        self.loadMoreHandler();
        self.masonry();        
    } );  
};

WidgetApp.prototype.likeButtonHandler = function() {
    var likeButton = document.getElementsByClassName( 'like-button' );
    var self = this;
    for( var i = 0; i < likeButton.length; i++ ) {
        likeButton[ i ].addEventListener( 'click', function( e ) {
            var viewer = this.getAttribute( 'id' );
            self.wh_project_likes.togglelike( viewer );
            changeTip( this );
            e.preventDefault();
        } );    
    }    
};

WidgetApp.prototype.followUserHandler = function() {
    var followUser = document.getElementsByClassName( 'follow-user' );
    var self = this;
    for( var i = 0; i < followUser.length; i++ ) {
        followUser[ i ].addEventListener( 'click', function( e ) {
            var viewer = this.getAttribute( 'id' );
            self.wh_project_follow.togglefollow( viewer );
            e.preventDefault();
        } );
    }    
};

WidgetApp.prototype.loadMoreHandler = function() {
    var loadMore = document.getElementById( 'projects_viewmore_link' );
    var self = this;
    loadMore.addEventListener( 'click', function( e ) {
        var identity       = this.getAttribute( 'data-identity' );
        var additionalData = this.getAttribute( 'data-addition' );
        projects_viewmore(identity, additionalData );
        e.preventDefault();
    } );    
};

WidgetApp.prototype.masonry = function() {
    $( 'media-browse_' + options.identity ).addEvent( 'masoned', function () {

        $( 'media-browse_' + options.identity ).setStyle( 'opacity', 1 );

    } ).masonry( {
        'singleMode'   :   true,
        'itemSelector' : '.media-browse-box'
    } );    
};

function IndexApp() {}

IndexApp.prototype.start = function() {
    this.followTagHandler();
    this.reccommendedHandler();
    this.bytimeHandler();
};

IndexApp.prototype.followTagHandler = function() {
    var followTag = document.getElementById( 'follow-tag' );
    var id = followTag.getAttribute( 'data-tagid' );
    followTag.addEventListener( 'click', function( e ) { 
        var request = new Request.JSON( {
            'url' : 'whmedia/index/hashtag',
            'method' : 'post',
            'data' : 'id=' + id,
            'onSuccess' : function( response ) {
                console.log( response );
                followTag.innerHTML = response.message;
            }

        } );

        request.send();
        e.preventDefault();

    } );

};

IndexApp.prototype.reccommendedHandler = function() {
    var wh_search_project = new whmedia.search();

    tag1 = document.getElementById( 'tag1' );
    tag1.addEventListener( 'click', function( e ) {
            
        //will route to specific tags post
        var tagName1 = document.getElementById( 'tag-name1' );
        var keyword = tagName1.textContent.substring( 1 );
        wh_search_project.tagAction( keyword );
        e.preventDefault();

    } );

    tag2 = document.getElementById( 'tag2' );
    tag2.addEventListener( 'click', function( e ) {
            
        //will route to specific tags post
        var tagName2 = document.getElementById( 'tag-name2' );
        var keyword = tagName2.textContent.substring( 1 );
        wh_search_project.tagAction( keyword );
            
        e.preventDefault();

    } );

    tag3 = document.getElementById( 'tag3' );
    tag3.addEventListener( 'click', function( e ) {
            
        //will route to specific tags post
        var tagName3 = document.getElementById( 'tag-name3' );
        var keyword = tagName3.textContent.substring( 1 );
        wh_search_project.tagAction( keyword );
            
        e.preventDefault();

    } );

    tag4 = document.getElementById( 'tag4' );
    tag4.addEventListener( 'click', function( e ) {
            
        //will route to specific tags post
        var tagName4 = document.getElementById( 'tag-name4' );
        var keyword = tagName4.textContent.substring( 1 );
        wh_search_project.tagAction( keyword );
            
        e.preventDefault();

    } );

    tag5 = document.getElementById( 'tag5' );
    tag5.addEventListener( 'click', function( e ) {
            
        //will route to specific tags post
        var tagName5 = document.getElementById( 'tag-name5' );
        var keyword = tagName5.textContent.substring( 1 );
        wh_search_project.tagAction( keyword );
            
        e.preventDefault();

    } );

};

IndexApp.prototype.bytimeHandler = function() {
    var wh_search_project = new whmedia.search();
    var byTime = document.getElementById( 'bytime' );
    var tag = byTime.getAttribute( 'data-tag' );
    byTime.addEventListener( 'change', function( e ) {
        var value = byTime.options[ byTime.selectedIndex ].text;
        wh_search_project.byTimeAction( tag, value );
         e.preventDefault();
    } );
}

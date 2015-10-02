var yamba = {
	App : null,
	appname : "yamba",

};

function addTag(name, attributes, sync) {
    var el = document.createElement(name), attrName;
    for (attrName in attributes) {
 	   el.setAttribute(attrName, attributes[attrName]);
    }
    sync ? document.write(outerHTML(el)) : headEl.appendChild(el);
};

function outerHTML(node){
        // if IE, Chrome take the internal method otherwise build one
    return node.outerHTML || (
        function(n){
            var div = document.createElement('div'), h;
            div.appendChild(n);
            h = div.innerHTML;
            div = null;
            return h;
   	 	})(node);
};

Array.prototype.merge = function( mergeArr ){
    if( !angular.isArray( mergeArr ) ) return this;
    var arr = this||[];
    for( var i = 0; i < mergeArr.length; i++ ){
        arr.push( mergeArr[ i ] ); 
    }
    return arr;
}

Number.prototype.num_format = function( decimals, dec_point, thousands_sep ){
    number = (this + '')
    .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
    if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
    }
    return s.join(dec);     
}

Array.prototype.getIndexByKeyVal = function( key, val ){
    var input = this;
    var indx = null;
    for( var i = 0; i < input.length; i++ ){
        if( input[ i ][ key ] == val ){
            indx = i;
            break;
        }
    }
    return indx;
}


Array.prototype.chunk = function array_chunk(size, preserve_keys) {
    var input = this;
    var x, p = '',
    i = 0,
    c = -1,
    l = input.length || 0,
    n = [];
    if (size < 1) {
        return null;
    }
    if (Object.prototype.toString.call(input) === '[object Array]') {
        if (preserve_keys) {
            while (i < l) {
                (x = i % size) ? n[c][i] = input[i] : n[++c] = {}, n[c][i] = input[i];
                i++;
            }
        } else {
            while (i < l) {
                (x = i % size) ? n[c][x] = input[i] : n[++c] = [input[i]];
                i++;
            }
        }
    } else {
        if (preserve_keys) {
            for (p in input) {
                if (input.hasOwnProperty(p)) {
                    (x = i % size) ? n[c][p] = input[p] : n[++c] = {}, n[c][p] = input[p];
                    i++;
                }
            }
        } else {
            for (p in input) {
                if (input.hasOwnProperty(p)) {
                    (x = i % size) ? n[c][x] = input[p] : n[++c] = [input[p]];
                    i++;
                }
            }
        }
    }
  return n;
}

yamba.App = angular.module( yamba.appname, [ "ngRoute","ngCookies", "ui.router", "ngFileUpload" ,"infinite-scroll", "angularModalService"] );

yamba.App.value('profile_cover',{
    cover : null,
    id : 0
});

yamba.App.value('follower_id',0);

yamba.App.value('following_id',0);

var Notifier = {

    currentTimer : null,

    show : function( message, options, timer ){
        var notElem = $( "#notification" );
        if( notElem.length > 0 ){

            var opts = angular.extend( {
                display : 'block'
            }, options||{} );

            notElem.css( opts ).html( "<h1>" + message + "</h1>" ); 

            if( typeof timer == "number" ){
                if( Notifier.currentTimer != null ){
                    clearTimeout( Notifier.currentTimer );
                }

                Notifier.currentTimer = setTimeout( function(){ 
                    Notifier.hide();
                }, timer ); 
            }

        }
    },

    hide : function(){
        var notElem = $( "#notification" );

        if( notElem.length > 0 ){
            notElem.css( {
                display : 'none'
            } ).html( '' );
        }
    }

}

/*************************************************************************
    This code is from Dynamic Web Coding at dyn-web.com
    Copyright 2008 by Sharon Paine 
    See Terms of Use at www.dyn-web.com/business/terms.php
    regarding conditions under which you may use this code.
    This notice must be retained in the code as is!

    unobtrusive event handling for use with dw_scroll.js
    version date: Nov 2008
*************************************************************************/

/////////////////////////////////////////////////////////////////////
// two ways to add style sheet for capable browsers

// Nov 2008 revision adds screen as option
// (may want printed copy to appear as on screen?)
dw_writeStyleSheet = function(file, screen) {
    var css = '<link rel="stylesheet" href="' + file + '"';
    if (screen !== false) {
        css += ' media="screen"';
    }
    document.write(css + ' />');
}

// slower, may flash unstyled ?
function dw_addLinkCSS(file, screen) {
    if ( !document.createElement ) return;
    var el = document.createElement("link");
    el.setAttribute("rel", "stylesheet");
    el.setAttribute("type", "text/css");
    if (screen !== false) {
        el.setAttribute("media", "screen");
    }
    el.setAttribute("href", file);
    document.getElementsByTagName('head')[0].appendChild(el);
}
/////////////////////////////////////////////////////////////////////

// Example class names: load_wn_lyr1, load_wn_lyr2_t2
dw_scrollObj.prototype.setUpLoadLinks = function(controlsId) {
    var wndoId = this.id; var el = document.getElementById(controlsId); 
    var links = el.getElementsByTagName('a');
    var cls, parts;
    for (var i=0; links[i]; i++) {
        cls = dw_scrollObj.get_DelimitedClass( links[i].className );
        parts = cls.split('_');
        if ( parts[0] == 'load' && parts[1] == wndoId && parts.length > 2 ) {
            // no checks on lyrId, horizId
            var lyrId = parts[2]; var horizId = parts[3]? parts[3]: null;
            dw_Event.add( links[i], 'click', function (wndoId, lyrId, horizId) {
                return function (e) {
                    dw_scrollObj.col[wndoId].load(lyrId, horizId);
                    if (e && e.preventDefault) e.preventDefault();
                    return false;
                }
            }(wndoId, lyrId, horizId) ); // see Crockford js good parts pg 39
        }
    }
}

dw_scrollObj.prototype.setUpScrollControls = function(controlsId, autoHide, axis) {
    var wndoId = this.id; var el = document.getElementById(controlsId); 
    if ( autoHide && axis == 'v' || axis == 'h' ) {
        dw_scrollObj.handleControlVis(controlsId, wndoId, axis);
        dw_Scrollbar_Co.addEvent( this, 'on_load', function() { dw_scrollObj.handleControlVis(controlsId, wndoId, axis); } );
        dw_Scrollbar_Co.addEvent( this, 'on_update', function() { dw_scrollObj.handleControlVis(controlsId, wndoId, axis); } );
    }
    
    var links = el.getElementsByTagName('a'), cls, eType;
    for (var i=0; links[i]; i++) { 
        cls = dw_scrollObj.get_DelimitedClass( links[i].className );
        eType = dw_scrollObj.getEv_FnType( cls.slice(0, cls.indexOf('_') ) );
        switch ( eType ) {
            case 'mouseover' :
            case 'mousedown' :
                dw_scrollObj.handleMouseOverDownLinks(links[i], wndoId, cls);
                break;
            case 'scrollToId': 
                dw_scrollObj.handleScrollToId(links[i], wndoId, cls);
                break;
            case 'scrollTo' :
            case 'scrollBy':
            case 'click': 
                dw_scrollObj.handleClick(links[i], wndoId, cls) ;
                break;
        }
    }
}

dw_scrollObj.handleMouseOverDownLinks = function (linkEl, wndoId, cls) {
    var parts = cls.split('_'); var eType = parts[0];
    var re = /^(mouseover|mousedown)_(up|down|left|right)(_[\d]+)?$/;
                
    if ( re.test(cls) ) { 
        var eAlt = (eType == 'mouseover')? 'mouseout': 'mouseup';
        var dir = parts[1];  var speed = parts[2] || null; 
        var deg = (dir == 'up')? 90: (dir == 'down')? 270: (dir == 'left')? 180: 0;
        
        dw_Event.add(linkEl, eType, function (e) { dw_scrollObj.col[wndoId].initScrollVals(deg, speed); } );
        dw_Event.add(linkEl, eAlt, function (e) { dw_scrollObj.col[wndoId].ceaseScroll(); } );
            
        if ( eType == 'mouseover') {
            dw_Event.add( linkEl, 'mousedown', function (e) { dw_scrollObj.col[wndoId].speed *= 3; } );
            dw_Event.add( linkEl, 'mouseup', function (e) { 
                dw_scrollObj.col[wndoId].speed = dw_scrollObj.prototype.speed; } ); 
        }
        dw_Event.add( linkEl, 'click', function(e) { if (e && e.preventDefault) e.preventDefault(); return false; } );
    }
}

// scrollToId_smile, scrollToId_smile_100, scrollToId_smile_lyr1_100    
dw_scrollObj.handleScrollToId = function (linkEl, wndoId, cls) {
    var parts = cls.split('_'); var id = parts[1], lyrId, dur;
    if ( parts[2] ) {
        if ( isNaN( parseInt(parts[2]) ) ) { 
            lyrId = parts[2];
            dur = ( parts[3] && !isNaN( parseInt(parts[3]) ) )? parseInt(parts[3]): null;
        } else {
            dur = parseInt( parts[2] );
        }
    }
    dw_Event.add( linkEl, 'click', function (e) {
            dw_scrollObj.scrollToId(wndoId, id, lyrId, dur);
            if (e && e.preventDefault) e.preventDefault();
            return false;
        } );
}

// doesn't checks if lyrId in wndo, el in lyrId
dw_scrollObj.scrollToId = function(wndoId, id, lyrId, dur) {
    var wndo = dw_scrollObj.col[wndoId];
    var el = document.getElementById(id);
    if (el) {
        if ( lyrId ) {
            if ( document.getElementById(lyrId) && wndo.lyrId != lyrId ) {
                wndo.load(lyrId);
            }
        }
        var lyr = document.getElementById(wndo.lyrId);
        var x = dw_getLayerOffset(el, lyr, 'left');
        var y = dw_getLayerOffset(el, lyr, 'top');
        wndo.initScrollToVals(x, y, dur);
    }
}

dw_scrollObj.handleClick = function (linkEl, wndoId, cls) {
    var wndo = dw_scrollObj.col[wndoId];
    var parts = cls.split('_'); var eType = parts[0]; 
    var dur_re = /^([\d]+)$/; var fn, re, x, y, dur;
    
    switch (eType) {
        case 'scrollTo' :
            fn = 'scrollTo';  re = /^(null|end|[\d]+)$/;
            x = re.test( parts[1] )? parts[1]: '';
            y = re.test( parts[2] )? parts[2]: '';
            dur = ( parts[3] && dur_re.test(parts[3]) )? parts[3]: null;
            break;
        case 'scrollBy': // scrollBy_m30_m40, scrollBy_null_m100, scrollBy_100_null
            fn = 'scrollBy';  re = /^(([m]?[\d]+)|null)$/;
            x = re.test( parts[1] )? parts[1]: '';
            y = re.test( parts[2] )? parts[2]: '';
            
            // negate numbers (m not - but vice versa) 
            if ( !isNaN( parseInt(x) ) ) {
                x = -parseInt(x);
            } else if ( typeof x == 'string' ) {
                x = x.indexOf('m') !=-1 ? x.replace('m', ''): x;
            }
            if ( !isNaN( parseInt(y) ) ) {
                y = -parseInt(y);
            } else if ( typeof y == 'string' ) {
                y = y.indexOf('m') !=-1 ? y.replace('m', ''): y;
            }
            
            dur = ( parts[3] && dur_re.test(parts[3]) )? parts[3]: null;
            break;
        
        case 'click': 
            var o = dw_scrollObj.getClickParts(cls);
            fn = o.fn; x = o.x; y = o.y; dur = o.dur;
            break;
    }
    if ( x !== '' && y !== '' ) {
        if (x == 'end') { x = wndo.maxX; }
        if (y == 'end') { y = wndo.maxY; }
        if (x === 'null' || x === null) { x = wndo.x; }
        if (y === 'null' || y === null) { y = wndo.y; }
        
        x = parseInt(x); y = parseInt(y);  
        dur = !isNaN( parseInt(dur) )? parseInt(dur): null;
        
        if (fn == 'scrollBy') {
            dw_Event.add( linkEl, 'click', function (e) {
                    dw_scrollObj.col[wndoId].initScrollByVals(x, y, dur);
                    if (e && e.preventDefault) e.preventDefault();
                    return false;
                } );
        } else if (fn == 'scrollTo') {
            dw_Event.add( linkEl, 'click', function (e) {
                    dw_scrollObj.col[wndoId].initScrollToVals(x, y, dur);
                    if (e && e.preventDefault) e.preventDefault();
                    return false;
                } );
        }
    }
}

// get info from className (e.g., click_down_by_100)
dw_scrollObj.getClickParts = function(cls) {
    var parts = cls.split('_');
    var re = /^(up|down|left|right)$/;
    var dir, fn = '', dur, ar, val, x = '', y = '';
    
    if ( parts.length >= 4 ) {
        ar = parts[1].match(re);
        dir = ar? ar[1]: null;
            
        re = /^(to|by)$/; 
        ar = parts[2].match(re);
        if (ar) {
            fn = (ar[0] == 'to')? 'scrollTo': 'scrollBy';
        } 
    
        val = parts[3]; // value on x or y axis
        re = /^([\d]+)$/;
        dur = ( parts[4] && re.test(parts[4]) )? parts[4]: null;
    
        switch (fn) {
            case 'scrollBy' :
                if ( !re.test( val ) ) {
                    x = ''; y = ''; break;
                }
                switch (dir) { // 0 for unspecified axis 
                    case 'up' : x = 0; y = val; break;
                    case 'down' : x = 0; y = -val; break;
                    case 'left' : x = val; y = 0; break;
                    case 'right' : x = -val; y = 0;
                 }
                break;
            case 'scrollTo' :
                re = /^(end|[\d]+)$/;
                if ( !re.test( val ) ) {
                    x = ''; y = ''; break;
                }
                switch (dir) { // null for unspecified axis 
                    case 'up' : x = null; y = val; break;
                    case 'down' : x = null; y = (val == 'end')? val: -val; break;
                    case 'left' : x = val; y = null; break;
                    case 'right' : x = (val == 'end')? val: -val; y = null;
                 } 
                break;
         }
    }
    return { fn: fn, x: x, y: y, dur: dur }
}

dw_scrollObj.getEv_FnType = function(str) {
    var re = /^(mouseover|mousedown|scrollBy|scrollTo|scrollToId|click)$/;
    if (re.test(str) ) {
        return str;
    }
    return '';
}

// return class name with underscores in it 
dw_scrollObj.get_DelimitedClass = function(cls) {
    if ( cls.indexOf('_') == -1 ) {
        return '';
    }
    var whitespace = /\s+/;
    if ( !whitespace.test(cls) ) {
        return cls;
    } else {
        var classes = cls.split(whitespace); 
        for(var i = 0; classes[i]; i++) { 
            if ( classes[i].indexOf('_') != -1 ) {
                return classes[i];
            }
        }
    }
}

dw_scrollObj.handleControlVis = function(controlsId, wndoId, axis) {
    var wndo = dw_scrollObj.col[wndoId];
    var el = document.getElementById(controlsId);
    if ( ( axis == 'v' && wndo.maxY > 0 ) || ( axis == 'h' && wndo.maxX > 0 ) ) {
        el.style.visibility = 'visible';
    } else {
        el.style.visibility = 'hidden';
    }
}
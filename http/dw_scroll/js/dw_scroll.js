/*************************************************************************
    This code is from Dynamic Web Coding at dyn-web.com
    Copyright 2001-2008 by Sharon Paine 
    See Terms of Use at www.dyn-web.com/business/terms.php
    regarding conditions under which you may use this code.
    This notice must be retained in the code as is!
    
    version date: Feb 2009 (opera mousewheel)
    printEnabled property added Nov 2008
*************************************************************************/

// horizId only needed for horizontal scrolling
function dw_scrollObj(wndoId, lyrId, horizId) {
    var wn = document.getElementById(wndoId);
    this.id = wndoId; dw_scrollObj.col[this.id] = this;
    this.animString = "dw_scrollObj.col." + this.id;
    this.load(lyrId, horizId);
    
    if (wn.addEventListener) {
        wn.addEventListener('DOMMouseScroll', dw_scrollObj.doOnMouseWheel, false);
    } 
    wn.onmousewheel = dw_scrollObj.doOnMouseWheel;
}

// If set true, position scrolling content div's absolute in style sheet (see documentation)
// set false in download file with position absolute set in .load method due to support issues 
// (Too many people remove the specification and then complain that the code doesn't work!)
dw_scrollObj.printEnabled = false;

dw_scrollObj.defaultSpeed = dw_scrollObj.prototype.speed = 100; // default for mouseover or mousedown scrolling
dw_scrollObj.defaultSlideDur = dw_scrollObj.prototype.slideDur = 500; // default duration of glide onclick

dw_scrollObj.isSupported = function () {
    if ( document.getElementById && document.getElementsByTagName 
         && document.addEventListener || document.attachEvent ) {
        return true;
    }
    return false;
}

dw_scrollObj.col = {}; // collect instances

// custom events 
dw_scrollObj.prototype.on_load = function() {} // when dw_scrollObj initialized or new layer loaded
dw_scrollObj.prototype.on_scroll = function() {}
dw_scrollObj.prototype.on_scroll_start = function() {}
dw_scrollObj.prototype.on_scroll_stop = function() {} // when scrolling has ceased (mouseout/up)
dw_scrollObj.prototype.on_scroll_end = function() {} // reached end
dw_scrollObj.prototype.on_update = function() {} // called in updateDims

dw_scrollObj.prototype.on_glidescroll = function() {}
dw_scrollObj.prototype.on_glidescroll_start = function() {}
dw_scrollObj.prototype.on_glidescroll_stop = function() {} // destination (to/by) reached
dw_scrollObj.prototype.on_glidescroll_end = function() {} // reached end

dw_scrollObj.prototype.load = function(lyrId, horizId) {
    var wndo, lyr;
    if (this.lyrId) { // layer currently loaded?
        lyr = document.getElementById(this.lyrId);
        lyr.style.visibility = "hidden";
    }
    this.lyr = lyr = document.getElementById(lyrId); // hold this.lyr?
    if ( !dw_scrollObj.printEnabled ) {
        this.lyr.style.position = 'absolute'; 
    }
    this.lyrId = lyrId; // hold id of currently visible layer
    this.horizId = horizId || null; // hold horizId for update fn
    wndo = document.getElementById(this.id);
    this.y = 0; this.x = 0; this.shiftTo(0,0);
    this.getDims(wndo, lyr); 
    lyr.style.visibility = "visible";
    this.ready = true; this.on_load(); 
}

dw_scrollObj.prototype.shiftTo = function(x, y) {
    if (this.lyr) {
        this.lyr.style.left = (this.x = x) + "px"; 
        this.lyr.style.top = (this.y = y) + "px";
    }
}

dw_scrollObj.prototype.getX = function() { return this.x; }
dw_scrollObj.prototype.getY = function() { return this.y; }

dw_scrollObj.prototype.getDims = function(wndo, lyr) { 
    this.wd = this.horizId? document.getElementById( this.horizId ).offsetWidth: lyr.offsetWidth;
    this.maxX = (this.wd - wndo.offsetWidth > 0)? this.wd - wndo.offsetWidth: 0;
    this.maxY = (lyr.offsetHeight - wndo.offsetHeight > 0)? lyr.offsetHeight - wndo.offsetHeight: 0;
}

dw_scrollObj.prototype.updateDims = function() {
    var wndo = document.getElementById(this.id);
    var lyr = document.getElementById( this.lyrId );
    this.getDims(wndo, lyr);
    this.on_update();
}

// for mouseover/mousedown scrolling
dw_scrollObj.prototype.initScrollVals = function(deg, speed) {
    if (!this.ready) return; 
    if (this.timerId) {
        clearInterval(this.timerId); this.timerId = 0;
    }
    this.speed = speed || dw_scrollObj.defaultSpeed;
    this.fx = (deg == 0)? -1: (deg == 180)? 1: 0;
    this.fy = (deg == 90)? 1: (deg == 270)? -1: 0;
    this.endX = (deg == 90 || deg == 270)? this.x: (deg == 0)? -this.maxX: 0; 
    this.endY = (deg == 0 || deg == 180)? this.y: (deg == 90)? 0: -this.maxY;
    this.lyr = document.getElementById(this.lyrId);
    this.lastTime = new Date().getTime();
    this.on_scroll_start(this.x, this.y);  
    this.timerId = setInterval(this.animString + ".scroll()", 10);    
}

dw_scrollObj.prototype.scroll = function() {
    var now = new Date().getTime();
    var d = (now - this.lastTime)/1000 * this.speed;
    if (d > 0) { 
        var x = this.x + Math.round(this.fx * d); var y = this.y + Math.round(this.fy * d);
        if ( ( this.fx == -1 && x > -this.maxX ) || ( this.fx == 1 && x < 0 ) || 
                ( this.fy == -1 && y > -this.maxY ) || ( this.fy == 1 && y < 0 ) ) 
       {
            this.lastTime = now;
            this.shiftTo(x, y);
            this.on_scroll(x, y);
        } else {
            clearInterval(this.timerId); this.timerId = 0;
            this.shiftTo(this.endX, this.endY);
            this.on_scroll(this.endX, this.endY);
            this.on_scroll_end(this.endX, this.endY);
        }
    }
}

// when scrolling has ceased (mouseout/up)
dw_scrollObj.prototype.ceaseScroll = function() {
    if (!this.ready) return;
    if (this.timerId) {
        clearInterval(this.timerId); this.timerId = 0; 
    }
    this.on_scroll_stop(this.x, this.y); 
}

// glide onclick scrolling
dw_scrollObj.prototype.initScrollByVals = function(dx, dy, dur) {
    if ( !this.ready || this.sliding ) return;
    this.startX = this.x; this.startY = this.y;
    this.destX = this.destY = this.distX = this.distY = 0;
    if (dy < 0) {
        this.distY = (this.startY + dy >= -this.maxY)? dy: -(this.startY  + this.maxY);
    } else if (dy > 0) {
        this.distY = (this.startY + dy <= 0)? dy: -this.startY;
    }
    if (dx < 0) {
        this.distX = (this.startX + dx >= -this.maxX)? dx: -(this.startX + this.maxX);
    } else if (dx > 0) {
        this.distX = (this.startX + dx <= 0)? dx: -this.startX;
    }
    this.destX = this.startX + this.distX; this.destY = this.startY + this.distY;
    this.glideScrollPrep(this.destX, this.destY, dur);
}

dw_scrollObj.prototype.initScrollToVals = function(destX, destY, dur) {
    if ( !this.ready || this.sliding ) return;
    this.startX = this.x; this.startY = this.y;
    this.destX = -Math.max( Math.min(destX, this.maxX), 0);
    this.destY = -Math.max( Math.min(destY, this.maxY), 0);
    this.distY = this.destY - this.startY;
    this.distX = this.destX - this.startX;
    this.glideScrollPrep(this.destX, this.destY, dur);
}

dw_scrollObj.prototype.glideScrollPrep = function(destX, destY, dur) {
    this.slideDur = (typeof dur == 'number')? dur: dw_scrollObj.defaultSlideDur;
    this.per = Math.PI/(2 * this.slideDur); this.sliding = true;
    this.lyr = document.getElementById(this.lyrId); 
    this.startTime = new Date().getTime();
    this.timerId = setInterval(this.animString + ".doGlideScroll()",10);
    this.on_glidescroll_start(this.startX, this.startY);
}

dw_scrollObj.prototype.doGlideScroll = function() {
    var elapsed = new Date().getTime() - this.startTime;
    if (elapsed < this.slideDur) {
        var x = this.startX + Math.round( this.distX * Math.sin(this.per*elapsed) );
        var y = this.startY + Math.round( this.distY * Math.sin(this.per*elapsed) );
        this.shiftTo(x, y); 
        this.on_glidescroll(x, y);
    } else {	// if time's up
        clearInterval(this.timerId); this.timerId = 0; this.sliding = false;
        this.shiftTo(this.destX, this.destY);
        this.on_glidescroll(this.destX, this.destY);
        this.on_glidescroll_stop(this.destX, this.destY);
        // end of axis reached ? 
        if ( this.distX && (this.destX == 0 || this.destX == -this.maxX) 
          || this.distY && (this.destY == 0 || this.destY == -this.maxY) ) { 
            this.on_glidescroll_end(this.destX, this.destY);
        } 
    }
}

//  resource: http://adomas.org/javascript-mouse-wheel/
dw_scrollObj.handleMouseWheel = function(id, delta) {
    var wndo = dw_scrollObj.col[id];
    var x = wndo.x;
    var y = wndo.y;
    wndo.on_scroll_start(x,y);
    var ny;
    ny = 12  * delta + y
    ny = (ny < 0 && ny >= -wndo.maxY)? ny: (ny < -wndo.maxY)? -wndo.maxY: 0;
    wndo.shiftTo(x, ny);
    wndo.on_scroll(x, ny);
}

dw_scrollObj.doOnMouseWheel = function(e) {
    var delta = 0;
    if (!e) e = window.event;
    if (e.wheelDelta) { /* IE/Opera. */
        delta = e.wheelDelta/120;
        //if (window.opera) delta = -delta; // not needed as of v 9.2
    } else if (e.detail) { // Mozilla 
        delta = -e.detail/3;
    }
    if (delta) { // > 0 up, < 0 down
        dw_scrollObj.handleMouseWheel(this.id, delta);
    }
    if (e.preventDefault) e.preventDefault();
    e.returnValue = false;
}

dw_scrollObj.GeckoTableBugFix = function() {} // no longer need old bug fix


// Get position of el within layer (oCont) sOff: 'left' or 'top'
// Assumes el is within oCont
function dw_getLayerOffset(el, oCont, sOff) {
    var off = "offset" + sOff.charAt(0).toUpperCase() + sOff.slice(1);
    var val = el[off];
    while ( (el = el.offsetParent) != oCont ) 
        val += el[off];
    var clientOff = off.replace("offset", "client");
    if ( el[clientOff] ) val += el[clientOff];
    return val;
}

/////////////////////////////////////////////////////////////////////
// Reminder about licensing requirements
// See Terms of Use at www.dyn-web.com/business/terms.php
// OK to remove after purchasing a license or if using on a personal site.
function dw_checkAuth() {
    var loc = window.location.hostname.toLowerCase();
    var msg = 'A license is required for all but personal use of this code.\n' + 
        'Please adhere to our Terms of Use if you use dyn-web code.';
    if ( !( loc == '' || loc == '127.0.0.1' || loc.indexOf('localhost') != -1 
         || loc.indexOf('192.168.') != -1 || loc.indexOf('dyn-web.com') != -1 ) ) {
        alert(msg);
    }
}
dw_Event.add( window, 'load', dw_checkAuth);
/////////////////////////////////////////////////////////////////////
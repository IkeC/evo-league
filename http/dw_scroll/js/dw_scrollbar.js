/*************************************************************************
    This code is from Dynamic Web Coding at dyn-web.com
    Copyright 2001-2008 by Sharon Paine 
    See Terms of Use at www.dyn-web.com/business/terms.php
    regarding conditions under which you may use this code.
    This notice must be retained in the code as is!
    
    for use with dw_scroll.js - provides scrollbar functionality
    version date: Aug 2008
        bug fixed in .setBarSize
*************************************************************************/

function dw_Slidebar(barId, trackId, axis, x, y) {
    var bar = document.getElementById(barId);
    var track = document.getElementById(trackId);
    this.barId = barId; this.trackId = trackId;
    this.axis = axis; this.x = x || 0; this.y = y || 0;
    dw_Slidebar.col[this.barId] = this;
    this.bar = bar;  this.shiftTo(x, y);
    
    // hold for setBarSize  
    this.trkHt = track.offsetHeight; 
    this.trkWd = track.offsetWidth; 
  
    if (axis == 'v') {
        this.maxY = this.trkHt - bar.offsetHeight - y; 
        this.maxX = x; this.minX = x; this.minY = y;
    } else {
        this.maxX = this.trkWd - bar.offsetWidth - x; 
        this.minX = x; this.maxY = y; this.minY = y;
    }
    
    this.on_drag_start =  this.on_drag =   this.on_drag_end = 
    this.on_slide_start = this.on_slide =  this.on_slide_end = function() {}
    
    bar.onmousedown = dw_Slidebar.prepDrag; 
    // pass barId to obtain instance from dw_Slidebar.col
    track.onmousedown = function(e) { dw_Slidebar.prepSlide(barId, e); }
    this.bar = bar = null; track = null; 
}

dw_Slidebar.col = {}; // hold instances for global access
dw_Slidebar.current = null; // hold current instance

dw_Slidebar.prototype.slideDur = 500;

// track received onmousedown event
dw_Slidebar.prepSlide = function(barId, e) {
    var _this = dw_Slidebar.col[barId];
    dw_Slidebar.current = _this;
    var bar = _this.bar = document.getElementById(barId);
    
    if ( _this.timer ) { clearInterval(_this.timer); _this.timer = 0; }
    e = e? e: window.event;
    
    e.offX = (typeof e.layerX != "undefined")? e.layerX: e.offsetX;
    e.offY = (typeof e.layerY != "undefined")? e.layerY: e.offsetY;
    _this.startX = parseInt(bar.style.left); _this.startY = parseInt(bar.style.top);

    if (_this.axis == "v") {
        _this.destX = _this.startX;
        _this.destY = (e.offY < _this.startY)? e.offY: e.offY - bar.offsetHeight;
        _this.destY = Math.min( Math.max(_this.destY, _this.minY), _this.maxY );
    } else {
        _this.destX = (e.offX < _this.startX)? e.offX: e.offX - bar.offsetWidth;
        _this.destX = Math.min( Math.max(_this.destX, _this.minX), _this.maxX );
        _this.destY = _this.startY;
    }
    _this.distX = _this.destX - _this.startX; _this.distY = _this.destY - _this.startY;
    _this.per = Math.PI/(2 * _this.slideDur);
    _this.slideStartTime = new Date().getTime();
    _this.on_slide_start(_this.startX, _this.startY);
    _this.timer = setInterval("dw_Slidebar.doSlide()", 10);
}

dw_Slidebar.doSlide = function() {
    var _this = dw_Slidebar.current;
    var elapsed = new Date().getTime() - _this.slideStartTime;
    if (elapsed < _this.slideDur) {
        var x = _this.startX + _this.distX * Math.sin(_this.per*elapsed);
        var y = _this.startY + _this.distY * Math.sin(_this.per*elapsed);
        _this.shiftTo(x,y);
        _this.on_slide(x, y);
    } else {	// if time's up
        clearInterval(_this.timer);
        _this.shiftTo(_this.destX,  _this.destY);
        _this.on_slide(_this.destX,  _this.destY);
        _this.on_slide_end(_this.destX, _this.destY);
        dw_Slidebar.current = null;
    }    
}

dw_Slidebar.prepDrag = function (e) { 
    var bar = this; // bar received onmousedown event
    var barId = this.id; // id of element mousedown event assigned to
    var _this = dw_Slidebar.col[barId]; // Slidebar instance
    dw_Slidebar.current = _this;
    _this.bar = bar;
    e = dw_Event.DOMit(e);
    if ( _this.timer ) { clearInterval(_this.timer); _this.timer = 0; }
    _this.downX = e.clientX; _this.downY = e.clientY;
    _this.startX = parseInt(bar.style.left);
    _this.startY = parseInt(bar.style.top);
    _this.on_drag_start(_this.startX, _this.startY);
    dw_Event.add( document, "mousemove", dw_Slidebar.doDrag, true );
    dw_Event.add( document, "mouseup",   dw_Slidebar.endDrag,  true );
    e.stopPropagation(); e.preventDefault();
}

dw_Slidebar.doDrag = function(e) {
    if ( !dw_Slidebar.current ) return; // avoid errors in ie if inappropriate selections
    var _this = dw_Slidebar.current;
    var bar = _this.bar;
    e = dw_Event.DOMit(e);
    var nx = _this.startX + e.clientX - _this.downX;
    var ny = _this.startY + e.clientY - _this.downY;
    nx = Math.min( Math.max( _this.minX, nx ), _this.maxX);
    ny = Math.min( Math.max( _this.minY, ny ), _this.maxY);
    _this.shiftTo(nx, ny);
    _this.on_drag(nx, ny);
    e.preventDefault(); e.stopPropagation();
}

dw_Slidebar.endDrag = function() {
    if ( !dw_Slidebar.current ) return; // avoid errors in ie if inappropriate selections
    var _this = dw_Slidebar.current;
    var bar = _this.bar;
    dw_Event.remove( document, "mousemove", dw_Slidebar.doDrag, true );
    dw_Event.remove( document, "mouseup",   dw_Slidebar.endDrag,  true );
    _this.on_drag_end( parseInt(bar.style.left), parseInt(bar.style.top) );
    dw_Slidebar.current = null;
}

dw_Slidebar.prototype.shiftTo = function(x, y) {
    if ( this.bar ) {
        this.bar.style.left = x + "px";
        this.bar.style.top = y + "px";
    }
}

/////////////////////////////////////////////////////////////////////
//  connect slidebar with scrollObj
dw_scrollObj.prototype.setUpScrollbar = function(barId, trkId, axis, offx, offy, bSize) {
    var scrollbar = new dw_Slidebar(barId, trkId, axis, offx, offy);
    if (axis == "v") {
        this.vBarId = barId; 
    } else {
        this.hBarId = barId;
    }
    scrollbar.wndoId = this.id;
    scrollbar.bSizeDragBar = (bSize == false)? false: true; 
    if (scrollbar.bSizeDragBar) {
        dw_Scrollbar_Co.setBarSize(this, scrollbar);
    }
    dw_Scrollbar_Co.setEvents(this, scrollbar);
}

// Coordinates slidebar and scrollObj 
dw_Scrollbar_Co = {
    
    // This function is called for each scrollbar attached to a scroll area (change from previous version)
    setBarSize: function(scrollObj, barObj) {
        var lyr = document.getElementById(scrollObj.lyrId);
        var wn = document.getElementById(scrollObj.id);
        if ( barObj.axis == 'v' ) {
            var bar = document.getElementById(scrollObj.vBarId);
            bar.style.height = (lyr.offsetHeight > wn.offsetHeight)? 
                barObj.trkHt / ( lyr.offsetHeight / wn.offsetHeight ) + "px":             
                barObj.trkHt - ( 2 * barObj.minY ) + "px";
            barObj.maxY = barObj.trkHt - bar.offsetHeight - barObj.minY; 
        } else if ( barObj.axis == 'h' ) {
            var bar = document.getElementById(scrollObj.hBarId);
            bar.style.width = (scrollObj.wd > wn.offsetWidth)? 
                barObj.trkWd / ( scrollObj.wd / wn.offsetWidth ) + "px": 
                barObj.trkWd - ( 2 * barObj.minX ) + "px";
            barObj.maxX = barObj.trkWd - bar.offsetWidth - barObj.minX;
        }
    },
    
    // Find bars associated with this scrollObj. if they have bSizeDragBar set true reevaluate size and reset position to top 
    resetBars: function(scrollObj) {
        var barObj, bar;
        if (scrollObj.vBarId) {
            barObj = dw_Slidebar.col[scrollObj.vBarId];
            bar = document.getElementById(scrollObj.vBarId);
            bar.style.left = barObj.minX + "px"; bar.style.top = barObj.minY + "px";
            if (barObj.bSizeDragBar) {
                dw_Scrollbar_Co.setBarSize(scrollObj, barObj);
            }
        }
        if (scrollObj.hBarId) {
            barObj = dw_Slidebar.col[scrollObj.hBarId];
            bar = document.getElementById(scrollObj.hBarId);
            bar.style.left = barObj.minX + "px"; bar.style.top = barObj.minY + "px";
            if (barObj.bSizeDragBar) {
                dw_Scrollbar_Co.setBarSize(scrollObj, barObj);
            }
        }
    },
    
    setEvents: function(scrollObj, barObj) {
        // scrollObj
        this.addEvent(scrollObj, 'on_load', function() { dw_Scrollbar_Co.resetBars(scrollObj); } );
        this.addEvent(scrollObj, 'on_scroll_start', function() { dw_Scrollbar_Co.getBarRefs(scrollObj) } );
        this.addEvent(scrollObj, 'on_glidescroll_start', function() { dw_Scrollbar_Co.getBarRefs(scrollObj) } );
        this.addEvent(scrollObj, 'on_scroll', function(x,y) { dw_Scrollbar_Co.updateScrollbar(scrollObj, x, y) } );
        this.addEvent(scrollObj, 'on_glidescroll', function(x,y) { dw_Scrollbar_Co.updateScrollbar(scrollObj, x, y) } );
        this.addEvent(scrollObj, 'on_scroll_stop', function(x,y) { dw_Scrollbar_Co.updateScrollbar(scrollObj, x, y); } );
        this.addEvent(scrollObj, 'on_glidescroll_stop', function(x,y) { dw_Scrollbar_Co.updateScrollbar(scrollObj, x, y); } );
        this.addEvent(scrollObj, 'on_scroll_end', function(x,y) { dw_Scrollbar_Co.updateScrollbar(scrollObj, x, y); } );
        this.addEvent(scrollObj, 'on_glidescroll_end', function(x,y) { dw_Scrollbar_Co.updateScrollbar(scrollObj, x, y); } );
            
        // barObj 
        this.addEvent(barObj, 'on_slide_start', function() { dw_Scrollbar_Co.getWndoLyrRef(barObj) } );
        this.addEvent(barObj, 'on_drag_start', function() { dw_Scrollbar_Co.getWndoLyrRef(barObj) } );
        this.addEvent(barObj, 'on_slide', function(x,y) { dw_Scrollbar_Co.updateScrollPosition(barObj, x, y) } );
        this.addEvent(barObj, 'on_drag', function(x,y) { dw_Scrollbar_Co.updateScrollPosition(barObj, x, y) } );
        this.addEvent(barObj, 'on_slide_end', function(x,y) { dw_Scrollbar_Co.updateScrollPosition(barObj, x, y); } );
        this.addEvent(barObj, 'on_drag_end', function(x,y) { dw_Scrollbar_Co.updateScrollPosition(barObj, x, y); } );
    
    },
    
    // Provide means to add functions to be invoked on pseudo events (on_load, on_scroll, etc) 
    // without overwriting any others that may already be set
    // by Mark Wubben (see http://simonwillison.net/2004/May/26/addLoadEvent/)
    addEvent: function(o, ev, fp) {
        var oldEv = o[ev];
        if ( typeof oldEv != 'function' ) {
            //o[ev] = fp;
            // almost all the functions (on_scroll, on_drag, etc.) pass x,y
            o[ev] = function (x,y) { fp(x,y); }
        } else {
            o[ev] = function (x,y) {
                  oldEv(x,y );
                  fp(x,y);
            }
        }
    },

    // Keep position of dragBar in sync with position of layer when scrolled by other means (mouseover, etc.)
    updateScrollbar: function(scrollObj, x, y) { // 
        var nx, ny;
        if ( scrollObj.vBar && scrollObj.maxY ) { 
            var vBar = scrollObj.vBar;
            ny = -( y * ( (vBar.maxY - vBar.minY) / scrollObj.maxY ) - vBar.minY );
            ny = Math.min( Math.max(ny, vBar.minY), vBar.maxY);  
            if (vBar.bar) { // ref to bar el
                nx = parseInt(vBar.bar.style.left);
                vBar.shiftTo(nx, ny);
            }
        }
        if ( scrollObj.hBar && scrollObj.maxX ) {
            var hBar = scrollObj.hBar;
            nx = -( x * ( (hBar.maxX - hBar.minX) / scrollObj.maxX ) - hBar.minX );
            nx = Math.min( Math.max(nx, hBar.minX), hBar.maxX);
            if (hBar.bar) {
                ny = parseInt(hBar.bar.style.top);
                hBar.shiftTo(nx, ny);
            }
        }
    },

    updateScrollPosition: function(barObj, x, y) { // on scrollbar movement
        var nx, ny; var wndo = barObj.wndo; 
        if ( !wndo.lyr ) {
            wndo.lyr = document.getElementById(wndo.lyrId);
        }
        if (barObj.axis == "v") {
            nx = wndo.x; // floating point values for loaded layer's position held in shiftTo method
            ny = -(y - barObj.minY) * ( wndo.maxY / (barObj.maxY - barObj.minY) ) || 0;
        } else {
            ny = wndo.y;
            nx = -(x - barObj.minX) * ( wndo.maxX / (barObj.maxX - barObj.minX) ) || 0;
        }
        wndo.shiftTo(nx, ny);
    },
    
    // Scroll area may have both vertical and horizontal bars 
    getBarRefs: function(scrollObj) { // References to Slidebar instance and dom element 
        if ( scrollObj.vBarId ) {
            scrollObj.vBar = dw_Slidebar.col[scrollObj.vBarId];
            scrollObj.vBar.bar = document.getElementById(scrollObj.vBarId);
        }
        if ( scrollObj.hBarId ) {
            scrollObj.hBar = dw_Slidebar.col[scrollObj.hBarId];
            scrollObj.hBar.bar = document.getElementById(scrollObj.hBarId);
        }
    },
    
    getWndoLyrRef: function(barObj) {
        var wndo = barObj.wndo = dw_scrollObj.col[ barObj.wndoId ];
        if ( wndo && !wndo.lyr ) {
            wndo.lyr = document.getElementById(wndo.lyrId);
        }
    }

}

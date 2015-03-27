/*
    intermediary functions to prevent errors before page loaded 
    when using html element event handler attributes
    Provided for backwards compatibility with updated dw_scroll.js 
*/

dw_scrollObj.loadLayer = function(wndoId, lyrId, horizId) {
    if ( dw_scrollObj.col[wndoId] ) dw_scrollObj.col[wndoId].load(lyrId, horizId);
}

// Support for mouseover/down scrolling at any angle has been removed 
dw_scrollObj.initScroll = function(wndoId, dir, speed) {
    var deg = dir == 'up'? 90: dir == 'down'? 270: dir == 'left'? 180: dir == 'right'? 0: dir;
    if ( deg != null && dw_scrollObj.col[wndoId] ) {
        dw_scrollObj.col[wndoId].initScrollVals(deg, speed);
    }
}

dw_scrollObj.stopScroll = function(wndoId) {
    if ( dw_scrollObj.col[wndoId] ) dw_scrollObj.col[wndoId].ceaseScroll();
}

// increase speed onmousedown of scroll links (for mouseover scrolling)
dw_scrollObj.doubleSpeed = function(wndoId) {
    if ( dw_scrollObj.col[wndoId] ) dw_scrollObj.col[wndoId].speed *= 2;
}

dw_scrollObj.resetSpeed = function(wndoId) {
    if ( dw_scrollObj.col[wndoId] ) dw_scrollObj.col[wndoId].speed /= 2;
}

// for glide onclick scrolling 
dw_scrollObj.scrollBy = function(wndoId, x, y, dur) {
    if ( dw_scrollObj.col[wndoId] ) dw_scrollObj.col[wndoId].initScrollByVals(x, y, dur);
}

dw_scrollObj.scrollTo = function(wndoId, x, y, dur) {
    if ( dw_scrollObj.col[wndoId] ) dw_scrollObj.col[wndoId].initScrollToVals(x, y, dur);
}

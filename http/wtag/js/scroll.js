/* A slightly modified version of the DHTML SCROLLBAR from http://13thparallel.com */

// We wrap all the code in an object so that it doesn't interfere with any other code
var scroller = {
  
  init:   function() {
  
  // Collect the variables
  scroller.docH = document.getElementById("content").offsetHeight;
  scroller.contH = document.getElementById("container").offsetHeight;
  scroller.scrollAreaH = document.getElementById("scrollArea").offsetHeight;
    
  // What is the effective scroll distance once the scoller's height has been taken into account
  scroller.scrollDist = Math.round(scroller.scrollAreaH-30);
    
  // Make the scroller div draggable
  if(scroller.docH > 160) {
   
  document.getElementById("scroller").style.height = "30px";
    
  // Change scroller width here
  document.getElementById("scroller").style.width = "8px";
    
  Drag.init(document.getElementById("scroller"),null,0,0,0,scroller.scrollDist);
   
  // Add ondrag function
  document.getElementById("scroller").onDrag = function (x,y) {
  var scrollY = parseInt(document.getElementById("scroller").style.top);
  var docY = 0 - (scrollY * (scroller.docH - scroller.contH) / scroller.scrollDist);
  document.getElementById("content").style.top = docY + "px";
  }  
  } 
  
    
    
}
}


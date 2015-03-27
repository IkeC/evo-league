/*------ JavaScript functions to make the css smileys panel work in IE. ------*/

/*
The function is a modified version of the JavaScript function from:
http://www.alistapart.com/d/horizdropdowns/drop_down.js
*/

// Fix li:hover in IE
startList = function() {

if (document.all&&document.getElementById) {
navRoot = document.getElementById("show_sm");
for (i=0; i<navRoot.childNodes.length; i++) {
node = navRoot.childNodes[i];

if (node.nodeName=="LI") {
node.onmouseover=function() {
this.className+=" over";
}
node.onmouseout=function() {
this.className=this.className.replace(" over", "");
}
}
}

// Fix submit button color change in IE
var submit = document.getElementById ("submit");
for (i=0; i<submit.childNodes.length; i++) {
node2 = submit.childNodes[i];

if (node2.nodeName=="P") {
node2.onmouseover=function() {
this.className+=" over";
}
node2.onmouseout=function() {
this.className=this.className.replace(" over", "");
}
}
}
   
// Fix refresh button color change in IE
var refresh = document.getElementById ("refresh");
for (i=0; i<refresh.childNodes.length; i++) {
node3 = refresh.childNodes[i];

if (node3.nodeName=="P") {
node3.onmouseover=function() {
this.className+=" over";
}
node3.onmouseout=function() {
this.className=this.className.replace(" over", "");
}
}
}   
   
   
}
}
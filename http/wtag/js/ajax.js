// Parse URLs to links
function autoLinks(msg) {

  var text = msg.replace(/((?:ht|f)tps?:\/\/([^\s,]*))/gi,
  "<a href='$1' target='_blank'>[<span>link&rarr;</span>]</a>");
  return text;

}

// Replace smileys tags with images
function replaceSmileys(message) {
  
  var sm = message.replace(/(:\)|:\-\))/g, "<img src='/wtag/smileys/smile.gif' width='15' height='15' alt=':)' title=':)' />").
  replace(/(:\(|:-\()/g, "<img src='/wtag/smileys/sad.gif' width='15' height='15' alt=':(' title=':(' />").
  replace(/(\;\)|\;\-\))/g, "<img src='/wtag/smileys/wink.gif' width='15' height='15' alt=';)' title=';)' />").
  replace(/(:-P)/g, "<img src='/wtag/smileys/tongue.gif' width='15' height='15' alt=':-P' title=':-P' />").
  replace(/(S\-\))/g, "<img src='/wtag/smileys/rolleyes.gif' width='15' height='15' alt='S-)' title='S-)' />").
  replace(/(\>\()/g, "<img src='/wtag/smileys/angry.gif' width='15' height='15' alt='>(' title='>(' />").
  replace(/(\:\*\))/g, "<img src='/wtag/smileys/embarassed.gif' width='15' height='15' alt=':*)' title=':*)' />").
  replace(/(\:-D)/g, "<img src='/wtag/smileys/grin.gif' width='15' height='15' alt=':-D' title=':-D' />").
  replace(/(QQ)/g, "<img src='/wtag/smileys/cry.gif' width='15' height='15' alt='QQ' title='QQ' />").
  replace(/(\=O)/g, "<img src='/wtag/smileys/shocked.gif' width='15' height='15' alt='=O' title='=O' />").
  replace(/(\=\/)/g, "<img src='/wtag/smileys/undecided.gif' width='15' height='15' alt='=/' title='=/' />").
  replace(/(8\-\))/g, "<img src='/wtag/smileys/cool.gif' width='15' height='15' alt='8-)' title='8-)' />").
  replace(/(:-X)/g, "<img src='/wtag/smileys/sealedlips.gif' width='15' height='15' alt=':-X' title=':-X' />").
  replace(/(O:\])/g, "<img src='/wtag/smileys/angel.gif' width='15' height='15' alt='O:]' title='O:]' />");
  
  return sm;
}

/*------ AJAX part -----------------------------------------------------------*/

/*
* The Ajax part of the shoutbox script is based on AJAX-Based Chat System
* by Alejandro Gervasio
* URL: http://images.devshed.com/da/stories/Building_AJAX_Chat/chat_example.zip
*/

// Create the XMLHttpRequestObject
function getXMLHttpRequestObject() {
  var xmlobj;	
  // Check for existing requests
  if (xmlobj!=null&&xmlobj.readyState!=0&&xmlobj.readyState!=4) {
  xmlobj.abort();
  }
  try {
  // Instantiate object for Mozilla, Nestcape, etc.
  xmlobj=new XMLHttpRequest();
  }
  catch(e) {
  try {
  // Instantiate object for Internet Explorer
  xmlobj=new ActiveXObject('Microsoft.XMLHTTP');
  }
  catch(e) {
  // Ajax is not supported by the browser
  xmlobj=null;
  return false;
  }
  }
  return xmlobj;
}



// Check status of sender object
function senderStatusChecker() {
  // Check if request is completed
  if(senderXMLHttpObj.readyState==4) {
  if(senderXMLHttpObj.status==200) {
 
  // If status == 200 display chat data
  displayChatData(senderXMLHttpObj);
  }
  else {
  var post=document.getElementById('content');
  var error_message = document.createTextNode('Invalid data ('+ senderXMLHttpObj.statusText + ')');
  post.appendChild(error_message);
  }
  }
}


// Check status of receiver object
function receiverStatusChecker() {
  // If request is completed
  if(receiverXMLHttpObj.readyState==4) {
  if(receiverXMLHttpObj.status==200) {
  // If status == 200 display chat data
  displayChatData(receiverXMLHttpObj);
  }
  else {
  var post=document.getElementById('content');
  var error_message = document.createTextNode('Invalid data ('+ receiverXMLHttpObj.statusText + ')');
  post.appendChild(error_message);
  }
  }
}

// Get messages from database each 60 seconds
function getChatData() {
  receiverXMLHttpObj.open('GET','/wtag/getchat.php',true);
  receiverXMLHttpObj.onreadystatechange=receiverStatusChecker;
  receiverXMLHttpObj.send(null);
  setTimeout('getChatData()',60*1000);
}


// instantiate sender XMLHttpRequest object
var senderXMLHttpObj = getXMLHttpRequestObject();
// instantiate receiver XMLHttpRequest object
var receiverXMLHttpObj = getXMLHttpRequestObject();

 
// Display messages
function displayChatData(reqObj) {
  
  var post=document.getElementById('content');
  
  if (!post) {
    return;
  }
  post.innerHTML = '';

  var xmldoc = receiverXMLHttpObj.responseXML;
  var messages = xmldoc.getElementsByTagName('message');
 
  for (i = 0; i < messages.length; i++) {
    var maintext = document.createElement('div');
    maintext.className='text';
    maintext.innerHTML = autoLinks(replaceSmileys(messages[i].firstChild.nodeValue));
    post.appendChild(maintext);
  }
  
  scroller.init();
}

// Initialize chat 
function startChat() {
  getChatData();
}

window.onload = startChat;
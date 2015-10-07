/**
   *  Object.extend(destination, source) -> Object
   *  - destination (Object): The object to receive the new properties.
   *  - source (Object): The object whose properties will be duplicated.
   *
   *  Copies all properties from the source to the destination object. Used by Prototype
   *  to simulate inheritance (rather statically) by copying to prototypes.
   *  
   *  Documentation should soon become available that describes how Prototype implements
   *  OOP, where you will find further details on how Prototype uses [[Object.extend]] and
   *  [[Class.create]] (something that may well change in version 2.0). It will be linked
   *  from here.
   *  
   *  Do not mistake this method with its quasi-namesake [[Element.extend]],
   *  which implements Prototype's (much more complex) DOM extension mechanism.
  **/
Object.extend = function(dest, source, allowOverwrite)
{
	for (var prop in source)
	{
		if (source.hasOwnProperty(prop) && (allowOverwrite || !dest.hasOwnProperty(prop)))
			dest[prop] = source[prop];
	}

	return dest;
};

var chat;
var myUsername = 'Unknown';
var retryCounter = 0;

function escapeRegExp(str)
{
  return str.replace(/[\-\[\]\/\{\}\(\)*\+\?\.\\\^\$\|]/g, "\\$&");
}

function UserList()
{
	this.initialized = false;
}
Object.extend(UserList.prototype,
{
  addUser: function(user)
  {
		$('#users').append('<li id="user_id_{0}">{1}</li>'.format(user.id, this.getUserElement(user)));
	},
	
	removeUser: function(user)
	{
	  $('#user_id_{0}'.format(user.id)).remove();
	},
	
	changeUser: function(event)
	{
	  var entry = $('#user_id_{0}'.format(event.id));
	  entry.children(0).attr('title', event.name_new);
	  entry.children(0).html(event.name_new);
	},
	
   getUserElement: function(user)
   {
    	return '<span title="{0}" style="color: {1}">{0}</span>'.format(user.name, user.color);
	 }
});

function BNDChat()
{
	if (BNDChat.instance)
		return BNDChat.instance;

	if (!(this instanceof BNDChat))
		return BNDChat.instance = new BNDChat();
		
	this.setUsername = null;
	this.webSocket = null;
	
	this.initialized = false;
	
	this.userList = null;

	this.init();
}
Object.extend(BNDChat.prototype,
{
	init: function()
	{
		this.userList = new UserList();
		this.initialized = true;
	},
	
	connect: function() {
	  this.webSocket = new WebSocket('ws://<?php echo $_SERVER['SERVER_NAME']; ?>:9002/');
	  
	  this.webSocket.onopen = function(ev) {
	    showSystemMessage('Connected');
	    retryCounter = 0;
	  }
	
	  this.webSocket.onmessage = function(ev) {
	    console.log('Incoming packet: {0}'.format(ev.data));
	    parseIncomingMessage(ev.data);
	  }
	  
	  this.webSocket.onerror = function(ev) {
	    var msg = 'Connection error';
	    var data = ev.data;
	    if (data != null) {
	      msg += ': '+data;
	    }
	    showSystemMessage(msg);
	  }
	
	 this.webSocket.onclose = function(ev) {
	    retryCounter++;
	    var retryTime = retryCounter*5 + 5;
	    showSystemMessage('Connection closed - retry in {0} seconds..'.format(retryTime));
	    setTimeout(chat.connect, retryTime*1000);
	  }
	},

	socketConnected: function()
	{
		return this.webSocket.readyState == 1;
	},

	sendMessage: function() {
	  if (!this.socketConnected()) {
	    showSystemMessage('Unable to send message - not connected');
	    return false;
	  }
	
	  var message = $('#message');
	  json = parseLocalMsg(message);
	  if (json != 0 ) { 
	  	this.sendJSON(json); 
	  }
	  message.val('');
	  return true;
	},
	
	sendJSON: function( jsonCode )
	{
		var jsonStr = JSON.stringify(jsonCode);

		console.log('Outgoing packet: {0}'.format(jsonStr));

		if( this.webSocket != null )
 			this.webSocket.send(jsonStr);
	},
	
	clearScreen: function()
	{
		$('#chat_container').empty();
	}
});


function parseLocalMsg(message) {
  var type = 'message';
  var content = message.val();
  var regSlash = new RegExp("^\/");
  var json;
  if(regSlash.test( content )) {
    matchList = content.match(/[0-9a-zA-Z]+/gi);
    type = matchList[0];
    if (type=="clear") {
      chat.clearScreen();
      showSystemMessage("Screen cleared");
      return 0;
    }
    if (type == 'disconnect') {
      if (socketConnected()) {
        chat.websocket.close();
        showSystemMessage('Connection closed');
      }
      else {
        showSystemMessage('Not connected');
      }
      return 0;
    }
    if (type == 'connect') {
      if (!socketConnected) {
        chat.connect();
      }
      else {
        showSystemMessage('Already connected');
      }
    }
    //if(matchList.length < 2) {
    //  showSystemMessage("Command " + type + ": Please provide a (valid) argument");
    //  return 0;
    //}
    if(type=="name") {
      json = {type: 'name', name: matchList[1]};
      myUsername = matchList[1];
      content = matchList[1];
      showSystemMessage("request name change to {0}".format(content));
    } else if(type=="color" || type=="colour") {
      content = matchList[1];
      //if( !( content.match(/[0-9a-f]{3}/i).length==1 || content.match(/[0-9a-f]{6}/i).length == 1)) {
      if( !content.match(/[0-9a-f]{6}/i) || content.length > 6) {
        showSystemMessage("{0}: Please provide a valid colour".format(type));
        return 0;
      }
      showSystemMessage("Request color change to #{0}".format(content));
    } else if(type=="w" | type=="pm" | type=="me") {
      content = matchList.join(" ");
    } else {
      showSystemMessage("Command '{0}' not recognized.".format(type));
      return 0;
    }      
  } 
  if (json == null) {
    var json = {
      type: type,
      message: $.trim(content)
    };
  }
  return json; 
}

function parseIncomingMessage(data) {
    var message = JSON.parse(data);

    if (message.type == 'message')
    {
      var container = $('#chat_container');
      var myNameRegex = new RegExp(escapeRegExp(myUsername), "i");

      container.append('<div class="user_message">');
      container.append('<span class="time">{0}</span> &lt;'.format(message.time));
      container.append('<span class="name">{0}</span>&gt; '.format(message.id));
      container.append('<span class="message{0}">{1}</span>'.format(myNameRegex.test(message.message) ? ' alerted_message' : '', message.message));
      container.append('</div>');
      
      scrollBottom(container);
    }
    else if (message.type == 'system') {
      showSystemMessage(message.message);
    } 
    else if (message.type == 'users') {
      $('#users').empty();
      for (user in message.users) {
          chat.userList.addUser(message.users[user]);
      }
    }
    else if (message.type == 'name') {
      showSystemMessage('User \'{0}\' is now known as \'{1}\''.format(message.id, message.name_new));
      chat.userList.changeUser(message);
    }
    else if (message.type == 'connect') {
      showSystemMessage('User \'{0}\' ({1}) connected'.format(message.name, message.ip));
      chat.userList.addUser(message);
    }
    else if (message.type == 'disconnect') {
      showSystemMessage('User \'{0}\' disconnected ({1})'.format(message.name, message.disconnect_type));
      chat.userList.removeUser(message);
    }
    else {
      // console.log("Received unknown type " + message.type);
    }
}

function showSystemMessage(msg) {
  var container = $('#chat_container');
  container.append('<div class="system_message">{0}</div>'.format(msg));
  scrollBottom(container);
}

$(document).ready(function() {
  chat = new BNDChat();
  chat.connect();

  var history = [];
  var historyCurrent = "";
  var historyIndex = -1;

  $('#message').keypress(function(event) {
    if (event.which == 13 && !event.shiftKey) {
      event.preventDefault();
      var message = $('#message').val();
      var isSent = chat.sendMessage();
      if (isSent) {
        history.unshift(message);
        historyCurrent = "";
      }
    }
  });

  $('#message').keyup(function(event) {
    var processHistory = false;

    if (event.which == 38) {
      if (historyIndex == -1) {
        historyCurrent = this.value;
      }
      historyIndex = Math.min(history.length - 1, historyIndex + 1);
      processHistory = true;
    } else if (event.which == 40) {
      historyIndex = Math.max(-1, historyIndex - 1);
      processHistory = true;
    }

    if (processHistory) {
      if (historyIndex < 0) {
        this.value = historyCurrent;
      } else {
        this.value = history[historyIndex];
      }
    }

  });

  window.onbeforeunload = function() {
    chat.sendJSON({type: 'disconnect'});
  }

});

// vim: syntax=javascript ts=2 sw=2

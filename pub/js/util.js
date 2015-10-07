/**
 * Allow calls of the type "{0}".format("bla").
 */
String.prototype.format = function() {
    var str = this;
    for (var i = 0; i < arguments.length; i++) {       
        var reg = new RegExp("\\{" + i + "\\}", "gm");             
        str = str.replace(reg, arguments[i]);
    }
    return str;
}

/**
 * Scroll an element to the bottom.
 */
function scrollBottom(element) {
  element.scrollTop(element.prop('scrollHeight'));
}

/**
 * Get the caret position of an element.
 */
function getCaret(element) { 
  if (element.selectionStart) { 
      return element.selectionStart; 
  } else if (document.selection) { 
      element.focus();
      var r = document.selection.createRange(); 
      if (r == null) { 
          return 0;
      }
      var re = element.createTextRange(), rc = re.duplicate();
      re.moveToBookmark(r.getBookmark());
      rc.setEndPoint('EndToStart', re);
      return rc.text.length;
  }  
  return 0; 
}


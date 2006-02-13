/*
This is a little script take a set of radiobutton and transform them to a multistate button.

A defaut code:
<form id="exemple"  action="test.php" method="post"> 

<fieldset class="multistate">
  <legend>Article 19</legend>
  action:
  <label for="on">on</label> <input name="id[19]" id="on" value="on" type="radio"/>
  <label for="off">off</label> <input name="id[19]" id="off" value="off" type="radio"/>
  <label for="perhaps">perhaps</label> <input name="id[19]" id="perhaps" value="perhaps" type="radio"/>
</fieldset>

<fieldset class="multistate">
  <legend>Article 18</legend>
action:
  <label for="on1">voir</label> <input name="ids[18]" value="on" id="on1" type="radio"/>
  <label for="off1">ajouter</label> <input name="id[18]"  value="off" id="off1" type="radio"/>
  <label for="perhaps1">enlever</label> <input name="id[18]"  value="perhaps" id="perhaps1" type="radio"/>
</fieldset>

</form>

will draw as something like:

Article 19 on X off O perhaps O
Article 18 on O off X perhaps O

If you apply MultiStateRadio on that, you will only get:

Article 19 |on|
Article18 |off|

and by clicking on the button (label of the radio) it will cicle between the radio. 
Ctrl+click will cicle in the other direction.

The scripts add the _multistateradio class name to every label of this radio.
 To give them a "button" style, you should apply a style that looks something like this:

label._multistate {
  border: 1px solid #c0c0ff;
  border-right-width: 3px;
  border-bottom-width: 3px;
  background: #f0faff;
  padding: 0 3px;
}

To use the script, you need to import prototype and call apply on the MultiStateRadio object.

	<script type="text/javascript" src="prototype.js"></script>
	<script type="text/javascript" src="MultiStateRadio.js"></script>
	<script type="text/javascript">
          MultiStateRadio.apply('.multistate');
    </script>

apply takes one argument which is a css selector of the container of the set of radio buttons.

If the effects.js scriptaculous library is present, then a transition effect will be used when switching between states.


===============================================================================

  Copyright (C) 2006  Pierre ANDREWS except the parts mentioned to be copyrighted otherwise.

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


var MultiStateRadio = {
  start: function() {
	Event.observe(window, 'load', function(){ MultiStateRadio.initialize()}, false);
  },

  //Add a .label reference to all input elements. Handy! Borrowed from...
  //http://www.codingforums.com/archive/index.php/t-14672
  addLabelProperties: function(f){
	if(typeof f.getElementsByTagName == 'undefined') return;
	var labels = f.getElementsByTagName("label"), label, elem, i = j = 0;
	
	while (label = labels[i++]){
	  if(typeof label.htmlFor == 'undefined') return;
	  elem = document.getElementById(label.htmlFor);
	  //elem = f.elements[label.htmlFor]; /* old method */
	  
	  if(typeof elem == 'undefined'){
		//no label defined, find first sub-input
		var inputs = label.getElementsByTagName("input");
		if(inputs.length==0){
		  continue;
		} else {
		  elem=inputs[0];
		}
	  } else if(typeof elem.label != 'undefined') { // label property already added
		continue;
	  } else if(typeof elem.length != 'undefined' && elem.length > 1 && elem.nodeName != 'SELECT'){
		for(j=0; j<elem.length; j++){
		  elem.item(j).label = label;
		}
	  }
	  elem.label = label;
	}
  },

  selectors: new Array(),

  apply: function(selector) {
	MultiStateRadio.selectors.push(selector);
  },
  
  initialize: function() {
	MultiStateRadio.selectors.each(MultiStateRadio.initializeSelectors);
  },

  initRadio: function(node) {
	  MultiStateRadio.addLabelProperties(node);
	  var checked = false;
	  var inputs = node.getElementsByTagName('input');
	  for(i=0;i<inputs.length;i++) {
		  var input = inputs[i];
		  if(input.type='radio') {
			  Element.addClassName(input.label,"_multistateradio");
			  if(input.checked) {
				  Element.show(input.label);
				  checked = true;
			  } else
				  Element.hide(input.label);
			  Element.hide(input);
			  
			  if((i+1) < inputs.length)
				  input.nextInput = inputs[i+1];
			  else
				  input.nextInput = inputs[0];

			  if((i-1) >= 0)
				  input.prevInput = inputs[i-1];
			  else
				  input.prevInput = inputs[inputs.length-1];
			  
		  }/* else if(input.childNodes.length > 0) {
			  $A(input.childNodes).each(MultiStateRadio.initRadio)
			  }*/
	  }
	  if(!checked) {
		  Element.show(inputs[0].label);
		  inputs[0].checked=true;
	  }
  },
  
  initializeSelectors: function(selector) {
	var multistates = $A($$(selector));
	multistates.each(MultiStateRadio.initRadio);
	$A($$(selector+" label")).each(function(el){
	  el.onclick = MultiStateRadio.switchState;
	});
	$A($$(selector+" label *")).each(function(el){
	  el.onclick = MultiStateRadio.switchState;
	});
  },
  
  switchState: function (ev) {
	var label = Event.findElement(ev,'label');
	var input = $(label.htmlFor);
	if(input && (input.type == 'radio')) {
	  input.checked = false;
	  var changeTo = input.nextInput;
	  if(ev.ctrlKey)
		  changeTo = input.prevInput;

	  changeTo.checked = true;		
	  if((typeof Effect != 'undefined') && (typeof Effect.Fade != 'undefined') && (typeof Effect.Appear != 'undefined')) {
		new Effect.Fade(label,{queue:'front',duration: 0.2});
		new Effect.Appear(changeTo.label,{queue:'end',duration: 0.2});
	  } else {
		Element.hide(label);
		Element.show(changeTo.label);
	  }
	  return false;
	}
  }
};
MultiStateRadio.start();

function $$() {
  var elements = new Array();

  for (var i = 0; i < arguments.length; i++) {
    var element = arguments[i];
    if (typeof element == 'string')
      element = document.getElementsBySelector(element);

    if (arguments.length == 1)
      return element;

    elements.push(element);
  }

  return elements;
}

/***********************************************************************/
/* The following code is Copyright (C) Simon Willison 2004 */
/*http://simon.incutio.com/js/getElementsBySelector.js*/
/*this is there because it's not yet supported by prototype.js
/* document.getElementsBySelector(selector)
   - returns an array of element objects from the current document
     matching the CSS selector. Selectors can contain element names, 
     class names and ids and can be nested. For example:
     
       elements = document.getElementsBySelect('div#main p a.external')
     
     Will return an array of all 'a' elements with 'external' in their 
     class attribute that are contained inside 'p' elements that are 
     contained inside the 'div' element which has id="main"

   New in version 0.4: Support for CSS2 and CSS3 attribute selectors:
   See http://www.w3.org/TR/css3-selectors/#attribute-selectors

   Version 0.4 - Simon Willison, March 25th 2003
   -- Works in Phoenix 0.5, Mozilla 1.3, Opera 7, Internet Explorer 6, Internet Explorer 5 on Windows
   -- Opera 7 fails 
*/

function getAllChildren(e) {
  // Returns all children of element. Workaround required for IE5/Windows. Ugh.
  return e.all ? e.all : e.getElementsByTagName('*');
}

document.getElementsBySelector = function(selector) {
  // Attempt to fail gracefully in lesser browsers
  if (!document.getElementsByTagName) {
    return new Array();
  }
  // Split selector in to tokens
  var tokens = selector.split(' ');
  var currentContext = new Array(document);
  for (var i = 0; i < tokens.length; i++) {
    token = tokens[i].replace(/^\s+/,'').replace(/\s+$/,'');;
    if (token.indexOf('#') > -1) {
      // Token is an ID selector
      var bits = token.split('#');
      var tagName = bits[0];
      var id = bits[1];
      var element = document.getElementById(id);
      if (tagName && element.nodeName.toLowerCase() != tagName) {
        // tag with that ID not found, return false
        return new Array();
      }
      // Set currentContext to contain just this element
      currentContext = new Array(element);
      continue; // Skip to next token
    }
    if (token.indexOf('.') > -1) {
      // Token contains a class selector
      var bits = token.split('.');
      var tagName = bits[0];
      var className = bits[1];
      if (!tagName) {
        tagName = '*';
      }
      // Get elements matching tag, filter them for class selector
      var found = new Array;
      var foundCount = 0;
      for (var h = 0; h < currentContext.length; h++) {
        var elements;
        if (tagName == '*') {
            elements = getAllChildren(currentContext[h]);
        } else {
            elements = currentContext[h].getElementsByTagName(tagName);
        }
        for (var j = 0; j < elements.length; j++) {
          found[foundCount++] = elements[j];
        }
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      for (var k = 0; k < found.length; k++) {
        if (found[k].className && found[k].className.match(new RegExp('\\b'+className+'\\b'))) {
          currentContext[currentContextIndex++] = found[k];
        }
      }
      continue; // Skip to next token
    }
    // Code to deal with attribute selectors
    if (token.match(/^(\w*)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/)) {
      var tagName = RegExp.$1;
      var attrName = RegExp.$2;
      var attrOperator = RegExp.$3;
      var attrValue = RegExp.$4;
      if (!tagName) {
        tagName = '*';
      }
      // Grab all of the tagName elements within current context
      var found = new Array;
      var foundCount = 0;
      for (var h = 0; h < currentContext.length; h++) {
        var elements;
        if (tagName == '*') {
            elements = getAllChildren(currentContext[h]);
        } else {
            elements = currentContext[h].getElementsByTagName(tagName);
        }
        for (var j = 0; j < elements.length; j++) {
          found[foundCount++] = elements[j];
        }
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      var checkFunction; // This function will be used to filter the elements
      switch (attrOperator) {
        case '=': // Equality
          checkFunction = function(e) { return (e.getAttribute(attrName) == attrValue); };
          break;
        case '~': // Match one of space seperated words 
          checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('\\b'+attrValue+'\\b'))); };
          break;
        case '|': // Match start with value followed by optional hyphen
          checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('^'+attrValue+'-?'))); };
          break;
        case '^': // Match starts with value
          checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) == 0); };
          break;
        case '$': // Match ends with value - fails with "Warning" in Opera 7
          checkFunction = function(e) { return (e.getAttribute(attrName).lastIndexOf(attrValue) == e.getAttribute(attrName).length - attrValue.length); };
          break;
        case '*': // Match ends with value
          checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) > -1); };
          break;
        default :
          // Just test for existence of attribute
          checkFunction = function(e) { return e.getAttribute(attrName); };
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      for (var k = 0; k < found.length; k++) {
        if (checkFunction(found[k])) {
          currentContext[currentContextIndex++] = found[k];
        }
      }
      // alert('Attribute Selector: '+tagName+' '+attrName+' '+attrOperator+' '+attrValue);
      continue; // Skip to next token
    }
    // If we get here, token is JUST an element (not a class or ID selector)
    tagName = token;
    var found = new Array;
    var foundCount = 0;
    for (var h = 0; h < currentContext.length; h++) {
      var elements = currentContext[h].getElementsByTagName(tagName);
      for (var j = 0; j < elements.length; j++) {
        found[foundCount++] = elements[j];
      }
    }
    currentContext = found;
  }
  return currentContext;
}

/* That revolting regular expression explained 
/^(\w+)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/
  \---/  \---/\-------------/    \-------/
    |      |         |               |
    |      |         |           The value
    |      |    ~,|,^,$,* or =
    |   Attribute 
   Tag
*/

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



===============================================================================

  Copyright (C) 2006  Pierre ANDREWS

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
	  input.nextInput.checked = true;		
	  if((typeof Effect != 'undefined') && (typeof Effect.Fade != 'undefined') && (typeof Effect.Appear != 'undefined')) {
		new Effect.Fade(label,{queue:'front',duration: 0.2});
		new Effect.Appear(input.nextInput.label,{queue:'end',duration: 0.2});
	  } else {
		Element.hide(label);
		Element.show(input.nextInput.label);
	  }
	  return false;
	}
  }
};
MultiStateRadio.start();

/*
  fichier javascript pour avoir un menu de suggestion a partir d'une url

  Testé sur 
  - firefox 1.0.6 (OsX et Linux)
  - firefox 1.5 (OsX)
  - Opera 8.51 (OsX)
  - Safari 1.3 et 2.0.1

  a utilier avec un code du genre:
  <input type="text" id="tags" name="tags" class='forml' cols='40' style="width: 100%;">
  <div id="suggest" class="suggest_list"></div>
  <div id="wait">Loading...</div>
  <script  type='text/javascript' src="prototype/prototype.js"></script>
  <script  type='text/javascript' src="ajaxTagMachine.js"></script>
  <script  type='text/javascript'> <!--
  groupe = new AjaxSuggestMenu('ajax.php', 'titre', 'tags','suggest');
  groupe.addVar('id_groupe','10');
  groupe.setWaiting('wait');
  groupe.setNbrLineVisible(5);
  --></script>

  - ajax.php est le nom du fichier à appeler
  - titre est la variable dans laquelle passer la valeur du champ
  - tags est l'identifiant du champ (ou un element de la dom)
  - suggest est l'identifiant de la DIV qui contiendra le menu (ou un element de la dom)

  * On peut ajouter des variables suplémentaires à passer au fichier ajax avec addVar
  * On peut spécifier l'identifiant d'une div (ou un element de la dom) qui contient un message d'attente avec setWaiting
  * On peut spécifier l'identifiant d'une div (ou un element de la dom) qui contiendra des messages de debug avec setAlert
  * On peut spécifier le nombre de ligne affichées avec setNbrLineVisible Si il y a juste une ligne de plus à afficher, on ne limite pas le nombre de lignes affichées. Les touches BAS et HAUT scroll
  * Le scroll est désactivé par défaut, il faut fait setScrollEnabled(true) pour qu'il s'active
  * On peut spécifier un nombre minimum de caractères avant d'envoyer des requetes ajax avec setMinLength

  Il faut aussi avoir ajaxTagMachine.css d'importé. On peut personaliser ce fichier jusqu'à un certain point.

  le fichier ajax.php doit retourner une chaîne JSON de la forme:
  {"propositions":[
                {"PropositionSimple": { "label":"test1" }},
                {"PropositionPair": { "label":"test2",
                                      "info":"0 forums"}},
                {"PropositionPair": { "label":"test3",
                                      "info":"10 forums"}},
                {"PropositionPair": { "label":"test4",
                                      "info":"0 forums"}},
                {"PropositionPair": { "label":"test5",
                                      "info":"10 forums"}}
  ]}

  représentant un tableau de propositions:
     o Les propositions de type PropositionSimple ont un attribut "label"
	 o Les propositions de type PropositionPair ont une paire d'attributs: "label" et "infos" qui sera affiché à droite en complément du label.

===============================================================================

  Copyright (C) 2005  Pierre ANDREWS

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

function printfire()
{
    if (document.createEvent)
    {
        printfire.args = arguments;
        var ev = document.createEvent("Events");
        ev.initEvent("printfire", false, true);
        dispatchEvent(ev);
    }
}

if((typeof Prototype=='undefined'))
	throw("La librairie Javascript Prototype doit etre presente.");

//======================================================================

Object.extend(Element, {
		visible: function() {
					  var visible = true;
					  for (var i = 0; i < arguments.length; i++) {
						  var element = $(arguments[i]);
						  visible = visible && (element.style.display != 'none');
					  }
					  return visible;
				  }
	});

//======================================================================

var AjaxSuggestMenu = Class.create();

AjaxSuggestMenu.prototype = {

	//variables
	//the url of the ajax query
	myUrl: '',
	//the variable that will pass the content of the field to the url
	urlVar: '',
	//the optional additional variable in the url
	vars: '',	
	//the current/future scrolling position
	scrollPos: 0,
	newScrollPos: 0,
	oldScrollPos: 0,
	//the number of elements in the menu
	totElem: 0,
	//the number of elements that should be displayed
	nbrLineVisible: 0,
	totHeight: 0,
	tailleListe: 0,
	scrollEnabled: false,
	
	//minimum number of characters before sending the request
	minLength: 0,

	//something has been selected in the menu by the user
	someSelected: false,
	selectedItem: -1,

	textfield: null,
	div: null,
   
	//the optional divs.
	alert: null,
	waiting: null,

	//constructeur
	initialize: function(myUrl, urlVar, textfieldID, suggestInID) {
		this.myUrl = myUrl;
		this.urlVar = urlVar;		
		//the textfield
		this.textfield = $(textfieldID);
		//the div containing the menu
		this.div = $(suggestInID);

		//move and set style correctly.
		var containerDiv = document.createElement('div');
		containerDiv.style.overflow = 'visible'; 
		containerDiv.style.width = this.textfield.clientWidth+'px';
		//copy the textfield style
		//		containerDiv.style.float = this.textfield.style.float;
		containerDiv.style.clear = this.textfield.style.clear;
		containerDiv.style.top = this.textfield.style.top;
		containerDiv.style.bottom = this.textfield.style.bottom;
		containerDiv.style.left = this.textfield.style.left;
		containerDiv.style.right = this.textfield.style.right;
		containerDiv.style.position = this.textfield.style.position;

		//correct textfield style		
		//		this.textfield.style.float='';
		this.textfield.style.clear='both';

		this.div.parentNode.insertBefore(containerDiv,this.div);
		containerDiv.appendChild(this.textfield);
		containerDiv.appendChild(this.div);
		//hide the div and set the width
		Element.hide(this.div);
		this.div.style.width = this.textfield.clientWidth+'px';
		this.div.style.clear="both";

		//register events
		//change the onsubmit behaviour to catch the Enter key
		Event.observe(this.textfield.form,'submit',this.formOnSubmitObserver.bindAsEventListener(this));	
		//if the textfield is unfocussed
		Event.observe(this.textfield,'blur',this.textfieldOnblur.bindAsEventListener(this));	
		Event.observe(this.textfield,'keyup',this.onkeyupObserver.bindAsEventListener(this));
		Event.observe(this.textfield,'keydown',this.onkeydownObserver.bindAsEventListener(this));		
	},

	formOnSubmitObserver: function(ev) {
		if(this.someSelected) {
			var sel = this.findSelected();
			if(sel != null)
				//this.textfield.value = this.findLastChild(sel.firstChild);
				this.updateLastWordText(this.findLastChild(sel.firstChild)); // erational upd
			else {
				var lst = $('suggested_list');
				this.textfield.value = this.findLastChild(lst.firstChild);
			}
		}
		this.unselectall();
		Element.hide(this.div);
		Event.element(ev).submit();
	},
	
	textfieldOnblur: function(ev) {
		this.unselectall();
		Element.hide(this.div);
	},

	/*******************************************************************************
	 *                          the seters for options                             *
	 *******************************************************************************/

	//add an additional variable to send to the url
	addVar: function(va,value) {
		this.vars = this.vars+"&"+va+"="+this.escape(value);
	},

	//set the debuging div
	setAlert: function(elem) {
		this.alert = $(elem);
	},

	//set the div to display while we are downloading
	setWaiting: function(elem) {
		this.waiting = $(elem);
		Element.hide(this.waiting);
	},
	
	//set the number of line visible, if more lines are to be displayed, the user can scroll
	setNbrLineVisible: function(nbr) {
		this.nbrLineVisible = nbr;
	},

	//enable scrolling (alpha)
	setScrollEnabled: function(bool) {
		this.scrollEnabled = bool;
	},

	setMinLength: function(intValue) {
		this.minLength = intValue;
	},

	//DEBUGing tool ************************************************************
	sendMsg: function(msg) {
		if(this.alert != null) this.alert.innerHTML = msg;
	 	else if(typeof printfire == 'function') printfire(msg);
	},

	appendMsg: function(msg) {
		if(this.alert != null) this.alert.innerHTML += msg;
		else if(typeof printfire == 'function') printfire(msg);
	},
	
	// erational extra func *************************************************
	updateLastWordText: function(word) {	   
	   originalTextfield = this.textfield.value;
	   lastSpace = originalTextfield.lastIndexOf(" ");
	   if (lastSpace==-1) this.textfield.value = word;
                   else this.textfield.value = originalTextfield.substr(0,lastSpace)+ " " +word;  
	   
  },

	//TOOLS ************************************************************
	escape: function(field) {
		if(escape) {
			return escape(field)
		} else if(encodeURIComponent) {
			return encodeURIComponent(field);
		}
	},
	
	//is there a second column in the array received
	withInfo: function(obj) {
		if(obj != null)
			return (obj.PropositionPair!=null);
		return false;
	},

	findLastChild: function(node) {
		if((node.childNodes != null) && (node.childNodes.length > 0)) {
			if(node.childNodes[0].innerHTML != null)
				return this.findLastChild(node.childNodes[0]);
		}
		return node.innerHTML;
	},

	findPosInParent: function(node) {
		if(node.previousSibling == null) return 0;
		return 1+this.findPosInParent(node.previousSibling);
	},

	findSelected: function() {
	  var list = this.div.firstChild;
		if(list != null && this.selectedItem >= 0) {
		  return list.childNodes.item(this.selectedItem);
		}
		return null;
	},

	unselectall: function() {
		var sel = this.findSelected();
		if(sel != null) sel.id = '';
	},

	//SCROLLING ************************************************************
	updateScrollPos: function(pos) {
	  this.oldScrollPos = this.scrollPos;
	  this.newScrollPos = pos;
	  this.scrollPos = pos;
	  if(this.scrollEnabled && (this.div.firstChild != null))  this.div.firstChild.style.top = this.scrollPos;
	  this.appendMsg("Pos: "+this.scrollPos);
	},

	worthScroll: function() {
	  this.sendMsg(this.totHeight+"+"+(this.newScrollPos-this.oldScrollPos)+"="+this.percent(this.newScrollPos,this.oldScrollPos,this.totHeight));
	  return this.scrollEnabled && (this.nbrLineVisible > 0) && (this.totElem > this.nbrLineVisible) && this.percent(this.newScrollPos,this.oldScrollPos,this.totHeight) >= 90;
	},

	percent: function(a,b,c) {
	  var d = a-b;
	  if(a < b) d = b-a;
	  return d*100/c;
	},

	scrollDown: function(size) {
	  this.newScrollPos -= size;
	  if(this.worthScroll()) {
		this.updateScrollPos(this.newScrollPos);
	  }
	},

	scrollUp: function(size) {
	  this.newScrollPos += size;
	  if(this.worthScroll()) {
		this.updateScrollPos(this.newScrollPos);
	  }
	},

	computeBottomPos: function() {
	  var last = this.div.firstChild.lastChild;
	  var init = last.offsetHeight;
	  var hauteur = 0;
	  while(hauteur < this.totHeight) {
		hauteur += last.offsetHeight;
		last = last.previousSibling;
	  }
	  return -(this.tailleListe - (hauteur-init));
	},

	//Selecting with the key ************************************************************
	selectUp: function() {
		var selected = this.findSelected();
		this.selectedItem--;
		this.sendMsg(this.selectedItem);
		if(selected != null) {
			selected.id = '';
			var prev = selected.previousSibling;
			if(prev != null) {
				prev.id = 'suggested_list_selected_item';
				this.scrollUp(prev.offsetHeight);
			} else {
				selected.parentNode.lastChild.id = 'suggested_list_selected_item';
				this.selectedItem = selected.parentNode.childNodes.length-1;
				
				this.updateScrollPos(this.computeBottomPos());
			}
		} else {
		  var list = this.div.firstChild;
			if(list != null && list.childNodes.length > 0) {
				list.lastChild.id = 'suggested_list_selected_item';
				this.selectedItem = list.childNodes.length-1;
				this.updateScrollPos(this.computeBottomPos());
			}
		}
	},

	selectDown: function() {
	  var selected = this.findSelected();
	  this.selectedItem++;
	  this.sendMsg(this.selectedItem);
		this.appendMsg(selected);
		if(selected != null) {
			selected.id = '';
			var next = selected.nextSibling;
			if(next != null) {
			  next.id = 'suggested_list_selected_item';
			  this.scrollDown(next.offsetHeight);
			} else {
				selected.parentNode.firstChild.id = 'suggested_list_selected_item';
				this.selectedItem = 0;
				this.appendMsg('ici');
				this.updateScrollPos(0);
			}
		} else {
		  var list = this.div.firstChild;
			if(list != null && list.childNodes.length > 0) {
				list.firstChild.id = 'suggested_list_selected_item';
				this.selectedItem = 0;
				this.appendMsg('la');
				this.updateScrollPos(0);
			}
		}
	},
  
	//create the list to display
	//<ul style="margin: 0pt; padding: 0pt; overflow: hidden; position: absolute; width: 100%; list-style-type: none; top: 0pt;" id="suggested_list">
    //<li class="pair" style="overflow: hidden; display: block; padding-left: 0pt;">
    //    <a> test1 </a>
    //</li>
    //<li style="overflow: hidden; display: block; padding-left: 0pt;">
    //    <a> test2 </a>
    //</li>
    //<li class="pair" style="overflow: hidden; display: block; padding-left: 0pt;">
    //    <a> test3 </a>
    //</li>
	//</ul>
	updateSuggest: function(data) {
		if(data != null) {
			suggestions = data.propositions;
		}
		if(suggestions != null) {
			while(this.div.childNodes.length>0) {
				this.div.removeChild(this.div.childNodes[0]);
			}

			var list = document.createElement("ul");
			list.id = "suggested_list"
			list.style.position = 'absolute';
			list.style.width = this.textfield.clientWidth+'px';
			list.style.padding = '0';
			list.style.margin = '0';
			list.style.listStyleType = 'none';
			list.style.overflow = 'hidden';
			list.style.clear="both";
			this.div.appendChild(list);
	  
			this.totElem = this.scrollEnabled?suggestions.length:((suggestions.length < this.nbrLineVisible)?suggestions.length:this.nbrLineVisible);
			this.selectedItem = -1;

			for(var sugI=0; sugI<this.totElem; ++sugI){
				var a = document.createElement("a");

				var item = document.createElement("li");
				item.style.display = 'block';
				item.style.paddingLeft = '0';
				item.style.overflow = 'hidden';
				item.style.width = this.textfield.clientWidth+'px';
				if(sugI % 2 == 0) Element.addClassName(item,'pair');

				Event.observe(item,'mouseover',this.liOnMouseOver.bind(this));
				Event.observe(item,'mousedown',this.liOnMouseDown.bind(this));
				
				if(this.withInfo(suggestions[sugI])) { 
					var spanLeft = document.createElement("span");
					Element.addClassName(spanLeft,'sugLeft');
					a.innerHTML = suggestions[sugI].PropositionPair.label;
					spanLeft.appendChild(a);
					var spanRight = document.createElement("span");
					Element.addClassName(spanRight,'sugRight');
					spanRight.innerHTML = suggestions[sugI].PropositionPair.info;
					item.appendChild(spanLeft);
					item.appendChild(spanRight);
				} else {		  
					a.innerHTML = suggestions[sugI].PropositionSimple.label;
					item.appendChild(a);
				}

				list.appendChild(item);
			}
			if(this.waiting != null) Element.hide(this.waiting);

			Element.show(this.div);
			this.tailleListe = list.offsetHeight;

	  
			this.updateScrollPos(0);
			if((this.nbrLineVisible <= 0) || (this.nbrLineVisible+1 >= this.totElem)) {
				this.totHeight = list.offsetHeight;
			} else {
			  this.totHeight = this.nbrLineVisible*(list.offsetHeight/this.totElem)+
								(this.scrollEnabled?(0.5*(list.offsetHeight/this.totElem)):0);
			}
			this.div.style.height =  this.totHeight+'px';
	  
		} else {
			this.unselectall();
			Element.hide(this.div)
		}
	},

	//££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££
	// mouse listeners for the list
	//££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££££

	liOnMouseOver:  function(ev) {
		this.unselectall(); 
		this.selectedItem = this.findPosInParent(Event.findElement(ev,'li'));
		Event.findElement(ev,'li').id = 'suggested_list_selected_item';
	},

	liOnMouseDown: function(ev) {
		this.unselectall();
		//this.textfield.value = this.findLastChild(Event.findElement(ev,'li'));    
    this.updateLastWordText(this.findLastChild(Event.findElement(ev,'li'))); // erational upd
		Element.hide(this.div);
	},

	// AJAX

	evalAndUpdate: function(originalRequest) {
		this.sendMsg(originalRequest.responseText);
		this.updateSuggest(eval('('+originalRequest.responseText+')'));
	},

	//send ajax request.
	charger_id_url: function(myUrl,pars) {
                if(myUrl.indexOf('?') > 0) {
	                pars = myUrl.substr(myUrl.indexOf('?')+1)+'&'+pars;
			myUrl = myUrl.substr(0,myUrl.indexOf('?'));
		}
		this.sendMsg(myUrl+'?'+pars);

		if(this.waiting != null) Element.show(this.waiting);
		Element.hide(this.div);
	
		var myAjax = new Ajax.Request( myUrl, {method: 'get', parameters: pars, onComplete: this.evalAndUpdate.bind(this)} ); 
	},

	/************************************************************************
	 *                            Listener for the textfield                *
	 ***********************************************************************/

	onkeyupObserver: function(ev) {
		var myKey = ev.keyCode;
		
		switch(myKey) {
			case Event.KEY_TAB: //TAB
			case Event.KEY_ESC: //ESC
			case Event.KEY_UP: //UP
			case Event.KEY_RIGHT: //RIGTH
			case Event.KEY_LEFT: //LEFT
			case Event.KEY_DOWN: //DWN
				if(Element.visible(this.div)) {
					ev.cancelBubble = true;
					if (ev.stopPropagation) ev.stopPropagation();
				}
				break;
			default: //any key
				//		if(ev.which > 0) //ça marchait mieux, verifié que la touche n'est pas une touche fleche etc..., mais ça colle pas avec IE!!!
				if((this.minLength == 0) || (this.textfield.value.length >= this.minLength))
					this.charger_id_url(this.myUrl,this.urlVar+'='+encodeURIComponent(this.textfield.value)+this.vars);
		}
	},

	onkeydownObserver: function(ev) {
		var myKey = ev.keyCode;
		
		if(!Element.visible(this.div)) return;

		switch(myKey) {
			case Event.KEY_TAB: //TAB				
				//for Safari compatibilité, stop the popagation of the event
				ev.cancelBubble = true;
				if (ev.stopPropagation) ev.stopPropagation();
				var sel = this.findSelected();
				if(sel != null)
					this.textfield.value = this.findLastChild(sel.firstChild);
				else {
					var lst = $('suggested_list');
					this.textfield.value = this.findLastChild(lst.firstChild);
				}					
				this.unselectall();
				Element.hide(this.div);
				setTimeout("$('"+this.textfield.id+"').focus()",0);
				break;
			case Event.KEY_ESC: //ESC
		//for Safari compatibilité, stop the popagation of the event
				ev.cancelBubble = true;
				if (ev.stopPropagation) ev.stopPropagation();
				this.unselectall();
				Element.hide(this.div);
				this.someSelected = false;
				this.selectedItem = 0;
				break;
			case Event.KEY_UP: //UP
				//for Safari compatibilité, stop the popagation of the event
				ev.cancelBubble = true;
				if (ev.stopPropagation) ev.stopPropagation();
				this.someSelected = true;
				this.selectUp();
				break;
			case Event.KEY_RIGHT: //RIGHT
				//for Safari compatibilité, stop the popagation of the event
				ev.cancelBubble = true;
				if (ev.stopPropagation) ev.stopPropagation();
				var sel = this.findSelected();
				if(sel != null)
					this.textfield.value = this.findLastChild(sel.firstChild);
				this.unselectall();
				Element.hide(this.div);
				break;
			case Event.KEY_DOWN: //DWN
				//for Safari compatibilité, stop the popagation of the event
				ev.cancelBubble = true;
				if (ev.stopPropagation) ev.stopPropagation();
				this.someSelected = true;
				this.selectDown();
				break;	  
			default:
				//	if((this.minLength == 0) || (this.textfield.value.length >= this.minLength))
				//	this.charger_id_url(this.myUrl,this.urlVar+'='+encodeURIComponent(this.textfield.value)+this.vars);
				break;
		}
	}
}

/*************************************************************
(C) www.dhtmlgoodies.com, October 2005

This is a script from www.dhtmlgoodies.com. You will find this
and a lot of other scripts at our website.	

Terms of use:
You are free to use this script as long as the copyright message
is kept intact. However, you may not redistribute, sell or repost
it without our permission.

Thank you!

www.dhtmlgoodies.com
Alf Magne Kalleland

**************************************************************/

/******************************
version fortement modifiee pour
adapter au deplacement dans des
listes imbriquees
******************************/
// Y offset for the little arrow indicating where the node should be inserted.
var offsetYInsertDiv = -3;
if(!document.all) offsetYInsertDiv = offsetYInsertDiv - 7; 	// No IE

// la partie dans laquelle on va pouvoir bricoler
var arrParent = false;
// le div dans lequel on va greffer les morceaux en cours de drag
var arrMoveCont = false;
// compteur pour determier quand on lache la souris ?
var arrMoveCounter = -1;

// la coordonnee gauche de la zone "draggable"
var leftPosArrangableNodes = false;
// la largeur de la zone "draggable"
var widthArrangableNodes = false;
// ???
var nodePositionsY = new Array();
// ???
var nodeHeights = new Array();

// le div qui contient la fleche d'insertion
var arrInsertDiv = false;

// le bloc qu'on pointe comme destination
var arrTarget = false;
// est-ce qu'on pointe pour inserer apres (true) ou en dessous (false)
var afterTarget = false;
// le bloc apres arrTarget
var arrNextSibling = false;
// true si on est en train de pointer au dessus de la racine
var insertAsFirstNode = false;
// le bloc qu'on valide comme destination
var arrNodesDestination = false;


// pour debugger ...
var whereAmI = false;
//var dump = false;

function cancelEvent() {
	return false;
}

function getTopPos(inputObj) {	
  var returnValue = inputObj.offsetTop;
  while((inputObj = inputObj.offsetParent) != null){
  	returnValue += inputObj.offsetTop;
  }
  return returnValue;
}

function getBottomPos(inputObj) {
//alert(inputObj.id+" => "+inputObj.offsetTop);
	var sousLi= inputObj.getElementsByTagName('LI');
	if(sousLi[0]) {
		inputObj= sousLi[0];
//alert(inputObj.id+" => "+inputObj.offsetTop);
	}
	var h= inputObj.offsetHeight;
	var returnValue = inputObj.offsetTop;
	while((inputObj = inputObj.offsetParent) != null){
//alert(inputObj.id+" ===> ".inputObj.offsetTop);
		returnValue += inputObj.offsetTop;
	}
	if(sousLi[0]) {
		return returnValue;
	} else {
		return returnValue+h;
	}
}

function getLeftPos(inputObj) {
  var returnValue = inputObj.offsetLeft;
  while((inputObj = inputObj.offsetParent) != null)
  	returnValue += inputObj.offsetLeft;
  return returnValue;
}

function clearMovableDiv() {
	if(arrMoveCont.getElementsByTagName('LI').length>0){
		if(arrNextSibling)
			arrParent.insertBefore(arrTarget,arrNextSibling);
		else
			arrParent.appendChild(arrTarget);			
	}
	arrTarget = false;
}

function initMoveNode(e) {
//alert("initMoveNode "+this.id+arrTarget);
	// pour ne pas reagir sur tous les parents d'un item
	if(arrTarget) {
		// pour annuler le hack de folder-tree-static.js
		//if(arrTarget==true) arrTarget=false;
		e.cancelBubble=true;
		return;
	}

	clearMovableDiv();
	if(document.all) e= event;
	arrMoveCounter = 0;
	arrTarget = this;
	if(this.nextSibling) {
		arrNextSibling = this.nextSibling;
	} else {
		arrNextSibling = false;
	}
	timerMoveNode();
	arrMoveCont.parentNode.style.left = e.clientX + 'px';
	arrMoveCont.parentNode.style.top = e.clientY + 'px';
	return false;
}

function timerMoveNode() {
	if(arrMoveCounter>=0 && arrMoveCounter<10){
		arrMoveCounter = arrMoveCounter +1;
		setTimeout('timerMoveNode()',20);
	}
	if(arrMoveCounter>=10){
		arrMoveCont.appendChild(arrTarget);
	}
}

function arrangeNodeMove(e) {
	if(document.all)e = event;
	if(arrMoveCounter<10)return;
	if(document.all && arrMoveCounter>=10 && e.button!=1){
		arrangeNodeStopMove();
	}

	arrMoveCont.parentNode.style.left = e.clientX + 'px';
	arrMoveCont.parentNode.style.top = e.clientY + 'px';	

	var tmpX = e.clientX;
	var tmpY = e.clientY;
	arrInsertDiv.style.display='none';
	arrNodesDestination = false;

	if(e.clientX<leftPosArrangableNodes
	 || e.clientX>leftPosArrangableNodes + widthArrangableNodes)
		return; 

	var subs = arrParent.getElementsByTagName('LI');
	// on parcours a l'envers pour traiter les plus profond d'abord
	for(var no=subs.length-1;no>=0;no--){
		var topPos = getTopPos(subs[no]);
		var leftPos= getLeftPos(subs[no]);
		var tmpHeight= subs[no].offsetHeight;
		var tmpWidth = subs[no].offsetWidth;

		// insertion avant le premier
		if(no==0 && tmpY<=topPos && tmpY>=topPos-5){
			arrInsertDiv.style.top = (topPos + offsetYInsertDiv) + 'px';
			arrInsertDiv.style.left = leftPos + 'px';
			arrInsertDiv.style.display = 'block';				
			arrNodesDestination = subs[no];	
			insertAsFirstNode = true;
			whereAmI.innerHTML='DEBUT';
			return;
		}

// A REVOIR : 20 en dur car width est énorme et déborde. comment ameliorer ca ?
		// on est au dessus d'un noeud
		if(tmpY>=topPos && tmpY<=(topPos+tmpHeight)){
			var bottomPos= getBottomPos(subs[no]);

			arrInsertDiv.style.top = (bottomPos + offsetYInsertDiv)+'px';
			arrInsertDiv.style.display = 'block';
			arrNodesDestination = subs[no];
			insertAsFirstNode = false;

			// sur sa partie gauche
			if(tmpX<=(leftPos+20)) {
				// = insertion apres ce noeud
				arrInsertDiv.style.left= leftPos + 'px';
				whereAmI.innerHTML= 'APRES '+arrNodesDestination.id;
				afterTarget= true;
			} else {
				// sinon = insertion comme fils de ce noeud
				arrInsertDiv.style.left= (leftPos+20) + 'px';
				whereAmI.innerHTML= 'SOUS '+arrNodesDestination.id;
				afterTarget= false;
			}
			return;
		}
	}
	whereAmI.innerHTML='???';
}

function arrangeNodeStopMove() {
	arrMoveCounter = -1; 
	arrInsertDiv.style.display='none';

	if(arrNodesDestination) {
		if(insertAsFirstNode) {
			arrParent.insertBefore(arrTarget, arrNodesDestination);		
		} else if(afterTarget) {
			next= arrNodesDestination.nextSibling;
			var parent= arrNodesDestination.parentNode;
			if(next) {
				parent.insertBefore(arrTarget, next);
			} else {
				parent.appendChild(arrTarget);
			}
		} else {
			subs= arrNodesDestination.getElementsByTagName('LI');
			if(subs && subs[0]) {
				var parent= subs[0].parentNode;
				parent.insertBefore(arrTarget, subs[0]);
			} else {
				ul= document.createElement('UL');
				ul.appendChild(arrTarget);
				arrNodesDestination.appendChild(ul);
			}
		}
		arrTarget= false;
	}
	arrNodesDestination = false;
	clearMovableDiv();
}

function saveArrangableNodes() {
	var nodes = arrParent.getElementsByTagName('LI');
	var string = "";
	for(var no=0;no<nodes.length;no++){
		if(string.length>0)string = string + ',';
		string = string + nodes[no].id;		
	}

	document.forms[0].hiddenNodeIds.value = string;

	// Just for testing
	document.getElementById('arrDebug').innerHTML = 'Ready to save these nodes:<br>' + string.replace(/,/g,',<BR>');	

	// document.forms[0].submit(); // Remove the comment in front of this line when you have set an action to the form.
}

function initArrangableNodes() {
//alert('initArrangableNodes');
	arrParent = document.getElementById('arrangableNodes');
	arrMoveCont = document.getElementById('movableNode').getElementsByTagName('UL')[0];
	arrInsertDiv = document.getElementById('arrDestIndicator');

	whereAmI= document.getElementById('whereami');
	//dump= document.getElementById('dump');

	leftPosArrangableNodes = getLeftPos(arrParent);
	arrInsertDiv.style.left = leftPosArrangableNodes - 5 + 'px';
	widthArrangableNodes = arrParent.offsetWidth;
	
	var subs = arrParent.getElementsByTagName('LI');
	for(var no=0;no<subs.length;no++){
		subs[no].onclick /*mousedown*/ = initMoveNode;
		subs[no].onselectstart = cancelEvent;	
	}

	document.documentElement.onmouseup = arrangeNodeStopMove;
	document.documentElement.onmousemove = arrangeNodeMove;
	document.documentElement.onselectstart = cancelEvent;
}

onloads += 'initArrangableNodes();';

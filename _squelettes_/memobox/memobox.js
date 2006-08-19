/*
 * memobox for SPIP
 *
 * Copyright (c) 2006 Renato Formato (renatoformato@virgilio.it)
 * Licensed under the GPL License:
 *   http://www.gnu.org/licenses/gpl.html
 *
 */
//CSS style selector to match the draggables
var DRAGGABLES_SEL = '.titre a';
//CSS style selector to match the element you want to append the drop box to
var MEMOBOX_CONTAINER = "#navigation";
var MEMOBOX_HEADER = '<p id="memobox_heading">Drag & drop titles here to store them</p>';
var MEMOBOX_ITEM = '<li> <a href="#" class="memobox_delete" title="delete this item"><span>delete</span></a></li>';

var memobox_ul,memobox;

$(document).ready(function(){
	$(DRAGGABLES_SEL).addClass('memobox_drag');
	$(MEMOBOX_CONTAINER).append('<div id="memobox"></div>');
	memobox = $('#memobox').Droppable({accept:'memobox_drag',ondrop:memobox_dropit}).append(MEMOBOX_HEADER).append('<ul></ul>');
	memobox_ul = $('ul',memobox);
	$(DRAGGABLES_SEL).Draggable({ghosting:true,revert:true,zIndex:1,opacity:0.8});
	memobox_init();
})

function memobox_dropit(drop,drag) {
	memobox_addItem.apply(memobox_ul.append(MEMOBOX_ITEM),[drag.cloneNode(true)]);
	memobox_createCookieString();
}

function memobox_addItem(node) {
	this.find('>li:last-child a').click(memobox_delItem).parent().prepend(node).end().end();	
}

function memobox_delItem() {
	$(this).parent().remove();
	/*if(!$('li',memobox_ul).size()) {
		memobox.prepend(MEMOBOX_HEADER);
	}*/
	memobox_createCookieString();
}

function memobox_createCookieString() {
	var c='';
	memobox_ul.find('li>a:first-child').each(function(){
		c+=this.href+'|'+this.title+'|'+$(this).text()+',';
	}).end();
	memobox_createCookie('memobox',c,365);
}

function memobox_init() {
	var cookie = memobox_readCookie('memobox');
	var items = cookie ? cookie.split(',') : [];
	for(var i=0;i<items.length-1;i++) {
		var args = items[i].split('|'); 
		memobox_addItem.apply(memobox_ul.append(MEMOBOX_ITEM),['<a href="'+args[0]+'" title="'+args[1]+'">'+args[2]+'</a>']);
	}
	//if($('li',memobox_ul).size()) $('>p',memobox).remove();
}

// http://www.quirksmode.org/js/cookies.html 
function memobox_createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function memobox_readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

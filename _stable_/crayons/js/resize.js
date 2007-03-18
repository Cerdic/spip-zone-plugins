/* ***** BEGIN LICENSE BLOCK *****
 * This file is part of DotClear.
 * Copyright (c) 2005 Nicolas Martin & Olivier Meunier and contributors. All
 * rights reserved.
 *
 * DotClear is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * DotClear is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with DotClear; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ***** END LICENSE BLOCK *****
*/

function jsToolBar(textarea) {
	if (!document.createElement) { return; }
	
	if (!textarea) { return; }
	
	if ((typeof(document["selection"]) == "undefined")
	&& (typeof(textarea["setSelectionRange"]) == "undefined")) {
		return;
	}
	
	this.textarea = textarea;
	
	this.editor = document.createElement('div');
	this.editor.className = 'jstEditor';
	
	this.textarea.parentNode.insertBefore(this.editor,this.textarea);
	this.editor.appendChild(this.textarea);
	
	this.toolbar = document.createElement("div");
	this.toolbar.className = 'jstElements';
	this.editor.parentNode.insertBefore(this.toolbar,this.editor);
	
	// Dragable resizing (only for gecko)
	if (this.editor.addEventListener)
	{
		this.handle = document.createElement('div');
		this.handle.className = 'jstHandle';
		var dragStart = this.resizeDragStart;
		var This = this;
		this.handle.addEventListener('mousedown',function(event) { dragStart.call(This,event); },false);
		// fix memory leak in Firefox (bug #241518)
		window.addEventListener('unload',function() { 
				var del = This.handle.parentNode.removeChild(This.handle);
				delete(This.handle);
		},false);
		
		this.editor.parentNode.insertBefore(this.handle,this.editor.nextSibling);
	}
	
	this.context = null;
	this.toolNodes = {}; // lorsque la toolbar est dessinée , cet objet est garni 
					// de raccourcis vers les éléments DOM correspondants aux outils.
}


/** Resizer
-------------------------------------------------------- */
jsToolBar.prototype.resizeSetStartH = function() {
	this.dragStartH = this.textarea.offsetHeight + 0;
};
jsToolBar.prototype.resizeDragStart = function(event) {
	var This = this;
	this.dragStartY = event.clientY;
	this.resizeSetStartH();
	document.addEventListener('mousemove', this.dragMoveHdlr=function(event){This.resizeDragMove(event);}, false);
	document.addEventListener('mouseup', this.dragStopHdlr=function(event){This.resizeDragStop(event);}, false);
};

jsToolBar.prototype.resizeDragMove = function(event) {
	this.textarea.style.height = (this.dragStartH+event.clientY-this.dragStartY)+'px';
};

jsToolBar.prototype.resizeDragStop = function(event) {
	document.removeEventListener('mousemove', this.dragMoveHdlr, false);
	document.removeEventListener('mouseup', this.dragStopHdlr, false);
};

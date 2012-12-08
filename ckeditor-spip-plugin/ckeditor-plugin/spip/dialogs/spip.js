/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

function numericEntitiesDecode(st) {
	return st.replace(/&#(\d+);/g,
				function() { return String.fromCharCode(arguments[1]); }
			).replace(/&nbsp;/ig, '\u00a0') ;
}

var spipDialog ;
var spipLinks ; 

function loadSelectFromSpipJSON(dialog, page, tabId, itemId) {
	jQuery.getJSON(CKEDITOR.spipurl+page, function (data) {
		var mySelect = dialog.getContentElement(tabId, itemId) ;
		mySelect.clear() ;
		dialog.options[tabId][itemId] = { } ;
		jQuery.each(data, function(ndx, item) {
			if (item.id != "-1") {
				var level,pretitre = '' ;
				level = item.level ;
				while(level-->0) { pretitre = pretitre + '\u00a0\u00a0' ; } // deux espaces insécables
				if (pretitre) { pretitre = pretitre + '\u21b3' ; } // fleche
				mySelect.add(pretitre + numericEntitiesDecode(item.titre), ndx) ;
				dialog.options[tabId][itemId][ndx] = item ;
			}
		}) ;
		if (itemId == 'selectCategorie') {
			updateItem(dialog) ;
		}
	}) ;
}

function ucfirst(st) {
	var parts = st.match(/^(.)(.*)$/) ;
  	return parts[1].toUpperCase() + parts[2] ;
}

function updateItem(dialog) {
	if (dialog) {
		var type = dialog.getValueOf('tab1', 'selectType') ;
		var selectCategorie = dialog.getContentElement('tab1', 'selectCategorie') ;
		var selectItem = dialog.getContentElement('tab1', 'selectItem') ;
		var editor = dialog.getParentEditor() ;
		if(selectCategorie.getLabel() != numericEntitiesDecode(spipLinks[type].nomCategorie)) {
			selectCategorie.setLabel(numericEntitiesDecode(spipLinks[type].nomCategorie)) ;
			loadSelectFromSpipJSON(dialog, spipLinks[type].selectCategorie, 'tab1', 'selectCategorie') ;
		}	
		if (spipLinks[type].selectItem) {
			selectItem.setLabel(numericEntitiesDecode(spipLinks[type].nomItem)) ;
			selectItem.getElement().show() ;
			var rubrique = dialog.getValueOf('tab1', 'selectCategorie') ;
			if (dialog.options['tab1']['selectCategorie'][rubrique]) {
				loadSelectFromSpipJSON(dialog, spipLinks[type].selectItem + dialog.options['tab1']['selectCategorie'][rubrique].id, 'tab1', 'selectItem') ;
			}
		} else {
			selectItem.setLabel('') ;
			selectItem.getElement().hide() ;
		}
	}
}

CKEDITOR.dialog.add( 'spip', function( editor )
{
	var dialog ;

	return {
		title : editor.lang.spip.title,
		minWidth : 360,
		minHeight : 150,
		onLoad : function() // tiré de uiColor
		{
			spipDialog = dialog = this;
			this.options = {'tab1' : { 'selectCategorie' : {}, 'selectItem' : {} } } ;
			this.setupContent() ;
	
			// #3808
			if ( CKEDITOR.env.ie7Compat )
				dialog.parts.contents.setStyle( 'overflow', 'hidden' );
		
		},
		onShow : function () {
			var editor = this.getParentEditor() ;
			if (editor.getSelection()) {
				var selection ;
				if (CKEDITOR.env.ie) {
					selection = editor.getSelection().document.$.selection.createRange().text;
				} else {
					selection  = editor.getSelection().getNative();
				}  
				this.setValueOf('tab1', 'linkText', selection) ;
			}
			jQuery.getJSON(CKEDITOR.spipurl + '?page=spiplinks-json', function (json) {
				var selectType = dialog.getContentElement('tab1', 'selectType') ;
				selectType.clear() ;
				spipLinks = json ;
				jQuery.each(spipLinks, function(type, item) {
					selectType.add(numericEntitiesDecode(item.label), type) ;
				}) ;
				updateItem(dialog) ;
			}) ;
		},
		onOk : function () {
			
			var idUrl, urlSpip, defaultText ;
		  	if (spipLinks[this.getValueOf('tab1', 'selectType')].selectItem) {
				idUrl = this.getValueOf('tab1', 'selectItem') ;
				defaultText = this.options['tab1']['selectItem'][idUrl].titre ;
				urlSpip = this.options['tab1']['selectItem'][idUrl].url ;
			} else {
				idUrl = this.getValueOf('tab1', 'selectCategorie') ;
				defaultText = this.options['tab1']['selectCategorie'][idUrl].titre ;
				urlSpip = this.options['tab1']['selectCategorie'][idUrl].url ;
			}
			defaultText = defaultText.replace(/^(\s|&#160;)+/g, '') ;
		
			var editor = this.getParentEditor() ;
			var sel = editor.getSelection() ;
			var element = sel.getSelectedElement() ;
			var linktext = this.getValueOf('tab1', 'linkText') ;
			var text=( linktext ? linktext : defaultText ) ;
			
			
			if (element && (element.getName() == 'a')) { // on a déja un <a >, on l'utilise :
				if (linktext) { element.setText(linktext) ; }
				element.setAttribute('href', urlSpip) ;
				editor.insertElement(element) ;
			} else if (element && (element.getParent().getName() == 'a')) { // le parent est un <a, on l'utilise :
				var elparent = element.getParent() ;
				if (linktext) { elparent.setText(linktext) ; }
				elparent.setAttribute('href', urlSpip) ;
				editor.insertElement(elparent) ;
			} else if (element && (element.getName() == 'img')) {
				var link = editor.document.createElement( 'a' );
				link.setAttribute('href', urlSpip) ;
				if (linktext) link.setText(linktext) ;
				link.append(element.clone()) ;
				editor.insertElement(link) ;
			} else { // sinon, on en crée un :
				var parentNode = editor.getSelection().getRanges()[0].startContainer.getParent() ;
				if (parentNode.getName() == 'a') {
					if (linktext) { parentNode.setText(linktext) ; }
					parentNode.setAttribute('href', urlSpip) ;
				} else {
					var link = editor.document.createElement( 'a' );
					link.setAttribute('href', urlSpip) ;
					link.setText(numericEntitiesDecode(text)) ;
					editor.insertElement(link) ;
				}
			}
		},
		contents : [
			{
				id : 'tab1',
				label : '',
				title : '',
				expand : true,
				padding : 0,
				elements :
				[
				{
					id : '',
					type : 'vbox',
					children :
					[
					{
						id : 'linkText',
						type : 'text',
						label : editor.lang.spip.linktext
					},
					{
						id : '',
						type : 'hbox',
						children :
						[
							{
								id : 'selectType',
								label : editor.lang.spip.linktype,
								type : 'select',
								items : [],
								onChange: function() {
									updateItem(dialog) ;
								}
							},
							{
								id : '',
								type : 'vbox',
								children :
								[
									{
										id : 'selectCategorie',
										label : '_',
										type : 'select',
										items : [],
										onChange: function() {
											updateItem(dialog) ;
										}

									},
									{
										id : 'selectItem',
										label : '_',
										type : 'select',
										items : []
									}
								]
							}
						]
					}
					]
				}
				]
			}
		],
		buttons : [ CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton ]
	};
} );

/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * Plugin d'insertion de note de bas de page. Basé sur le plugin place holder. Frank SAURET.
 */

// Enregistre la commande.
FCKCommands.RegisterCommand( 'notebasdepage', new FCKDialogCommand( 'notebasdepage', FCKLang.notebasdepageDlgTitle, FCKPlugins.Items['notebasdepage'].Path + 'fck_notebasdepage.html', 300,250) ) ;

// Création du bouton «notedebasdepage».
var onotebasdepageItem = new FCKToolbarButton( 'notebasdepage', FCKLang.notebasdepageBtn ) ;
onotebasdepageItem.IconPath = FCKPlugins.Items['notebasdepage'].Path + 'notebasdepage.gif' ;

FCKToolbarItems.RegisterItem( 'notebasdepage', onotebasdepageItem ) ;


// Création de l'objet.
var FCKnotebasdepages = new Object() ;

// Ajouter une note de bas de page.
FCKnotebasdepages.Add = function( name )
{
	var oSpan = FCK.CreateElement( 'SPAN' ) ;
	this.SetupSpan( oSpan, name ) ;
}

FCKnotebasdepages.SetupSpan = function( span, name )
{
	span.innerHTML = '[[ ' + name + ' ]]' ;

	span.style.backgroundColor = '#ffff00' ;
	span.style.color = '#000000' ;

	if ( FCKBrowserInfo.IsGecko )
		span.style.cursor = 'default' ;

	span._fcknotebasdepage = name ;
	span.contentEditable = false ;

	// Pour laisser la fenêtre redimensionnable.
	span.onresizestart = function()
	{
		FCK.EditorWindow.event.returnValue = false ;
		return false ;
	}
}

// On Gecko we must do this trick so the user select all the SPAN when clicking on it.
FCKnotebasdepages._SetupClickListener = function()
{
	FCKnotebasdepages._ClickListener = function( e )
	{
		if ( e.target.tagName == 'SPAN' && e.target._fcknotebasdepage )
			FCKSelection.SelectNode( e.target ) ;
	}

	FCK.EditorDocument.addEventListener( 'click', FCKnotebasdepages._ClickListener, true ) ;
}

// Ouvre la boite de dialogue sur double click.
FCKnotebasdepages.OnDoubleClick = function( span )
{
	if ( span.tagName == 'SPAN' && span._fcknotebasdepage )
		FCKCommands.GetCommand( 'notebasdepage' ).Execute() ;
}

FCK.RegisterDoubleClickHandler( FCKnotebasdepages.OnDoubleClick, 'SPAN' ) ;

// Vérifie si notedebasdepage est déjà ouvert.
FCKnotebasdepages.Exist = function( name )
{
	var aSpans = FCK.EditorDocument.getElementsByTagName( 'SPAN' ) ;

	for ( var i = 0 ; i < aSpans.length ; i++ )
	{
		if ( aSpans[i]._fcknotebasdepage == name )
			return true ;
	}

	return false ;
}

if ( FCKBrowserInfo.IsIE )
{
	FCKnotebasdepages.Redraw = function()
	{
		if ( FCK.EditMode != FCK_EDITMODE_WYSIWYG )
			return ;

		var aPlaholders = FCK.EditorDocument.body.innerText.match( /\[\[[^\[\]]+\]\]/g ) ;
		if ( !aPlaholders )
			return ;

		var oRange = FCK.EditorDocument.body.createTextRange() ;

		for ( var i = 0 ; i < aPlaholders.length ; i++ )
		{
			if ( oRange.findText( aPlaholders[i] ) )
			{
				var sName = aPlaholders[i].match( /\[\[\s*([^\]]*?)\s*\]\]/ )[1] ;
				oRange.pasteHTML( '<span style="color: #000000; background-color: #ffff00" contenteditable="false" _fcknotebasdepage="' + sName + '">' + aPlaholders[i] + '</span>' ) ;
			}
		}
	}
}
else
{
	FCKnotebasdepages.Redraw = function()
	{
		if ( FCK.EditMode != FCK_EDITMODE_WYSIWYG )
			return ;

		var oInteractor = FCK.EditorDocument.createTreeWalker( FCK.EditorDocument.body, NodeFilter.SHOW_TEXT, FCKnotebasdepages._AcceptNode, true ) ;

		var	aNodes = new Array() ;

		while ( ( oNode = oInteractor.nextNode() ) )
		{
			aNodes[ aNodes.length ] = oNode ;
		}

		for ( var n = 0 ; n < aNodes.length ; n++ )
		{
			var aPieces = aNodes[n].nodeValue.split( /(\[\[[^\[\]]+\]\])/g ) ;

			for ( var i = 0 ; i < aPieces.length ; i++ )
			{
				if ( aPieces[i].length > 0 )
				{
					if ( aPieces[i].indexOf( '[[' ) == 0 )
					{
						var sName = aPieces[i].match( /\[\[\s*([^\]]*?)\s*\]\]/ )[1] ;

						var oSpan = FCK.EditorDocument.createElement( 'span' ) ;
						FCKnotebasdepages.SetupSpan( oSpan, sName ) ;

						aNodes[n].parentNode.insertBefore( oSpan, aNodes[n] ) ;
					}
					else
						aNodes[n].parentNode.insertBefore( FCK.EditorDocument.createTextNode( aPieces[i] ) , aNodes[n] ) ;
				}
			}

			aNodes[n].parentNode.removeChild( aNodes[n] ) ;
		}

		FCKnotebasdepages._SetupClickListener() ;
	}

	FCKnotebasdepages._AcceptNode = function( node )
	{
		if ( /\[\[[^\[\]]+\]\]/.test( node.nodeValue ) )
			return NodeFilter.FILTER_ACCEPT ;
		else
			return NodeFilter.FILTER_SKIP ;
	}
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FCKnotebasdepages.Redraw ) ;

// Vire le surlignage.
FCKXHtml.TagProcessors['span'] = function( node, htmlNode )
{
	if ( htmlNode._fcknotebasdepage )
		node = FCKXHtml.XML.createTextNode( '[[' + htmlNode._fcknotebasdepage + ']]' ) ;
	else
		FCKXHtml._AppendChildNodes( node, htmlNode, false ) ;

	return node ;
}
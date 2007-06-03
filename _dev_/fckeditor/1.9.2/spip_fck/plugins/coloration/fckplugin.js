/*
 *
 * Plugin de coloration syntaxique par Frank SAURET.
 * En utilisant largement le travail de Richard Tuin 
 */

// Enregistre la commande.
FCKCommands.RegisterCommand( 'coloration', 
  new FCKDialogCommand( 'coloration', 
    FCKLang.ColorationTitre, 
    FCKPlugins.Items['coloration'].Path + 'fck_coloration.html', 
    640,480) ) ;

// Création du bouton «coloration».
var ocolorationItem = new FCKToolbarButton( 'coloration', FCKLang.ColorationBulle ) ;
ocolorationItem.IconPath = FCKPlugins.Items['coloration'].Path + 'coloration.gif' ;
FCKToolbarItems.RegisterItem( 'coloration', ocolorationItem ) ;

// Création de l'objet.
var FCKcolorations = new Object() ;

// Ajouter une note de bas de page.
FCKcolorations.Add = function( name )
{
	var oSpan = FCK.CreateElement( 'SPAN' ) ;
	this.SetupSpan( oSpan, name ) ;
}

FCKcolorations.SetupSpan = function( span, name )
{
	span.innerHTML = '[[ ' + name + ' ]]' ;

	span.style.backgroundColor = '#ffff00' ;
	span.style.color = '#000000' ;

	if ( FCKBrowserInfo.IsGecko )
		span.style.cursor = 'default' ;

	span._fckcoloration = name ;
	span.contentEditable = false ;

	// Pour laisser la fenêtre redimensionnable.
	span.onresizestart = function()
	{
		FCK.EditorWindow.event.returnValue = false ;
		return false ;
	}
}

// On Gecko we must do this trick so the user select all the SPAN when clicking on it.
FCKcolorations._SetupClickListener = function()
{
	FCKcolorations._ClickListener = function( e )
	{
		if ( e.target.tagName == 'SPAN' && e.target._fckcoloration )
			FCKSelection.SelectNode( e.target ) ;
	}

	FCK.EditorDocument.addEventListener( 'click', FCKcolorations._ClickListener, true ) ;
}

// Ouvre la boite de dialogue sur double click.
FCKcolorations.OnDoubleClick = function( span )
{
	if ( span.tagName == 'SPAN' && span._fckcoloration )
		FCKCommands.GetCommand( 'coloration' ).Execute() ;
}

FCK.RegisterDoubleClickHandler( FCKcolorations.OnDoubleClick, 'SPAN' ) ;

// Vérifie si notedebasdepage est déjà ouvert.
FCKcolorations.Exist = function( name )
{
	var aSpans = FCK.EditorDocument.getElementsByTagName( 'SPAN' ) ;

	for ( var i = 0 ; i < aSpans.length ; i++ )
	{
		if ( aSpans[i]._fckcoloration == name )
			return true ;
	}

	return false ;
}

if ( FCKBrowserInfo.IsIE )
{
	FCKcolorations.Redraw = function()
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
				oRange.pasteHTML( '<span style="color: #000000; background-color: #ffff00" contenteditable="false" _fckcoloration="' + sName + '">' + aPlaholders[i] + '</span>' ) ;
			}
		}
	}
}
else
{
	FCKcolorations.Redraw = function()
	{
		if ( FCK.EditMode != FCK_EDITMODE_WYSIWYG )
			return ;

		var oInteractor = FCK.EditorDocument.createTreeWalker( FCK.EditorDocument.body, NodeFilter.SHOW_TEXT, FCKcolorations._AcceptNode, true ) ;

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
						FCKcolorations.SetupSpan( oSpan, sName ) ;

						aNodes[n].parentNode.insertBefore( oSpan, aNodes[n] ) ;
					}
					else
						aNodes[n].parentNode.insertBefore( FCK.EditorDocument.createTextNode( aPieces[i] ) , aNodes[n] ) ;
				}
			}

			aNodes[n].parentNode.removeChild( aNodes[n] ) ;
		}

		FCKcolorations._SetupClickListener() ;
	}

	FCKcolorations._AcceptNode = function( node )
	{
		if ( /\[\[[^\[\]]+\]\]/.test( node.nodeValue ) )
			return NodeFilter.FILTER_ACCEPT ;
		else
			return NodeFilter.FILTER_SKIP ;
	}
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FCKcolorations.Redraw ) ;

// Vire le surlignage.
FCKXHtml.TagProcessors['span'] = function( node, htmlNode )
{
	if ( htmlNode._fckcoloration )
		node = FCKXHtml.XML.createTextNode( '[[' + htmlNode._fckcoloration + ']]' ) ;
	else
		FCKXHtml._AppendChildNodes( node, htmlNode, false ) ;

	return node ;
}
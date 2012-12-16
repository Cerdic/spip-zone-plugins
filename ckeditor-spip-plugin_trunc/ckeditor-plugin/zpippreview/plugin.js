/*
Copyright (c) 2011, Frédéric Bonnaud
base on preview plugin from : CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file ZpipPreview plugin.
 */
(function()
{
	var zpippreviewCmd =
	{
		modes : { wysiwyg:1, source:1 },
		canUndo : false,
		exec : function( editor )
		{
			var sHTML = editor.getData() ;
			var config = editor.config ;
			var iWidth, iHeight, iLeft, screen ;
			
			if (screen = window.screen) {
				iWidth = Math.round( screen.width * 0.8 );
				iHeight = Math.round( screen.height * 0.7 );
				iLeft = Math.round( screen.width * 0.1 );
			} else {
				iWidth = 640 ;	// 800 * 0.8,
				iHeight = 420 ;	// 600 * 0.7,
				iLeft = 80 ;	// (800 - 0.8 * 800) /2 = 800 * 0.1.
			}

			var contexte = $("#contexte").val() ;
			var type = '' ;
			if (contexte) {
				var m = contexte.match(/^#(.*)$/) ;
				if (m) {
					contexte = m[1] ;
					type = 'id' ;
				} else {
					m =  contexte.match(/^\.(.*)$/) ;
					if (m) {
						contexte = m[1] ;
						type= 'class' ;
					}
				}
			} else {
			  	contexte = '' ;
			}	

			$.post(CKEDITOR.spipurl + '?page=preview'+(contexte?'&contexte='+encodeURI(contexte):'')+(type?'&attr='+encodeURI(type):'') , { 'preview': sHTML }, function(data){

				var sOpenUrl = '';
				// il faut préciser la 'base' des urls, sinon, zpip ne retrouve pas ses petits
				var sHTML =  data.replace(/(<head[^>]*>)/, '$1'+'<base href="' + CKEDITOR.ckdirRacine + '"/>') ;
				var oWindow = window.open( '', null, 'toolbar=yes,location=no,status=yes,menubar=yes,scrollbars=yes,resizable=yes,width=' + iWidth + ',height=' + iHeight + ',left=' + iLeft );
				oWindow.document.write( sHTML ) ;
				oWindow.document.close() ;

			}) ;
		}
	};

	var pluginName = 'zpippreview';

	// Register a plugin named "zpippreview".
	CKEDITOR.plugins.add( pluginName,
	{
		init : function( editor )
		{
			editor.addCommand( pluginName, zpippreviewCmd );
			editor.ui.addButton( 'ZpipPreview',
				{
					label : editor.lang.preview + ' ZPIP',
					command : pluginName,
					icon : this.path + 'preview.png'
				});
		}
	});
})();

/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.plugins.add( 'spip',
{
	requires : [ 'dialog' ],
	lang : [ 'ar', 'en', 'fr' ],

	init : function( editor )
	{
		if ( CKEDITOR.env.ie6Compat )
			return;

		editor.addCommand( 'spip', new CKEDITOR.dialogCommand( 'spip' ) );
		editor.ui.addButton( 'Spip',
			{
				label : editor.lang.spip.title,
				command : 'spip',
				icon : this.path + 'spip.gif'
			});
		CKEDITOR.dialog.add( 'spip', this.path + 'dialogs/spip.js' );

	}
} );

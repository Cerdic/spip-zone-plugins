/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file Image plugin
 */

CKEDITOR.plugins.add( 'spipdoc',
{
	lang : [ 'ar', 'en', 'fr' ],

	init : function( editor )
	{
		var pluginName = 'spipdoc';

		// Register the dialog.
		CKEDITOR.dialog.add( pluginName, this.path + 'dialogs/spipdoc.js' );

		// Register the command.
		editor.addCommand( pluginName, new CKEDITOR.dialogCommand( pluginName ) );

		// Register the toolbar button.
		editor.ui.addButton( 'SpipDoc',
			{
				label : editor.lang.spipdoc.title,
				command : pluginName,
				icon : this.path + 'spipdoc.gif'
			});

		// If the "menu" plugin is loaded, register the menu items.
		if ( editor.addMenuItems )
		{
			editor.addMenuItems(
				{
					spipdoc :
					{
						icon : this.path + 'spipdoc.gif',
						label : editor.lang.spipdoc.property,
						command : 'spipdoc',
						group : 'image'
					}
				});
		}

		// If the "contextmenu" plugin is loaded, register the listeners.
		if ( editor.contextMenu )
		{ 
			editor.contextMenu.addListener( function( element, selection )
				{
					if ( !element || !element.is( 'img' ) || element.getAttribute( '_cke_realelement' ) )
						return null;

					return { spipdoc : CKEDITOR.TRISTATE_OFF };
				});
			
		}
	}
} );

/**
 * Whether to remove links when emptying the link URL field in the image dialog.
 * @type Boolean
 * @default true
 * @example
 * config.image_removeLinkByEmptyURL = false;
 */
CKEDITOR.config.image_removeLinkByEmptyURL = true;

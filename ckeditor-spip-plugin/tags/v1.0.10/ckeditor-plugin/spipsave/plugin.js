/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileSave plugin.
 */

(function()
{
	var saveCmd =
	{
		modes : { wysiwyg:1, source:1 },

		exec : function( editor )
		{
			var $form = editor.element.$.form;

			if ( $form )
			{
				$form.save.click() ;
			}
		}
	};

	var pluginName = 'spipsave';

	// Register a plugin named "spipsave".
	CKEDITOR.plugins.add( pluginName,
	{
		init : function( editor )
		{
			var command = editor.addCommand( pluginName, saveCmd );
			command.modes = { wysiwyg : !!( editor.element.$.form ) };

			editor.ui.addButton( 'SpipSave',
				{
					label : editor.lang.save,
					command : pluginName,
					iconOffset : 2,
					icon: editor.skinPath + 'icons.png'
					
				});
		}
	});
})();

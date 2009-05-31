/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2006 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: fckplugin.js
 * 	Plugin to insert "Placeholders" in the editor.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

// Register the related command.
FCKCommands.RegisterCommand( 'HelpPanel', new FCKDialogCommand( 'HelpPanel', FCKLang.HelppanelDlgTitle, FCKPlugins.Items['helppanel'].Path + 'fck_help.html', 800, 600 ) ) ;

// Create the "Helppanel" toolbar button.
var oHelppanelItem = new FCKToolbarButton( 'HelpPanel', FCKLang.HelppanelBtn ) ;
oHelppanelItem.IconPath = FCKPlugins.Items['helppanel'].Path + 'helppanel.gif' ;

FCKToolbarItems.RegisterItem( 'HelpPanel', oHelppanelItem ) ;
<?php

	echo '
		FCKConfig.SkinPath = "' . $_GET['path'] . '/spip_fck/skins/office2003/";
		FCKConfig.EditorAreaCSS = "' . $_GET['path'] . '/spip_fck/css/fck_editorarea.css";
		FCKConfig.TemplatesXmlPath = "' . $_GET['path'] . '/spip_fck/fcktemplates.xml" ;
		FCKConfig.StylesXmlPath = "' . $_GET['path'] . '/spip_fck/fckstyles.xml" ;
		
		// exemple pour int�grer les raccroucis SPIP
		//FCKConfig.ProtectedSource.Add( /<\?[\s\S]*?\?>/g ) ; 
		
		// Ajout des plug-ins
		FCKConfig.Plugins.Add( "helppanel" , "fr,en", "' . $_GET['path'] . '/spip_fck/plugins/");
		FCKConfig.Plugins.Add( "spipeditor" , "fr,en", "' . $_GET['path'] . '/spip_fck/plugins/");
		

		FCKConfig.ToolbarSets["Spip"] = [
			["Source","-","Save","NewPage","Preview","-","Templates"],
			["Undo","Redo","-","Find","Replace","-","SelectAll","RemoveFormat"],
			["Cut","Copy","Paste","PasteText","PasteWord","-","Print","SpellCheck"],
			"/",
			["Bold","Italic","Underline","StrikeThrough","-","Subscript","Superscript"],
			["OrderedList","UnorderedList","-","Outdent","Indent"],
			["JustifyLeft","JustifyCenter","JustifyRight","JustifyFull"],
			["Link","Unlink","Anchor"],
			"/",
			["Style","FontFormat","FontName","FontSize"],
			["Image","Flash","Table","Rule","Smiley","SpecialChar","PageBreak","UniversalKey"],
			["TextColor","BGColor"],
			["FitWindow","-","About","HelpPanel","SpipEditor"]
		] ;

		// The following value defines which File Browser connector and Quick Upload 
		// "uploader" to use. It is valid for the default implementaion and it is here
		// just to make this configuration file cleaner. 
		// It is not possible to change this value using an external file or even 
		// inline when creating the editor instance. In that cases you must set the 
		// values of LinkBrowserURL, ImageBrowserURL and so on.
		// Custom implementations should just ignore it.
		var _FileBrowserLanguage	= "php" ;	// asp | aspx | cfm | lasso | perl | php | py
		var _QuickUploadLanguage	= "php" ;	// asp | aspx | cfm | lasso | php
		
		// Don t care about the following line. It just calculates the correct connector 
		// extension to use for the default File Browser (Perl uses "cgi").
		var _FileBrowserExtension = _FileBrowserLanguage == "perl" ? "cgi" : _FileBrowserLanguage ;
		
		FCKConfig.LinkBrowser = true ;
		FCKConfig.LinkBrowserURL = "' . $_GET['path'] . '/spip_fck/filemanager/browser/spip/browser.html?Connector=connectors/" + _FileBrowserLanguage + "/connector." + _FileBrowserExtension ;
		
		FCKConfig.ImageBrowser = true ;
		FCKConfig.ImageBrowserURL = "' . $_GET['path'] . '/spip_fck/filemanager/browser/spip/browser.html?Type=Image&Connector=connectors/" + _FileBrowserLanguage + "/connector." + _FileBrowserExtension ;

		FCKConfig.FlashBrowser = true ;
		FCKConfig.FlashBrowserURL = "' . $_GET['path'] . '/spip_fck/filemanager/browser/spip/browser.html?Type=Flash&Connector=connectors/" + _FileBrowserLanguage + "/connector." + _FileBrowserExtension ;
		
		FCKConfig.LinkUpload = true ;
		FCKConfig.LinkUploadURL = "' . $_GET['path'] . '/spip_fck/filemanager/upload/spip/upload." + _QuickUploadLanguage ;
		
		FCKConfig.ImageUpload = true ;
		FCKConfig.ImageUploadURL = "' . $_GET['path'] . '/spip_fck/filemanager/upload/spip/upload." + _QuickUploadLanguage + "?Type=Image" ;
		
		FCKConfig.FlashUpload = true ;
		FCKConfig.FlashUploadURL = "' . $_GET['path'] . '/spip_fck/filemanager/upload/spip/upload." + _QuickUploadLanguage + "?Type=Flash" ;
	';
?>
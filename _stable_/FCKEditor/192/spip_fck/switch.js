function Toggle()
{
	
	// Try to get the FCKeditor instance, if available.
	var oEditor ;
	if ( typeof( FCKeditorAPI ) != 'undefined' )
		oEditor = FCKeditorAPI.GetInstance( 'fckeditor_data' ) ;
	
	// If the _Textarea DIV is visible, switch to FCKeditor.
	if ( $("textarea[@name=texte]").css("display") != 'none' )
	{
		//oEditor.SetHTML( eTextarea.value ) ;
		oEditor.SetHTML( $("textarea[@name=texte]").val() ) ;
		
		// Switch the DIVs display.
		$("textarea[@name=texte]").css("display", "none");
		$("#fckeditor_switch").css("display", "none");
		$("#fckeditor_div").css("display", "");
		$(".spip_barre").css("display", "none");
		
		// This is a hack for Gecko 1.0.x ... it stops editing when the editor is hidden.
		if ( oEditor && !document.all )
		{
			if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG )
				oEditor.MakeEditable() ;
		}
	}
	else
	{
		// Set the textarea value to the editor value.
		$("textarea[@name=texte]").val( oEditor.GetXHTML() );
		
		$("textarea[@name=texte]").css("display", "");
		$("#fckeditor_switch").css("display", "");
		$("#fckeditor_div").css("display", "none");
		$(".spip_barre").css("display", "");
	}
}

function PrepareSave()
{
	// Get the _Textarea and _FCKeditor DIVs.
	var eTextarea	= document.getElementsByName( 'texte' ) ;
	var eFCKeditor	= document.getElementById( 'fckeditor_div' ) ;
	
	// If the textarea isn't visible update the content from the editor.
	if ( $("textarea[@name=texte]").css("display") == 'none' )
	{
		var oEditor = FCKeditorAPI.GetInstance( 'fckeditor_data' ) ;
		$("textarea[@name=texte]").val( oEditor.GetXHTML() );
	}
}

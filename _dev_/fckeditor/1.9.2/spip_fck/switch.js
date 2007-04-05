function Toggle()
{
	
	// Try to get the FCKeditor instance, if available.
	var oEditor ;
	if ( typeof( FCKeditorAPI ) != 'undefined' )
		oEditor = FCKeditorAPI.GetInstance( 'fckeditor_data' ) ;
	
	// If the _Textarea DIV is visible, switch to FCKeditor.
	if ( $("#text_area").css("display") != 'none' )
	{
		//oEditor.SetHTML( eTextarea.value ) ;
		oEditor.SetHTML( $("#text_area").val() ) ;
		
		// Switch the DIVs display.
		$("#text_area").css("display", "none");
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
		$("#text_area").val( oEditor.GetXHTML() );
		
		$("#text_area").css("display", "");
		$("#fckeditor_switch").css("display", "");
		$("#fckeditor_div").css("display", "none");
		$(".spip_barre").css("display", "");
	}
}

function PrepareSave()
{
	// Get the _Textarea and _FCKeditor DIVs.
	var eTextarea	= document.getElementById( 'text_area' ) ;
	var eFCKeditor	= document.getElementById( 'fckeditor_div' ) ;
	
	// If the textarea isn't visible update the content from the editor.
	if ( $("#text_area").css("display") == 'none' )
	{
		var oEditor = FCKeditorAPI.GetInstance( 'fckeditor_data' ) ;
		$("#text_area").val( oEditor.GetXHTML() );
	}
}

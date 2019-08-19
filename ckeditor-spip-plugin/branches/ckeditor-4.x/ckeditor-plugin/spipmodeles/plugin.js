function insertTag(editor, value, tag, params, tags) {
	// cas des tags avec tag fermant
	var close_tag=false;var syntaxe='spip';
	for(var this_tag in tags) {
		if((tags[this_tag][0] == '/' + tag) || ( (tags[this_tag][0] == tag) && (tags[this_tag][6]==1) )) {
			close_tag = true ;
		}
		if(tags[this_tag][0] == tag){
			syntaxe = tags[this_tag][7] ;
		}
	}
	editor.focus();
	editor.fire( 'saveSnapshot' );
	editor.insertHtml('&lt;' + value + (params?(syntaxe=='html'?' ':'|')+params:'') + '&gt;' + (close_tag?'&lt;/' + tag + '&gt;':''));
	editor.fire( 'saveSnapshot' );
}


CKEDITOR.plugins.add( 'spipmodeles',
{
   lang : [ 'fr' ],   
   requires : ['richcombo'],
   init : function( editor )
   {
      var config = editor.config,
         lang = editor.lang.spipmodeles;

      CKEDITOR.dialog.add( 'spipmodelesDlg',this.path + 'dialogs/spipmodeles.js');
      var command = new CKEDITOR.dialogCommand('spipmodelesDlg' ) ;
      editor.addCommand( 'spipmodelesCmd', command );

      // Gets the list of tags from the settings.
      var tags = [];
      // on n'utilise pas $.getJSON car asynchrone
      try {
		var json = $.ajax({
                  url: CKEDITOR.spipurl + '?page=spiptags-json', 
	              async: false, // pour avoir une réponse synchrone
                  global: false,		
				  dataType: 'json'
				}).responseText ;
        tags = $.parseJSON(json) ;
      } catch(e) {
		tags = [] ;
      }
      // Create style objects for all defined styles.

      editor.ui.addRichCombo( 'SpipModeles',
         {
            label : lang.label,
            title : lang.title,
            voiceLabel : lang.voiceLabel,
            className : 'cke_format',
            multiSelect : false,

            panel :
           {
               css : [ config.contentsCss, CKEDITOR.getUrl( editor.skinPath + 'editor.css' ) ],
               voiceLabel : lang.voiceLabel
            },

            init : function()
            {
               this.startGroup( lang.groupName );
               for (var this_tag in tags){
                  if (!tags[this_tag][0].match(/^\//)) {
					 var label, value, text ;
				  	 value = tags[this_tag][0] ;
					 text = tags[this_tag][1] ; if(text=='')text=value ;
					 label = tags[this_tag][2] ; if (label=='')label=text ;
                     this.add(value, text, label);
                  }
               }
            },

            onClick : function( value )
            {
               var m,tag=value ;
               editor.openDialog('spipmodelesDlg', function() { this.tag_requested = value ; this.tags = tags ;});
            }
         });
   }
});


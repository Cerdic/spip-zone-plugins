// on charge les fichiers de langue des POPUPS
tinyMCEPopup.requireLangPack();

// notre plugin SPIP
var SpipCodeDialog = {

	init : function() {
		var t = this;
		t.special_class = tinyMCEPopup.getWindowArg('special_class');
		t.special_class_regexp = tinyMCEPopup.getWindowArg('special_class_regexp');
		t.mustbeprotected = tinyMCEPopup.getWindowArg('must_be_protected');
		t.form = document.forms[0];

		// recuperation du contenu selectionne et insertion dans la textarea
    	t.form.spipcode.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
    	t.form.spipcode.focus();
	},

	insert : function() {
		// insertion du contenu dans l'editeur avec protection
		var c = this.form.spipcode.value;
		if ( this.mustbeprotected===true ){
			c = '<span class="'+this.special_class+'">'+c+'</span>';
		}
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, c);
		tinyMCEPopup.close();
	}
};

// enregistrement du plugin
tinyMCEPopup.onInit.add(SpipCodeDialog.init, SpipCodeDialog);

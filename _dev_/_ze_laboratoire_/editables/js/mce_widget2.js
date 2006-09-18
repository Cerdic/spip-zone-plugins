tinyMCE.init({
  	mode  : 'none',
	elements : "titre,surtitre,soustitre,descriptif,chapo,text_area,ps",

	theme : "advanced",
	plugins : "table,save,advimage,advlink,iespell,searchreplace,contextmenu",

    width : "50%",

	theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
	theme_advanced_buttons3_add_before : "bold,italic,separator",
	theme_advanced_buttons3_add : "iespell,separator,tablecontrols",

	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",

	theme_advanced_disable : "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,formatselect,fontselect,fontsizeselect,styleselect,sub,sup,forecolor,backcolor,newdocument",

	save_callback: "beforeSaveCallBack"
});

function beforeSaveCallBack(element_id, html, body) {
	return "<!-- TINY_MCE -->"+html;
}

var currentTinyMCE = false;

function toggleEdit(id) {
	if(currentTinyMCE==false) {
		setTinyMCE(id);
		currentTinyMCE= id;
	} else {
		if(currentTinyMCE!=id) {
			unsetTinyMCE(currentTinyMCE);
			setTinyMCE(id);
			currentTinyMCE= id;
		} else {
			unsetTinyMCE(id);			
			currentTinyMCE= false;
		}
	}
}

function setTinyMCE(sEditorID) {
	var oEditor = document.getElementById(sEditorID);
	if(oEditor) {
		tinyMCE.execCommand('mceAddControl', true, sEditorID);
		currentTinyMCE = sEditorID;
	}
	return;
}

function unsetTinyMCE() {
	var oEditor = document.getElementById(currentTinyMCE);
	if(oEditor) {
		tinyMCE.triggerSave();
		tinyMCE.execCommand('mceRemoveControl', true, currentTinyMCE);
		currentTinyMCE= false;
	}
	return;
}

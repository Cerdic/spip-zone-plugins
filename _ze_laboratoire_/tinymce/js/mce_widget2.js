tinyMCE.init({
  	mode  : 'none',
	elements : "titre,surtitre,soustitre,descriptif,chapo,text_area,ps",

	theme : "advanced",
	plugins : "table,save,advimage,advlink,iespell,searchreplace,contextmenu",

    width : "50%",

	theme_advanced_buttons2_add_before: "save,cut,copy,paste,separator,search,replace,separator",
	theme_advanced_buttons3_add_before : "bold,italic,separator",
	theme_advanced_buttons3_add : "iespell,separator,tablecontrols",

	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",

	theme_advanced_disable : "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,formatselect,fontselect,fontsizeselect,styleselect,sub,sup,forecolor,backcolor,newdocument",

	save_callback: "beforeSaveCallBack"
});

function beforeSaveCallBack(element_id, html, body) {

alert('element_id='+element_id+'\nhtml='+html+'\nbody='+body);

	// createXmlHttp viens de img_pack/mlayer.js
	if (!(xmlhttp[element_id] = createXmlHttp()))
		return false;

	url= "?exec=ajax_edit_article&OK=ok";

// A REVOIR : comment en faire du post pour poster des textes > 4Ko ?
	//xmlhttp[element_id].open("GET", '?exec=ajax_edit_article&champ='+element_id+'&id='+id_article+'&texte='+escape(html), true);
	xmlhttp[element_id].open("POST", url);
	xmlhttp[element_id].setRequestHeader('Content-Type','application/x-www-form-urlencoded');

	// traiter la reponse du serveur
	xmlhttp[element_id].onreadystatechange = function() {
		if (xmlhttp[element_id].readyState == 4) { 
			// si elle est non vide, l'afficher
			if (xmlhttp[element_id].responseText != '') {
				body.innerHTML = xmlhttp[element_id].responseText;
			}
		}
	}
    xmlhttp[element_id].send('exec=ajax_edit_article&champ='+element_id+'&id='+id_article+'&texte='+escape(html)); 

	return html;
}

var currentTinyMCE = false;

function toggleEdit(id) {
	if(currentTinyMCE==false) {
		setTinyMCE(id);
	} else {
		if(currentTinyMCE!=id) {
			unsetTinyMCE(currentTinyMCE);
			setTinyMCE(id);
		} else {
			unsetTinyMCE(id);			
		}
	}
}

function setTinyMCE(id) {
	var open = document.getElementById(id+'_open');
	open.style.display='none';
	var close = document.getElementById(id+'_close');
	close.style.display='inline';

	var oEditor = document.getElementById(id);
	if(oEditor) {
		tinyMCE.execCommand('mceAddControl', true, id);
		currentTinyMCE = id;
	}
	return;
}

function unsetTinyMCE(id) {
	var open = document.getElementById(id+'_open');
	open.style.display='inline';
	var close = document.getElementById(id+'_close');
	close.style.display='none';

	var oEditor = document.getElementById(id);
	if(oEditor) {
		tinyMCE.triggerSave();
		tinyMCE.execCommand('mceRemoveControl', true, currentTinyMCE);
		currentTinyMCE= false;
	}
	return;
}

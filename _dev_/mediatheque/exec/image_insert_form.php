<?php
// ---------------------------------------------
//	simeray@tektonika.com
//	last modification: 24/11/06
// ---------------------------------------------

function exec_image_insert_form() {

	global $set_wysiwyg;
	global $prefs;
	global $Submit_photo_insert, $photo_insert_name;
	global $photo_insert_alt, $photo_insert_longdesc, $photo_insert_legende, $photo_insert_alignement, $photo_insert_langue;
	global $connect_statut; // string
	global $auteur_session; // assoc array
		
	//print_r($prefs);
	
	// init
	$display = false;
	$writedb = true;
	
	$id_document = _request('id_document');
	$id_article = _request('id_article');
	
	$id_auteur = $auteur_session['id_auteur'];
	
	$site_root 		= $_SERVER['DOCUMENT_ROOT'];
	$web_root 		= _DIR_RACINE;
	
	$ikono_site_path = "$site_root/drass/IMG";
	$ikono_web_path  = "$web_root" . "IMG";
		
	// general spip includes
		include_spip('inc/presentation');
		include_spip('inc/article_select');
		include_spip('inc/rubriques');
		include_spip('inc/actions');
		include_spip('inc/documents');
		include_spip('inc/barre');
		include_spip('inc/logos');
	
	// specific plugin includes
		include_spip('inc/mediatheque_tools');
	
	// ---------------------------------------------
	//		put header
	// ---------------------------------------------	
	print display_popup_header("Modifier la rubrique");
	
	// ---------------------------------------------
	//		page title
	// ---------------------------------------------
	$link_back = "<a href=\"?exec=image_insert\">" . _T('mediatheque:com_li_back') . "</a>";
	print "<H3>Ins&eacute;rer l'image $id_document dans l'article $id_article</H3>";
	
	if (!$Submit_photo_insert) {
		print "<p>$link_back</p>";
	}
	
	
	// ---------------------------------------------
	//		GET DATA
	// ---------------------------------------------
	$query = "select * from spip_documents
				where id_document = '$id_document'
				";
	$result = spip_query($query);


	$obj = mysql_fetch_object($result);
		$fichier 		= $obj->fichier;
		$titre 			= $obj->titre;
		$descriptif		= $obj->descriptif;
		$photo_credit	= $obj->photo_credit;
		$id_owner		= $obj->id_owner;
		
		$url_fichier 	= $obj->url_fichier;

		$url_fichier = generer_url_document($id_document);

		if (file_exists($url_fichier)) {
			$size = @getimagesize($url_fichier);
			$file_size = @filesize($url_fichier);
		}

			$fichier_lib = ereg_replace('IMG/.*/', '', $fichier);
			
			$image = "<img style='border: none' src=\"$web_root$fichier\" width=\"$max_width\" />";
	
	// ---------------------------------------------
	//	CHECK DATA	
	// ---------------------------------------------
	$formcorrect = true;
	$erreur='';
	
	if ($Submit_photo_insert) {
				
		
	}
	
	if ($erreur != '') {print "<p>$erreur</p>";}
	
	// ---------------------------------------------
	//	TREATMENT 
	// ---------------------------------------------
	if ($Submit_photo_insert AND $formcorrect) {
		
		
		// format return string
		if ($photo_insert_name != '' or $photo_insert_legende != '') {
			$rs_what    = "doc";
		}
		else {
			$rs_what    = "img";
		}
		
		$rs_alt 	= ($photo_insert_alt != '') 		? "|alt=$photo_insert_alt|" 			: "|";
		$rs_lang	= "langue=$photo_insert_langue";
		
		$rs_name	= ($photo_insert_name != '') 		? "|titrelegende=$photo_insert_name|" 			: "|";
		$rs_legende = ($photo_insert_legende != '') 	? "|legende=$photo_insert_legende|" 	: "|";
		
		$rs_align 	= ($photo_insert_alignement != '') 	? "|$photo_insert_alignement|" 		: "|";
				
		$result_string = "<$rs_what$id_document$rs_align$rs_alt$rs_name$rs_legende|$rs_lang>";
		
		$result_string = ereg_replace("\|+", '|', $result_string); // clean extra pipes
	}
	
	
	  
	// ---------------------------------------------
	//	DISPLAY PAGE AND FORM
	// ---------------------------------------------
	// ---------------------------------------------
	//		before exit
	// ---------------------------------------------
	if ($Submit_photo_insert and $formcorrect) {
		$image_to_show = "<img src=\"$ikono_web_path/$type_lib/$photo_to_load_name\" />";
		$image_path = "$ikono_web_path/$type_lib/$photo_to_load_name";
		
		//$link_back = "<a href=\"?exec=image_insert\">" . _T('mediatheque:com_li_back') . "</a>";
		
		print "<p>" . _T('mediatheque:imi_msg_confirm') . "</p>";
		print _T('mediatheque:imi_msg_insert') . "<br /><tt>" . htmlentities($result_string, ENT_NOQUOTES, 'UTF-8') . "</tt></p>";
		print "<p>$link_back</p>";
		
	}
	
	// ---------------------------------------------
	//		form
	// ---------------------------------------------
	else {
		print "<form name=\"form\" action=\"\" method=\"post\">\r\n";
		print "<fieldset>\r\n";
		print "<legend>\r\n";
		print _T('mediatheque:imi_legend');
		print "</legend>\r\n";
		print "$image";
		print "<p>Nom dans la photoht&egrave;que : $titre</p>";
		print "<p>Descriptif dans la photoht&egrave;que : $descriptif</p>";
		print "</fieldset>\r\n";
		
		print "<p><em>" . _T('mediatheque:imi_explain') . "</em></p>";
		
		print "<fieldset>\r\n";
		print "<legend>\r\n";
		print _T('mediatheque:imi_inter_access');
		print "</legend>\r\n";
		
		// alt
		print "<p><label for=\"photo_insert_alt\">" . _T('mediatheque:imi_fi_alt') . "</label><br/>";
		print "<input name = \"photo_insert_alt\" id=\"photo_insert_alt\" type=\"text\" value=\"$photo_insert_alt\"  size=\"60\" maxlength=\"60\" /></p>\r\n";
			
		// lang
		print "<p><label for=\"photo_insert_langue\">" . _T('mediatheque:imi_fi_lang') . "</label><br/>";
		print "<select name=\"photo_insert_langue\" id=\"photo_insert_langue\">";
		print "<option value=\"fr\">fr</option>";
		print "<option value=\"en\">en</option>";
		print "</select></p>";
		
		print "</fieldset>\r\n";
		
		
		print "<fieldset>\r\n";
		print "<legend>\r\n";
		print _T('mediatheque:imi_inter_edito');
		print "</legend>\r\n";
		
		
		if (isset($set_wysiwyg)) {
			if ($set_wysiwyg == 'on' OR $prefs['wysiwyg'] == 'oui') {
				print"";
			}else {
				// name
				print "<p><label for=\"photo_insert_name\">" . _T('mediatheque:imi_fi_leg_title') . "</label><br/>";
				print "<input name = \"photo_insert_name\" id=\"photo_insert_name\" type=\"text\" value=\"$photo_insert_name\" size=\"30\" /></p>\r\n";
				// legend
				print "<p><label for=\"photo_insert_legende\">" . _T('mediatheque:imi_fi_legend') . "</label><br/>";
				// print "<input name = \"photo_insert_legende\" id=\"photo_insert_legende\" type=\"text\" value=\"$photo_insert_legende\" size=\"60\" /></p>\r\n";
				print "<textarea name=\"photo_insert_legende\" id=\"photo_insert_legende\" cols=\"50\" rows=\"5\">$photo_insert_legende</textarea></p>\r\n";
			}
		}	
		
		// align
		print "<p><label for=\"photo_insert_alignement\">" . _T('mediatheque:imi_fi_align') . "</label><br/>";
		print "<select name=\"photo_insert_alignement\" id=\"photo_insert_alignement\">";
		print "<option value=\"\">Aucun</option>";
		print "<option value=\"floatleft\">&agrave; gauche</option>";
		print "<option value=\"floatnone\">au centre</option>";
		print "<option value=\"floatright\">&agrave; droite</option>";
		print "</select></p>";
		
		print "</fieldset>\r\n";
		
		// action
		$action1 = "<input type=\"submit\" name=\"Submit_photo_insert\" value=\"" . _T('mediatheque:imi_bu_ok') . "\" class=\"boutonBOform\" onclick='retourneimg();return false;'/>\r\n";
		print "<script type='text/javascript'>

		function setCaretToEnd (input) {
			setSelectionRange(input, input.value.length, input.value.length);
		}


		function setSelectionRange(input, selectionStart, selectionEnd) {
		if (input.setSelectionRange) {
			input.focus();
			input.setSelectionRange(selectionStart, selectionEnd);
		}
		else if (input.createTextRange) {
			var range = input.createTextRange();
			range.collapse(true);
			range.moveEnd('character', selectionEnd);
			range.moveStart('character', selectionStart);
			range.select();
		}
		}

		// From http://www.massless.org/mozedit/
		function mozWrap(txtarea, open, close){
		var selLength = txtarea.textLength;
		var selStart = txtarea.selectionStart;
		var selEnd = txtarea.selectionEnd;
		if (selEnd == 1 || selEnd == 2){
			selEnd = selLength;
		var selTop = txtarea.scrollTop;
		}
		// Raccourcir la selection par double-clic si dernier caractere est espace	
		if (selEnd - selStart > 0 && (txtarea.value).substring(selEnd-1,selEnd) == ' '){
			selEnd = selEnd-1;
		}
		var s1 = (txtarea.value).substring(0,selStart);
		var s2 = (txtarea.value).substring(selStart, selEnd)
		var s3 = (txtarea.value).substring(selEnd, selLength);

		// Eviter melange bold-italic-intertitre
		if ((txtarea.value).substring(selEnd,selEnd+1) == '}' && close.substring(0,1) == '}') close = close + ' ';
		if ((txtarea.value).substring(selEnd-1,selEnd) == '}' && close.substring(0,1) == '}') close = ' ' + close;
		if ((txtarea.value).substring(selStart-1,selStart) == '{' && open.substring(0,1) == '{') open = ' ' + open;
		if ((txtarea.value).substring(selStart,selStart+1) == '{' && open.substring(0,1) == '{') open = open + ' ';

		txtarea.value = s1 + open + s2 + close + s3;
		selDeb = selStart + open.length;
		selFin = selEnd + close.length;
		window.setSelectionRange(txtarea, selDeb, selFin);
		txtarea.scrollTop = selTop;
		txtarea.focus();

		return;
		}

		// Insert at Claret position. Code from
		// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
		function storeCaret (textEl) {
		if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
		}
		
		
		
		function barre_inserer(text,champ) {
		var txtarea = champ;
		if (txtarea.createTextRange && txtarea.caretPos) {
			var caretPos = txtarea.caretPos;
			caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
			txtarea.focus();
			window.close();
		} else {
			//txtarea.value  += text;
			//txtarea.focus();
			mozWrap(txtarea, '', text);
			window.close();
			return;
		}
		}
		
		String.prototype.htmlEntities = function(){
  			var chars = new Array ('&','Ã ','Ã¡','Ã¢','Ã£','Ã¤','Ã¥','Ã¦','Ã§','Ã¨','Ã©',
                         'Ãª','Ã«','Ã¬','Ã­','Ã®','Ã¯','Ã°','Ã±','Ã²','Ã³','Ã´',
                         'Ãµ','Ã¶','Ã¸','Ã¹','Ãº','Ã»','Ã¼','Ã½','Ã¾','Ã¿','Ã',
                         'Ã','Ã','Ã','Ã','Ã','Ã','Ã','Ã','Ã','Ã','Ã',
                         'Ã','Ã','Ã','Ã','Ã','Ã','Ã','Ã','Ã','Ã','Ã',
                         'Ã','Ã','Ã','Ã','Ã','Ã','Ã','Â','\"','Ã','<',
                         '>','Â¢','Â£','Â¤','Â¥','Â¦','Â§','Â¨','Â©','Âª','Â«',
                         'Â¬','Â­','Â®','Â¯','Â°','Â±','Â²','Â³','Â´','Âµ','Â¶',
                         'Â·','Â¸','Â¹','Âº','Â»','Â¼','Â½','Â¾');

  			var entities = new Array ('amp','agrave','aacute','acirc','atilde','auml','aring',
                            'aelig','ccedil','egrave','eacute','ecirc','euml','igrave',
                            'iacute','icirc','iuml','eth','ntilde','ograve','oacute',
                            'ocirc','otilde','ouml','oslash','ugrave','uacute','ucirc',
                            'uuml','yacute','thorn','yuml','Agrave','Aacute','Acirc',
                            'Atilde','Auml','Aring','AElig','Ccedil','Egrave','Eacute',
                            'Ecirc','Euml','Igrave','Iacute','Icirc','Iuml','ETH','Ntilde',
                            'Ograve','Oacute','Ocirc','Otilde','Ouml','Oslash','Ugrave',
                            'Uacute','Ucirc','Uuml','Yacute','THORN','euro','quot','szlig',
                            'lt','gt','cent','pound','curren','yen','brvbar','sect','uml',
                            'copy','ordf','laquo','not','shy','reg','macr','deg','plusmn',
                            'sup2','sup3','acute','micro','para','middot','cedil','sup1',
                            'ordm','raquo','frac14','frac12','frac34');

  			newString = this;
  			for (var i = 0; i < chars.length; i++){
    			myRegExp = new RegExp();
    			myRegExp.compile(chars[i],'g')
    			newString = newString.replace (myRegExp, '&' + entities[i] + ';');
  			}
  			return newString;
		}
		";
		
			if (isset($set_wysiwyg)) {
				if ($set_wysiwyg == 'on'  OR $prefs['wysiwyg'] == 'oui') {
					print "function retourneimg(){
					if(document.getElementById('photo_insert_alignement').options[document.getElementById('photo_insert_alignement').selectedIndex].value!=''){
					var monalign = 'style=\"'+document.getElementById('photo_insert_alignement').options[document.getElementById('photo_insert_alignement').selectedIndex].value+'\"';
					} else{
					var monalign ='';
					}
					if(document.getElementById('photo_insert_alt').value!=''){
					valeuralt=document.getElementById('photo_insert_alt').value;
					valeuralthtml=valeuralt.htmlEntities()
					var monalt = 'alt=\"'+valeuralthtml+'\"';
					} else{
					var monalt ='';
					}					
					if(document.getElementById('photo_insert_langue').options[document.getElementById('photo_insert_langue').selectedIndex].value!=''){
					var malangue = 'lang=\"'+document.getElementById('photo_insert_langue').options[document.getElementById('photo_insert_langue').selectedIndex].value+'\"';
					} else{
					var malangue ='';
					}
					var imgtoreturn='<img src=\"$web_root$fichier\" '+monalign+' '+monalt+' '+malangue+'>';
					opener.window.jInsertEditorText(imgtoreturn);
					window.close();
					}
					</script>";
				}else {
					print "function retourneimg(){
					if(document.getElementById('photo_insert_alignement').options[document.getElementById('photo_insert_alignement').selectedIndex].value!=''){
					var monalign = '|'+document.getElementById('photo_insert_alignement').options[document.getElementById('photo_insert_alignement').selectedIndex].value;
					} else{
					var monalign ='';
					}					
					if(document.getElementById('photo_insert_alt').value!=''){
					var monalt = '|alt='+document.getElementById('photo_insert_alt').value;
					} else{
					var monalt ='';
					}
					if(document.getElementById('photo_insert_name').value!=''){
					var montitre = '|titrelegende='+document.getElementById('photo_insert_name').value;
					} else{
					var montitre ='';
					}
					if(document.getElementById('photo_insert_legende').value!=''){
					var malegende = '|legende='+document.getElementById('photo_insert_legende').value;
					} else{
					var malegende ='';
					}
					if(document.getElementById('photo_insert_langue').options[document.getElementById('photo_insert_langue').selectedIndex].value!=''){
					var malangue = '|langue='+document.getElementById('photo_insert_langue').options[document.getElementById('photo_insert_langue').selectedIndex].value;
					} else{
					var malangue ='';
					}
					
					if(montitre!=''||malegende!=''){
						var imgtoreturn='<doc$id_document'+monalign+''+monalt+''+montitre+''+malegende+''+malangue+'>';
					}else{
						var imgtoreturn='<img$id_document'+monalign+''+monalt+''+montitre+''+malegende+''+malangue+'>';
					}
					barre_inserer(imgtoreturn,opener.window.document.getElementById('text_area'));
					}
					</script>";
				}
			}	
				
		
		print "<p>$action1</p>";
		
		//print "</fieldset>\r\n";
		print "</form>\r\n";	
	} 
	print display_popup_footer();
}

?>
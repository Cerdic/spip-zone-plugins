<?php


	function flickr_cc_interface ( $vars="" ) {
		$exec = $vars["args"]["exec"];
		$id_rubrique = $vars["args"]["id_rubrique"];
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];
		
		$spip_lang = $_COOKIE["spip_lang_ecrire"];
		
		$ret = "";
		
		if ($exec == "articles_edit" && $id_article > 0 && autoriser('modifier','article', $id_article)) {

			$ret .= "<script type='text/javascript'><!--\n"
				. "function fermer_flickr_cc() {\n$('body').css('overflow','auto');\n $('#flickr_cc').fadeOut();\n}\n"
				. "function resultat_flickr(debut) {\n var ordre=$('[name=flickr_ordre][checked]').attr('value');\n var rech = $('#recherche_flickr').attr('value');\n var champs=$('#licenses').attr('value');\n $('#loading_flickr').fadeIn();\n $('#resultat_flickr').hide(); $('#resultat_flickr').load('../?page=flickr_cc_resultat&id_article=$id_article&recherche='+escape(rech)+'&ordre='+ordre+'&debut='+debut+'&champs='+champs, function() {\n$('#resultat_flickr').fadeIn();$('#loading_flickr').hide();\n});  \n}\n"
				. "--></script>\n";
		
		
			$div = "<div onclick=\'xfermer_flickr_cc(); return false;\' id=\'flickr_cc\' style=\'z-index: 1000; position: absolute; top: 0px; width: 100%; height: 100%;\'></div>";

			$url = _DIR_PLUGIN_FLICKR_CC."imgs/flickrcc.png";

		
			$ret .= "<div style='text-align:center; margin-top: 20px;'><a onclick=\"$('#flickr_cc').remove();$('body').scrollTop(0).css('height','100%').css('overflow','hidden').append('$div');$('#flickr_cc').load('../?page=flickr_cc&id_article=$id_article&lang=$spip_lang');return false;\" href=\"#\">"._T("flickrcc:ajouter_image_flickr")."<br /><br /><img src='$url' alt='Ajouter une image Flickr CC' /></a></div>";			
			
			
		} 
		$data .= $ret;
	
		$vars["data"] = $data;
		return $vars;
	}



?>
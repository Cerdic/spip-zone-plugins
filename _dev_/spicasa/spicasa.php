<?php


	function spicasa_interface ( $vars="" ) {
		$exec = $vars["args"]["exec"];
		$id_rubrique = $vars["args"]["id_rubrique"];
		$id_article = $vars["args"]["id_article"];
		$data =	$vars["data"];
		
		$ret = "";
		
		if ($exec == "articles_edit" && $id_article > 0 && autoriser('modifier','article', $id_article)) {

			$ret .= "<script type='text/javascript'><!--\n"
				. "function close_spicasa() {\n$('body').css('overflow','auto');\n $('#spicasa').fadeOut();\n}\n"
				. "function spicasa_buscar(debut) {\n "
				. " buscar = $('#spicasa_busqueda').attr('value');\n "
				. "$('#spicasa_loading').fadeIn(); $('#spicasa_resultados').hide(); "
				. "$('#spicasa_resultados').load('../?page=spicasa_resultados&id_article=$id_article&buscar='+escape(buscar)+'&debut='+debut, function() {\n"
				. "$('#spicasa_resultados').fadeIn();$('#spicasa_loading').hide();\n});  \n}\n"
				. "//--></script>\n";
		
		
			$div = "<div id=\'spicasa\' style=\'z-index: 1000; position: absolute; top: 0px; width: 100%; height: 100%;\'></div>";

			$url = _DIR_PLUGIN_SPICASA."imgs/spicasa-logo-thumb.jpg";

		
			$ret .= "<div style='text-align:center; margin-top: 20px;'><a onclick=\"$('#spicasa').remove();$('body').scrollTop(0).css('height','100%').css('overflow','hidden').append('$div');$('#spicasa').load('../?page=spicasa&id_article=$id_article');return false;\" href=\"#\"><img src='$url' alt='Ajouter une image Flickr CC' align='left' />"._T('spipcasa:adjuntar_imagenes')."</a></div>";			
			
			
		} 
		$data .= $ret;
	
		$vars["data"] = $data;
		return $vars;
	}



?>
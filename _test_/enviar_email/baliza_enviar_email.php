<?php

function preparar_enlace_enviar($arg){

// funcion para ventana pop-up centrada en la pantalla
$javascript_centrar_enviar = "<script language=\"JavaScript1.2\">
	/*
	Open Centered Popup Window Script-
	© DHTMLShock (www.dhtmlshock.com)
	To add more shock to your site, visit www.DHTMLShock.com
	*/
	function centrarVentana(theURL,winName,features, myWidth, myHeight, isCenter) { //v3.0
		if(window.screen)if(isCenter)if(isCenter==\"true\"){
		var myLeft = (screen.width-myWidth)/2;
		var myTop = (screen.height-myHeight)/2;
		features+=(features!='')?',':'';
		features+=',left='+myLeft+',top='+myTop;
	}
	window.open(theURL,winName,features+((features!='')?',':'')+'width='+myWidth+',height='+myHeight);
	}
</script>";

// prepara logo
		$logo = find_in_path('sobre.gif');
		$url = generer_url_public('',$arg);
		
// prepara el enlace completo
		$enlace = $javascript_centrar_enviar."<a href=\"javascript:;\" onClick=\"centrarVentana('$url','Enviar_documento','scrollbars=yes,resizable=yes','440','460','true')\" title=\""._T('enviarmail:enviar_title')."\" style=\"border:none\"><img src=\"$logo\" style=\"border:none\" alt=\""._T('enviarmail:enviar_title')." title=\""._T('enviarmail:enviar_title')."\" /> "._T('enviarmail:enviar_por_email')."</a>";
		return $enlace;

}

// la baliza a llamar como #ENVIAR_EMAIL** (importante los dos asteriscos para que funcione el javascript anterior de centrar la ventan por-up)

function balise_ENVIAR_EMAIL($p) {

// numero y enlace al artículo o breve
  	$_id_article = champ_sql('id_article', $p);
	$arg = "'page=enviar_email_articulo&amp;id_article='.".$_id_article;
	$url = generer_url_public('',$arg);
	if (!$_id_article){
		$_id_breve = champ_sql('id_breve', $p);
		$arg = "'page=enviar_email_breve&amp;id_breve='.".$_id_breve;
		$url = generer_url_public('',$arg);
	}
   $p->code ="preparar_enlace_enviar($arg)";
   $p->statut = 'html';
   return $p;
}

?>

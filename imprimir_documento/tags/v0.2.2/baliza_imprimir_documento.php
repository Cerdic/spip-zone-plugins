<?php

function preparar_enlace_imprimir($arg)
{

    // funcion para ventana pop-up centrada en la pantalla
    $javascript_centrar_imprimir = "<script type=\"text/javascript\">
	/*
	Open Centered Popup Window Script-
	 DHTMLShock (www.dhtmlshock.com)
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
    $logo = find_in_path('impresora.gif');

    $url = generer_url_public('', $arg);

    // "'page=imprimir_articulo&amp;id_article='.".

    // prepara el enlace completo
    $enlace = $javascript_centrar_imprimir."<a href=\"javascript:;\" onclick=\"centrarVentana('$url','Imprimir_documento','scrollbars=yes,resizable=yes','700','470','true')\" title=\""._T('imprimirdocumento:version_doc_imprimir')."\" style=\"border:none\"><img src=\"$logo\" style=\"border:none\" alt=\""._T('imprimirdocumento:version_doc_imprimir').'" /> '._T('imprimirdocumento:version_imprimir').'</a>';

    return $enlace;
}

function balise_IMPRIMIR_DOCUMENTO($p)
{
    if (isset($GLOBALS['contexte']['id_article'])) {
        $arg = "'page=imprimir_articulo&amp;id_article='.".champ_sql('id_article', $p);
    } elseif (isset($GLOBALS['contexte']['id_breve'])) {
        $arg = "'page=imprimir_breve&amp;id_breve='.".champ_sql('id_breve', $p);
    }

    $p->code = "preparar_enlace_imprimir($arg)";
    $p->statut = 'html';

    return $p;
}

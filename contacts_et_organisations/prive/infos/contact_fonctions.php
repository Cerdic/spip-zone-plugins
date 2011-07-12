<?php

// Voir en ligne, ou apercu, ou rien (renvoie tout le bloc)
function voir_contact_en_ligne($type, $id){

	$en_ligne = $message = '';

	$image='racine-24.gif';
	$en_ligne = 'calcul';
	$af = 0;
	$inline=0;

	if ($en_ligne == 'calcul')
		$message = _T('icone_voir_en_ligne');
	else if ($en_ligne == 'preview'
	AND autoriser('previsualiser'))
		$message = _T('previsualiser');
	else
		return '';

	$h = generer_url_public('contact', "id_contact=$id&var_mode=$en_ligne");

	return $inline  
	  ? icone_inline($message, $h, $image, "rien.gif", $GLOBALS['spip_lang_left'])
	: icone_horizontale($message, $h, $image, "rien.gif",$af);
}

?>
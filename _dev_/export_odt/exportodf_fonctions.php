<?php
$GLOBALS['debug_oasis']=true;
function env2url($env){
	if (is_string($env)) $env = unserialize($env);
	$params = "";
	foreach($env as $key=>$value)
		if (!in_array($key,array('fond','recurs')))
		$params .= (strlen($params)?"&":"") . "$key=".urlencode($value);
	return $params;
}

function spip2odt($env){
	if (is_string($env)) $env = unserialize($env);
	if (isset($env['fond'])) unset($env['fond']);
	if (isset($env['page'])) unset($env['page']);
	if (isset($env['recurs'])) unset($env['recurs']);

	$template = 'article.odt';
	if (isset($env['template'])){
		$template = $env['template'];
		unset($env['template']);
	}
	$nom_fichier = 'export';
	if (isset($env['nom_fichier'])){
		$nom_fichier = $env['nom_fichier'];
		unset($env['nom_fichier']);
	}
	
	include_spip('inc/odf_api');
	return spipoasis_recuperer_fond($template,$env,$nom_fichier);
}

$GLOBALS['spip_matrice']['image_resolution'] = '';
function image_resolution($img,$dpi){
	list($hauteur,$largeur) = taille_image($img);
	$largeur = round($largeur*72/$dpi);
	$hauteur = round($hauteur*72/$dpi);
	return image_tag_changer_taille($img,$largeur,$hauteur);
}
?>
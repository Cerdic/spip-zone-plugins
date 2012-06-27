<?php

// Insertion dans le porte-plume
function commhtml_porte_plume_barre_pre_charger($barres) {
	$barre = &$barres['edition'];
	$barre->ajouterApres('grpCaracteres', array(
		"id" => "sepCommHtml",
		"separator" => "---------------",
		"display"   => true,
	));
	$barre->ajouterApres('sepCommHtml', array(
		"id"            => 'commhtml',
		"name"          => _T('commhtml:outil_commentaire_html'),
		"className"     => 'outil_commentaire_html',
		"openWith"      => "<!-- ",
		"closeWith"     => " -->",
		"display"       => true,
		"selectionType" => "word"
	));
	return $barres;
}

// Icone pour le porte-plume
function commhtml_porte_plume_lien_classe_vers_icone($flux) {
	$icones = array();
	$icones['outil_commentaire_html'] = 'commentaire_html.png';
	return array_merge($flux, $icones);
}

// Affichage dans l'espace prive
function commhtml_pre_propre($texte) {
	if (test_espace_prive())
		$texte = preg_replace('#\<\!--(.+)--\>#U','<span style="color:green;">&lt;!--$1--&gt;</span>',$texte);
	 else
		$texte = preg_replace('#\<\!--(.+)--\>#U','',$texte);
	return $texte;
}

?>
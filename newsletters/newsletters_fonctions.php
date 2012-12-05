<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Lister les patrons disponibles
 * (en enlevant les masques par configuration et en les titrant comme dans la configuration)
 *
 * @return array
 */
function liste_choix_patrons($selected=null){
	$patrons = array();
	$files = find_all_in_path("newsletters/","\.html$");
	if (!$files) return $patrons;

	include_spip("inc/config");
	$masquer = lire_config("newsletters/masquer_fond");
	foreach ($files as $k=>$file){
		$fond = basename($k,'.html');
		//  ignorer les variantes .texte.html et .page.html utilisee pour generer les version textes et page en ligne
		if (count($e = explode(".",$fond))<2
			OR !in_array(end($e),array('page','texte'))){

			if (!in_array($fond,$masquer) OR $fond==$selected)
				$patrons[$fond] = afficher_titre_patron($fond);

		}
	}
	return $patrons;
}

/**
 * Afficher le titre d'un patron
 * @param string $patron
 * @return string
 */
function afficher_titre_patron($patron){
	include_spip("inc/newsletters");
	$infos = newsletters_fond_extraire_infos($patron);
	if (isset($infos['titre']))
		return "[$patron] ".$infos['titre'];

	return "[$patron]";
}

/**
 * Inliner du contenu base64 pour presenter les versions de newsletter dans une iframe
 * @param string $texte
 * @param string $type
 * @return string
 */
function inline_base64src($texte, $type="text/html"){
	return "data:$type;base64,".base64_encode($texte);
}

/**
 * Mises en formes pour la version en ligne de la newsletter :
 * - ajoute des styles specifiques surchargeables dans css/newsletter_inline.css
 *
 * @param string $page
 * @return string
 */
function newsletter_affiche_version_enligne($page){

	if ($f = find_in_path("css/newsletter_inline.css")){
		lire_fichier($f,$css);
		$css = '<style type="text/css">'.$css.'</style>';
		$p = stripos($page,"</head>");
		if ($p)
			$page = substr_replace($page,$css,$p,0);
		else
			$page .= $css;
	}
	return $page;
}
?>
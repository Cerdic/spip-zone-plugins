<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * un filtre pour transformer les URLs relatives en URLs absolues ;
 * ne s'applique qu'aux textes contenant des liens
 *
 * idem le filtre liens_absolus du core mais ne touche pas aux urls commencant par @@ qui sont en fait des variables
 *
 * @param string $texte
 * @param string $base
 * @return string
 */
function newsletters_liens_absolus($texte, $base='') {
	if (preg_match_all(',(<(a|link|image)[[:space:]]+[^<>]*>),imsS',$texte, $liens, PREG_SET_ORDER)) {
		foreach ($liens as $lien) {
			$href = extraire_attribut($lien[0],"href");
			if ($href AND strncmp($href,'#',1)!==0 AND strncmp($href,'@',1)!==0){
				$abs = url_absolue($href, $base);
				if ($abs <> $href){
					$href = str_replace($href,$abs,$lien[0]);
					$texte = str_replace($lien[0], $href, $texte);
				}
			}
		}
	}
	if (preg_match_all(',(<(img|script)[[:space:]]+[^<>]*>),imsS',$texte, $liens, PREG_SET_ORDER)) {
		foreach ($liens as $lien) {
			if ($src = extraire_attribut($lien[0],"src")){
				$abs = url_absolue($src, $base);
				if ($abs <> $src){
					$src = str_replace($src,$abs,$lien[0]);
					$texte = str_replace($lien[0], $src, $texte);
				}
			}
		}
	}
	return $texte;
}


/**
 * Lister les patrons disponibles
 * (en enlevant les masques par configuration et en les titrant comme dans la configuration)
 *
 * @param string $selected
 * @param bool $tout_voir
 * @return array
 */
function liste_choix_patrons($selected=null, $tout_voir = false){
	$patrons = array();
	$files = find_all_in_path("newsletters/","\.html$");
	if (!$files) return $patrons;

	include_spip("inc/config");
	$masquer = lire_config("newsletters/masquer_fond");
	if (!$masquer)
		$masquer = array();
	foreach ($files as $k=>$file){
		$fond = basename($k,'.html');
		//  ignorer les variantes .texte.html et .page.html utilisee pour generer les version textes et page en ligne
		if (count($e = explode(".",$fond))<2
			OR !in_array(end($e),array('page','texte'))){

			if ($tout_voir OR !in_array($fond,$masquer) OR $fond==$selected)
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
function newsletters_inline_base64src($texte, $type="text/html"){
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

	// contextualiser !
	$contextualize = charger_fonction("contextualize","newsletter");
	$infos = array(
		'email' => 'mail@example.org',
		'nom' => '',
		'lang' => $GLOBALS['spip_lang'],
		'url_unsubscribe' => _DIR_RACINE?_DIR_RACINE:"./",
		'listes' => array(),
	);
	$page = $contextualize($page, $infos);

	// css-izer
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

/**
 * Encapsuler les img du bon markup pour qu'elles ne depassent pas de la largeur maxi
 * sans pour autant etre deformee en plein ecran
 *
 * max-width:100% sur un img ne suffit pas
 * Il faut les mettre dans un div en width:100% avec un max-width:Npx correspondant a la taille maxi de l'image
 * et appliquer un width:100% sur l'image
 *
 * @param $texte
 * @return mixed
 */
function newsletter_responsive_img($texte){
	$texte = preg_replace_callback(",<img[^>]*>,Uims",'newsletter_responsive_img_wrap',$texte);
	return $texte;
}

/**
 * Callback de la fonction de dessus
 * @param $m
 * @return string
 */
function newsletter_responsive_img_wrap($m){
	$img = $m[0];
	$w = largeur($img);
	// on n'encapsule que les images de plus de 100px
	if ($w<100) return $img;

	$s = extraire_attribut($img,"style");
	if ($s) $s=rtrim($s,";").";";
	return
		"<div class='responsive-img' style='{$s}width:100%;max-width:{$w}px'>"
		.inserer_attribut($img,"style","{$s}width:100%;height:auto;")
		."</div>";
}

/**
 * Un filtre pour fixer une image
 * appele par l'action fixer_newsletter, mais peut etre utilise aussi directement dans la newsletter pour fixer les images
 * manuellement et forcer une url absolue sur un domaine particulier
 *
 * Le filtre genere une url finissant par #fixed pour ne pas y retoucher si il est rappelle
 * (y compris si c'est un domaine qu'il ne connait pas)
 *
 * @param $src
 * @param $id_newsletter
 * @return bool|string
 */
function newsletter_fixer_image($src,$id_newsletter){
	static $dir = array();
	if (!isset($dir[$id_newsletter])){
		$dir[$id_newsletter] = sous_repertoire(_DIR_IMG,"nl");
		$dir[$id_newsletter] = sous_repertoire($dir[$id_newsletter],$id_newsletter);
	}
	include_spip("inc/documents"); //deplacer_fichier_upload

	// recuperer l'image par copie directe si possible
	if (strncmp($src,$GLOBALS['meta']['adresse_site'].'/',$l=strlen($GLOBALS['meta']['adresse_site'].'/'))==0)
		$src = _DIR_RACINE . substr($src,$l);

	$url = parse_url($src);
	// hack : mettre un #fixed sur une url d'image pour indiquer qu'elle a deja ete fixee
	// on ne fait plus rien dans ce cas
	if ($url['fragment']=='fixed') return false;

	$path_parts = pathinfo($url['path']);
	$dest = $dir[$id_newsletter].md5($src).".".$path_parts['extension'];

	if (!$url['scheme']
		AND !$url['host']
	  AND file_exists($src)){
		deplacer_fichier_upload($src, $dest, false);
	}
	else {
		include_spip("inc/distant");
		recuperer_page($src,$dest);
	}

	if (!file_exists($dest))
		return false;

	return "$dest#fixed";
}

/**
 * Afficher la regle de recurrence en clair a partir de l'ics simplifie
 * @param string $ics
 * @param string $sep
 * @return string
 */
function newsletter_afficher_recurrence_regle($ics,$sep=", "){
	include_spip("inc/newsletters");
	list($date_debut,$rule) = newsletter_ics_to_date_rule($ics);
	include_spip("inc/when");
	return when_rule_to_texte($rule,$sep);
}

/**
 * Afficher la date de debut de la recurrence a partir de l'ics simplifie
 * @param string $ics
 * @return string
 */
function newsletter_afficher_recurrence_debut($ics){
	include_spip("inc/newsletters");
	list($date_debut,$rule) = newsletter_ics_to_date_rule($ics);
	return $date_debut;
}

?>
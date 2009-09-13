<?php
/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */


/**
 * Calcule une cle de jeton pour un formulaire
 *
 * @param string $form nom du formulaire
 * @return string cle calculee
 */
function creer_jeton($form, $qui=NULL) {
	$time = date('Y-m-d-H');
	if (is_null($qui)){
		if (isset($GLOBALS['visiteur_session']['id_auteur']) AND intval($GLOBALS['visiteur_session']['id_auteur']))
			$qui = ":".$GLOBALS['visiteur_session']['id_auteur'].":".$GLOBALS['visiteur_session']['nom'];
		else {
			include_spip('inc/session');
			$qui = hash_env();
		}
	}
	include_spip('inc/securiser_action');
	// le jeton prend en compte l'heure et l'identite de l'internaute
	return calculer_cle_action("jeton$form$time$qui");
}

/**
 * Verifie une cle de jeton pour un formulaire
 *
 * @param string $form nom du formulaire
 * @param string cle recue
 * @return bool cle correcte ?
 */
function verifier_jeton($jeton, $form, $qui=NULL) {
	$time = time();
	$time_old = date('Y-m-d-H',$time-3600);
	$time = date('Y-m-d-H',$time);

	if (is_null($qui)){
		if (isset($GLOBALS['visiteur_session']['id_auteur']) AND intval($GLOBALS['visiteur_session']['id_auteur']))
			$qui = ":".$GLOBALS['visiteur_session']['id_auteur'].":".$GLOBALS['visiteur_session']['nom'];
		else {
			include_spip('inc/session');
			$qui = hash_env();
		}
	}
	
	return (verifier_cle_action("jeton$form$time$qui",$jeton)
			or verifier_cle_action("jeton$form$time_old$qui",$jeton));
}


/**
 * Compte le nombre de caracteres d'une chaine,
 * mais en supprimant tous les liens 
 * (qu'ils soient ou non ecrits en raccourcis SPIP)
 * ainsi que tous les espaces en trop
 *
 * @param string $texte texte d'entree
 * @return int compte du texte nettoye
 */
function compter_caracteres_utiles($texte, $propre=true) {
	if ($propre) $texte = propre($texte);
	// regarder si il y a du contenu en dehors des liens !
	$texte = PtoBR($texte);
	$texte = preg_replace(',<a.*</a>,Uims','',$texte);
	$texte = trim(preg_replace(',[\W]+,uims',' ',$texte));
	return strlen($texte);
}


/**
 * Retourne un tableau d'analyse du texte transmis
 * Cette analyse concerne principalement des statistiques sur les liens
 *
 * @param string $texte texte d'entree
 * @return array rapport d'analyse
 */
function analyser_spams($texte) {
	$infos = array(
		'caracteres_utiles' => 0, // nombre de caracteres sans les liens
		'nombre_liens' => 0, // nombre de liens
		'caracteres_texte_lien_min' => 0, // nombre de caracteres du plus petit titre de lien
	);

	if (!$texte) return $infos;
	
	$texte = propre($texte);

	// caracteres_utiles
	$infos['caracteres_utiles'] = compter_caracteres_utiles($texte, false);

	// nombre de liens
	$liens = extraire_balises($texte,'a');
	$infos['nombre_liens'] = count($liens);

	// taille du titre de lien minimum
	if (count($liens)) {
		// supprimer_tags() s'applique a tout le tableau,
		// mais attention a verifier dans le temps que ca continue a fonctionner
		# $titres_liens = array_map('supprimer_tags', $liens);
		$titres_liens = supprimer_tags($liens);
		$titres_liens = array_map('strlen', $titres_liens);
		$infos['caracteres_texte_lien_min'] = min($titres_liens);
		spip_log($infos,'liens');
	}
	return $infos;
}



?>

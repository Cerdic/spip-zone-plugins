<?php
/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function nospam_hash_env() {
	static $res ='';
	if ($res) return $res;
	$ip = explode('.',$GLOBALS['ip']);
	array_pop($ip);
	$ip = implode('.',$ip).".xxx";
	$res = md5($ip. $_SERVER['HTTP_USER_AGENT']);
	#spip_log("jeton $res pour ".$ip. $_SERVER['HTTP_USER_AGENT'],"jetons");
	return $res;
}


/**
 * Calcule une cle de jeton pour un formulaire
 *
 * @param string $form
 *   nom du formulaire
 * @param string $qui
 *   identifiant du visiteur a qui est attribue le jeton
 * @return string
 *   cle calculee
 */
function creer_jeton($form, $qui=NULL) {
	$time = date('Y-m-d-H');
	if (is_null($qui)){
		if (isset($GLOBALS['visiteur_session']['id_auteur']) AND intval($GLOBALS['visiteur_session']['id_auteur']))
			$qui = ":".$GLOBALS['visiteur_session']['id_auteur'].":".$GLOBALS['visiteur_session']['nom'];
		elseif (!defined('_IS_BOT') OR !_IS_BOT) { // pas de jeton pour les bots qui n'ont rien d'interessant a poster
			$qui = nospam_hash_env();
		}
	}
	include_spip('inc/securiser_action');
	// le jeton prend en compte l'heure et l'identite de l'internaute
	return calculer_cle_action("jeton$form$time$qui");
}

/**
 * Verifie une cle de jeton pour un formulaire
 *
 * @param string $jeton
 *   cle recue
 * @param string $form nom du formulaire
 *   nom du formulaire
 * @param string $qui
 *   identifiant du visiteur a qui est attribue le jeton
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
			$qui = nospam_hash_env();
		}
	}
	
	$ok = (verifier_cle_action("jeton$form$time$qui",$jeton)
			or verifier_cle_action("jeton$form$time_old$qui",$jeton));
	#if (!$ok)
	#	spip_log("Erreur form:$form qui:$qui agent:".$_SERVER['HTTP_USER_AGENT']." ip:".$GLOBALS['ip'],'fauxjeton');
	return $ok;
}


/**
 * Compte le nombre de caracteres d'une chaine,
 * mais en supprimant tous les liens 
 * (qu'ils soient ou non ecrits en raccourcis SPIP)
 * ainsi que tous les espaces en trop
 *
 * @param string $texte
 *   texte d'entree
 * @param bool $propre
 *   passer le texte dans propre ou non
 * @return int
 *   compte du texte nettoye
 */
function compter_caracteres_utiles($texte, $propre=true) {
	include_spip('inc/charsets');
	if ($propre) $texte = propre($texte);
	$u = $GLOBALS['meta']['pcre_u'];
	// regarder si il y a du contenu en dehors des liens !
	$texte = PtoBR($texte);
	$texte = preg_replace(",<a.*</a>,{$u}Uims",'',$texte);
	// \W matche tous les caracteres non ascii apres 0x80
	// et vide donc les chaines constitues de caracteres unicodes uniquement
	// on remplace par un match qui elimine uniquement
	// les non \w  et les non unicodes
	$texte = trim(preg_replace(",[^\w\x80-\xFF]+,ims",' ',$texte));

	// on utilise spip_strlen pour compter la longueur correcte
	// pour les chaines unicodes
	return spip_strlen($texte);
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

	// on travaille d'abord sur le texte 'brut' tel que saisi par
	// l'utilisateur pour ne pas avoir les class= et style= que spip ajoute
	// sur les raccourcis.
	
	// on ne tient pas compte des blocs <code> et <cadre> ni de leurs contenus
	include_spip("inc/texte_mini");
	if (!function_exists('echappe_html')) // SPIP 2.x
		include_spip("inc/texte");
	$texte_humain = echappe_html($texte);
	// on repère dans ce qui reste la présence de style= ou class= qui peuvent
	// servir à masquer du contenu
	// les spammeurs utilisent le laxisme des navigateurs pour envoyer aussi style =
	// soyons donc mefiant
	// (mais en enlevant le base64 !)
	$texte_humain = str_replace('class="base64"','',$texte_humain);
	$hidden = ",(<(img|object)|\s(?:style|class)\s*=[^>]+>),UimsS";
	if (preg_match($hidden,$texte_humain)) {
		// suspicion de spam
		$infos['contenu_cache'] = true;
	}

	include_spip('inc/texte');
	$texte = propre($texte);

	// caracteres_utiles
	$infos['caracteres_utiles'] = compter_caracteres_utiles($texte, false);

	// nombre de liens
	$liens = array_filter(extraire_balises($texte,'a'),'pas_lien_ancre');
	$infos['nombre_liens'] = count($liens);
	$infos['liens'] = $liens;

	// taille du titre de lien minimum
	if (count($liens)) {
		// supprimer_tags() s'applique a tout le tableau,
		// mais attention a verifier dans le temps que ca continue a fonctionner
		# $titres_liens = array_map('supprimer_tags', $liens);
		$titres_liens = supprimer_tags($liens);
		$titres_liens = array_map('strlen', $titres_liens);
		$infos['caracteres_texte_lien_min'] = min($titres_liens);
	}
	return $infos;
}

/**
 * Vérifier si un lien est *n'est pas* une ancre : dans ce cas, ne pas le compte (ici, fonction de filtre de tableau)
 * Cette analyse concerne principalement des statistiques sur les liens
 *
 * @param string $texte lien
 * @return boolean : true -> 
 */
function pas_lien_ancre($texte){
	return substr(extraire_attribut($texte,'href'),0,1) == '#' ? false : true;
		
}

/**
 * Compare les domaines des liens fournis avec la presence dans la base
 *
 * @param array $liens
 *   liste des liens html
 * @param int $seuil
 *   seuil de detection de presence : nombre d'enregistrement qui ont deja un lien avec le meme domaine
 * @param string $table
 *   table sql
 * @param array $champs
 *   champs a prendre en compte dans la detection
 * @param null|string $condstatut
 *   condition sur le statut='spam' pour ne regarder que les enregistrement en statut spam
 * @return bool
 */
function rechercher_presence_liens_spammes($liens,$seuil,$table,$champs,$condstatut=null){
	include_spip("inc/filtres");

	if (is_null($condstatut))
		$condstatut = "statut=".sql_quote('spam');
	if ($condstatut)
		$condstatut = "$condstatut AND ";

	// limiter la recherche au mois precedent
	$trouver_table = charger_fonction("trouver_table","base");
	if ($desc = $trouver_table($table)
	  AND isset($desc['date'])){
		$depuis = date('Y-m-d H:i:s',strtotime("-1 month"));
		$condstatut .= $desc['date'].">".sql_quote($depuis)." AND ";
	}

	// ne pas prendre en compte les liens sur le meme domaine que celui du site
	$allowed = array();
	$tests = array($GLOBALS['meta']['adresse_site'],url_de_base());
	foreach ($tests as $t){
		if ($parse = parse_url($t)
			AND $parse['host']){
			$host = explode(".",$parse['host']);
			while (count($host)>2) array_shift($host);
			$allowed[] = implode(".",$host);
		}
	}
	if (count($allowed)){
		$allowed = array_map('preg_quote',$allowed);
		$allowed = implode("|",$allowed);
		$allowed = "/($allowed)$/";
		spip_log("domaines whitelist pour les liens spams : $allowed","nospam");
	}
	else
		$allowed = "";


	$hosts = array();
	foreach ($liens as $lien){
		$url = extraire_attribut($lien,"href");
		if ($parse = parse_url($url)
		  AND $parse['host']
		  AND (!$allowed OR !preg_match($allowed,$parse['host'])))
			$hosts[] = $parse['host'];
	}

	$hosts = array_unique($hosts);
	$hosts = array_filter($hosts);

	// pour chaque host figurant dans un lien, regarder si on a pas deja eu des spams avec ce meme host
	// auquel cas on refuse poliment le message
	foreach($hosts as $h){
		$like = " LIKE ".sql_quote("%$h%");
		$where = $condstatut . "(".implode("$like OR ",$champs)."$like)";
		if (($n=sql_countsel($table,$where))>=$seuil){
			// loger les 10 premiers messages concernes pour aider le webmestre
			$all = sql_allfetsel(id_table_objet($table),$table,$where,'','','0,10');
			$all = array_map('reset',$all);
			spip_log("$n liens trouves $like dans table $table (".implode(",",$all).") [champs ".implode(',',$champs)."]","nospam");
			return $h;
		}
	}
	return false;
}
?>

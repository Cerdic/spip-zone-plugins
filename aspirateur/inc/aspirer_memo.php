<?php
/**
 * Plugin Aspirateur pour Spip 3.0
 * Licence GPL 3
 *
 * (c) 2014 Anne-lise Martenot
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

	
/**
 * 
 * Enregistre les urls des pages aspirées dans un fichier
 *
 * Retourne l'url si la page nécessite d'être aspirée, sinon rien
 *
 * Utilisé par la fonction verifier_le_lien
 *
**/
function need_traitement($url_page,$url_site_aspirer){
	$page_referente = lire_config('aspirateur/page_referente');
	//on sort si la page n'a pas à être scanné ou est un document
	$motif_chemin_pages_exclure = lire_config('aspirateur/motif_chemin_pages_exclure');
	$motif_chemin_documents = lire_config('aspirateur/motif_chemin_documents');
	if (preg_match("'$motif_chemin_documents'", $url_page) OR preg_match("'$motif_chemin_pages_exclure'", $url_page)) 
	return;
	
	$aspirateur_tmp_liste=aspirateur_tmp_liste($url_site_aspirer);
		//lit le fichier qui liste les urls des pages traitées
		$data = @file_get_contents($aspirateur_tmp_liste); 
		$all_urls = @explode("\n", $data); 
		//si le lien n'est pas dedans, l'enregistrer dans une ligne
		if (empty($data) OR empty($all_urls) OR !in_array($url_page,$all_urls)){
			@file_put_contents($aspirateur_tmp_liste,$url_page."\n",FILE_APPEND);
			return $url_page;
		}
}


/**
 * 
 * Renvoie le nom du fichier unique
 *
 * qui enregistre les urls des pages aspirées
 *
 * créé avec un md5 sur @param $url_parent
 *
 *
**/
function aspirateur_tmp_liste($url_parent){
	$path=_DIR_IMG."aspirateur/";
	// verif si repertoire aspirateur dispo
	if (!is_dir($path)) {                                     
                   if (!mkdir ($path, 0777)) // on essaie de le creer  
                        return _T('aspirateur:erreur_ecrire_stockage').$path; 
        }
        //passage en minuscules (filtre SPIP d'urls_etendus)
	$url_parent=aspirateur_url_nettoyer($url_parent,50);
	$m = md5($url_parent);
	$md5url_parent=substr($m, 0, 5)."_".basename($url_parent);
	$aspirateur_tmp_liste=$path.$md5url_parent.".txt";
	return $aspirateur_tmp_liste;
}


/**
 *
 * Fonction reprise de SPIP (plugin dist urls_etendues)
 *
 *
**/
function aspirateur_url_nettoyer($titre,$longueur_maxi,$longueur_min=0,$separateur='-',$filtre=''){

	$titre = supprimer_tags(supprimer_numero(extraire_multi($titre)));
	$url = translitteration(corriger_caracteres($titre));

	if ($filtre)
		$url = $filtre($url);

	// on va convertir tous les caracteres de ponctuation et espaces
	// a l'exception de l'underscore (_), car on veut le conserver dans l'url
	$url = str_replace('_', chr(7), $url);
	$url = @preg_replace(',[[:punct:][:space:]]+,u', ' ', $url);
	$url = str_replace(chr(7), '_', $url);

	// S'il reste trop de caracteres non latins, les gerer comme wikipedia
	// avec rawurlencode :
	if (preg_match_all(",[^a-zA-Z0-9 _]+,", $url, $r, PREG_SET_ORDER)) {
		foreach ($r as $regs) {
			$url = substr_replace($url, rawurlencode($regs[0]),
				strpos($url, $regs[0]), strlen($regs[0]));
		}
	}

	// S'il reste trop peu, renvoyer vide
	if (strlen($url) < $longueur_min)
		return '';

	// Sinon couper les mots et les relier par des $separateur
	$mots = preg_split(",[^a-zA-Z0-9_%]+,", $url);
	$url = '';
	foreach ($mots as $mot) {
		if (!strlen($mot)) continue;
		$url2 = $url.$separateur.$mot;

		// Si on depasse $longueur_maxi caracteres, s'arreter
		// ne pas compter 3 caracteres pour %E9 mais un seul
		$long = preg_replace(',%.,', '', $url2);
		if (strlen($long) > $longueur_maxi) {
			break;
		}

		$url = $url2;
	}
	$url = substr($url, 1);

	// On enregistre en utf-8 dans la base
	$url = rawurldecode($url);

	if (strlen($url) < $longueur_min)
		return '';
	return $url;
}

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
 * @param string $url_page
 *	l'url de la page à traiter
 *
 * @param string $url_site_aspirer
 *	l'url du site à aspirer
 *
 * @return string $url_page
 *	l'url de la page à traiter ou rien
 *
**/
function need_traitement($url_page,$url_site_aspirer){
	$page_referente = lire_config('aspirateur/page_referente');
	//on sort si la page n'a pas à être scanné ou est un document
	$motif_chemin_pages_exclure = lire_config('aspirateur/motif_chemin_pages_exclure');
	$motif_chemin_documents = lire_config('aspirateur/motif_chemin_documents');
	if($motif_chemin_documents && preg_match("'$motif_chemin_documents'", $url_page)) return;
	if($motif_chemin_pages_exclure && preg_match("'$motif_chemin_pages_exclure'", $url_page)) return;
	
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
 * @param string $url_parent
 *	l'url du site à aspirer
 *
 * @return string
 *	le fichier créé dans IMG/aspirateur/
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
        // pour url_nettoyer
        include_spip('action/editer_url'); 
	$url_parent=url_nettoyer($url_parent,50);
	$m = md5($url_parent);
	$md5url_parent=substr($m, 0, 5)."_".basename($url_parent);
	$aspirateur_tmp_liste=$path.$md5url_parent.".txt";
	return $aspirateur_tmp_liste;
}

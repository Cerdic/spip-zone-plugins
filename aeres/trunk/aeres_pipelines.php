<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function aeres_affiche_milieu($flux) {
	$exec = $flux['args']['exec'];
	if ($exec=='configurer_zotspip' && autoriser('webmestre'))
		$flux['data'] .= recuperer_fond('prive/inclure/configurer_aeres');
	return $flux;
}

function aeres_affiche_droite($flux) {
	$exec = $flux['args']['exec'];
	if ($exec=='ticket')
		$flux['data'] .= '<h3>Correspondances<br />Zotero / AERES</h3><p style="text-align:right;"><a href="./?exec=infos_aeres" class="mediabox">Tableau détaillé</a></p>'.recuperer_fond('inclure/correspondances_zotero_aeres');
	return $flux;
}

/* pour que le pipeline ne rale pas ! */
function aeres_autoriser(){}

/**
 * Autorisation de vérifier les références biblio
  * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */
function autoriser_aeresbiblio_verifier_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;

	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	$type = lire_config('aeres/autorisation_verif_type');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('aeres/autorisation_verif_statuts',array('0minirezo')))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('aeres/autorisation_verif_statuts',array()));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('aeres/autorisation_verif_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
		$utiliser_defaut = false;
	}

	// Si pas configuré ou pas autorisé dans la conf => webmaster
	$autorise = ($qui['webmestre'] == 'oui');

	return $autorise;
}

function autoriser_bibliounite_voir_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;

	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	$type = lire_config('aeres/autorisation_biblio_unite_type');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('aeres/autorisation_biblio_unite_statuts',array('0minirezo')))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('aeres/autorisation_biblio_unite_statuts',array()));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('aeres/autorisation_biblio_unite_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
		$utiliser_defaut = false;
	}

	// Si pas configuré ou pas autorisé dans la conf => webmaster
	$autorise = ($qui['webmestre'] == 'oui');

	return $autorise;
}

function autoriser_aeresstat_voir_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;

	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	$type = lire_config('aeres/autorisation_stats_type');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Traitement spécifique pour la valeur 'tous'
				if(in_array('tous',lire_config('aeres/autorisation_stats_statuts',array('0minirezo')))){
					return true;
				}
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('aeres/autorisation_stats_statuts',array()));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('aeres/autorisation_stats_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
		$utiliser_defaut = false;
	}

	// Si pas configuré ou pas autorisé dans la conf => webmaster
	$autorise = ($qui['webmestre'] == 'oui');

	return $autorise;
}


?>
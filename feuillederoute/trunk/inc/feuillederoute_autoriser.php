<?php
/**
 * Plugin Feuille de Route
 * reglage des autorisations largement tire du plugin Tickets
 * Licence GPL (c) 2008-2012
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function feuillederoute_autoriser(){}

/**
 * Renvoie la liste des auteurs ou des statuts autorises pour une action donnee
 * 
 * @param string $action L'action que l'on souhaite faire
 * @param boolean $utiliser_defaut [optional]
 * @return 
 */ 
function definir_autorisations_feuillederoute($action,$utiliser_defaut=true){
	$aut = null;

	switch(strtolower($action)){
		case 'modifier':
			$define = (defined('_FEUILLEDEROUTE_AUTORISATION_ECRIRE')) ? _FEUILLEDEROUTE_AUTORISATION_ECRIRE : ($utiliser_defaut ? '0minirezo':'');
			break;
		case 'lire':
			$define = (defined('_FEUILLEDEROUTE_AUTORISATION_NOTIFIER')) ? _FEUILLEDEROUTE_AUTORISATION_NOTIFIER : ($utiliser_defaut ? '0minirezo':'');
			break;
		default:
			$define = $utiliser_defaut ? '0minirezo' : '';
			break;
	}

	if($define){
		$liste = explode(':', $define);
		if (in_array('webmestre', $liste))
			$aut['auteur'] = explode(':', _ID_WEBMESTRES);
		else if (in_array('0minirezo', $liste))
			$aut['statut'] = array('0minirezo');
		else if (in_array('1comite', $liste))
			$aut['statut'] = array('0minirezo', '1comite');
		else
			$aut['auteur'] = $liste;
	}

	return $aut;
}

/**
 * Autorisation de modification de la feuillederoute
 * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */ 
function autoriser_feuillederoute_modifier_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;

	if(!function_exists('lire_config'))
		include_spip('inc/config');

	$type = lire_config('feuillederoute/autorisations/modifier_type', 'par_statut');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('feuillederoute/autorisations/modifier_statuts',array('0minirezo')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('feuillederoute/autorisations/modifier_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
		$utiliser_defaut = false;
	}

	// Si $utiliser_defaut = true, on utilisera les valeurs par défaut
	// Sinon on ajoute la possibilité de régler par define
	$liste = definir_autorisations_feuillederoute('modifier',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);
	
	return $autorise;
}

/**
 * Autorisation de lire la feuillederoute
 * 
 * @param string $faire : l'action à faire
 * @param string $type : le type d'objet sur lequel porte l'action
 * @param int $id : l'identifiant numérique de l'objet
 * @param array $qui : les éléments de session de l'utilisateur en cours
 * @param array $opt : les options
 * @return boolean true/false : true si autorisé, false sinon
 */
function autoriser_feuillederoute_lire_dist($faire, $type, $id, $qui, $opt){
	$autorise = false;
	$utiliser_defaut = true;
	
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	$type = lire_config('feuillederoute/autorisations/lire_type');
	if($type){
		switch($type) {
			case 'webmestre':
				// Webmestres uniquement
				$autorise = ($qui['webmestre'] == 'oui');
				break;
			case 'par_statut':
				// Autorisation par statut
				$autorise = in_array($qui['statut'], lire_config('feuillederoute/autorisations/lire_statuts',array('0minirezo')));
				break;
			case 'par_auteur':
				// Autorisation par id d'auteurs
				$autorise = in_array($qui['id_auteur'], lire_config('feuillederoute/autorisations/lire_auteurs',array()));
				break;
		}
		if($autorise == true){
			return $autorise;
		}
		$utiliser_defaut = false;
	}

	// Si pas configuré ou pas autorisé dans la conf => on teste les define
	$liste = definir_autorisations_feuillederoute('lire',$utiliser_defaut);
	if ($liste['statut'])
		$autorise = in_array($qui['statut'], $liste['statut']);
	else if ($liste['auteur'])
		$autorise = in_array($qui['id_auteur'], $liste['auteur']);

	return $autorise;
}

?>

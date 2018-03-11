<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/objets');
include_spip('action/editer_objet');

/**
 * Point d'entrée pour dupliquer un objet
 *
 * On ne peut entrer que par un appel en fournissant $id et $objet
 * ou avec un argument d'action sécurisée de type "objet/id"
 *
 * @param string $objet
 * 		Type de l'objet à dupliquer
 * @param int $id
 * 		Identifiant de l'objet à dupliquer
 * @param array $modifications
 * 		Tableau de champ=>valeur avec les modifications à apporter sur le contenu dupliqué
 * @return array
 */
function action_dupliquer_objet_dist($objet = null, $id_objet = null, $modifications = null) {
	// appel direct depuis une url avec arg = "objet/id"
	if (is_null($id_objet) or is_null($objet)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		list($objet, $id_objet) = array_pad(explode("/", $arg, 2), 2, null);
	}
	
	if ($objet and $id_objet) {
		$id_objet_duplicata = objet_dupliquer($objet, $id_objet, $modifications);
	}
	
	return $id_objet_duplicata;
}

/**
 * Duplique un objet, ses liaisons et ses enfants
 * 
 * @param $objet
 * 		Type de l'objet à dupliquer
 * @param $id_objet 
 * 		Identifiant de l'objet à dupliquer
 * @param $modifications 
 * 		Tableau de champ=>valeur avec les modifications à apporter sur le contenu dupliqué
 * @param $options
 * 		Tableau d'options :
 * 		- dupliquer_liens : booléen précisant si on duplique les liens ou pas, par défaut oui
 * 		- dupliquer_enfants : booléen précisant si on duplique les enfants ou pas, par défaut non
 * 		- liens_exclus : liste d'objets liables dont on ne veut pas dupliquer les liens
 * @return int
 * 		Retourne l'identifiant du duplicata
 */
function objet_dupliquer($objet, $id_objet, $modifications=array(), $options=array()) {
	$cle_objet = id_table_objet($objet);
	$id_objet = intval($id_objet);
	
	// On cherche la liste des champs à dupliquer, par défaut tout
	$champs = lire_config("duplicator/$objet/champs", array());
	if (empty($champs)) {
		$champs = '*';
	}
	
	// On récupère les infos à dupliquer
	$infos_a_dupliquer = sql_fetsel($champs, table_objet_sql($objet), "$cle_objet = $id_objet");
	
	// On applique des modifications s'il y en a
	$infos_a_dupliquer = array_merge($infos_a_dupliquer, $modifications);
	
	// On commence la duplication de l'objet lui-même
	$id_objet_duplicata = objet_inserer($objet, 0, $infos_a_dupliquer);
	
	// Si on a bien notre nouvel objet
	if ($id_objet_duplicata = intval($id_objet_duplicata)) {
		// On cherche quels liens
		$liens = $liens_exclus = null;
		if (isset($options['liens'])) {
			$liens = $options['liens'];
		}
		if (isset($options['liens_exclus'])) {
			$liens_exclus = $options['liens_exclus'];
		}
		
		// On duplique les liens
		objet_dupliquer_liens($objet, $id_objet, $id_objet_duplicata, $liens, $liens_exclus);
		
		// On duplique les logos
		logo_dupliquer($objet, $id_objet, $id_objet_duplicata, 'on');
		logo_dupliquer($objet, $id_objet, $id_objet_duplicata, 'off');
		
		// On continue de lancer l'ancien pipeline
		pipeline('duplicator', array(
			'objet' => 'rubrique',
			'id_objet_origine' => $id_rubrique,
			'id_objet' => $id_nouvelle_rubrique
		));
	}
}

if (!function_exists('logo_dupliquer')) {
/**
 * Dupliquer un logo entre deux contenus
 * 
 * Cette fonction est destinée à être remplacé par une plus moderne dans le plugin Rôles de documents, qui gère alors aussi les logos en documents.
 * 
 * @param $objet 
 * 		Type de l'objet dont on veut dupliquer le logo
 * @param $id_source 
 * 		Identifiant de l'objet dont on veut dupliquer le logo
 * @param $id_cible 
 * 		Identifiant de l'objet sur lequel mettre le logo dupliqué
 * @param $etat 
 * 		État du logo (on ou off)
 * @return
 * 		Retourne le chemin du nouveau logo si tout s'est déroulé correctement
 */
function logo_dupliquer($objet, $id_source, $id_cible, $etat='on') {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$cle_objet = id_table_objet($objet);
	
	// Si on trouve le logo pour la source
	if ($logo_source = $chercher_logo($id_source, $cle_objet, $etat)) {
		return logo_modifier($objet, $id_cible, $etat, $logo_source[0]);
	}
	
	return false;
}
}

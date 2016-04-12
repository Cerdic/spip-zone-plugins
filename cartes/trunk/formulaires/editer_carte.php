<?php
/**
 * Gestion du formulaire de d'édition de carte
 *
 * @plugin     Création de cartes
 * @copyright  2016
 * @author     kent1
 * @licence    GNU/GPL
 * @package    SPIP\Cartes\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_carte
 *     Identifiant du carte. 'new' pour un nouveau carte.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un carte source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du carte, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_carte_identifier_dist($id_carte='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_carte)));
}

/**
 * Chargement du formulaire d'édition de carte
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_carte
 *     Identifiant du carte. 'new' pour un nouveau carte.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un carte source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du carte, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_carte_charger_dist($id_carte='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('carte', $id_carte, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	if(isset($valeurs['bounds']) && intval($id_carte) > 0){
		$valeurs['bounds'] = sql_getfetsel("AsText(bounds)","spip_cartes","id_carte = $id_carte");
		spip_log($valeurs['bounds'],'test.'._LOG_ERREUR);
		include_spip('gisgeom_fonctions');
		$valeurs['geojson'] = wkt_to_json($valeurs['bounds']);
		spip_log($valeurs['geojson'],'test.'._LOG_ERREUR);
	}
	if(isset($valeurs['controles']) && strlen($valeurs['controles']) > 1){
		$valeurs['controles'] = unserialize($valeurs['controles']);
	}
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de carte
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_carte
 *     Identifiant du carte. 'new' pour un nouveau carte.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un carte source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du carte, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_carte_verifier_dist($id_carte='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('carte', $id_carte, array('titre'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de carte
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_carte
 *     Identifiant du carte. 'new' pour un nouveau carte.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un carte source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du carte, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_carte_traiter_dist($id_carte='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	if(_request('controles')){
		set_request('controles',serialize(_request('controles')));
	}
	$retours = formulaires_editer_objet_traiter('carte', $id_carte, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	if(_request('geojson')){
		sql_update('spip_cartes', array('bounds'=>"GeomFromText('".json_to_wkt(_request('geojson'))."')"),"id_carte=".intval($retours['id_carte']));
	}
	return $retours;
}
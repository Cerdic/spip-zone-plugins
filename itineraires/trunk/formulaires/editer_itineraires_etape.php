<?php
/**
 * Gestion du formulaire de d'édition d'une étape
 *
 * @plugin     Itinéraires
 * @copyright  2013
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Itineraires\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_itineraires_etape
 *     Identifiant du itineraires_etape. 'new' pour un nouveau itineraires_etape.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraires_etape source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraires_etape, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_itineraires_etape_identifier_dist($id_itineraires_etape='new', $id_itineraire=0, $id_itineraire=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_itineraires_etape)));
}

/**
 * Saisies du formulaire d'édition de itineraires_etape
 *
 * Déclarer les saisies de formulaire à utiliser et les vérifications
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_itineraires_etape
 *     Identifiant du itineraires_etape. 'new' pour un nouveau itineraires_etape.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraires_etape source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraires_etape, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_itineraires_etape_saisies_dist($id_itineraires_etape='new', $id_itineraire=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	include_spip('inc/config');
	
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('itineraires_etape:champ_titre_label'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'texte',
				'label' => _T('itineraires_etape:champ_texte_label'),
				'rows' => 10,
			),
		),
	);
	
	return $saisies;
}

/**
 * Chargement du formulaire d'édition de itineraires_etape
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_itineraires_etape
 *     Identifiant du itineraires_etape. 'new' pour un nouveau itineraires_etape.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraires_etape source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraires_etape, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_itineraires_etape_charger_dist($id_itineraires_etape='new', $id_itineraire=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	if (!intval($id_itineraires_etape) and !$id_itineraire = intval($id_itineraire)) {
		return false;
	}
	
	$valeurs = formulaires_editer_objet_charger('itineraires_etape', $id_itineraires_etape, $id_itineraire, $lier_trad, $retour, $config_fonc, $row, $hidden);
	$id_itineraires_etape = intval($id_itineraires_etape);
	
	// On ajoute l'identifiant dans l'envoi
	$valeurs['_hidden'] .= '<input type="hidden" name="id_itineraires_etape" value="'.$id_itineraires_etape.'" />';
	
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de itineraires_etape
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_itineraires_etape
 *     Identifiant du itineraires_etape. 'new' pour un nouveau itineraires_etape.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraires_etape source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraires_etape, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_itineraires_etape_verifier_dist($id_itineraires_etape='new', $id_itineraire=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('itineraires_etape', $id_itineraires_etape, array('titre'));
	
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de itineraires_etape
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_itineraires_etape
 *     Identifiant du itineraires_etape. 'new' pour un nouveau itineraires_etape.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraires_etape source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraires_etape, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_itineraires_etape_traiter_dist($id_itineraires_etape='new', $id_itineraire=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$id_itineraire = intval($id_itineraire);
	
	// Si c'est une création
	if (!intval($id_itineraires_etape)) {
		// On utilise l'itinéraire en param
		set_request('id_itineraire', $id_itineraire);
		
		// On cherche le dernier rang
		$dernier_rang = sql_getfetsel('rang', 'spip_itineraires_etapes', 'id_itineraire = '.$id_itineraire, '', 'rang desc', '0,1');
		if (!$dernier_rang) {
			$dernier_rang = 0;
		}
		set_request('rang', $dernier_rang + 1); // On passe au rang suivant
	}
	
	$retours = formulaires_editer_objet_traiter('itineraires_etape',$id_itineraires_etape, $id_itineraire, $lier_trad, $retour, $config_fonc, $row, $hidden);
	$id_itineraires_etape = intval($retours['id_itineraires_etape']);
	
	return $retours;
}

<?php
/**
 * Gestion du formulaire de d'édition de projets_activite
 *
 * @plugin     DayFill
 * @copyright  2013
 * @author     Cyril Marion
 * @licence    GNU/GPL
 * @package    SPIP\Dayfill\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_projets_activite
 *     Identifiant du projets_activite. 'new' pour un nouveau projets_activite.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un projets_activite source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du projets_activite, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_projets_activite_identifier_dist($id_projets_activite='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_projets_activite)));
}

/**
 * Chargement du formulaire d'édition de projets_activite
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_projets_activite
 *     Identifiant du projets_activite. 'new' pour un nouveau projets_activite.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un projets_activite source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du projets_activite, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_projets_activite_charger_dist($id_projets_activite='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('projets_activite',$id_projets_activite,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de projets_activite
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_projets_activite
 *     Identifiant du projets_activite. 'new' pour un nouveau projets_activite.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un projets_activite source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du projets_activite, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_projets_activite_verifier_dist($id_projets_activite='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('projets_activite',$id_projets_activite);

	// verifier et changer en datetime sql la date envoyee
	$verifier = charger_fonction('verifier', 'inc');
	$dates = array('date_debut','date_fin');
	foreach($dates AS $champ) {
		$normaliser = null;
		if ($erreur = $verifier(_request($champ), 'date', array('normaliser'=>'datetime'), $normaliser)) {
			$erreurs[$champ] = $erreur;
			// si une valeur de normalisation a ete transmis, la prendre.
		} elseif (!is_null($normaliser)) {
			set_request($champ, $normaliser);
		}
	}

	return $erreurs;

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de projets_activite
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_projets_activite
 *     Identifiant du projets_activite. 'new' pour un nouveau projets_activite.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un projets_activite source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du projets_activite, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_projets_activite_traiter_dist($id_projets_activite='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('projets_activite',$id_projets_activite,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>

<?php
/**
 * Gestion du formulaire de d'édition de reservation_credit_mouvement
 *
 * @plugin     Réseŕvations Crédits
 * @copyright  2015-20
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_credits\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_reservation_credit_mouvement
 *     Identifiant du reservation_credit_mouvement. 'new' pour un nouveau reservation_credit_mouvement.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_credit_mouvement source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_credit_mouvement, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_reservation_credit_mouvement_identifier_dist($id_reservation_credit_mouvement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_reservation_credit_mouvement)));
}

/**
 * Chargement du formulaire d'édition de reservation_credit_mouvement
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_reservation_credit_mouvement
 *     Identifiant du reservation_credit_mouvement. 'new' pour un nouveau reservation_credit_mouvement.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_credit_mouvement source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_credit_mouvement, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_reservation_credit_mouvement_charger_dist($id_reservation_credit_mouvement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	include_spip('inc/config');
	$valeurs = formulaires_editer_objet_charger('reservation_credit_mouvement',$id_reservation_credit_mouvement,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	$devises = charger_fonction('reservations_devises','inc');
	$valeurs['devises'] = $devises();
	if (count($valeurs['devises']) == 1) {
		foreach ($valeurs['devises'] AS $key => $value) {
			$valeurs['devise'] = $key;
		}
		$valeurs['cacher_devise'] = true;
	}
	$valeurs['id_reservation_credit'] = _request('id_reservation_credit') ? _request('id_reservation_credit') : $valeurs['id_reservation_credit'];
	$valeurs['date_creation'] = _request('date_creation') ? _request('date_creation') : $valeurs['date_creation'] ? $valeurs['date_creation'] : date('Y-m-d H:i:s');
	$valeurs['_hidden'] = '<input type="hidden" name="id_reservations_detail" value="' .$valeurs['id_reservations_detail']. '"/>';

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de reservation_credit_mouvement
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_reservation_credit_mouvement
 *     Identifiant du reservation_credit_mouvement. 'new' pour un nouveau reservation_credit_mouvement.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_credit_mouvement source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_credit_mouvement, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_reservation_credit_mouvement_verifier_dist($id_reservation_credit_mouvement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	$erreurs = array();
	$verifier = charger_fonction('verifier', 'inc');

	foreach (array('date_creation') AS $champ){
		$normaliser = null;
		if ($erreur = $verifier(_request($champ), 'date', array('normaliser'=>'datetime'), $normaliser)) {
			$erreurs[$champ] = $erreur;
		// si une valeur de normalisation a ete transmis, la prendre.
		} elseif (!is_null($normaliser)) {
			set_request($champ, $normaliser);
		// si pas de normalisation ET pas de date soumise, il ne faut pas tenter d'enregistrer ''
		} else {
			set_request($champ, null);
		}
	}

	$erreurs += formulaires_editer_objet_verifier('reservation_credit_mouvement',$id_reservation_credit_mouvement, array('type', 'montant', 'date_creation', 'devise', 'id_reservation_credit' ,'descriptif'));

	return $erreurs;

}

/**
 * Traitement du formulaire d'édition de reservation_credit_mouvement
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_reservation_credit_mouvement
 *     Identifiant du reservation_credit_mouvement. 'new' pour un nouveau reservation_credit_mouvement.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_credit_mouvement source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_credit_mouvement, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_reservation_credit_mouvement_traiter_dist($id_reservation_credit_mouvement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('reservation_credit_mouvement',$id_reservation_credit_mouvement,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
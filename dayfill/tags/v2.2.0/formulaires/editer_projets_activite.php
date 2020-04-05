<?php
/**
 * Gestion du formulaire de d'édition de projets_activite
 *
 * @plugin     DayFill
 * @copyright  2014
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

	if (!intval($id_projets_activite) and $id_projet = _request('id_projet')){
		$valeurs['id_projet'] = $id_projet;
	}

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
	include_spip('dayfill_fonctions');
	$obligatoires = array('id_auteur','descriptif');
    foreach ($obligatoires as $obligatoire) {
        if (!_request($obligatoire)) {
            $erreurs[$obligatoire] = _T('info_obligatoire');
        }
    }

	$date_debut = _request('date_debut');
	// On reformate au format datetime pour pouvoir l'utiliser 'proprement'
	if (is_array($date_debut)) {
		$date_debut['date']		= explode('/', $date_debut['date']);
		$date_debut['heure']	= explode(':', $date_debut['heure']);
		$datetime_debut			= date('Y-m-d H:i:s', mktime($date_debut['heure'][0], $date_debut['heure'][1], 0, $date_debut['date'][1], $date_debut['date'][0], $date_debut['date'][2]));
	}

	$date_fin   = _request('date_fin');
	// On reformate au format datetime pour pouvoir l'utiliser 'proprement'
	if (is_array($date_fin)) {
		$date_fin['date']		= explode('/', $date_fin['date']);
		$date_fin['heure']		= explode(':', $date_fin['heure']);
		$datetime_fin			= date('Y-m-d H:i:s', mktime($date_fin['heure'][0], $date_fin['heure'][1], 0, $date_fin['date'][1], $date_fin['date'][0], $date_fin['date'][2]));
	}

	$datetime_heures_passees	= calcul_duree($datetime_fin, $datetime_debut, true);
	$nb_heures_passees			= _request('nb_heures_passees');

	// Si le nombre d'heures passées est saisi et différents de ce que ça devrait être
	// il y a une erreur.
	if ($datetime_heures_passees != $nb_heures_passees and $nb_heures_passees != '0.00') {
		$erreurs['nb_heures_passees'] = _T('dayfill:erreur_saisie_nb_h_passees_diff', array('indic' => $datetime_heures_passees));
	} elseif ($nb_heures_passees == '0.00') {
		// Si on a une heure de début et de fin saisies et aucun nombre d'heures passées saisies
		// il y a une erreur
		$erreurs['nb_heures_passees'] = _T('dayfill:erreur_saisie_nb_h_passees_vide', array('indic' => $datetime_heures_passees));
	}

	// La date de début ne peut pas être antérieure à la date de fin
	if ($datetime_debut > $datetime_fin) {
		$erreurs['erreur_date_fin_plus_ancien'] = _T('dayfill:erreur_date_fin_plus_ancien');
		// Si on est dans ce cas, pas la peine d'afficher une erreur sur la saisie du nombre d'heures
		unset($erreurs['nb_heures_passees']);
	}

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
	$retour = formulaires_editer_objet_traiter('projets_activite',$id_projets_activite,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	if ($id_auteur = _request('id_auteur') and $id_projet = _request('id_projet')) {
		include_spip('action/editer_liens');
		objet_associer(array('auteur'=>$id_auteur), array('projet'=>$id_projet));
	}
	return $retour;
}


?>

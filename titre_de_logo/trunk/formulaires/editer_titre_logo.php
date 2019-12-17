<?php

/*
 * Plugin Titre de logo
 *
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/abstract_sql');
include_spip('inc/config');

function formulaires_editer_titre_logo_charger_dist($objet, $id_objet, $retour = '', $options = array()) {
	$exec = (_request('exec')) ? _request('exec') : '';
	$objet = objet_type($objet);
	$table_objet = table_objet_sql($objet);
	$_id_objet = id_table_objet($objet);
	$titre_logo = '';
	$descriptif_logo = '';
	$objets_autorises = lire_config('titre_logo/objets_autorises', array('spip_articles'));
	$objets_autorises = (isset($objets_autorises))
		? array_filter($objets_autorises)
		: array();

	// Si on est sur ?exec=configurer_identite
	// on n'affiche pas le formulaire.
	if ($exec == 'configurer_identite') {
		return false;
	} elseif (!in_array($table_objet, $objets_autorises)) {
		return false;
	}

	$requete_sql = sql_fetsel('titre_logo,descriptif_logo', $table_objet, $_id_objet.'='.intval($id_objet));
	if ($requete_sql) {
		$titre_logo = $requete_sql['titre_logo'];
		$descriptif_logo = $requete_sql['descriptif_logo'];
	}

	$valeurs = array(
		'objet'	 => $objet,
		'id_objet'  => $id_objet,
		'titre_logo' => $titre_logo,
		'descriptif_logo' => $descriptif_logo,
		'exec' => $exec,
		'_options' => $options,
	);

	return $valeurs;
}

function formulaires_editer_titre_logo_verifier_dist($objet, $id_objet, $retour = '', $options = array()) {
	$erreurs = array();

	return $erreurs;
}

function formulaires_editer_titre_logo_traiter_dist($objet, $id_objet, $retour = '', $options = array()) {
	$res = array();
	$table_objet = table_objet_sql($objet);
	$titre_logo = _request('titre_logo');
	$descriptif_logo = _request('descriptif_logo');
	$_id_objet = id_table_objet($objet);

	if (include_spip('action/editer_'.$objet) && function_exists($objet.'_modifier')) {
		$function = charger_fonction('modifier', $objet);
		$erreur = $function($id_objet, array('titre_logo' => $titre_logo, 'descriptif_logo' => $descriptif_logo));
		if (!$modif) {
			$update_sql = true;
		}
	} else {
		$update_sql = sql_updateq(
			$table_objet,
			array('titre_logo' => $titre_logo, 'descriptif_logo' => $descriptif_logo),
			$_id_objet.'='.intval($id_objet)
		);
	}

	if ($update_sql) {
		refuser_traiter_formulaire_ajax();
		$res['message_ok'] = _T('info_modification_enregistree');
		$res['redirect'] = generer_url_entite($id_objet, $objet);
	} else {
		$res['message_erreur'] = _T('avis_erreur');
	}

	return $res;
}

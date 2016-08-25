<?php
/**
 * Utilisations de pipelines par Emplois
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajouter les objets sur les vues de rubriques
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function emplois_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
		AND $e['type'] == 'rubrique'
		AND $e['edition'] == false) {

		$id_rubrique = $flux['args']['id_rubrique'];
		$lister_objets = charger_fonction('lister_objets', 'inc');

		$bouton = '';
		if (autoriser('creeroffredans', 'rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T("offre:icone_creer_offre"), generer_url_ecrire("offre_edit", "id_rubrique=$id_rubrique"), "offre-24.png", "new", "right")
					. "<br class='nettoyeur' />";
		}

		$flux['data'] .= $lister_objets('offres', array('titre'=>_T('offre:titre_offres_rubrique') , 'id_rubrique'=>$id_rubrique));
		$flux['data'] .= $bouton;

		$bouton = '';
		if (autoriser('creercvdans', 'rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T("cv:icone_creer_cv"), generer_url_ecrire("cv_edit", "id_rubrique=$id_rubrique"), "cv-24.png", "new", "right")
					. "<br class='nettoyeur' />";
		}

		$flux['data'] .= $lister_objets('cvs', array('titre'=>_T('cv:titre_cvs_rubrique') , 'id_rubrique'=>$id_rubrique));
		$flux['data'] .= $bouton;

	}
	return $flux;
}


/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function emplois_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/cvs', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('cv:info_cvs_auteur')
		), array('ajax' => true));

	}
	return $flux;
}


/**
 * Compter les enfants d'une rubrique
 *
 * @pipeline objets_compte_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/
function emplois_objet_compte_enfants($flux) {
	if ($flux['args']['objet']=='rubrique' AND $id_rubrique=intval($flux['args']['id_objet'])) {
		// juste les publiés ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['offres'] = sql_countsel('spip_offres', "id_rubrique = ".intval($id_rubrique)." AND (statut = 'publie')");
		} else {
			$flux['data']['offres'] = sql_countsel('spip_offres', "id_rubrique = ".intval($id_rubrique)." AND (statut <> 'poubelle')");
		} 		// juste les publiés ?
		if (array_key_exists('statut', $flux['args']) and ($flux['args']['statut'] == 'publie')) {
			$flux['data']['cvs'] = sql_countsel('spip_cvs', "id_rubrique = ".intval($id_rubrique)." AND (statut = 'publie')");
		} else {
			$flux['data']['cvs'] = sql_countsel('spip_cvs', "id_rubrique = ".intval($id_rubrique)." AND (statut <> 'poubelle')");
		} 
	}
	return $flux;
}


/**
 * Optimiser la base de données 
 * 
 * Supprime les objets à la poubelle.
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function emplois_optimiser_base_disparus($flux){

	sql_delete("spip_offres", "statut='poubelle' AND maj < " . $flux['args']['date']);

	sql_delete("spip_cvs", "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

/**
 * Synchroniser la valeur de id secteur
 *
 * @pipeline trig_propager_les_secteurs
 * @param  string $flux Données du pipeline
 * @return string       Données du pipeline
**/
function emplois_trig_propager_les_secteurs($flux) {

	// synchroniser spip_offres
	$r = sql_select("A.id_offre AS id, R.id_secteur AS secteur", "spip_offres AS A, spip_rubriques AS R",
		"A.id_rubrique = R.id_rubrique AND A.id_secteur <> R.id_secteur");
	while ($row = sql_fetch($r)) {
		sql_update("spip_offres", array("id_secteur" => $row['secteur']), "id_offre=" . $row['id']);
	}

	// synchroniser spip_cvs
	$r = sql_select("A.id_cv AS id, R.id_secteur AS secteur", "spip_cvs AS A, spip_rubriques AS R",
		"A.id_rubrique = R.id_rubrique AND A.id_secteur <> R.id_secteur");
	while ($row = sql_fetch($r)) {
		sql_update("spip_cvs", array("id_secteur" => $row['secteur']), "id_cv=" . $row['id']);
	}

	return $flux;
}

/**
 * Gestion des menus : afficher ou non les boutons pour les Offres et les CVs
 *
 * @pipeline ajouter_menus
 * @param  string $flux Données du pipeline
 * @return string       Données du pipeline
**/
function emplois_ajouter_menus($flux) {
	include_spip('inc/config');

	// les offres d'emplois
	$offres_active = lire_config('emplois/offres/activer_offres');
	if (isset($offres_active) AND $offres_active == 'non') {
		unset($flux['menu_activite']->sousmenu['offres']);
	}

	// les CVs
	$cvs_active = lire_config('emplois/cvs/activer_cvs');
	if (isset($cvs_active) AND $cvs_active == 'non') {
		unset($flux['menu_activite']->sousmenu['cvs']);
	}
	return $flux;
}

/**
 * Envoyer une notification aux administrateurs du site lorsque une offre a été postée
 *
 * @pipeline post_insertion
 * @param  string $flux Données du pipeline
 * @return string       Données du pipeline
**/
function emplois_post_insertion($flux) {
	$email_webmaster = lire_config('email_webmaster');
	if ($flux['args']['table'] == 'spip_offres' AND $flux['data']['statut'] =='prepa') {
		$envoyer_mail = charger_fonction('envoyer_mail','inc');

		$email_to = $email_webmaster;
		$sujet = "Nouveau dépot Offre Emploi";
		$message = "une nouvelle offre d'emploi vient d'être postée sur le site.";

		$send = $envoyer_mail($email_to,$sujet,$message);
	}

}


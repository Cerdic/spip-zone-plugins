<?php
/**
 * Utilisations de pipelines par Emplois
 *
 * @plugin     Emplois
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Emplois\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
 * Gestion des Notifications
 * ici envoi d'une notification au webmaster : un dépôt (offre ou CV) vient d'être fait 
 *
 * @pipeline post_insertion
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function emplois_post_insertion($flux) {
	$tables_emplois = array( 'spip_offres', 'spip_cvs');
	if (isset ($flux['args']['table']) && in_array($flux['args']['table'], $tables_emplois) && !test_espace_prive()) {

		// ici c'est bien le statut "prepa". 
		if ($flux['data']['statut'] == 'prepa') {
			// récupérer le nom de la table (offres ou cvs)
			$table = $flux['args']['table'];
			$id_table = id_table_objet($table);
			$type = objet_type($table);
			$type == 'offre' ? $type_sujet = 'nouvel emploi' : $type_sujet = 'nouveau CV';

			// récupérer le mail du webmaster
			$mail_webmaster = lire_config('email_webmaster');

			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			$email_to = $mail_webmaster;
			$email_from = $mail_webmaster;
			$sujet = "Un $type_sujet en attente de validation";
			$message = "Un $type_sujet vient d‘être déposé." ;

			//allez zou, on envoi
			$envoyer_mail($email_to,$sujet,$message,$email_from);
		}
	}
}

/**
 * Gestion des Notifications
 * ici envoi d'une notification au déposant d'une nouvelle offre d'emploi  ou d'un nouveau CV
 *
 * @pipeline post_edition
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function emplois_post_edition($flux) {
	$tables_emplois = array( 'spip_offres', 'spip_cvs');
	if (isset ($flux['args']['table']) 
		&& in_array($flux['args']['table'], $tables_emplois)
		&& $flux['args']['action'] =='instituer'
		&& $flux['args']['statut_ancien'] == 'prop'
		) {
			// récupérer le nom de la table (offres ou cvs)
			$table = $flux['args']['table'];
			$id_table = id_table_objet($table);
			$id = $flux['args']['id_objet'];
			$type = objet_type($table);

			// pas d'email dans la table CVs. On va le chercher dans la table spip_auteurs
			if ($table == 'spip_cvs') {
				$table = 'spip_auteurs';
				$id_table  = 'id_auteur';
				$id = sql_getfetsel('id_auteur', 'spip_cvs', 'id_cv='.intval($id));
			}

			$type == 'offre' ? $type_sujet = 'proposition d‘offre d‘emploi' : $type_sujet = 'CV';

			// récupérer le mail du webmaster
			$mail_webmaster = lire_config('email_webmaster');

			// récupérer le mail de l'internaute à qui envoyer la notification
			$email_deposant = sql_getfetsel('email', $table, $id_table.'='.intval($id));

			if ($email_deposant AND in_array($flux['data']['statut'], array('publie', 'refuse'))) {
				if ($flux['data']['statut'] == 'publie') {
					$message = "Votre $type_sujet a été validé.";
				}
				if ($flux['data']['statut'] == 'refuse') {
					$message = "Votre $type_sujet a été refusé.";
				}

				$message .= "\nBien cordialement.";
				$envoyer_mail = charger_fonction('envoyer_mail','inc');
				$email_to = $email_deposant;
				$email_from = $mail_webmaster;
				$sujet = "Votre $type_sujet";

				//zou, on envoi
				$envoyer_mail($email_to,$sujet,$message,$email_from);
    		}

    }
    return $flux;
}

/**
 * Gestion de l'ajout d'un document depuis le back-office dans les offres 
 * - Insertion : on force le statut du document à "publie" et on copie l'id_document dans la table spip_offres
 * - suppression : on met la valeur "0" dans la champ id_document_offre de la table spip_offres
 *
 * @pipeline post_edition_lien
 * @param  string $flux Données du pipeline
 * @return string       Données du pipeline
**/
function emplois_post_edition_lien($flux) {

	if ($flux['args']['table_lien'] == 'spip_documents_liens' AND $flux['args']['objet'] == 'offre') {

		$id_doc = $flux['args']['id_objet_source'];
		$id_offre = $flux['args']['id_objet'];

		if ($flux['args']['action'] == 'insert') {
			// forcer le statut du document à "publié"
			sql_updateq('spip_documents', array('statut' => 'publie'), 'id_document='.intval($id_doc));

			// copier l'id_document dans la table spip_offres
			sql_updateq('spip_offres', array('id_document_offre' => $id_doc), "id_offre =".intval($id_offre));
		}

		if ($flux['args']['action'] == 'delete') {
			// en cas de suppression, mettre l'id_document à "0" dans la table spip_offres
			sql_updateq('spip_offres', array('id_document_offre' => '0'), "id_offre =".intval($id_offre));
		}
	}
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



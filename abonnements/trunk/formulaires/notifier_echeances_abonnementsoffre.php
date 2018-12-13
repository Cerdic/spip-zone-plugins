<?php
/**
 * Envoyer ponctuellement les notifications aux abonnés d'une offre
 * 
 * On restreindre la liste des gens à notifier selon certains critères : statut, dates, ...
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration des saisies du formulaire
 *
 * @param int id_abonnements_offre
 *    Numéro d'une offre d'abonnements
 * @param string $retour
 *     URL de redirection
 * @return array
 */
function formulaires_notifier_echeances_abonnementsoffre_saisies_dist($id_abonnements_offre, $retour = ''){
	$saisies = array(
		array(
			'saisie' => 'selection',
			'options' => array(
				'nom' => 'statut',
				'label' => _T('abonnement:champ_notifier_statut_label'),
				'datas' => array(
					'' => ucfirst(_T('abonnement:statut_tous')),
					'actif' => ucfirst(_T('abonnement:statut_actifs')),
					'inactif' => ucfirst(_T('abonnement:statut_inactifs')),
				),
				'cacher_option_intro' => 'oui',
			),
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom'   => 'fieldset_date_debut',
				'label' => _T('abonnement:champ_dates_debut_label'),
				'pliable' => 'oui',
				'plie' => 'oui',
			),
			'saisies' => array(
				array(
					'saisie' => 'date',
					'options' => array(
						'nom'   => 'date_debut_du',
						'label' => _T('abonnement:champ_date_du_label'),
						'li_class' => 'date_inline',
					),
				),
				array(
					'saisie' => 'date',
					'options' => array(
						'nom'    => 'date_debut_au',
						'label'  => _T('abonnement:champ_date_au_label'),
						'li_class' => 'date_inline',
					),
				),
			),
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_date_fin',
				'label' => _T('abonnement:champ_dates_fin_label'),
				'pliable' => 'oui',
				'plie' => 'oui',
			),
			'saisies' => array(
				array(
					'saisie' => 'date',
					'options' => array(
						'nom' => 'date_fin_du',
						'label' => _T('abonnement:champ_date_du_label'),
						'li_class' => 'date_inline',
					),
				),
				array(
					'saisie' => 'date',
					'options' => array(
						'nom' => 'date_fin_au',
						'label' => _T('abonnement:champ_date_au_label'),
						'li_class' => 'date_inline',
					),
				),
			),
		),
	);
	
	return $saisies;
}


/**
 * Charger les valeurs
 *
 * @param int id_abonnements_offre
 *    Numéro d'une offre d'abonnement
 * @param string $retour
 *     URL de redirection
 * @return array
 */
function formulaires_notifier_echeances_abonnementsoffre_charger_dist($id_abonnements_offre, $retour = ''){

	$contexte = array(
		'id_abonnements_offre' => $id_abonnements_offre,
		'is_verifie'         => false,
		'is_annule'          => false,
		'confirmer_verifier' => false,
	);
	
	// Il faut obligatoirement un ID valide
	if (!intval($id_abonnements_offre)){
		$contexte['message_erreur'] = _T('erreur');
		$contexte['editable'] = false;
	}
	
	return $contexte;
}


/**
 * Vérifier les valeurs postées
 *
 * @param int id_abonnements_offre
 *    Numéro d'une offre d'abonnement
 * @param string $retour
 *     URL de redirection
 * @return array
 */
function formulaires_notifier_echeances_abonnementsoffre_verifier_dist($id_abonnements_offre, $retour = ''){
	$erreurs = array();
	return $erreurs;
}


/**
 * Traitements
 *
 * @param int id_abonnements_offre
 *    Numéro d'une offre d'abonnement
 * @param string $retour
 *     URL de redirection
 * @return array
 */
function formulaires_notifier_echeances_abonnementsoffre_traiter_dist($id_abonnements_offre, $retour = ''){
	$res = array();
	$message_ok = '';
	$message_erreur = '';
	
	// Empêcher le traitement AJAX en cas de redirection
	if ($retour) {
		refuser_traiter_formulaire_ajax();
	}
	
	// Récupérer et normaliser les valeurs postées
	$statut        = _request('statut');
	$date_debut_du = normaliser_date(str_replace('/', '-', _request('date_debut_du')));
	$date_debut_au = _request('date_debut_au') ? normaliser_date(str_replace('/', '-', _request('date_debut_au')) . ' 23:59:59') : '';
	$date_fin_du   = normaliser_date(str_replace('/', '-', _request('date_fin_du')));
	$date_fin_au   = _request('date_fin_au') ? normaliser_date(str_replace('/', '-', _request('date_fin_au')) . ' 23:59:59') : '';

	// Préparer la partie du WHERE conçernant les dates, utilisée dans le SQL et le JS
	$where = array("date_fin > 0"); // Il faut forcément une date de fin
	if ($date_debut_du and !$date_debut_au) {
		$where[] = 'date_debut >= ' . sql_quote($date_debut_du);
	} elseif (!$date_debut_du and $date_debut_au) {
		$where[] = 'date_debut <= ' . sql_quote($date_debut_au);
	} elseif ($date_debut_du and $date_debut_au) {
		$where[] = '(date_debut BETWEEN ' . sql_quote($date_debut_du) . ' AND ' . sql_quote($date_debut_au) . ')';
	}
	if ($date_fin_du and !$date_fin_au) {
		$where[] = 'date_fin >= ' . sql_quote($date_fin_du);
	} elseif (!$date_fin_du and $date_fin_au) {
		$where[] = 'date_fin <= ' . sql_quote($date_fin_au);
	} elseif ($date_fin_du and $date_fin_au) {
		$where[] = '(date_fin BETWEEN ' . sql_quote($date_fin_du) . ' AND ' . sql_quote($date_fin_au) . ')';
	}
	
	// Préparer le JS pour remettre les listes à zéro
	$js_reset = '<script type="text/javascript">' .
		'ajaxReload("abonnements_actifs", {args:{"statut":"actif","where":"","nb":"","sinon":"","titre_singulier":"abonnement:info_1_abonnement_actif","titre_pluriel":"abonnement:info_nb_abonnements_actifs","date_fin":""}});' .
		'ajaxReload("abonnements_inactifs",{args:{"statut":"inactif"}});' .
		'</script>';
	
	// ========================================================
	// Vérification des abonnements à notifier avant validation
	// ========================================================
	if (_request('btn_verifier')){
		// Renvoyer du JS dans le squelette pour recharger la liste avec les abonnements à notifier
		// On cache la 2ème liste en passant un statut inexistant
		$params = array(
			'"titre_singulier":"abonnement:info_1_abonnement_notifier"',
			'"titre_pluriel":"abonnement:info_nb_abonnements_notifier"',
			'"sinon":"' . _T('abonnement:info_aucun_abonnement_notifier') . '"',
			'"nb":"50"',
			'"where":"' . join(' AND ', $where) . '"',
			'"statut":"' . $statut . '"',
		);
		$params = join(',', array_filter($params));
		$js = '<script type="text/javascript">' .
			'ajaxReload("abonnements_actifs",{args:{'. $params .'}});' .
			'ajaxReload("abonnements_inactifs",{args:{"statut":"non","sinon":""}});' .
			'</script>';
		$res['editable'] = true;
		set_request('is_verifie', true);
		set_request('is_annule', false);
		set_request('confirmer_verifier', true);
		$message_ok = $js;
	}
	
	// =============================
	// Annulation après vérification
	// =============================
	if (_request('btn_annuler')){
		$res['editable'] = true;
		set_request('is_verifie', false);
		set_request('is_annule', true);
		set_request('confirmer_verifier', false);
		$message_ok = $js_reset;
	}
	
	// ============================
	// OK : envoi des notifications
	// ============================
	// Récupérer tous les abonnements qui n'ont pas de job ce jour (éviter 2 notifs le même jour !)
	if (_request('btn_notifier')){
		$where[] = 'id_abonnements_offre = ' . intval($id_abonnements_offre);
		$where[] = 'email IS NOT NULL';
		$where[] = 'j.id_job IS NULL ' .
			'OR (j.id_job IS NOT NULL AND DATE_FORMAT(j.date, "%Y-%m-%d") != ' . sql_quote(date('Y-m-d')) . ')';
		$where[] = $statut ? 'abo.statut = ' . sql_quote($statut) : '';
		$where = array_filter($where);
		if ($a_notifier = sql_allfetsel(
			'id_abonnement, nom, email, date_fin',
			'spip_abonnements as abo' .
				' INNER JOIN spip_auteurs AS aut ON abo.id_auteur = aut.id_auteur' .
				' LEFT JOIN spip_jobs_liens AS l ON abo.id_abonnement = l.id_objet AND l.objet = "abonnement"' .
				' LEFT JOIN spip_jobs AS j ON l.id_job = j.id_job AND j.fonction = "abonnements_notifier_echeance"',
			$where
		)){
			// Pour chacun on programme un envoi de mail
			foreach ($a_notifier as $abonnement){
				$aujourdhui = new DateTime('now');
				$echeance   = new DateTime($abonnement['date_fin']);
				$difference = $aujourdhui->diff($echeance);
				$duree      = $difference->days;
				$periode    = 'jours';
				$quand      = $duree === 0 ?
					('pendant') :
					($aujourdhui > $echeance ? 'apres' : 'avant');
				$id_job = job_queue_add(
					'abonnements_notifier_echeance',
					"Notifier manuellement {$abonnement['nom']} $duree $periode $quand l'échéance de son abonnement {$abonnement['id_abonnement']}",
					array(
						$abonnement['id_abonnement'],
						$abonnement['nom'],
						$abonnement['email'],
						$duree,
						$periode,
						$quand,
					),
					'inc/abonnements',
					true
				);
				job_queue_link(
					$id_job,
					array(
						'objet'    => 'abonnement',
						'id_objet' => $abonnement['id_abonnement']
					)
				);
			}
		}
		
		if (count($a_notifier) > 0) {
			$message_ok = _T('abonnementsoffre:message_notifier_ok');
			$message_ok .= $js_reset;
		} else {
			$message_erreur = _T('abonnement:info_aucun_abonnement_notifier');
			$message_erreur .= $js_reset;
		}
		$res['editable'] = false;
		set_request('is_annule', false);
		set_request('is_verifie', false);
		set_request('confirmer_verifier', false);
		
		// Redirection éventuelle
		if ($retour) {
			$res['redirect'] = $retour;
		}
	}

	// Messages de retour
	if ($message_erreur) {
		$res['message_erreur'] = $message_erreur;
	} elseif ($message_ok) {
		$res['message_ok'] = $message_ok;
	}
	
	return $res;
}

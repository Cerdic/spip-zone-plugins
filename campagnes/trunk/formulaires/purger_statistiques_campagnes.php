<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_purger_statistiques_campagnes_saisies_dist($id_campagne=null, $id_annonceur=null){
	$saisies = array(
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date_debut',
				'label' => _T('campagne:champ_date_debut_label'),
				'pleine_largeur' => 'oui',
			),
			'verifier' => array(
				'type' => 'date',
				'options' => array(
					'normaliser' => 'datetime',
				),
			),
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date_fin',
				'label' => _T('campagne:champ_date_fin_label'),
				'pleine_largeur' => 'oui',
			),
			'verifier' => array(
				'type' => 'date',
				'options' => array(
					'normaliser' => 'datetime',
				),
			),
		),
		'options' => array(
			'inserer_debut' => '<h3 class="titrem">'._T('campagne:purger_statistiques_titre').'</h3>',
			'texte_submit' => _T('campagne:purger_statistiques_bouton'),
		),
	);
	
	return $saisies;
}

function formulaires_purger_statistiques_campagnes_verifier_dist($id_campagne=null, $id_annonceur=null){
	include_spip('inc/campagnes');
	$erreurs = array();
	
	// S'il y a des dates, on vérifie le format et l'ordre
	$date_debut = _request('date_debut');
	$date_fin = _request('date_fin');
	if ($date_debut and $date_fin and $date_fin < $date_debut){
		$erreurs['message_erreur'] = _T('campagne:erreur_date_avant_apres');
	}
	// Soit aucune date soit les deux
	if (($date_debut and !$date_fin) or (!$date_debut and $date_fin)){
		$erreurs['message_erreur'] = _T('campagne:erreur_date_deux');
	}
	
	return $erreurs;
}

function formulaires_purger_statistiques_campagnes_traiter_dist(){
	$retours = array('editable' => true);
	
	$date_debut = _request('date_debut');
	$date_fin = _request('date_fin');
	$where = null;
	$message_ok = _T('campagne:purger_statistiques_message_ok');
	
	if ($date_debut and $date_fin) {
		$where = array(
			'date >= ' . sql_quote($date_debut),
			'date <= ' . sql_quote($date_fin),
		);
		include_spip('inc/filtres');
		$message_ok = _T('campagne:purger_statistiques_message_ok_date', array('debut_fin'=>affdate_debut_fin($date_debut, $date_fin, false)));
	}
	
	// Supprimer les vues
	sql_delete('spip_campagnes_vues', $where);
	
	// Supprimer les clics
	sql_delete('spip_campagnes_clics', $where);
	
	$retours['message_ok'] = $message_ok;
	
	return $retours;
}

<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_editer_commande_saisies($id_commande='new', $id_auteur, $retour=''){
	include_spip('inc/config');
	return array(
		array(
			'saisie' => 'auteurs',
			'options' => array(
				'nom' => 'id_auteur',
				'label' => _T('commandes:contact_label'),
				'class' => 'chosen',
				'defaut' => $id_auteur
			)
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date_envoi',
				'label' => _T('commandes:date_envoi_label'),
			)
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date_paiement',
				'label' => _T('commandes:date_paiement_label'),
			)
		),
	);
}

function formulaires_editer_commande_charger($id_commande='new', $retour=''){
	include_spip('inc/editer');
	$contexte = formulaires_editer_objet_charger('commande', $id_commande, '', '', 0, $retour);
	unset($contexte['id_commande']);
	return $contexte;
}

function formulaires_editer_commande_verifier($id_commande='new', $id_auteur, $retour=''){
	include_spip('inc/editer');
	return formulaires_editer_objet_verifier('commande', $id_commande);
}

function formulaires_editer_commande_traiter($id_commande='new', $id_auteur, $retour=''){
	include_spip('inc/editer');	
	
	//array des champs dates
	$type_dates=array('date','date_envoi','date_paiement');
	
	foreach ($type_dates as $type_date){
		$date = _request($type_date);	
		if($date){
			list($jour, $mois, $annee) = explode('/',$date);
			$date =$annee.'-'.$mois.'-'.$jour;
			set_request($type_date,$date);
			spip_log("commande_traiter pour id_commande=$id_commande $type_date = $date",'commandes');
			}		
	
	}
	
	$retours = formulaires_editer_objet_traiter('commande',$id_commande,'','',$retour,'',$champs);
	return $retours;
}


?>

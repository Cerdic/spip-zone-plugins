<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function abonnement_i2_cfg_form($flux) {
    $flux .= recuperer_fond('fonds/inscription2_abonnement');
	return $flux;
}

function abonnement_i2_form_debut($flux) {
	if (lire_config('abonnement/proposer_paiement')) {
		$contexte = array("abonnement" => $flux['args']['abonnement'],"hash" => $flux['args']['hash']);
		$flux['data'] .= recuperer_fond('formulaires/liste_abonnements',$contexte);
	}
	return $flux;
}

function abonnement_i2_charger_formulaire($flux) {
	if (lire_config('abonnement/proposer_paiement')) {
		// valeur par defaut
		$flux['data']['abonnement'] = '1' ;
		include_spip('inc/acces');
		$hash = creer_uniqid();	
		$flux['data']['hash'] = $hash ;
	}
	return $flux;
}

function abonnement_i2_verifier_formulaire($flux) {
	//if (lire_config('abonnement/proposer_paiement')) {
		// rien a faire, mais sait on jamais ! un jour peut etre !
	//}
	return $flux;
}

// inscrire l'abonnement dans la base, statut "a confirmer"
// et afficher un formulaire de paiement (uniquement si la config le permet)
	
function abonnement_i2_traiter_formulaire($flux) {	
	if (lire_config('abonnement/proposer_paiement')) {
		if($id_abonnement = intval(_request('abonnement'))){	
			$id_auteur = $flux['args']['id_auteur'] ;
			$hash = _request('hash');

			// (verififier si ca emplile pas en mode edition)
			sql_insertq('spip_auteurs_elargis_abonnements', array(
				'id_auteur' => $id_auteur,
				'id_abonnement' => $id_abonnement,
				'date' => date("Y-m-d H:i:s"),
				'hash'=>$hash,
				'statut_paiement' => 'a_confirmer')
			);
		}
		$flux['data']['ne_pas_confirmer_par_mail'] = true ;
		$flux['data']['message_ok'] = " " ;
	}
	
	return $flux;
}

function abonnement_i2_confirmation($flux) {
	// afficher un formulaire de paiement pour l'utilisateur (uniquement si la config le permet)
	if (lire_config('abonnement/proposer_paiement')) {
		$env = $flux['args'];
		$row = sql_fetsel(array('id_auteur'), 'spip_auteurs', 'email='.sql_quote($env['email']));		$env['id_auteur'] = $row['id_auteur'] ;
		$flux['data'] .= recuperer_fond('formulaires/abonnement_paiement',$env);
	}
	return $flux;
}

//utiliser le cron pour gerer les dates de validite des abonnements et envoyer les messages de relance
function abonnement_taches_generales_cron($taches_generales){
	$taches_generales['abonnement'] = 60*60*24 ;
	return $taches_generales;
}

?>

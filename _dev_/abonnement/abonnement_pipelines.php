<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function abonnement_I2_cfg_form($flux) {
    $flux .= recuperer_fond('fonds/inscription2_abonnement');
	return ($flux);
}

function abonnement_i2_form_debut($flux) {
   	$env = unserialize($flux['args']);
   	$contexte = array("abonnement" => $env['abonnement']);
    $flux['data'] .= recuperer_fond('formulaires/abonnement_liste',$contexte);
	return ($flux);
}

function abonnement_i2_charger_formulaire($flux) {
	$flux['data']['abonnement'] = '1' ;
	return ($flux);
}

function abonnement_i2_confirmation($flux) {
	$env = unserialize($flux['args']);
    $flux['data'] .= recuperer_fond('formulaires/abonnement_paiement',$env);
	return ($flux);
}

function abonnement_i2_verifier_formulaire($flux) {
	return ($flux);
}

function abonnement_i2_traiter_formulaire($flux) {

	if(intval(_request('abonnement'))){
	$value = _request('abonnement') ;	
	$n = $flux['args']['id_auteur'] ;
	spip_log("$value,$n","logabo");
	sql_insertq('spip_auteurs_elargis_abonnements', array('id_auteur' => $n,'id_abonnement' => $value, 'date' => date("Y-m-d H:i:s") ));
	}
	$flux['data']['ne_pas_confirmer_par_mail'] = true ;
	$flux['data']['message_ok'] = " " ;

	/*
	if(isset($declaration['article']) AND ($declaration['article'] > 0 OR ereg('breve',$declaration['article']) )){
		$value = $declaration['article'] ;	
		//var_dump($value);
		include_spip('inc/acces');
		$montant =  lire_config('abonnement/prix_article');
		$hash = creer_uniqid();
			spip_query("INSERT INTO `spip_auteurs_elargis_articles` (`id_auteur_elargi`, `id_article`, `statut_paiement` , `hash`,`montant`) VALUES ('$n', '$value', 'a_confirmer','$hash','$montant')");
		$declaration['hash_article'] = $hash ;

	}
	*/
	
	return ($flux);
}


//utiliser le cron pour gerer les dates de validite des abonnements et envoyer les messages de relance
function abonnement_taches_generales_cron($taches_generales){
	$taches_generales['abonnement'] = 60*60*24 ;
	return $taches_generales;
}

?>

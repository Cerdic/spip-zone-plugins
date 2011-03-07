<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_priveperso_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
/*	// droits
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'priveperso')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
*/	
	@list($arg, $rub_id) = explode ('/', $arg);
	

	// actions possibles
	if (!in_array($arg, array(
		'supp_rub','activer','desactiver'
		))){
			include_spip('inc/minipres');
			echo minipres(_T('priveperso:erreur_action',array("action"=>$arg)));
			exit;		
	}

	
	// Supprimer une personnalisation de rubrique
	if ($arg == 'supp_rub'){
		action_supprimer_rubrique_perso($rub_id);
	}

	// Activer une personnalisation de rubrique
	if ($arg == 'activer'){
		action_activer_rubrique_perso($rub_id);
	}

	// Désactiver une personnalisation de rubrique
	if ($arg == 'desactiver'){
		action_desactiver_rubrique_perso($rub_id);
	}

}


function action_supprimer_rubrique_perso($rub_id){

	sql_delete('spip_priveperso', 'rub_id = ' . intval($rub_id));
	sql_delete('spip_priveperso_texte', 'rub_id = ' . intval($rub_id));
	
	
	}

function action_activer_rubrique_perso($rub_id){

	sql_updateq('spip_priveperso', array('activer_perso'=>'oui'), 'rub_id = ' . intval($rub_id));
	
	}

function action_desactiver_rubrique_perso($rub_id){

	sql_updateq('spip_priveperso', array('activer_perso'=>'non'), 'rub_id = ' . intval($rub_id));
	
	
	}

?>
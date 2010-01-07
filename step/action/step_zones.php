<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_step_zones_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// droits
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	list($arg, $id) = explode('/', $arg);
	
	// actions possibles
	if (!in_array($arg, array(
		'supprimer_zone'))){
			include_spip('inc/minipres');
			echo minipres(_T('step_erreur_action',array("action"=>$arg)));
			exit;		
	}
	
	include_spip('inc/step');
	
	if ($arg == 'supprimer_zone'){
		step_supprimer_zone($id);
	}
	
	// retour
	include_spip('inc/headers');
	redirige_par_entete(urldecode(_request('redirect')));

}

?>

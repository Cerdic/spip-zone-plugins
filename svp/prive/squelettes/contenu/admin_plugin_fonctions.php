<?php

// on installe les plugins maintenant,
// cela permet aux scripts d'install de faire des affichages (moches...)
include_spip('inc/plugin'); // plugin_installes_meta();


function svp_presenter_actions_realisees() {
	// presenter les traitements realises... si tel est le cas...
	include_spip('inc/svp_actionner');
	$actionneur = new Actionneur();
	$actionneur->log = true;
	
	// s'il ne reste aucune action a faire ou si on force un nettoyage.
	if (_request('nettoyer_actions')) {
		$actionneur->nettoyer_actions();
	}
	
	$actionneur->get_actions();
	$pres = $actionneur->presenter_actions($fin = true);

	// s'il ne reste aucune action a faire ou si on force un nettoyage.
	if (!$actionneur->est_verrouille()) {
		$actionneur->nettoyer_actions();
	}
		
	return $pres;
	
}
?>

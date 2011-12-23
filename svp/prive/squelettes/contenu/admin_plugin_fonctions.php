<?php

// on installe les plugins maintenant,
// cela permet aux scripts d'install de faire des affichages (moches...)
include_spip('inc/plugin'); // plugin_installes_meta();


// on fait la verif du path avant tout,
// et l'installation des qu'on est dans la colonne principale
// si jamais la liste des plugins actifs change, il faut faire un refresh du hit
// pour etre sur que les bons fichiers seront charges lors de l'install
$new = actualise_plugins_actifs();
if ($new AND _request('actualise')<2) {
	include_spip('inc/headers');
	redirige_par_entete(parametre_url(self(),'actualise',_request('actualise')+1,'&'));
}


	

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

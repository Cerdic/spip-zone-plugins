<?php

// on installe les plugins maintenant,
// cela permet aux scripts d'install de faire des affichages (moches...)
include_spip('inc/plugin'); // plugin_installes_meta();


function svp_presenter_actions_realisees() {
	// presenter les traitements realises... si tel est le cas...
	include_spip('inc/svp_actionner');
	$actionneur = new Actionneur();
	$actionneur->log = true;
	$actionneur->get_actions();
	$pres = $actionneur->presenter_actions($fin = true);
	# /!\ peut poser problème avec 2 admins... cela peut ecraser les actions
	#     que le premier était tranquilement en train de faire
	#     si le second visite admin_plugins pendant ce temps...
	$actionneur->nettoyer_actions();
	return $pres;
	
}
?>

<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/extra_plus');
function action_spiplistes_changer_statut_abonne_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$redirect = urldecode(_request('redirect'));

	//changer de statut
	$statut = _request('statut');
	//if (autoriser())
	if(($statut=='html') OR ($statut=='texte') OR ($statut=='non')){
		$extras = get_extra($id_auteur,"auteur");
		$extras["abo"] = $statut;
		set_extra($id_auteur,$extras,"auteur");
	}
	
	if ($redirect){
		redirige_par_entete($redirect);
	}
}

?>
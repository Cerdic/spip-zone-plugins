<?php
/**
 * Plugin Grappes
 * Licence GPL (c) Matthieu Marcillaud
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('action/editer_liens');

function action_lier_objets_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($action,$source,$id_source,$cible,$id_cible) = explode('/',$arg);

	if ($action != 'lier' AND $action != 'delier') {
		include_spip('inc/minipres');
		minipres(_T('grappes:action_inconnue',array('action'=>$action)));
	}

	if (!autoriser('associer',$source,$id_source)){
		include_spip('inc/minipres');
		minipres(_T('grappes:autoriser_associer_non'));
	}

	if ($action == 'lier')
		lier_objets($source,$id_source,$cible,$id_cible);
	elseif ($action == 'delier')
		delier_objets($source,$id_source,$cible,$id_cible);

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_grappe/$id_source'");
}


function lier_objets($source,$id_source,$cible,$id_cible){
	// si la source n'est pas une grappe un inverse le sens de la liaison pour matcher l'autorisation grappe_associer
	if ($source != 'grappe')
		objet_associer(array($cible=>$id_cible),array($source=>$id_source));
	else
		objet_associer(array($source=>$id_source),array($cible=>$id_cible));
}

function delier_objets($source,$id_source,$cible,$id_cible){
	objet_dissocier(array($source=>$id_source),array($cible=>$id_cible));
}

?>

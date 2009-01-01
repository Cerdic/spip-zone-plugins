<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_forms_donnee_dist() {

	$securiser_action = charger_fonction('securiser_action','inc');
	$args = $securiser_action();
	
	list($id_donnee, $statut) = preg_split('/\W/', $args);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	include_spip('inc/autoriser');
	if ($id_donnee = intval($id_donnee)
	  AND autoriser('modifier',"donnee",$id_donnee)){
		sql_updateq("spip_forms_donnees",array("statut"=>$statut),"id_donnee=".intval($id_donnee));
		
		if ($rang_nouv = intval(_request('rang_nouv'))){
			include_spip("base/forms_base_api_v2");
			forms_ordonner_donnee($id_donnee,$rang_nouv);
		}
	}
}

?>
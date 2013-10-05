<?php
/**
 * Plugin Grappes
 * Licence GPL (c) Matthieu Marcillaud
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

/**
 * Action de duplication d'une grappe
 * 
 * Doit recevoir comme argument ($arg) l'identifiant numérique de la grappe à dupliquer
 */
function action_dupliquer_grappe_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_dupliquer_grappe_dist $arg pas compris",'test.'._LOG_ERREUR);
	} else action_dupliquer_grappe_post($r[1]);
}

/**
 * La duplication de la grappe
 * 
 * @param int $id_grappe
 * 	Identification numérique de la grappe à supprimer
 * @return int|bool 
 * 	Retourne l'identifiant numérique de la nouvelle grappe si tout s'est bien passé, 
 * 	sinon false, si la duplication n'a pu avoir lieu
 */
function action_dupliquer_grappe_post($id_grappe)
{
	include_spip('action/editer_grappe');
	$grappe = sql_fetsel('*','spip_grappes','id_grappe='.intval($id_grappe));
	if($grappe){
		$nouvelle_grappe = grappe_inserer();
		unset($grappe['id_grappe']);
		unset($grappe['id_admin']);
		unset($grappe['date']);
		unset($grappe['maj']);
		grappe_modifier($nouvelle_grappe, $grappe);
		
		include_spip('action/lier_objets');
		$objets_lies = sql_select('*','spip_grappes_liens','id_grappe='.intval($id_grappe));
		spip_log($nouvelle_grappe,'test.'._LOG_ERREUR);
		while($objet_lie = sql_fetch($objets_lies)){
			spip_log($objet_lie,'test.'._LOG_ERREUR);
			lier_objets('grappe',$nouvelle_grappe,$objet_lie['objet'],$objet_lie['id_objet']);
		}
		return $nouvelle_grappe;
	}
	return false;
}
?>
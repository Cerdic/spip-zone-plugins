<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

// https://code.spip.net/@exec_puce_statut_dist
function exec_puce_statut_formulaires_dist()
{
	exec_puce_statut_formulaires_args(_request('id'),  _request('type'));
}

// https://code.spip.net/@exec_puce_statut_args
function exec_puce_statut_formulaires_args($id, $type)
{
	if (in_array($type,array('formulaires','formulaires_reponse'))) {
		$table = table_objet_sql($type);
		$prim = id_table_objet($type);
		$id = intval($id);
		$r = sql_fetsel("id_formulaire,statut", "$table", "$prim=$id");
		$statut = $r['statut'];
		$id_formulaire = $r['id_formulaire'];
	} else {
		$id_formulaire = intval($id);
		$statut = 'prop'; // arbitraire
	}
	$puce_statut = charger_fonction('puce_statut', 'inc');
	ajax_retour($puce_statut($id,$statut,$id_formulaire,$type, true));
}
?>

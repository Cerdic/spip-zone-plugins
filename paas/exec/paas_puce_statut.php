<?php

/**
*
 * Plugin « Puce active pour les articles syndiqués»
 * Licence GNU/GPL
 * 
  */
// Copié sur le fichier exec/puce_statut_dist
  
  
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


// Identique a la fonction exec_puce_statut_dist de exec/puce_statut.php
function exec_paas_puce_statut()	{

		exec_paas_puce_statut_args(_request('id'),  _request('type'));
		
}


// Copié en trés grande partie sur le début de la fonction exec_puce_statut_args de exec/puce_statut.php,
// Cela permet d'afficher les 1er popup de puces  ( Bof, l'explication)
function exec_paas_puce_statut_args($id, $type)	{
	if (in_array($type,array('syndic_article'))) {
		$table = table_objet_sql($type);
		$prim = id_table_objet($type);
		$id = intval($id);
		$r = sql_fetsel("id_syndic,statut", "$table", "$prim=$id");
		$statut = $r['statut'];
		$id_syndic = $r['id_syndic'];
		$r2 = sql_fetsel("id_rubrique", "spip_syndic", "id_syndic=$id_syndic");
		$id_rubrique = $r['id_rubrique'];
	} else {
		$id_rubrique = intval($id);
		$statut = 'prop'; // arbitraire
	}
	$puce_statut = charger_fonction('puce_statut', 'inc');
	ajax_retour($puce_statut($id,$statut,$id_rubrique,$type, true));
}
?>

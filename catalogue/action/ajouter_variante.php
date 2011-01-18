<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_variante_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_article = intval($arg);

	// echo'<script type="text/javascript">alert("Ajout variante article " + '.$id_article.')</script>';
	
	sql_insertq('spip_cat_variantes', array(
		'id_article' => $id_article,
		'date' => date("Y-m-d H:i:s"),
		'statut' => 'prepa'
		)
	);

	// retour
	include_spip('inc/headers');
	redirige_par_entete(urldecode(_request('redirect')));

}


?>

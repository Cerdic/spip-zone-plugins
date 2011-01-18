<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_variante_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_cat_variante = intval($arg);

	// echo'<script type="text/javascript">alert("Suppression variante " + '.$id_variante.')</script>';
	
	// suppression
	sql_delete('spip_cat_variantes', 'id_cat_variante='.$id_cat_variante);

	// retour
	include_spip('inc/headers');
	redirige_par_entete(_request('redirect'));

}


?>

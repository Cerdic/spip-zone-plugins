<?php

function filtre_puce_statut_dist($id_objet, $statut, $id_rubrique, $type){
	$puce_statut = charger_fonction('puce_statut', 'inc');
	return $puce_statut($id_objet, $statut, $id_rubrique, $type);
}

?>

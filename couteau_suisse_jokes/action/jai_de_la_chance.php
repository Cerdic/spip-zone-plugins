<?php

function action_jai_de_la_chance_dist() {
	$objets = array('article','rubrique');
	$i = rand(0, count($objets)-1);
	$objet = $objets[$i];

	$table = table_objet_sql($objet);
	$id = id_table_objet($objet);
	$res = sql_fetsel(array($id, 'rand() as rand'), $table, 'statut=' . sql_quote('publie'), '', 'rand');
	$url = generer_url_entite($res[$id], $objet);

	set_request('redirect', $url);
}
?>

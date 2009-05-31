<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_typer_dist()
{
	exec_typer_args(intval(_request('id')), _request('objet'));
}

function exec_typer_args($id, $objet)
{
	if (!$id OR !autoriser('voir',$objet,$id)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$table = table_objet_sql($objet);
		if (!id_table_objet($table)) {
			spip_log("typer: $objet table inconnue");
			$objet = 'article';
			$table = $objet . 's';
		}
		$prim = 'id_' . $objet;
		$row = sql_fetsel("*", $table, "$prim=$id"); //peut-etre limiter le select a type et id_parent si rubrique
		$type = $row[_TYPE];
		$racine = $row["id_parent"] == 0;
		$script = ($objet == 'rubrique') ? 'naviguer' : 'articles';
		$typer = charger_fonction('typer', 'inc');
		ajax_retour($typer($id, 'ajax', $type, $objet, $script, $racine));
	}
}
?>

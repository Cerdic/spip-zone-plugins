<?php 

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_tradlang_forum_extraire_titre($id_objet){
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table(table_objet_sql('tradlang'));
	$_titre = $desc['titre'] ? $desc['titre'] : ($desc['field']['titre'] ? 'titre' : '');
	$_table = $desc['table'];
	$_primary = id_table_objet($_table);
	if ($_titre and $res = sql_fetsel($_titre, $_table, array(
		"$_primary = ". sql_quote($id_objet))
	)) {
		$titre = $res['titre'];
	}
	return $titre;
}

?>
<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_groupe_supprimer_dist() {
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'nom')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}


	supprimer_groupe_func(_request('id_groupe'));
}

function supprimer_groupe_func($id) {
	include_spip('base/abstract_sql');
	sql_delete('spip_groupes', 'id_groupe='.$id);
	sql_delete('spip_groupes_auteurs', 'id_groupe='.$id);
	echo 'ok';
}
?>
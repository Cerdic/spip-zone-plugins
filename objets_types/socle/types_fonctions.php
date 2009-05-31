<?php

include_spip('base/types_serial');

function types_pre_boucle($boucle) {
	// Restreindre au type par defaut
	if(in_array($boucle->type_requete, array('articles', 'rubriques'))) {
		$id_table = $boucle->id_table;
		$mtype = $id_table . '.' . _TYPE;
		$ntype = preg_replace('/s$/', '', $boucle->type_requete);
		if (!isset($boucle->modificateur['criteres'][_TYPE])) {
			array_unshift($boucle->where,array("'='", "'$mtype'", "'\\'$ntype\\''"));
		}
	}

	return $boucle;
}

?>
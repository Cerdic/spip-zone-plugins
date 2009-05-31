<?php

function valeur_champ_tags($table, $id, $champ) {
	
	$r = spip_query('SELECT ALL titre FROM spip_mots AS m RIGHT JOIN spip_mots_'.$table.'s AS j ON m.id_mot=j.id_mot WHERE j.id_'.$table.'='.$id);
	$liste = array();
	while($a = spip_fetch_array($r)){
		array_push($liste,$a['titre']);
	}
	$liste = join(', ', $liste);
	return $liste;
	
}

?>

<?php

// {calendrier}
// {calendrier date_autre_boucle}
function critere_calendrier_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$champ_date = "'".$boucle->id_table.".".$GLOBALS['table_date'][$boucle->type_requete]."'";
	
	// definition du nom de la variable date_XXX
	$nom_date = !isset($crit->param[0]) ? "'date".$idb."'" : calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);

	$boucle->modificateur['plage'] = 'interdire_scripts(_request('.$nom_date.'))';
	$boucle->modificateur['fragment'] = 'fragment_'.$boucle->descr['nom'].$idb;
	$boucle->where[] = array(
		'REGEXP',
		$champ_date, 
		'_q("^".'.$boucle->modificateur['plage'].')'
	);
}

?>
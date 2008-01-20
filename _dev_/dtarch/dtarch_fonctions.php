<?php

function compare_date_expose($date, $date_compare, $precision = 'mois') {
	static $precisions = array(
		'annee'		=> 'Y',
		'mois'		=> 'Y-m',
		'jour'		=> 'Y-m-d',
		'heure'		=> 'Y-m-d H',
		'minutes'	=> 'Y-m-d H:i',
		'secondes'	=> 'Y-m-d H:i:s'
	);
	if(!in_array($precision, $precisions)) $precision = 'mois';
	$fmt = $precisions[$precision];
	$date_compare = date($fmt, strtotime($date_compare));
	$date = date($fmt, strtotime(normaliser_date($date)));
	return ($date == $date_compare);
}

function calculer_balise_date_expose($p, $on, $off) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$key = $p->boucles[$b]->primary; 
	$type = $p->boucles[$b]->type_requete;
	$date = $GLOBALS['table_date'][$type];

	if (!$key) {
		erreur_squelette(_T('zbug_champ_hors_boucle', array('champ' => '#DATE_EXPOSER')), $b);
	}
	
	if (!$date) {
		erreur_squelette(_T('dtarch:zbug_balise_interdite'), $b);
	}

	$da  = index_pile($p->id_boucle, $date, $p->boucles, $b);
	$p->code = "(compare_date_expose($da, \$Pile[0]['date']) ? $on : $off)";

	$p->interdire_scripts = false;
	return $p;
}

function balise_DATE_EXPOSE_dist($p) {
	$on = "'on'";
	$off= "''";

	if (($v = interprete_argument_balise(1,$p))!==NULL){
		$on = $v;
		if (($v = interprete_argument_balise(2,$p))!==NULL)
			$off = $v;
	
		// autres filtres
		array_shift($p->param);
	}
	return calculer_balise_date_expose($p, $on, $off);
}

function critere_fusion_date_mois($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$type = $boucle->type_requete;
	$date = $GLOBALS['table_date'][$type];
	$champ_date = $boucle->id_table.'.'.$date;

	$boucles[$idb]->group[]  = 'DATE_FORMAT('.$champ_date.', \'%Y-%m\')'; 
	$boucles[$idb]->select[] = 'DATE_FORMAT('.$champ_date.', \'%Y-%m\') AS date';
}

?>
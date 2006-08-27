<?php


	
	
function http_calendrier_mini_agenda ($annee, $mois, $jour_ved, $mois_ved, $annee_ved, $semaine = false,  $script='', $ancre='', $evt=''){
	include_spip('inc/agenda');
	return http_calendrier_agenda ($annee, $mois, $jour_ved, $mois_ved, $annee_ved, $semaine, $script, $ancre, $evt);
}

function balise_OBJ($p) {
	if ($p->param && !$p->param[0][0]) {
		$php = array_shift( $p->param );
		array_shift($php);
		$obj = calculer_liste($php[0],
					$p->descr,
					$p->boucles,
					$p->id_boucle);
	}
	
	if($obj) {
		$p->code = $obj;
		if(count($php)>1) $p->code.='->'.$php[1][0]->texte;
	}
	
	return $p;
}

?>

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

//
// #URL_ACTION{logout} -> ecrire/?action=naviguer
//
function balise_URL_ACTION($p) {

	if ($p->param && !$p->param[0][0]) {
		$p->code =  calculer_liste($p->param[0][1],
					$p->descr,
					$p->boucles,
					$p->id_boucle);

		$args =  calculer_liste($p->param[0][2],
					$p->descr,
					$p->boucles,
					$p->id_boucle);

		if ($args != "''")
			$p->code .= ','.$args;

		// autres filtres (???)
		array_shift($p->param);
	}

	$p->code = 'generer_url_action(' . $p->code .')';

	#$p->interdire_scripts = true;
	return $p;
}


?>

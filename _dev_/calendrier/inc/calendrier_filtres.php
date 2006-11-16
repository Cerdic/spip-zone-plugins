<?php

// surcharge de agenda_affiche pour le #CALENDRIER 
// http://doc.spip.org/@agenda_calendrier
function agenda_calendrier($date) {
	$jour = affdate($date, 'jour');
	set_request('jour', $jour?$jour:1);
	set_request('mois', affdate($date, 'mois'));
	set_request('annee', affdate($date, 'annee'));
	return agenda_affiche(1, '', 'mois_unique');
}

//
// fonction standard de calcul de la balise #CALENDRIER
// on peut la surcharger en definissant dans mes_fonctions :
// function calendrier($plage, $nom, $bloc_cal, $modele) {...}
//

// http://doc.spip.org/@calcul_calendrier
function calcul_calendrier($plage, $nom, $bloc_cal = true, $modele = 'articles_mois'){
	static $ancres = array();
	$bloc_ancre = "";

	if (function_exists("calendrier"))
		return calendrier($plage, $nom, $bloc_cal, $modele);

	$date = 'date'.$nom;
	$ancre = 'calendrier'.$nom;

	// n'afficher l'ancre qu'une fois
	if (!isset($ancres[$ancre]))
		$bloc_ancre = $ancres[$ancre] = "<a name='$ancre' id='$ancre'></a>";

	$calendrier = array(
		'var_date' => $date,
		'date' => $plage ? $plage : date('Y-m'),
		'ancre' => $ancre,
		'bloc_ancre' => $bloc_ancre,
		'self' => parametre_url(self(),'fragment','')
	);

	// liste = false : on ne veut que l'ancre
	if (!$bloc_cal)
		return $bloc_ancre;

	return recuperer_fond("modeles/calendrier_$modele",$calendrier);
}

// http://doc.spip.org/@thead_calendrier
function thead_calendrier($lang, $forme = 'abbr'){
	$ret = '';
	$debut = 2;
	if($lang == 'en') $debut = 1;
	$forme = $forme ? '_'.$forme : '';
	for($i=0;$i<7;$i++) {
		$ret .= '
				<th class="jour'.$debut.'" scope="col"><abbr title="'._T('date_jour_'.$debut).'">' .
		_T('date_jour_'.$debut.$forme) . '</abbr></th>';
		$debut = $debut == 7 ? 1 : $debut+1;
	}
	return '
		<thead>
			<tr>' .$ret. '
			</tr>
		</thead>'."\n";
}

?>

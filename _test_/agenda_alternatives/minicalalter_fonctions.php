<?php
/*
	Mini Calendrier pour Alternatives
 	Patrice VANNEUFVILLE - patrice.vanneufville<@>laposte.net
		(idee de James)
	(c) 2007 - Distribue sous licence GPL
	Plugin pour spip 1.9
	Licence GNU/GPL
*/

function url_evenements($D, $M, $Y) {
 $Time = mktime(0, 0, 0, $M, $D, $Y);
 return parametre_url(generer_url_public('evenements'),VAR_DATE_CAL,
 	sprintf("%04d-%02d-%02d",intval(date('Y', $Time)),intval(date('m', $Time)),intval(date('d', $Time)))
 );
}			  

// A supprimer bientot !! (En principe, intégrée dans Agenda...)
//
// décale les mois de la date.
// cette fonction peut raboter le jour si le nouveau mois ne les contient pas
// exemple 31/01/2007 + 1 mois => 28/02/2007
function Agenda_moisdecal2($date,$decalage,$format){
	include_spip('inc/filtres');
	$date_array = recup_date($date);
	if ($date_array) list($annee, $mois, $jour) = $date_array;
	if (!$jour) $jour=1;
	if (!$mois) $mois=1;
	$mois2 = $mois + $decalage;
	$date2 = mktime(1, 1, 1, $mois2, $jour, $annee);
	// mois normalement attendu
	$mois3 = date('m', mktime(1, 1, 1, $mois2, 1, $annee));
	// et si le mois de la nouvelle date a moins de jours...
	$mois2 = date('m', $date2);
	if ($mois2 - $mois3) $date2 = mktime(1, 1, 1, $mois2, 0, $annee);
	return date($format, $date2);
}

function verif_DATE_REQUEST($date) {
	preg_match(',^([0-9]{4})(-[0-9]{2})?(-[0-9]{2})?,', $date, $matches);
	$Y = sprintf('%04d', $y=intval($matches[1]));
	$M = sprintf('%02d', $m=-intval($matches[2]));
	$D = sprintf('%02d', $j=-intval($matches[3]));
	
	if(!$y) $Y = sprintf('%04d', date('Y', time()));
	if(!$m) $M = sprintf('%02d', $Y==date('Y', time())?date('m', time()):1);
	if(!$j) $D = sprintf('%02d', $M.$Y==date('mY', time())?date('d', time()):1);
	
	return "$Y-$M-$D";
}

function balise_DATE_REQUEST($p) {
	$p->code = "_request('".VAR_DATE_CAL."')";
	#$p->interdire_scripts = true;
	return $p;
}
function balise_DATE_REQUEST_SURE($p) {
	$p->code = "verif_DATE_REQUEST(_request('".VAR_DATE_CAL."'))";
	#$p->interdire_scripts = true;
	return $p;
}


function critere_archives($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
 $champ_date = "'" . $boucle->id_table ."." .
  $GLOBALS['table_date'][$boucle->type_requete] . "'";
 $boucle->where[] = array(
  'REGEXP',
  $champ_date, 
  "spip_abstract_quote(('^' . interdire_scripts(entites_html(\$Pile[0]['".VAR_DATE_CAL."']))))"
 );
 
}

function thead($lang){
	$ret = '';
	$debut = 2;
	if($lang == 'en') $debut = 1;
	for($i=0;$i<7;$i++) {
		$ret .= "\n\t\t\t\t".'<th scope="col"><abbr title="'._T('date_jour_'.$debut).'">' .
		_T("date_jour_{$debut}_initiale") . '</abbr></th>';
		$debut = $debut == 7 ? 1 : $debut+1;
	}
	return "\n\t\t".'<thead>
			<tr>' .$ret. '
			</tr>
		</thead>'."\n";
}

function alter_agenda_mini($i) {
  $args = func_get_args();
  $une_date = array_shift($args); // une date comme balise
  $sinon = array_shift($args);
  if (!$une_date) return $sinon;
  $type = 'mini';
  $agenda = agenda_memo(0);
  $evt = array();
  foreach (($args ? $args : array_keys($agenda)) as $k) {  
      if (is_array($agenda[$k]))
		foreach($agenda[$k] as $d => $v) { 
		  $evt[$d] = $evt[$d] ? (array_merge($evt[$d], $v)) : $v;
		}
    }
	$la_date = mktime(0, 0, 0, mois($une_date), 1, annee($une_date));
    //include_spip('inc/agenda');
    //return http_calendrier_init($la_date, $type, '', '', '', array('', $evt));
}

function http_MINICAL_ALTER($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt) {
	list($sansduree, $evenements, $premier_jour, $dernier_jour) = $evt;

	if ($sansduree)
		foreach($sansduree as $d => $r) {
			$evenements[$d] = !$evenements[$d] ? $r : 
				 array_merge($evenements[$d], $r);
			 }

	if (!$premier_jour) $premier_jour = '01';
	if (!$dernier_jour) {
		$dernier_jour = 31;
		while (!(checkdate($mois,$dernier_jour,$annee))) $dernier_jour--;
	}

	// affichage du debut de semaine hors periode
	$lang = _request('lang')?_request('lang'):$GLOBALS['spip_lang'];
	$ligne = '';
	$debut = date("w",mktime(1,1,1,$mois,$premier_jour,$annee));
	$jour_semaine_lang=1; 
	if($lang=='en') {
		$debut=$debut+1;
		if($debut==7) $debut=0;
		$jour_semaine_lang=0;
	} 
	for ($i=$debut ? $debut : 7;$i>1;$i--) {
		$ligne .= "\n\t<td>&nbsp;</td>";
	}

	$total = '';
	for ($j=$premier_jour; $j<=$dernier_jour; $j++) {
		$nom = mktime(1,1,1,$mois,$j,$annee);
		$jour = date("d",$nom);
		$jour_semaine = date("w",$nom);
		$mois_en_cours = date("m",$nom);
		$annee_en_cours = date("Y",$nom);
		$amj = date("Y",$nom) . $mois_en_cours . $jour;

		if ($jour_semaine==$jour_semaine_lang AND $ligne != '') { 
			$total .= "\n<tr>$ligne\n</tr>";
			$ligne = '';
		}

		$evts = $evenements[$amj];
		if ($evts) {
			$evts = "<a href=\"".$evts[0]['URL']."\">".$evts[0]['SUMMARY']."</a>";
		}
		else {
			$evts = intval($jour);
		}
		$ligne .= "\n\t<td".($amj == date("Ymd")?' class="today"':'').">" . $evts . "\n\t</td>";
	}
	// affichage de la fin de semaine hors periode
	for($j=$jour_semaine ? $jour_semaine+(1-$jour_semaine_lang) : 7; $j<7; $j++) {
		$ligne .= "\n\t<td>&nbsp;</td>";
	}

	return $total . ($ligne ? "\n<tr>$ligne\n</tr>" : '');
}


?>
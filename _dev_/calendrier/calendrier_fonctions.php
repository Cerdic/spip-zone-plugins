<?php

/* Balise #CALENDRIER
   Auteur James (c) 2006
   Plugin pour spip 1.9.2
   Licence GNU/GPL
*/

function critere_calendrier_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$champ_date = "'" . $boucle->id_table ."." .
		$GLOBALS['table_date'][$boucle->type_requete] . "'";
	$boucle->where[] = array(
		'REGEXP',
		$champ_date, 
		"_q(('^' . interdire_scripts(" .
		'_request("date'.$idb.'")'
		. ")))"
	);
}

function balise_CALENDRIER_dist($p, $bloc_cal='true') {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	$boucle = $p->boucles[$b];
	$_type = $boucle->type_requete;

	// s'il n'y a pas de nom de boucle, on ne peut pas paginer
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#CALENDRIER')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}

	$_modele = interprete_argument_balise(1,$p);
	if(!$_modele) $_modele = "'$_type'";
	$_periode = interprete_argument_balise(2,$p);
	if(!$_periode) $_periode = "'mois'";

	$p->code = "calcul_calendrier('$b',
	$bloc_cal,
	$_modele,
	$_periode)";

	return $p;
}

// N'afficher que l'ancre du calendrier (au-dessus, par exemple, alors
// qu'on mettra le calendrier en-dessous de la liste paginee)
function balise_ANCRE_CALENDRIER_dist($p) {
	$p = balise_CALENDRIER_dist($p, $bloc_cal='false');
	return $p;
}

function calcul_calendrier($nom, $bloc_cal = true, $modele = 'articles', $periode = 'mois'){
	static $ancres = array();
	$bloc_ancre = "";

	if (function_exists("calendrier"))
		return calendrier($nom, $bloc_cal, $modele, $periode);

	$date = 'date'.$nom;
	$ancre = 'calendrier'.$nom;

	// n'afficher l'ancre qu'une fois
	if (!isset($ancres[$ancre]))
		$bloc_ancre = $ancres[$ancre] = "<a name='$ancre' id='$ancre'></a>";

	$calendrier = array(
		'var_date' => $date,
		'date' => _request($date)?
			interdire_scripts(_request($date)):
			date('Y-m'),
		'ancre' => $ancre,
		'bloc_ancre' => $bloc_ancre,
		'self' => parametre_url(self(),'fragment','')
	);

	// liste = false : on ne veut que l'ancre
	if (!$bloc_cal)
		return $bloc_ancre;

	return recuperer_fond("modeles/calendrier_${modele}_$periode",$calendrier);
}

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

function agenda_calendrier($date) {
	set_request('jour', affdate($date, 'jour'));
	set_request('mois', affdate($date, 'mois'));
	set_request('annee', affdate($date, 'annee'));
	return agenda_affiche(1, '', 'mois_unique');
}

function http_calendrier_mois_unique($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt) {
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
	$nom = mktime(1,1,1,$mois,$premier_jour,$annee);
	$debut = date("w", $nom);
	$W = date("W",$nom);
	$jour_semaine_lang=1; 
	$_w = 2;
	if($lang=='en') {
		$_w = 1;
		$debut=$debut+1;
		if($debut==7) $debut=0;
		$jour_semaine_lang=0;
	} 
	for ($i=$debut ? $debut : 7;$i>1;$i--) {
		$ligne .= "\n\t<td class=\"horsperiode semaine$W jour".$_w++."\">&nbsp;</td>";
	}

	$total = '';
	for ($j=$premier_jour; $j<=$dernier_jour; $j++) {
		$nom = mktime(1,1,1,$mois,$j,$annee);
		$jour = date("d",$nom);
		$jour_semaine = date("w",$nom);
		$W = date("W",$nom);
		$exception = ($jour_semaine==0 AND $jour_semaine_lang==0) ? 1 : 0;
		$mois_en_cours = date("m",$nom);
		$annee_en_cours = date("Y",$nom);
		$amj = date("Y",$nom) . $mois_en_cours . $jour;

		if ($jour_semaine==$jour_semaine_lang AND $ligne != '') { 
			$total .= "\n<tr>$ligne\n</tr>";
			$ligne = '';
		}

		$evts = $evenements[$amj];
		if ($evts) {
			$title = $evts[0]['DESCRIPTION'] ?
			" title=\"".$evts[0]['DESCRIPTION']."\"":
			'';
			$evts = "<a$title href=\"".$evts[0]['URL']."\">".$evts[0]['SUMMARY']."</a>";
		}
		else {
			$evts = intval($jour);
		}
		$class = "semaine".($W+$exception)." jour".(1+$jour_semaine);
		$class .= $amj == date("Ymd") ? ' today' : '';
		$ligne .= "\n\t<td".($class ?" class=\"$class\"":'').">" . $evts . "\n\t</td>";
	}
	// affichage de la fin de semaine hors periode
	$_w = $jour_semaine+1;
	for($j=$jour_semaine ? $jour_semaine+(1-$jour_semaine_lang) : 7; $j<7; $j++) {
		$_w = ($_w == 7) ? 1 : $_w+=1;
		$ligne .= "\n\t<td class=\"horsperiode semaine$W jour$_w\">&nbsp;</td>";
	}

	return $total . ($ligne ? "\n<tr>$ligne\n</tr>" : '');
}

?>
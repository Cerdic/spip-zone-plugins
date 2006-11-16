<?php

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
	for($j=$jour_semaine ? $jour_semaine+(1-$jour_semaine_lang) : 7; $j<7; $j++) {
		$nom = mktime(1,1,1,$mois,$j,$annee);
		$_jour_semaine = date("w",$nom);
		$ligne .= "\n\t<td class=\"horsperiode semaine$W jour$_jour_semaine\">&nbsp;</td>";
	}

	return $total . ($ligne ? "\n<tr>$ligne\n</tr>" : '');
}

?>
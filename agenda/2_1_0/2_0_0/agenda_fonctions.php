<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

include_spip('inc/agenda_filtres');


function agenda_mini($i) {
  $args = func_get_args();
  $une_date = array_shift($args); // une date comme balise
  $sinon = array_shift($args);
  if (!$une_date) return $sinon;
  $type = 'mini';
  $agenda = Agenda_memo_full(0);
  $evt = array();
  foreach (($args ? $args : array_keys($agenda)) as $k) {  
      if (is_array($agenda[$k]))
		foreach($agenda[$k] as $d => $v) { 
		  $evt[$d] = $evt[$d] ? (array_merge($evt[$d], $v)) : $v;
		}
    }
	$la_date = mktime(0, 0, 0, mois($une_date), 1, annee($une_date));
    include_spip('inc/agenda');
    return http_calendrier_init($la_date, $type, '', '', '', array('', $evt));
}

function http_calendrier_mini($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt) {
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
	$ligne = '';
	$debut = date("w",mktime(1,1,1,$mois,$premier_jour,$annee));
	for ($i=$debut ? $debut : 7;$i>1;$i--) {
		$mois_precedent = mktime(1,1,1,$mois-1,1,$annee);
		$jour_mois_precedent = date('t', $mois_precedent)+2-$i;
		$ligne .= "\n\t<td class=\"horsperiode\">$jour_mois_precedent</td>";
	}

	$total = '';
	for ($j=$premier_jour; $j<=$dernier_jour; $j++) {
		$nom = mktime(1,1,1,$mois,$j,$annee);
		$jour = date("d",$nom);
		$jour_semaine = date("w",$nom);
		$mois_en_cours = date("m",$nom);
		$annee_en_cours = date("Y",$nom);
		$amj = date("Y",$nom) . $mois_en_cours . $jour;

		if ($jour_semaine==1 AND $ligne != '') { 
			$total .= "\n<tr>$ligne\n</tr>";
			$ligne = '';
		}

		$evts = $evenements[$amj];
		$class="";
		if ($evts) {
			$nb_elmts= @count($evts);
			if ($nb_elmts>1){
				$evts = "<a href='".$evts[0]['URL']."' title='".$nb_elmts." ".utf8_encode(_T('agenda:evenements'))."'>".intval($jour)."</a>";
			}
			else{
				$evts = "<a href='".$evts[0]['URL']."' title='".$evts[0]['SUMMARY'].
			"'>".intval($jour)."</a>";
			}
			$class='occupe';
			
		}
		else {
			$evts = intval($jour);
			$class='libre';
		}
		$ligne .= "\n\t<td  class='$class".($amj == date("Ymd")?' today':'')."'>" . $evts . "\n\t</td>";
	}
	$jour_mois_suivant=1;
	// affichage de la fin de semaine hors periode
	for($j=$jour_semaine ? $jour_semaine : 7; $j<7; $j++) {
		$ligne .= "\n\t<td class=\"horsperiode\">".$jour_mois_suivant++."</td>";			
	}

	return $total . ($ligne ? "\n<tr>$ligne\n</tr>" : '');
}


/**
 * #NB_INSCRITS
 * pour afficher le nombre d'inscrits (qui ont repondu oui) a un evenement
 * 
 * @param <type> $p
 * @return <type>
 */
function balise_NB_INSCRITS_dist($p) {
        $id_evenement = champ_sql('id_evenement', $p);
        $p->code = "sql_countsel('spip_evenements_participants','id_evenement='.intval($id_evenement).' AND reponse=\'oui\'')";
        return $p;
}


?>
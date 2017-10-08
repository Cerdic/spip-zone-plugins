<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pas besoin de contexte de compilation
global $balise_CALENDRIER_MINI_collecte;
$balise_CALENDRIER_MINI_collecte = array('id_rubrique','id_article','id_mot');

function balise_CALENDRIER_MINI ($p) {
	return calculer_balise_dynamique($p,'CALENDRIER_MINI', array('id_rubrique','id_article', 'id_mot'));
}

function balise_CALENDRIER_MINI_stat($args, $filtres) {
	return $args;
}
 
function balise_CALENDRIER_MINI_dyn($id_rubrique=0, $id_article = 0, $id_mot = 0,$date, $var_date = 'date', $url = '') {
	if(!$url)
		$url = self();
	// nettoyer l'url qui est passee par htmlentities pour raison de securités
	$url = str_replace("&amp;","&",$url);

	return array('formulaires/calendrier_mini', 0, 
		array(
			'date' => $date,
			'id_rubrique' => $id_rubrique,
			'id_article' => $id_article,
			'id_mot' => $id_mot,
			'var_date' => $var_date,
			'self' => $url,
		));
}

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

		if ($jour_semaine==1) { 
			$total .= "\n<tr>$ligne\n</tr>";
			$ligne = '';
		}

		$evts = $evenements[$amj];
		$class="";
		if ($evts) {
			$evts = "<a href='".parametre_url($evts[0]['URL'],'date',"$annee_en_cours-$mois_en_cours-$jour")."' title='".$evts[0]['SUMMARY'].
			"'>".intval($jour)."</a>";
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

?>

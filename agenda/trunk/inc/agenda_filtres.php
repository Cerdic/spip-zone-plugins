<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

/**
 * Filtres&criteres deprecies/obsoletes
 */


/**
 * {agendafull ..} variante etendue du crietre agenda du core
 * qui accepte une date de debut et une date de fin
 *
 * {agendafull date_debut, date_fin, jour, #ENV{annee}, #ENV{mois}, #ENV{jour}}
 * {agendafull date_debut, date_fin, semaine, #ENV{annee}, #ENV{mois}, #ENV{jour}}
 * {agendafull date_debut, date_fin, mois, #ENV{annee}, #ENV{mois}}
 * {agendafull date_debut, date_fin, periode, #ENV{annee}, #ENV{mois}, #ENV{jour},
 *                                            #ENV{annee_fin}, #ENV{mois_fin}, #ENV{jour_fin}}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_agendafull_dist($idb, &$boucles, $crit)
{
	$params = $crit->param;

	if (count($params) < 1)
	      erreur_squelette(_T('zbug_info_erreur_squelette'),
			       "{agenda ?} BOUCLE$idb");

	$parent = $boucles[$idb]->id_parent;

	// les valeurs $date et $type doivent etre connus a la compilation
	// autrement dit ne pas etre des champs

	$date_deb = array_shift($params);
	$date_deb = $date_deb[0]->texte;

	$date_fin = array_shift($params);
	$date_fin = $date_fin[0]->texte;

	$type = array_shift($params);
	$type = $type[0]->texte;

	$annee = $params ? array_shift($params) : "";
	$annee = "\n" . 'sprintf("%04d", ($x = ' .
		calculer_liste($annee, array(), $boucles, $parent) .
		') ? $x : date("Y"))';

	$mois =  $params ? array_shift($params) : "";
	$mois = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($mois, array(), $boucles, $parent) .
		') ? $x : date("m"))';

	$jour =  $params ? array_shift($params) : "";
	$jour = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($jour, array(), $boucles, $parent) .
		') ? $x : date("d"))';

	$annee2 = $params ? array_shift($params) : "";
	$annee2 = "\n" . 'sprintf("%04d", ($x = ' .
		calculer_liste($annee2, array(), $boucles, $parent) .
		') ? $x : date("Y"))';

	$mois2 =  $params ? array_shift($params) : "";
	$mois2 = "\n" . 'sprintf("%02d", ($x = ' .
		calculer_liste($mois2, array(), $boucles, $parent) .
		') ? $x : date("m"))';

	$jour2 =  $params ? array_shift($params) : "";
	$jour2 = "\n" .  'sprintf("%02d", ($x = ' .
		calculer_liste($jour2, array(), $boucles, $parent) .
		') ? $x : date("d"))';

	$boucle = &$boucles[$idb];
	$date = $boucle->id_table . ".$date";

	if ($type == 'jour')
		$boucle->where[]= array("'AND'",
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m%d\')'",("$annee . $mois . $jour")),
					array("'>='", "'DATE_FORMAT($date_fin, \'%Y%m%d\')'",("$annee . $mois . $jour")));
	elseif ($type == 'mois')
		$boucle->where[]= array("'AND'",
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m\')'",("$annee . $mois")),
					array("'>='", "'DATE_FORMAT($date_fin, \'%Y%m\')'",("$annee . $mois")));
	elseif ($type == 'semaine')
		$boucle->where[]= array("'AND'",
					array("'>='",
					     "'DATE_FORMAT($date_fin, \'%Y%m%d\')'",
					      ("date_debut_semaine($annee, $mois, $jour)")),
					array("'<='",
					      "'DATE_FORMAT($date_deb, \'%Y%m%d\')'",
					      ("date_fin_semaine($annee, $mois, $jour)")));
	elseif (count($crit->param) > 3)
		$boucle->where[]= array("'AND'",
					array("'>='",
					      "'DATE_FORMAT($date_fin, \'%Y%m%d\')'",
					      ("$annee . $mois . $jour")),
					array("'<='", "'DATE_FORMAT($date_deb, \'%Y%m%d\')'", ("$annee2 . $mois2 . $jour2")));
	// sinon on prend tout
}


/**
 * Afficher de facon textuelle les dates de debut et fin en fonction des cas
 * - Le lundi 20 fevrier a 18h
 * - Le 20 fevrier de 18h a 20h
 * - Du 20 au 23 fevrier
 * - du 20 fevrier au 30 mars
 * - du 20 fevrier 2007 au 30 mars 2008
 * $horaire='oui' permet d'afficher l'horaire, toute autre valeur n'indique que le jour
 * $forme peut contenir abbr (afficher le nom des jours en abbrege) et ou hcal (generer une date au format hcal)
 * 
 * @param string $date_debut
 * @param string $date_fin
 * @param string $horaire
 * @param string $forme
 * @return string
 */
function agenda_affdate_debut_fin($date_debut, $date_fin, $horaire = 'oui', $forme=''){
	static $trans_tbl=NULL;
	if ($trans_tbl==NULL){
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
	}
	
	$abbr = '';
	if (strpos($forme,'abbr')!==false) $abbr = 'abbr';
	$affdate = "affdate_jourcourt";
	if (strpos($forme,'annee')!==false) $affdate = 'affdate';
	
	$dtstart = $dtend = $dtabbr = "";
	if (strpos($forme,'hcal')!==false) {
		$dtstart = "<abbr class='dtstart' title='".date_iso($date_debut)."'>";
		$dtend = "<abbr class='dtend' title='".date_iso($date_fin)."'>";
		$dtabbr = "</abbr>";
	}
	
	$date_debut = strtotime($date_debut);
	$date_fin = strtotime($date_fin);
	$d = date("Y-m-d", $date_debut);
	$f = date("Y-m-d", $date_fin);
	$h = $horaire=='oui';
	$hd = date("H:i",$date_debut);
	$hf = date("H:i",$date_fin);
	$au = " " . strtolower(_T('agenda:evenement_date_au'));
	$du = _T('agenda:evenement_date_du') . " ";
	$s = "";
	if ($d==$f)
	{ // meme jour
		$s = ucfirst(nom_jour($d,$abbr))." ".$affdate($d);
		if ($h)
			$s .= " $hd";
		$s = "$dtstart$s$dtabbr";
		if ($h AND $hd!=$hf) $s .= "-$dtend$hf$dtabbr";
	}
	else if ((date("Y-m",$date_debut))==date("Y-m",$date_fin))
	{ // meme annee et mois, jours differents
		if ($h){
			$s = $du . $dtstart . affdate_jourcourt($d) . " $hd" . $dtabbr;
			$s .= $au . $dtend . $affdate($f);
			if ($hd!=$hf) $s .= " $hf";
			$s .= $dtabbr;
		}
		else {
			$s = $du . $dtstart . jour($d) . $dtabbr;
			$s .= $au . $dtend . $affdate($f) . $dtabbr;
		}
	}
	else if ((date("Y",$date_debut))==date("Y",$date_fin))
	{ // meme annee, mois et jours differents
		$s = $du . $dtstart . affdate_jourcourt($d);
		if ($h) $s .= " $hd";
		$s .= $dtabbr . $au . $dtend . $affdate($f);
		if ($h) $s .= " $hf";
		$s .= $dtabbr;
	}
	else
	{ // tout different
		$s = $du . $dtstart . affdate($d);
		if ($h)
			$s .= " ".date("(H:i)",$date_debut);
		$s .= $dtabbr . $au . $dtend. affdate($f);
		if ($h)
			$s .= " ".date("(H:i)",$date_fin);
		$s .= $dtabbr;
	}
	return unicode2charset(charset2unicode(strtr($s,$trans_tbl),''));	
}

/**
 * Ajoute un evenement dans un buffer, comme le filtre agenda_memo,
 * mais en prenant une date de debut et de fin de l'evenement.
 * 
 * La liste est retournee et videe par un appel vide a cette fonction
 * (voir le filtre agenda_mini)
 *
 * @param string $date_deb Date de debut de l'evenement '2010-10-09 13:30:00'
 * @param string $date_fin Date de fin de l'evenement
 * @param string $titre Titre de l'evenement
 * @param string $descriptif Descriptif de l'evenement
 * @param string $lieu Lieu de l'evenement
 * @param string $url URL du lien
 * @param string $cal ?
 * @param string $var_date
 * 	Inserer la date du jour parcouru dans l'URL du lien (de chaque evenement).
 * 	Passer pour cela le nom de la variable a utiliser
 * (typiquement, #ENV{var_date} avec le mini-calendrier).
 * 
 * @return
**/
function agenda_memo_full($date_deb=0, $date_fin=0 , $titre='', $descriptif='', $lieu='', $url='', $cal='', $var_date='')
{
	static $agenda = array();
	if (!$date_deb) {
		$res = $agenda;
		$agenda=array();
		return $res;
	}
	$url=str_replace("&amp;","&",$url);
	
	$idatedeb = date_ical($date_deb);
	$idatefin = date_ical($date_fin);
	$cal = trim($cal); // func_get_args (filtre alterner) rajoute \n !!!!
	$startday1=explode(' ',$date_deb);
	$startday1=$startday1['0'].' 00:00:00';
	$ts_startday1=strtotime($startday1);
	$ts_date_fin=strtotime($date_fin);
	$maxdays=365;
	$d1 = date('Y-m-d', strtotime($date_deb));
	
	while (($ts_startday1 <= $ts_date_fin) && ($maxdays-- > 0))
	{
		$day=date('Y-m-d H:i:s',$ts_startday1);
		$d2 = date('Y-m-d', $ts_startday1);
		
		if ($var_date) {
			$url = parametre_url($url, $var_date, $d2);
		}
		
		// element a ajouter:
		$a2 = array (
			     'CATEGORIES' => $cal,
			     'DTSTART' => $idatedeb,
			     'DTEND' => $idatefin,
			     'DESCRIPTION' => $descriptif,
			     'SUMMARY' => $titre,
			     'LOCATION' => $lieu,
			     'URL' => $url);
		//DEBUG echo "\n<!-- ";
		//DEBUG echo "" . sprintf("d1=%s d2=%s",$d1,$d2) . "";
		// On extrait la bonne liste:
		$tab = (array)$agenda[$cal][(date_anneemoisjour($day))];
		// si la date de debut de l'element est exactement la
		// date du jour courant ET qu'il y a deja des
		// evenements, on met l'element a ajouter en premier
		// dans la liste; sinon on l'ajoute a la fin comme
		// d'habitude:
		if( $d1 == $d2 && count($tab)>0 ) {
		  //DEBUG echo " a2a1 ";
		  array_unshift($tab,$a2);
		} else {
		  //DEBUG echo " a1a2 ";
		  $tab[] = $a2;
		}
		// On remplace par la nouvelle liste:
		$agenda[$cal][(date_anneemoisjour($day))] = $tab;
		//DEBUG echo " -->";
		$ts_startday1 += 26*3600; // le jour suivant : +26 h pour gerer les changements d'heure
		$ts_startday1 = mktime(0, 0, 0, date("m",$ts_startday1), 
		date("d",$ts_startday1), date("Y",$ts_startday1)); // et remise a zero de l'heure	
	}
	// toujours retourner vide pour qu'il ne se passe rien
	return "";
}


function agenda_memo_evt_full($date_deb=0, $date_fin=0 , $titre='', $descriptif='', $lieu='', $url='', $cal='')
{
	static $evenements = array();
	if (!$date_deb) return $evenements;
	$url=str_replace("&amp;","&",$url);
	
	$idatedeb = date_ical(reset(explode(" ",$date_deb))." 00:00:00");
	$idatefin = date_ical(reset(explode(" ",$date_fin))." 00:00:00");
	$cal = trim($cal); // func_get_args (filtre alterner) rajoute \n !!!!
	$startday1=explode(' ',$date_deb);
	$startday1=$startday1['0'].' 00:00:00';
	$ts_startday1=strtotime($startday1);
	$ts_date_fin=strtotime($date_fin);
	$maxdays=365;
	while (($ts_startday1<=$ts_date_fin)&&($maxdays-->0))
	{
		$day=date('Y-m-d H:i:s',$ts_startday1);
		$evenements[$cal][(date_anneemoisjour($day))][] =  array(
			'CATEGORIES' => $cal,
			'DTSTART' => $idatedeb,
			'DTEND' => $idatefin,
			'DESCRIPTION' => $descriptif,
			'SUMMARY' => $titre,
			'LOCATION' => $lieu,
			'URL' => $url);
		$ts_startday1 += 26*3600; // le jour suivant : +26 h pour gerer les changements d'heure
		$ts_startday1 = mktime(0, 0, 0, date("m",$ts_startday1), 
		date("d",$ts_startday1), date("Y",$ts_startday1)); // et remise a zero de l'heure	
	}

	// toujours retourner vide pour qu'il ne se passe rien
	return "";
}

function agenda_affiche_full($i)
{
	$args = func_get_args();
	$nb = array_shift($args); // nombre d'evenements (on pourrait l'afficher)
	$evt = array_shift($args);
	$type = array_shift($args);

	$agenda = agenda_memo_full(0);
	$evt_avec = array();
	$evt_sans = array();
	if ($nb) {
		foreach (($args ? $args : array_keys($agenda)) as $k) {
			if (isset($agenda[$k])&&is_array($agenda[$k]))
				foreach($agenda[$k] as $d => $v) {
					$evt_avec[$d] = isset($evt_avec[$d]) ? (array_merge($evt_avec[$d], $v)) : $v;
				}
		}
		$evenements = agenda_memo_evt_full(0);
		foreach (($args ? $args : array_keys($evenements)) as $k) {
			if (isset($evenements[$k])&&is_array($evenements[$k]))
				foreach($evenements[$k] as $d => $v) {
					$evt_sans[$d] = isset($evt_sans[$d]) ? (array_merge($evt_sans[$d], $v)) : $v;
				}
		}
	}

	return agenda_periode($type, $nb, $evt_avec, $evt_sans);
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
?>

<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

if (!defined('VAR_DATE')) define('VAR_DATE', 'archives');

function balise_DATE_ARCHIVES($p) {
	$p->code = "_request('".VAR_DATE."')";

	#$p->interdire_scripts = true;
	return $p;
}

function critere_archives($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$objet = objet_type($boucle->id_table);
	$date = objet_info($objet,'date');
	$champ_date = "'" . $boucle->id_table ."." .
	$date . "'";
	$boucle->where[] = array(
		'REGEXP',
		$champ_date, 
		"sql_quote(('^' . interdire_scripts(entites_html(\$Pile[0]['".VAR_DATE."']))))"
	);
}

function thead($lang){
	$ret = '';
	$debut = 2;
	if($lang == 'en') $debut = 1;
	for($i=0;$i<7;$i++) {
		$ret .= "\n\t\t\t\t".'<th scope="col"><abbr title="'._T('date_jour_'.$debut).'">' .
		_T('minical:date_jour_abbr_'.$debut) . '</abbr></th>';
		$debut = $debut == 7 ? 1 : $debut+1;
	}
	return "\n\t\t".'<thead>
			<tr>' .$ret. '
			</tr>
			</thead>'."\n";
}

if(!function_exists('agenda_memo')){
	// Cette fonction memorise dans un tableau indexe par son 5e arg
	// un evenement decrit par les 4 autres (date, descriptif, titre, URL).
	// Appellee avec une date nulle, elle renvoie le tableau construit.
	// l'indexation par le 5e arg autorise plusieurs calendriers dans une page
	function agenda_memo($date=0 , $descriptif='', $titre='', $url='', $cal=''){
		static $agenda = array();
		if (!$date) return $agenda;
		$idate = date_ical($date);
		$cal = trim($cal);
		$agenda[$cal][(date_anneemoisjour($date))][] =  array(
			'CATEGORIES' => $cal,
			'DTSTART' => $idate,
			'DTEND' => $idate,
			'DESCRIPTION' => texte_script($descriptif),
			'SUMMARY' => texte_script($titre),
			'URL' => $url);
		// toujours retourner vide pour qu'il ne se passe rien
		return "";
	}
}
function agenda_minical($i) {
	$args = func_get_args();
	$une_date = array_shift($args); // une date comme balise
	$sinon = array_shift($args);
	if (!$une_date) return $sinon;
	$type = 'minical';
	$agenda = agenda_memo(0);
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

if(!function_exists('http_calendrier_init')){
	///
	/// init: calcul generique des evenements a partir des tables SQL
	/// Fonction récupérée des anciens SPIP
	///
	
	// https://code.spip.net/@http_calendrier_init
	function http_calendrier_init($time='', $type='mois', $echelle='', $partie_cal='', $script='', $evt=null){
		if (is_array($time)) {
			list($j,$m,$a) = $time;
			if ($j+$m+$a) $time = @mktime(0,0,0, $m, $j, $a);
		}
	
		if (!is_numeric($time)) $time = time();
	
		$jour = date("d",$time);
		$mois = date("m",$time);
		$annee = date("Y",$time);
			if (!$echelle = intval($echelle)) $echelle = DEFAUT_D_ECHELLE;
			if (!is_string($type) OR !preg_match('/^\w+$/', $type)) $type ='mois';
			if (!is_string($partie_cal) OR !preg_match('/^\w+$/', $partie_cal)) 
				$partie_cal =  DEFAUT_PARTIE;
		list($script, $ancre) = 
		  calendrier_retire_args_ancre($script); 
		if (is_null($evt)) {
			$g = 'quete_calendrier_' . $type;
			$evt = quete_calendrier_interval($g($annee,$mois, $jour));
			quete_calendrier_interval_articles("'$annee-$mois-00'", "'$annee-$mois-1'", $evt[0]);
		}
	
		$f = 'http_calendrier_' . $type;
		if (!function_exists($f)) $f = 'http_calendrier_mois';
		return $f($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt);
	}
}
if(!function_exists('calendrier_retire_args_ancre')){
	///
	///Utilitaires sans html ni sql
	///Fonction récupérée des anciens SPIP
	///
	
	/// Utilitaire de separation script / ancre
	/// et de retrait des arguments a remplacer
	/// (a mon avis cette fonction ne sert a rien, puisque parametre_url()
	/// sait remplacer les arguments au bon endroit -- Fil)
	/// Pas si simple: certains param ne sont pas remplaces 
	/// et doivent reprendre leur valeur par defaut -- esj.
	/// https://code.spip.net/@calendrier_retire_args_ancre
	function calendrier_retire_args_ancre($script){
	
		if (preg_match(',^(.*)#([\w-]+)$,',$script, $m)) {
			$script = $m[1];
			$ancre = $m[2];
		} else { $ancre = ''; }
	
		foreach(array('echelle','jour','mois','annee', 'type', 'partie_cal', 'bonjour') as $arg) {
			$script = preg_replace("/([?&])$arg=[^&]*&/",'\1', $script);
			$script = preg_replace("/([?&])$arg=[^&]*$/",'\1', $script);
		}
		if (in_array(substr($script,-1),array('&','?'))) $script =   substr($script,0,-1);
		return array(quote_amp($script), $ancre);
	}
}

function http_calendrier_minical($annee, $mois, $jour, $echelle, $partie_cal, $script, $ancre, $evt) {
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
	if ($lang=='en') {
		for($j=($jour_semaine != 6) ? $jour_semaine + 1 : 7; $j<7; $j++) {
			$ligne .= "\n\t<td>&nbsp;</td>";
		}
	} else {
		for($j=$jour_semaine ? $jour_semaine : 7; $j<7; $j++) {
			$ligne .= "\n\t<td>&nbsp;</td>";
		}
	}
	
	return $total . ($ligne ? "\n<tr>$ligne\n</tr>" : '');
}

?>
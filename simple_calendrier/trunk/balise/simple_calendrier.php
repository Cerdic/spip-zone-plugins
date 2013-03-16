<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3.0
 * Licence GNU/GPL
 * 2010-2012
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/simplecal_utils');

function balise_SIMPLE_CALENDRIER($p) { 
	
	$req_select = "e.*";
	$req_from = "spip_evenements AS e";
	$req_where = "e.date_debut >= CONCAT(DATE_FORMAT(NOW(),'%Y-%m'), '-01') AND e.date_debut <= LAST_DAY(NOW()) AND e.statut = 'publie'";
	$req_orderby = "e.date_debut, e.date_fin";
	
	// ---- Acces restreint ---
	$ar = simplecal_get_where_rubrique_exclure();
	if ($ar){
		$req_where .= " $ar";
	}
	// ------------------------
	
	/*
	SELECT e.*
	FROM `spip_evenements` as e
	WHERE e.date_debut >= CONCAT(DATE_FORMAT(NOW(),'%Y-%m'), '-01')
	  AND e.date_debut <= LAST_DAY(NOW())
	*/
	
	$dates = array();
	$rows = sql_allfetsel($req_select, $req_from, $req_where, $req_orderby);
	foreach ($rows as $row){
		$date_debut = $row['date_debut'];
		$dates[] = substr($date_debut, 0,10);
	}
	
	$imois = intval(date('m'));
	$iannee = intval(date('Y'));
	$calendrier = simplecal_generer_calendrier($imois, $iannee, $dates);
		
	$p->code = "'$calendrier'";
	return $p; 
}


function simplecal_generer_calendrier($mois, $annee, $tab_dates){
		
	$sannee = "".$annee;
	$smois = "".$mois;
	if (strlen(smois)<2){
		$smois = "0".$smois;
	}
	//$date_1er_du_mois = date('Y-m-d-N', mktime(0, 0, 0, $mois , 1, $annee)); // Ne gere pas les annees <= 1900...
	$date_1er_du_mois = date_format(date_create("".$sannee."-".$smois."-1"), 'Y-m-d-N');
	
	
	if (preg_match("#([1-2][0-9]{3})\-([0-9]{2})\-([0-9]{2})\-([0-9]{1})#i", $date_1er_du_mois, $matches)){
		$annee = intval($matches[1]);
		$mois = intval($matches[2]);
		$jour = intval($matches[3]);
		$num_jour = intval($matches[4]);
	} 
	
	
	// ****************
	
	$nom_mois = array(
		1 => ucfirst(_T('date_mois_1')), 
		2 => ucfirst(_T('date_mois_2')), 
		3 => ucfirst(_T('date_mois_3')), 
		4 => ucfirst(_T('date_mois_4')), 
		5 => ucfirst(_T('date_mois_5')), 
		6 => ucfirst(_T('date_mois_6')), 
		7 => ucfirst(_T('date_mois_7')), 
		8 => ucfirst(_T('date_mois_8')), 
		9 => ucfirst(_T('date_mois_9')), 
		10 => ucfirst(_T('date_mois_10')), 
		11 => ucfirst(_T('date_mois_11')), 
		12 => ucfirst(_T('date_mois_12'))
	);
	$max_des_mois = array(1=>31, 2=>28, 3=>31, 4=>30, 5=>31, 6=>30, 7=>31, 8=>31, 9=>30, 10=>31, 11=>30, 12=>31);
	
	// Annee bissextile ?
	if (($annee%4 == 0 && $annee%100 != 0) || $annee%400 == 0){
		$max_des_mois[2] = 29;
	}
	
	$jour_now = intval(date('d'));
	$mois_now = intval(date('m'));
	$annee_now = intval(date('Y'));
	$aujourdhui = $jour_now.' '.$nom_mois[$mois_now].' '.$annee_now;

	$rc = "\n";
	
	$s = '';    
	$s .= $rc.'<div class="ui-datepicker ui-widget ui-widget-content ui-corner-all">';
	$s .= $rc.'<div class="ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all">';
	//$s .= '    <a class="ui-datepicker-prev ui-corner-all" title="'._T('simplecal:date_precedent').'" href="#">';
	//$s .= '        <span class="ui-icon ui-icon-circle-triangle-w">'._T('simplecal:date_precedent').'</span>';
	//$s .= '    </a>';    
	//$s .= '    <a class="ui-datepicker-next ui-corner-all" title="'._T('simplecal:date_suivant').'" href="#">';
	//$s .= '        <span class="ui-icon ui-icon-circle-triangle-e">'._T('simplecal:date_suivant').'</span>';
	//$s .= '    </a>';
	$s .= $rc.'    <div class="ui-datepicker-title">';
	$s .= $rc.'        <span class="ui-datepicker-month">'.$nom_mois[$mois].'</span>';
	$s .= $rc.'        <span class="ui-datepicker-year">'.$annee.'</span>';
	$s .= $rc.'    </div>';
	$s .= $rc.'</div>';
	
	$s .= $rc.'<table class="ui-datepicker-calendar">';
	$s .= $rc.'<thead>';
	$s .= $rc.'<tr>';
	$s .= $rc.'    <th title="'.ucfirst(_T('date_jour_2')).'">'._T('simplecal:date_lundi_abbr').'</th>';
	$s .= $rc.'    <th title="'.ucfirst(_T('date_jour_3')).'">'._T('simplecal:date_mardi_abbr').'</th>';
	$s .= $rc.'    <th title="'.ucfirst(_T('date_jour_4')).'">'._T('simplecal:date_mercredi_abbr').'</th>';
	$s .= $rc.'    <th title="'.ucfirst(_T('date_jour_5')).'">'._T('simplecal:date_jeudi_abbr').'</th>';
	$s .= $rc.'    <th title="'.ucfirst(_T('date_jour_6')).'">'._T('simplecal:date_vendredi_abbr').'</th>';
	$s .= $rc.'    <th title="'.ucfirst(_T('date_jour_7')).'" class="ui-datepicker-week-end">'._T('simplecal:date_samedi_abbr').'</th>';
	$s .= $rc.'    <th title="'.ucfirst(_T('date_jour_1')).'" class="ui-datepicker-week-end">'._T('simplecal:date_dimanche_abbr').'</th>';
	$s .= $rc.'</tr>';
	$s .= $rc.'</thead>';
	
	$s .= $rc.'<tbody>';
	$s .= $rc.'<tr>';
	
	$isem = 0;
	
	// remplissage du debut du calendrier (les jours avant le 1er du mois (qui ne tombe pas forcement un lundi)) 
	for ($i=1; $i<=($num_jour-1); $i++){
		$mois_davant = $mois-1;
		if ($mois_davant == 0){
			$mois_davant = 12;
		}
		$s .= $rc.'<td class="ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">';
		$s .= $rc.'    <span>'.($max_des_mois[$mois_davant] + ($i-($num_jour-1))).'</span>';
		$s .= $rc.'</td>';
		$isem++;
	}
	
	// remplissage du mois (du 1er au 30 par exemple)
	$max_du_mois = $max_des_mois[$mois];
	for ($i=1; $i<=$max_du_mois; $i++){
		if ($isem == 0){
			$s .= '<tr>';
		}
		
		if ($i<10){
			$j = '0'.$i;
		} else {
			$j = $i;
		}
		if ($mois<10){
			$m = '0'.$mois;
		} else {
			$m = $mois;
		}
		
		
		$curdate = $annee."-".$m."-".$j;
		
		if (in_array($curdate, $tab_dates)){
			$active = true;
		} else {
			$active = false;
		}
		
		
		
		if ($jour_now==$i && $mois_now==$mois && $annee_now==$annee) {
			$s .= $rc.'<td class="ui-datepicker-today">';
			$s .= $rc.'    <span class="ui-state-default ui-state-highlight">'.$i.'</span>';
			$s .= $rc.'</td>';
		}
		else {
			$s .= $rc.'<td>';
			if ($active){
				$s .= $rc.'    <span class="ui-state-default ui-state-active">'.$i.'</span>';
			} else {
				$s .= $rc.'    <span class="ui-state-default">'.$i.'</span>';
			}
			$s .= $rc.'</td>';
		}
		$isem++;
		if ($isem == 7){
			$s .= $rc.'</tr>';
			$isem = 0;
		}
	}
	
	// remplissage de la fin du calendrier (les jours qui suivent le max du mois (qui ne tombe pas forcement un dimanche)) 
	for ($i=1; $isem!=0; $i++){
		$s .= $rc.'<td class="ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">';
		$s .= $rc.'    <span>'.$i.'</span>';
		$s .= $rc.'</td>';
		$isem++;
		if ($isem == 7){
			$s .= $rc.'</tr>';
			$isem = 0;
		}
	}
	$s .= $rc.'</tbody>';
	$s .= $rc.'</table>';
	$s .= $rc.'</div>';
	$s .= $rc;
	
	return $s;
}


?>
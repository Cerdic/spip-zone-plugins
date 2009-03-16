<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@aff_statistique_visites_popularite
function aff_statistique_visites_popularite($serveur, $id_article, &$classement, &$liste){
	// Par popularite
	$result = sql_select("id_article, titre, popularite, visites", "spip_articles", "statut='publie' AND popularite > 0", "", "popularite DESC",'','',$serveur);
	$out = '';
	while ($row = sql_fetch($result,$serveur)) {
		$l_article = $row['id_article'];
		$liste++;
		$classement[$l_article] = $liste;

		if ($liste <= 30) {
			$articles_vus[] = $l_article;
			$out .= statistiques_populaires($row, $id_article, $liste);
		}
	}
	$recents = array();
	$q = sql_select("id_article", "spip_articles", "statut='publie' AND popularite > 0", "", "date DESC", "10",'',$serveur);
	while ($r = sql_fetch($q,$serveur))
		if (!in_array($r['id_article'], $articles_vus))
			$recents[]= $r['id_article'];

	if ($recents) {
		$result = sql_select("id_article, titre, popularite, visites", "spip_articles", "statut='publie' AND " . sql_in('id_article', $recents), "", "popularite DESC",'','',$serveur);

		$out .= "</ul><div style='text-align: center'>[...]</div>" .
		"<ul class='classement'>";
		while ($row = sql_fetch($result,$serveur)) {
			$l_article = $row["id_article"];
			$out .= statistiques_populaires($row, $id_article, $classement[$l_article]);
		}
	}

	return !$out ? '' : (
		"<br />\n"
		."<div class='iconeoff' style='padding: 5px'>\n"
		."<div class='verdana1 spip_x-small'>"
		.typo(_T('info_visites_plus_populaires'))
		."<ul class='classement'>"
		.$out

		."</ul>"

		."<b>"._T('info_comment_lire_tableau')."</b><br />"._T('texte_comment_lire_tableau')

		."</div>"
		."</div>");
}

function statistiques_populaires($row, $id_article, $classement)
{
	$titre = typo(supprime_img($row['titre'], ''));
	$l_article = $row['id_article'];

	if ($l_article == $id_article){
		return "<li class='on'><em>$classement.</em>$titre</li>";
	} else {
		$visites = $row['visites'];
		$popularite = round($row['popularite']);
		return "<li><em>$classement.</em><a href='" . generer_url_ecrire("statistiques_visites","id_article=$l_article") . "' title='"._T('info_popularite_3', array('popularite' => $popularite, 'visites' => $visites))."'>$titre</a></li>";
	}
}

// http://doc.spip.org/@aff_statistique_visites_par_visites
function aff_statistique_visites_par_visites($serveur='', $id_article=0, $classement= array()) {
	$res = "";
	// Par visites depuis le debut
	$result = sql_select("id_article, titre, popularite, visites", "spip_articles", "statut='publie' AND popularite > 0", "", "visites DESC", "30",'',$serveur);

	while ($row = sql_fetch($result,$serveur)) {
		$titre = typo(supprime_img($row['titre'],''));
		$l_article = $row['id_article'];

		if ($l_article == $id_article){
			$res.= "<li class='on'><em>"
			. $classement[$l_article]
			. ".</em>$titre</li>";
		} else {
			$t = _T('info_popularite_4',
				array('popularite' => round($row['popularite']), 'visites' =>  $row['visites']));
			$h = generer_url_ecrire("statistiques_visites","id_article=$l_article");
			$out = "<a href='$h'\ntitle='$t'>$titre</a>";
			$res.= "<li><em>"
				. $classement[$l_article]
				. ".</em>$out</li>";
		}
	}

	if (!$res) return '';

	return "<br /><div class='iconeoff' style='padding: 5px;'>"
	  . "<div style='overflow:hidden;' class='verdana1 spip_x-small'>"
	  . typo(_T('info_affichier_visites_articles_plus_visites'))
	  . "<ul class='classement'>"
	  . $res
	  . '</ul></div></div>';
}

// http://doc.spip.org/@cadre_stat
function cadre_stat($stats, $table, $id_article)
{
	if (!$stats) return '';
	return debut_cadre_relief("statistiques-24.gif", true)
	. join('', $stats)
	. fin_cadre_relief(true)
	. statistiques_mode($table, $id_article);
}

// http://doc.spip.org/@statistiques_collecte_date
function statistiques_collecte_date($count, $date, $table, $where, $serveur)
{
	$result = sql_select("$count AS n, $date AS d", $table, $where, 'd', '', '','', $serveur);
	$log = array();

	while ($r = sql_fetch($result,$serveur)) $log[$r['d']] = $r['n'];
	return $log;
}

// http://doc.spip.org/@statistiques_tous
function statistiques_tous($log, $id_article, $table, $where, $order, $serveur, $duree, $interval, $total, $popularite, $liste='', $classement=array(), $script='')
{
	$r = array_keys($log);
	$date_fin = max($r);
	$date_debut = min($r);
	$date_premier = sql_getfetsel("UNIX_TIMESTAMP($order) AS d", $table, $where, '', $order, 1,'',$serveur);
	$last = (time()-$date_fin>$interval) ? 0 : $log[$date_fin];
	$max = max($log);

	list($moyenne,$prec, $res, $res_mois) = stat_log1($log, $date_fin, $interval, $script);

	$stats = 
	  "<table class='visites' id='visites_quotidiennes'>"
	  . "<caption>"._T('visites_journalieres')."</caption>"
	  . "<thead><tr class='row_first'><th>".trim(trim(_T('date'),':'))."</th><th class='valeur'>".trim(trim(_T('info_visites'),':'))."</th><th class='moyenne'>".trim(trim(_T('info_moyenne'),':'))."</th></tr></thead>"
	  . "<tbody>"
	  . $res
	  . (!$liste ? '' : // prevision que pour les visites
	     statistiques_prevision($id_article, $moyenne, $popularite, $last))
	  . "</tbody>"
	  . "</table>";
	if ($res_mois) {
		$stats .=
		  "<table class='visites' id='visites_mensuelles'>"
		  . "<caption>"._T('visites_mensuelles')."</caption>"
		  . "<thead><tr class='row_first'><th>".trim(trim(_T('date'),':'))."</th><th class='valeur'>".trim(trim(_T('info_visites'),':'))."</th><th class='moyenne'>".trim(trim(_T('info_moyenne'),':'))."</th></tr></thead>"
		  . "<tbody>"
		  . $res_mois
		  . "</tbody>"
		  . "</table>";
	}


	if  ($liste) {
		$liste = statistiques_classement($id_article, $classement, $liste);
		$legend =  statistiques_resume($max, $moyenne, $last, $prec, $popularite,$total,$liste);
	} else {
	  $legend = "<table width='100%'><tr><td width='50%'>" .
	    affdate_heure(date("Y-m-d H:i:s", $date_debut)) .
	    "</td><td width='50%' align='right'>" .
	    affdate_heure(date("Y-m-d H:i:s", $date_fin)) .
	    '</td></tr></table>';
	  $resume = '';
	}

	$x = (!$duree) ? 1 : (420/ $duree);
	$zoom = statistiques_zoom($id_article, $x, $date_premier, $date_debut, $date_fin);
	return array($zoom,$legend, $stats );
}

// http://doc.spip.org/@statistiques_resume
function statistiques_resume($max, $moyenne, $last, $prec, $popularite,$total, $classement=null)
{
	return  "<table class='info'>
	<thead>
		<tr class='row_first'>
		<th>".trim(trim(_T('info_maximum'),':'))."</th><th>".trim(trim(_T('info_moyenne'),':'))."</th>
		<th>".'<a href="'
		. generer_url_ecrire("statistiques_referers")
		. '" title="'._T('titre_liens_entrants').'">'
		. trim(trim(_T('info_aujourdhui'),':'))
		. '</a> '."</th>"
		. (($prec <= 0) ? '' :
		     '<th><a href="'
		      . generer_url_ecrire("statistiques_referers","jour=veille")
		      .'"  title="'._T('titre_liens_entrants').'">'
		      .trim(trim(_T('info_hier'),':')).'</a></th>')
		. (!$popularite ? '' :"<th>".trim(trim(_T('info_popularite_5'),':'))."</th>")
		. (!$total ? '' :"<th>".trim(trim(_T('info_total'),':'))."</th>")
		. (!$classement ? '' :"<th>".trim(trim($classement[0],':'))."</th>")
		. "</tr>
	</thead>
	<tbody>
		<tr>
		<td class='num'>$max</td><td class='num'>".round($moyenne)."</td><td class='num'>$last</td>"
		. (($prec <= 0) ? '' :"<td class='num'>$prec</td>")
		. (!$popularite ? '' :"<td class='num'>$popularite</td>")
		. (!$total ? '' :"<td class='num'>$total</td>")
		. (!$classement ? '' :"<td class='num'>".$classement[1]."</td>")
		. "</tr>
	</tbody>
	</table>";
}

// http://doc.spip.org/@statistiques_classement
function statistiques_classement($id_article, $classement, $liste)
{
	if ($id_article) {
		if ($classement[$id_article] > 0) {
			if ($classement[$id_article] == 1)
			      $ch = _T('info_classement_1', array('liste' => $liste));
			else
			      $ch = _T('info_classement_2', array('liste' => $liste));
			return array('',$classement[$id_article].$ch);
		}
	  } else
		return array(_T('info_popularite_2'),ceil($GLOBALS['meta']['popularite_total']));
}

// http://doc.spip.org/@statistiques_zoom
function statistiques_zoom($id_article, $largeur_abs, $date_premier, $date_debut, $date_fin)
{
	if ($largeur_abs > 1) {
		$inc = ceil($largeur_abs / 5);
		$duree_plus = 420 / ($largeur_abs - $inc);
		$duree_moins = 420 / ($largeur_abs + $inc);
	}

	if ($largeur_abs == 1) {
		$duree_plus = 840;
		$duree_moins = 210;
	}

	if ($largeur_abs < 1) {
		$duree_plus = 420 * ((1/$largeur_abs) + 1);
		$duree_moins = 420 * ((1/$largeur_abs) - 1);
	}

	$pour_article = $id_article ? "&id_article=$id_article" : '';

	$zoom = '';

	if ($date_premier < $date_debut)
		$zoom= http_href(generer_url_ecrire("statistiques_visites","duree=$duree_plus$pour_article"),
			 http_img_pack('loupe-moins.gif',
				       _T('info_zoom'). '-',
				       "style='border: 0px; vertical-align: middle;'"),
			 "&nbsp;");
	if ( (($date_fin - $date_debut) / (24*3600)) > 30)
		$zoom .= http_href(generer_url_ecrire("statistiques_visites","duree=$duree_moins$pour_article"),
			 http_img_pack('loupe-plus.gif',
				       _T('info_zoom'). '+',
				       "style='border: 0px; vertical-align: middle;'"),
			 "&nbsp;");

	return $zoom;
}

define('MOYENNE_GLISSANTE_JOUR', 30);
define('MOYENNE_GLISSANTE_MOIS', 12);

function moyenne_glissante_jour($valeur = false) {
	static $v = array();
	// pas d'argument, on rend la moyenne
	if ($valeur === false) {
		return round(statistiques_moyenne($v),2);
	}
	// argument, on l'ajoute au tableau...
	// surplus, on enleve...
	$v[] = $valeur;
	if (count($v) > MOYENNE_GLISSANTE_JOUR)
		array_shift($v);
}

function moyenne_glissante_mois($valeur = false) {
	static $v = array();
	// pas d'argument, on rend la moyenne
	if ($valeur === false) {
		return round(statistiques_moyenne($v),2);
	}
	// argument, on l'ajoute au tableau...
	// surplus, on enleve...
	$v[] = $valeur;
	if (count($v) > MOYENNE_GLISSANTE_MOIS)
		array_shift($v);
}


// Presentation graphique
// (rq: on n'affiche pas le jour courant, c'est a la charge de la prevision)
// http://doc.spip.org/@stat_log1
function stat_log1($log, $date_today, $interval, $script) {
	$res = '';
	$res_mois = '';

	$cumul = $decal_jour = $decal_mois = $date_prec = $val_prec = $moyenne = 0;
	foreach ($log as $key => $value) {
		if ($key == $date_today) break;
		moyenne_glissante_jour($value);
		// Inserer des jours vides si pas d'entrees
		if ($date_prec > 0) {
			$ecart = $key-$date_prec-$interval;
			for ($i=$interval; $i <= $ecart; $i+=$interval){
				moyenne_glissante_jour($value);
				$res .= statistiques_jour($date_prec+$i, 0, moyenne_glissante_jour(), $script);
				if (date('m',$date_prec+$i+$interval)!=date('m',$date_prec+$i)){
					moyenne_glissante_mois($cumul);				
					$res_mois .= statistiques_mois($date_prec+$i, $cumul, moyenne_glissante_mois(), $script);
					$cumul = 0;
				}
			}
		}
		
		$cumul += $value;
		$moyenne = moyenne_glissante_jour();

		$res .= statistiques_jour($key, $value, $moyenne, $script);
		if (date('m',$key+$interval)!=date('m',$key)){
			moyenne_glissante_mois($cumul);			
			$res_mois .= statistiques_mois($key, $cumul, moyenne_glissante_mois(), $script);
			$cumul = 0;
		}

		$date_prec = $key;
		$val_prec = $value;
	}
	return array($moyenne, $val_prec, $res, $res_mois);
}

// http://doc.spip.org/@statistiques_href
function statistiques_href($jour, $moyenne, $script, $value='')
{
	$ce_jour=date("Y-m-d H:i:s", $jour);
	$title = nom_jour($ce_jour) . ' '
	  . ($script ? affdate_heure($ce_jour) :
	     (affdate_jourcourt($ce_jour)  .' '.
	      (" | " ._T('info_visites')." $value | " ._T('info_moyenne')." "
	       . round($moyenne,2))));
	return attribut_html(supprimer_tags($title));
}

// http://doc.spip.org/@statistiques_prevision
function statistiques_prevision($id_article, $moyenne, $val_popularite, $visites_today)
{
	// $total_absolu = $total_absolu + $visites_today;
	// prevision de visites jusqu'a minuit
	// basee sur la moyenne (site) ou popularite (article)
	if (! $id_article) $val_popularite = $moyenne;
	$prevision = (1 - (date("H")*60 + date("i"))/(24*60)) * $val_popularite;
	
	$prevision = (round($prevision,0)+$visites_today);
	return statistiques_jour(_T('info_aujourdhui'),"$visites_today<em>(<span>$prevision</span>)</em>","","");
}

// Dimanche en couleur foncee
// http://doc.spip.org/@statistiques_jour
function statistiques_jour($key, $value, $moyenne, $script)
{
	if (is_int($key)){
		$ce_jour=date("Y-m-d H:i:s", $key);
		$title = /*nom_jour($ce_jour) . ' '
		  .*/ ($script ? affdate_heure($ce_jour) :
		     (affdate_jourcourt($ce_jour)));
		if ($script)
			$script .= "&amp;date=$key";
		else  {
			$script = generer_url_ecrire('calendrier', 
					"date=" . date("Y-m-d", $key), false, true);
		}
	
		$couleur = "c_". substr(date("l",$key),0,3);
		$res = "<tr class='$couleur'>"
		  . "<th title='" . date("Y/m/d", $key) . "'><a href='$script'>" . $title . "</a></th>";
	}
	else {
		// c'est aujourd'hui
		$couleur = "c_". substr(date("l"),0,3)." c_today";
		$res = "<tr class='$couleur'>"
		  . "<th title='" . date("Y/m/d", time()) . "'>" . $key . "</th>";
	}
	
	$res .= "<td class='val'>" . $value . "</td>"
	. "<td class='mean'>" . $moyenne . "</td>"
	." </tr>";

	return $res;
}

function statistiques_mois($key, $value, $moyenne, $script) {
	$res = "<tr>"
		. "<th title='" . date("Y/m/01", $key) . "'>" . affdate_mois_annee(date('Y-m-d',$key)) . "</th>"
		. "<td class='val'>" . $value . "</td>"
		. "<td class='mean'>" . $moyenne . "</td>"
		. "</tr>";
		
	return $res;	  
}


// http://doc.spip.org/@statistiques_moyenne
function statistiques_moyenne($tab)
{
	if (!$tab) return 0;
	$moyenne = 0;
	foreach($tab as $v) $moyenne += $v;
	return  $moyenne / count($tab);
}


// http://doc.spip.org/@statistiques_signatures_dist
function statistiques_signatures_dist($duree, $interval, $type, $id_article, $serveur)
{
	$where = "id_article=$id_article";
	$total = sql_countsel("spip_signatures", $where);
	if (!$total) return '';

	$order = 'date_time';
	if ($duree)
		$where .= " AND $order > DATE_SUB(".sql_quote(date('Y-m-d H:i:s')).",INTERVAL $duree $type)";

	$log = statistiques_collecte_date('COUNT(*)', "(FLOOR(UNIX_TIMESTAMP($order) / $interval) *  $interval)", 'spip_signatures', $where, $serveur);

	$script = generer_url_ecrire('controle_petition', "id_article=$id_article");
	if (count($log) > 1) {
		$res = statistiques_tous($log, $id_article, "spip_signatures", "id_article=$id_article", "date_time", $serveur, $duree, $interval, $total, 0, '', array(), $script);
		$res = gros_titre(_T('titre_page_statistiques_signatures_jour'),'', false) . cadre_stat($res, 'spip_signatures', $id_article);
	} else $res = '';

	$mois = statistiques_collecte_date( "COUNT(*)",
		"FROM_UNIXTIME(UNIX_TIMESTAMP(date_time),'%Y-%m')",
		"spip_signatures",
		"date_time > DATE_SUB(NOW(),INTERVAL 2700 DAY)"
		. (" AND id_article=$id_article"),
		$serveur);

	return "<br />"
	. $res
	. ($res ? '' : statistiques_mode("spip_signatures", $id_article))
;
}

// http://doc.spip.org/@statistiques_forums_dist
function statistiques_forums_dist($duree, $interval, $type, $id_article, $serveur)
{
	$where = "id_article=$id_article AND statut='publie'";
	$total = sql_countsel("spip_forum", $where);
	if (!$total) return '';
	$order = 'date_heure';
	$interval = 24 * 3600;
	$oldscore = 420;
	$oldlog = array();
	while ($interval >= 1) {
		$log = statistiques_collecte_date('COUNT(*)', "(FLOOR(UNIX_TIMESTAMP($order) / $interval) *  $interval)", 'spip_forum', $where, $serveur);
		if (count($log) > 3) break;
		$oldlog = $log;
		$oldinterval = $interval;
		$interval /= ($interval>3600) ?  24 : 60;
	}
	if (count($log) > 20) {
	  $interval = $oldinterval;
	  $log = $oldlog;
	}
	$script = generer_url_ecrire('articles_forum', "id_article=$id_article");
	$date = sql_getfetsel('UNIX_TIMESTAMP(date)', 'spip_articles', $where);
	$back = 10*ceil((time()-$date) / 3600);
	$jour = statistiques_tous($log, $id_article, "spip_forum", $where, "date_heure", $serveur, $back, $interval, $total, 0, '', array(), $script);

	return gros_titre(_T('titre_page_statistiques_messages_forum'),'', false)
	  . cadre_stat($jour, 'spip_forum', $id_article);
}

// Le bouton pour CSV

// http://doc.spip.org/@statistiques_mode
function statistiques_mode($table, $id=0)
{
	global $spip_lang_left;
	$t = str_replace('spip_', '', $table);
	$fond = (strstr($t, 'visites') ? 'statistiques' : $t);
	$args = array();
	if ($id) {
		$fond .= "_article"; 
		$args['id_article'] = $id;
	}
	include_spip('inc/acces');
	$args = param_low_sec($fond, $args, '', 'transmettre');
	$url = generer_url_public('transmettre', $args);
	return "<a style='float: $spip_lang_left;' href='$url'>CSV</a>";
}
?>

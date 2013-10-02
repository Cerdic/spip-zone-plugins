<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 2.0 - 06/2009 - SPIP 2.x
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| D. Chiche . pour la maj 2.0
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| Requetes principales sur base
+--------------------------------------------+
*/

// nombre de jours depuis debut stats
function nb_jours_stats() {
	$q = sql_select("COUNT(*) as nbj FROM spip_visites");
	$r = sql_fetch($q);
	if ($r['nbj'] > 1){ $nb = $r['nbj']; }
	else { $nb = "1"; }
	return $nb;
}

// date debut stats
function prim_jour_stats() {
	$q = sql_select("DATE_FORMAT(date,'%d/%m/%Y') AS jourj FROM spip_visites LIMIT 0,1");
	$r = sql_fetch($q);
	return $r['jourj'];
}

// total visites du jour
function global_jour($date) {
	$g=array();
	$q = sql_select("visites, DATE_FORMAT(maj,'%d/%m/%y %H:%i') as date FROM spip_visites WHERE date='$date'");
	if ($r = @sql_fetch($q)) {
		$g = $r;
	} else {
		$g['visites'] = '0';
		$g['date']=date('d/m/y H:i', mktime(0, 1, 0, date("m"), date("d"), date("Y")));
	}
	return $g;
}

// Total visite depuis debut stats
function global_stats() {
	$q = sql_select("SUM(visites) AS total_absolu FROM spip_visites");
	$r = sql_fetch($q);
	$t = $r['total_absolu'];
	return $t;
}

// jour maxi-visites depuis debut stats
function max_visites_stats() {
	$qv = sql_select(" MAX(visites) as maxvi FROM spip_visites");
	$rv = sql_fetch($qv);
	$valmaxi = $rv['maxvi'];
    if (isset($valmaxi)){
        $qd = sql_select(" DATE_FORMAT(date,'%d/%m/%y') AS jmax FROM spip_visites WHERE visites = $valmaxi");
        $rd = sql_fetch($qd);
        $jourmaxi = $rd['jmax'];
        $a = array($valmaxi,$jourmaxi);
    }
    else{
        $a = array(0,0);
        }
	return $a;
}

// Cumul pages visitees
function global_pages_stats() {
	$q = sql_select("SUM(visites) AS nb_pag FROM spip_visites_articles");
	if ($r = sql_fetch($q)) {
		$t = $r['nb_pag'];
	}
	return $t;
}

// articles visites jour
function articles_visites_jour($date) {
	$q=sql_select("visites FROM spip_visites_articles WHERE date='$date'");
	$add_visit_art = array();
	while ($r=sql_fetch($q)) {
		$add_visit_art[]=$r['visites'];
	}
	return $add_visit_art;
}

// derniere maj visite articles
function derniere_maj_articles($date) {
	$q=sql_select("DATE_FORMAT(maj,'%d/%m/%y %H:%i') as dmaj 
					FROM spip_visites_articles 
					WHERE date="._q($date)." 
					ORDER BY maj DESC 
					LIMIT 0,1");
	$r=sql_fetch($q);
	return $r['dmaj'];
}

// nbr posts du jour sur vos forum
function nombre_posts_forum($date) {
	$q=sql_select("id_forum 
					FROM spip_forum 
					WHERE DATE_FORMAT(date_heure,'%Y-%m-%d') = '$date' AND statut !='perso'");
	return $nbr=sql_count($q);
}

?>
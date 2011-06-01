<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.55 - 05/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| Requetes principales sur base
+--------------------------------------------+
*/

// nombre de jours depuis debut stats
function nb_jours_stats() {
	$q = spip_query("SELECT COUNT(*) as nbj FROM spip_visites");
	$r = spip_fetch_array($q);
	if ($r['nbj'] > 1){ $nb = $r['nbj']; }
	else { $nb = "1"; }
	return $nb;
}

// date debut stats
function prim_jour_stats() {
	$q = spip_query("SELECT DATE_FORMAT(date,'%d/%m/%Y') AS jourj FROM spip_visites LIMIT 0,1");
	$r = spip_fetch_array($q);
	return $r['jourj'];
}

// total visites du jour
function global_jour($date) {
	$g=array();
	$q = spip_query("SELECT visites, DATE_FORMAT(maj,'%d/%m/%y %H:%i') as date FROM spip_visites WHERE date='$date'");
	if ($r = @spip_fetch_array($q)) {
		$g = $r;
	} else {
		$g['visites'] = '0';
		$g['date']=date('d/m/y H:i', mktime(0, 1, 0, date("m"), date("d"), date("Y")));
	}
	return $g;
}

// Total visite depuis debut stats
function global_stats() {
	$q = spip_query("SELECT SUM(visites) AS total_absolu FROM spip_visites");
	$r = spip_fetch_array($q);
	$t = $r['total_absolu'];
	return $t;
}

// jour maxi-visites depuis debut stats
function max_visites_stats() {
	$qv = spip_query("SELECT MAX(visites) as maxvi FROM spip_visites");
	$rv = spip_fetch_array($qv);
	$valmaxi = $rv['maxvi'];

	$qd = spip_query("SELECT DATE_FORMAT(date,'%d/%m/%y') AS jmax FROM spip_visites WHERE visites = $valmaxi");
	$rd = spip_fetch_array($qd);
	$jourmaxi = $rd['jmax'];
	$a = array($valmaxi,$jourmaxi);
	return $a;
}

// Cumul pages visitees
function global_pages_stats() {
	$q = spip_query("SELECT SUM(visites) AS nb_pag FROM spip_visites_articles");
	if ($r = spip_fetch_array($q)) {
		$t = $r['nb_pag'];
	}
	return $t;
}

// articles visites jour
function articles_visites_jour($date) {
	$q=spip_query("SELECT visites FROM spip_visites_articles WHERE date='$date'");
	$add_visit_art = array();
	while ($r=spip_fetch_array($q)) {
		$add_visit_art[]=$r['visites'];
	}
	return $add_visit_art;
}

// derniere maj visite articles
function derniere_maj_articles($date) {
	$q=spip_query("SELECT DATE_FORMAT(maj,'%d/%m/%y %H:%i') as dmaj 
					FROM spip_visites_articles 
					WHERE date="._q($date)." 
					ORDER BY maj DESC 
					LIMIT 0,1");
	$r=spip_fetch_array($q);
	return $r['dmaj'];
}

// nbr posts du jour sur vos forum
function nombre_posts_forum($date) {
	$q=spip_query("SELECT id_forum 
					FROM spip_forum 
					WHERE DATE_FORMAT(date_heure,'%Y-%m-%d') = '$date' AND statut !='perso'");
	return $nbr=spip_num_rows($q);
}

?>

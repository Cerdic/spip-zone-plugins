<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.52 - 08/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| diverses fonctions de requetes ...
+--------------------------------------------+
*/

function tranches_liste_art($encours,$nligne,$fl)
	{
	$exec = _request("exec");
	#global $nligne, $fl;
	$fract=ceil($nligne/$fl);
	for ($i=0; $i<$fract; $i++)
		{
		$debaff=($i*$fl)+1;
		$f_aff=($i*$fl)+$fl;
		$liais=$i*$fl;
		if ($f_aff<$nligne) { $finaff=$f_aff; $sep = " | "; }
		else { $finaff=$nligne; $sep = ""; }
		if ($debaff==$encours)
			{
			echo "<b>$debaff - $finaff</b> $sep";}
		else
			{
			echo "<a href='".generer_url_ecrire($exec,"vl=".$liais)."'>".$debaff." - ".$finaff."</a> ".$sep;
			}
		}
	}



#
#  requetes
#

// nombre de jours depuis debut stats
function nb_jours_stats() {
	$q = spip_query("SELECT COUNT(*) as nbj FROM spip_visites");
	$r = spip_fetch_array($q);
	#h.21/03/07 .. correctif : $r['nbj'] > 1
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
	$q = spip_query("SELECT visites FROM spip_visites WHERE date='$date'");
	if ($r = @spip_fetch_array($q))
		$g = $r['visites'];
	else
		$g = 0;
			
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
	$q=spip_query("SELECT id_article, visites FROM spip_visites_articles WHERE date='$date'");
	$add_visit_art = array();
	while ($r=spip_fetch_array($q)) {
		$add_visit_art[]=$r['visites'];
	}
	return $add_visit_art;
}

// nbr posts du jour sur vos forum
function nombre_posts_forum($date) {
	$q=spip_query("SELECT id_forum FROM spip_forum WHERE DATE_FORMAT(date_heure,'%Y-%m-%d') = '$date' AND statut !='perso'");
	return $nbr=spip_num_rows($q);
}


//
// visites sur rubrique
//

// lister rubrique/secteur => visites
function rubriques_du_jour($date) {
	//recup les id_article visites du jour
	$query="SELECT sva.visites, ".
			"sa.id_rubrique, sa.id_secteur ".
			"FROM spip_visites_articles sva LEFT JOIN spip_articles sa ON sva.id_article = sa.id_article ".
			"WHERE sva.date='$date' ".
			"ORDER BY sa.id_secteur";
	$result = spip_query($query);
	$tab_rubart=array();

	while($r=spip_fetch_array($result)) {
		$id_secteur=$r['id_secteur'];
		$id_rubrique=$r['id_rubrique'];
		$visa=$r['visites'];
		
		if($tab_rubart[$id_secteur]) {
			$tab_rubart[$id_secteur]['vis']+=$visa;

			if($id_rubrique!=$id_secteur) {
				$tab_rubart[$id_secteur]['rub'][$id_rubrique]+=$visa;
			}
		}
		else {
			$tab_rubart[$id_secteur]['vis']=$visa;
			if($id_rubrique!=$id_secteur) {
				$tab_rubart[$id_secteur]['rub'][$id_rubrique]=$visa;
			}
		}
		
	}
	return $tab_rubart;
}

function info_rubrique($id) {
	$inforub = array();
	$q = spip_query("SELECT titre FROM spip_rubriques WHERE id_rubrique = $id");
	$r=spip_fetch_array($q);
	return $r['titre'];
}

// affichage visites par rubrique
function tableau_visites_rubriques($date) {
	global $couleur_claire;
	$tab_rubart = rubriques_du_jour($date);
	if($tab_rubart) {
		// add visites
		$nbr=0;
		foreach($tab_rubart as $s => $c) { $nbr+=$c['vis'];	}
		
		$ifond = 0;
	
		echo "<table cellpadding='2' cellspacing='0' width='100%' border='0'>\n";
		
		foreach($tab_rubart as $sect => $cat) {
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
			$s_titre = typo(info_rubrique($sect));
			$prct_s = round(($cat['vis']/$nbr)*100, 1);
			
			echo "<tr bgcolor='$couleur'>\n<td colspan='2'>".
					http_img_pack('secteur-12.gif','ico','align=\'absmiddle\'','')."&nbsp;<b>".
					$s_titre."</b></td>\n<td width='8%'><div align='right'><b>".$cat['vis']."</b></div></td>\n".
					"<td width='12%'><div class='verdana2 bold' align='right'>$prct_s%</div></td>\n</tr>\n";
			
			if($cat['rub']) {
				foreach($cat['rub'] as $idr => $vis) {
					$r_titre = typo(info_rubrique($idr));
					$prct_r = round(($vis/$nbr)*100, 1);
					echo "<tr bgcolor='$couleur'>\n<td width='2%'>&nbsp;</td><td>".
					http_img_pack('rubrique-12.gif','ico','align=\'absmiddle\'','')."&nbsp;".
							$r_titre."</td>\n<td width='8%'><div align='right'>".$vis."</div></td>\n".
							"<td width='12%'><div class='verdana1' align='right'>$prct_r%</div></td>\n</tr>\n";
				}
			}
		}
		echo "</table>\n";
	}
}


// affiche lien titre art. : vers stats spip ou stats popup actijour
function affiche_lien_graph($id_article, $titre, $statut, $type='actijour') {
	if ($statut == 'publie') {
		$graph_pop = 
		"<a href=\"".generer_url_ecrire("actijour_graph","id_article=".$id_article)."\" 
		target=\"graph_article\" 
		onclick=\"javascript:window.open(this.href, 'graph_article', 
		'width=530,height=450,menubar=no,scrollbars=yes,resizable=yes'); 
		if(neo.window.focus){neo.window.focus();} return false; \" 
		title=\""._T('acjr:title_vers_popup_graph')."\">".supprimer_numero(typo($titre))."</a>";

		$graph_std =
		"<a href ='".generer_url_ecrire("statistiques_visites", "id_article=".$id_article)."' 
		title='"._T('acjr:title_vers_page_graph')."'>$id_article</a>";
	}
	else if ($etat == '') {
		$graph_pop = _T('acjr:article_inexistant');
		$graph_std = $id_article;
	}
	else {
		$graph_pop = $statut." - ".supprimer_numero(typo($titre));
		$graph_std = $id_article;
	}
	
	if($type=='actijour') { return $graph_pop; } else { return $graph_std; }
}


/*
// inscrit table auteur
# h. 9/11 .. pas interessant :
# 'maj' est modifie a chaque passage de l_auteur 
function inscrit_auteur($date_auj) {
	$modif_aut=array();
	$q=spip_query("SELECT id_auteur, nom, login, statut FROM spip_auteurs WHERE FROM_UNIXTIME(UNIX_TIMESTAMP(maj),'%Y-%m-%d')= '$date_auj'");
	while($r=spip_fetch_array($q)) {
		$modif_aut[$r['id_auteur']]=array($r['nom'],$r['login'],$r['statut']);
	}
	return $modif_aut;
}
*/



?>

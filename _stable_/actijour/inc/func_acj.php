<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.53 - 12/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| diverses fonctions communes ...
+--------------------------------------------+
*/

# affiche les tranches de tableau
function tranches_liste_art($encours,$nligne,$fl) {
	$exec = _request("exec");
	$fract=ceil($nligne/$fl);
	$aff='';
	for ($i=0; $i<$fract; $i++) {
		$debaff=($i*$fl)+1;
		$f_aff=($i*$fl)+$fl;
		$liais=$i*$fl;
		if ($f_aff<$nligne) { $finaff=$f_aff; $sep = " | "; }
		else { $finaff=$nligne; $sep = ""; }
		if ($debaff==$encours) {
			$aff.= "<b>$debaff - $finaff</b> $sep";
		}
		else {
			$aff.= "<a href='".generer_url_ecrire($exec,"vl=".$liais)."'>"
				.$debaff." - ".$finaff."</a> ".$sep;
		}
	}
	return $aff;
}

# affiche le logo actijour + gros titre
function entete_page($titre) {
	echo "<div style='float:left; margin-right:5px; min-height:70px;'>"; 
	echo "<img src='"._DIR_PLUGIN_ACTIJOUR."/img_pack/acjr_48.gif' alt='acjr' />";
	echo "</div>";
	gros_titre($titre);
	echo "<div style='clear:both;'></div>";
}

# bouton retour haut de page
function bouton_retour_haut() {
	echo "<div style='float:right; margin-top:6px;' class='icone36' title='"._T('acjr:haut_page')."'>\n";
	echo "<a href='#haut_page'>";
	echo "<img src='"._DIR_IMG_PACK."spip_out.gif' border='0' align='absmiddle' />\n";
	echo "</a></div>";
	echo "<div style='clear:both;'></div>\n";
}




# lister rubrique/secteur => visites
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
} //rubriques_du_jour


# renvois titre rubrique
function info_rubrique($id) {
	$inforub = array();
	$q = spip_query("SELECT titre FROM spip_rubriques WHERE id_rubrique = $id");
	$r=spip_fetch_array($q);
	return $r['titre'];
}


# affiche lien titre art. : vers stats spip ou stats popup actijour
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
	
	if($type=='actijour') {
		return $graph_pop;
	} else {
		return $graph_std;
	}
} //info_rubrique


/*---------------------------------------------------------------------------*\
produire date formatee : "d/m", moins 'n' jour(s) - ou son timestamp
\*---------------------------------------------------------------------------*/
function ante_date_jour($moins,$formater=false) {
	if($formater) {
		$ante = date('d/m', mktime(0, 0, 0, date("m"), date("d")-$moins, date("Y")));
	}
	else {
		$ante = mktime(0, 0, 0, date("m"), date("d")-$moins, date("Y"));
	}
	return $ante;
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

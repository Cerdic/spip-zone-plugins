<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.55 - 05/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| diverses fonctions communes ...
+--------------------------------------------+
*/

# initialise metas sur install ou MaJ
function initialise_metas_actijour($old_vers=''){
	$metas=array();
	
	if($old_vers) {
		foreach($GLOBALS['actijour'] as $k => $v) {
			# corriger version
			if($k=='version') {
				$metas[$k]=$GLOBALS['actijour_plug_version'];
			}
			else {
				$metas[$k]=$v;
			}
		}
	}
	else {
		$metas['version']=$GLOBALS['actijour_plug_version'];
		# 1.53
		$metas['nbl_art']='20';
		$metas['nbl_aut']='20';
		$metas['nbl_mensuel']='18';
		$metas['nbl_topsem']='10';
		$metas['nbl_topmois']='10';
		$metas['nbl_topgen']='10';
		#
	}	

	$chaine = serialize($metas);
	ecrire_meta('actijour',$chaine);
	ecrire_metas();
	
	# on relit ..
	$GLOBALS['actijour'] = @unserialize($GLOBALS['meta']['actijour']);
}


# affiche les tranches de tableau
function tranches_liste_art($encours,$nligne,$fl) {
	$exec = _request("exec");
	$fract=ceil($nligne/$fl);
	
	$gt=12; // nombre de tranches par ligne ::: modifiable a loisir !!
	$lgt=1;
	
	$aff='';
	for ($i=0; $i<$fract; $i++) {
		# retour ligne affichee
		if(($i+1)==$lgt*$gt) { $br = "<br />"; $lgt++; }
		else { $br =''; }
		
		$debaff=($i*$fl)+1;
		$f_aff=($i*$fl)+$fl;
		$liais=$i*$fl;
		if ($f_aff<$nligne) { $finaff=$f_aff; $sep = " | "; }
		else { $finaff=$nligne; $sep = ""; }
		
		# recolle parametres :
		$params='';
		# statut : actijour_connect
		if($st=_request('st')) { $params.='&st='.$st; }
		
		# affiche
		if ($debaff==$encours) {
			$aff.= "<b>$debaff - $finaff</b> $sep";
		}
		else {
			if(($i+1)==$fract) {
				$aff.= "<a href='".generer_url_ecrire($exec,"vl=".$liais).$params."'>"
					.$debaff." - ".$finaff."</a> ".$sep;				
			}
			else {
				$aff.= "<a href='".generer_url_ecrire($exec,"vl=".$liais).$params."'>"
					.$debaff."</a> ".$sep;
			}
		}
	}
	return $aff;
}

# affiche le logo actijour + gros titre
function entete_page() {
	$q=spip_query("SELECT DATE_FORMAT(NOW(),'%d/%m/%Y %H:%i') as date_serveur");
	$r=spip_fetch_array($q);
	$datetime_sql=$r['date_serveur'];

	$aff.= "<div style='float:left; margin-right:5px; min-height:55px;'>" 
		. "<img src='"._DIR_IMG_ACJR."acjr_48.gif' alt='acjr' />"
		. "</div>";
	$aff.= gros_titre(_T('acjr:titre_actijour'),'',false);
	$aff.= "<div style='clear:both;'></div>"
		. "<div class='cell_info verdana2'>"
		. "<img src='"._DIR_IMG_ACJR."icon_php.png' align='absmiddle' title='"._T('acjr:date_serveur_php')."' />\n"
		. date('d/m/Y H:i')."<br />"
		. "<img src='"._DIR_IMG_ACJR."icon_mysql.png' align='absmiddle' title='"._T('acjr:date_serveur_mysql')."' />\n"
		. $datetime_sql
		. "</div>"
		. "<p class='space_10'></p>";
	
	return $aff;
}

# bouton retour haut de page
function bouton_retour_haut() {
	return $aff= "<div style='float:right; margin-top:6px;' class='icone36' title='"
				. _T('acjr:haut_page')."'>\n"
				. "<a href='#haut_page'>"
				. "<img src='"._DIR_IMG_PACK."spip_out.gif' border='0' align='absmiddle' />\n"
				. "</a></div>"
				. "<div style='clear:both;'></div>\n";
}

# generer liste des onglets
function onglets_actijour($actif) {
	# script => icone
	$pages=array('actijour_pg' => _DIR_IMG_ACJR."activ_jour.gif",
				'actijour_hier' => _DIR_IMG_ACJR."activ_hier.gif",
				'actijour_top' => "article-24.gif",
				'actijour_prev' => _DIR_IMG_ACJR."acjr_prev.gif",
				'actijour_connect' => "annonce.gif",
				'actijour_conf' => '' // icone : laisser vide !
				);
	$res='';
	foreach($pages as $exec => $icone) {
		$res.= onglet(_T('acjr:onglet_'.$exec),generer_url_ecrire($exec), $exec,($actif==$exec?$exec:''),$icone);
	}
	$aff=debut_onglet().$res.fin_onglet()."<p class='space_20'></p>";
	return $aff;
}

# signature plugin
function signature_plugin() {
	$aff="<p class='space_10'></p>"
		. debut_boite_info(true)
		. _T('acjr:signature_plugin',array('version'=>$GLOBALS['actijour_plug_version']))."\n"
		. fin_boite_info(true);
	return $aff;
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
produire date formatee : "d/m", moins 'n' jour(s) // ou son timestamp - 'n' jours
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


/*---------------------------------------------------------------------------*\
recense les sessions tmp/visites/ --> visites en attente de traitement
\*---------------------------------------------------------------------------*/
function calcul_prevision_visites() {
	# requis spip
	include_spip('inc/visites');
	
	# h. issue de ecrire/inc/visites.php : calculer_visites()
	// Initialisations
	$visites = ''; # visites du site
	$visites_a = array(); # tableau des visites des articles
	$referers = array(); # referers du site
	$referers_a = array(); # tableau des referers des articles
	$articles = array(); # articles vus dans ce lot de visites

	// charger un certain nombre de fichiers de visites,
	// et faire les calculs correspondants

	# h. passe à 5 minutes
	#Traiter jusqu'a 100 sessions datant d'au moins "5" minutes
	$sessions = preg_files(sous_repertoire(_DIR_TMP, 'visites'));
	$compteur = 100;
	$date_init = time()-5*60;

	foreach ($sessions as $item) {
		$tps_file=@filemtime($item);
		$temps[]=$tps_file;
		
		if ($tps_file < $date_init) {
			# lire fichier tmp/visites
			compte_fichier_visite($item,
				$visites, $visites_a, $referers, $referers_a, $articles);

			if (--$compteur <= 0)
				break;
		}
	}
	return array($temps,$visites,$visites_a);
}


?>

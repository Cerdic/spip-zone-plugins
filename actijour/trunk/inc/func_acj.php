<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 2.1 - 06/2011 - SPIP 2.1
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| D. Chiche . pour la maj 2.0
| T. Payet . pour la maj 2.1
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
	$q=sql_select("DATE_FORMAT(NOW(),'%d/%m/%Y %H:%i') as date_serveur");
	$r=sql_fetch($q);
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
	$query="sva.visites, ".
			"sa.id_rubrique, sa.id_secteur ".
			"FROM spip_visites_articles sva LEFT JOIN spip_articles sa ON sva.id_article = sa.id_article ".
			"WHERE sva.date='$date' ".
			"ORDER BY sa.id_secteur";
	$result = sql_select($query);
	$tab_rubart=array();

	while($r=sql_fetch($result)) {
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
	$q = sql_select("titre FROM spip_rubriques WHERE id_rubrique = $id");
	$r=sql_fetch($q);
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
	# requis spip
	include_spip('inc/visites');
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

	# h. passe 5 minutes
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

// FONCTIONS QUI ONT DISPARU DANS SPIP 2.0 Fichier ecrire/inc/statistiques.php
//


// Afficher les referers d'un article (ou du site)
//
// http://doc.spip.org/@aff_referers
function aff_referers ($result, $limit, $plus) {
	global $spip_lang_right, $source_vignettes;
	// Charger les moteurs de recherche
	$arr_engines = stats_load_engines();
	$nbvisites = array();
	$aff = '';
	while ($row = sql_fetch($result)) {
		$referer = interdire_scripts($row['referer']);
		$visites = $row['vis'];
		$tmp = "";
		
		$buff = stats_show_keywords($referer, $referer);
		
		if ($buff["host"]) {
			$numero = substr(md5($buff["hostname"]),0,8);
	
			$nbvisites[$numero] = $nbvisites[$numero] + $visites;

			if (strlen($buff["keywords"]) > 0) {
				$criteres = substr(md5($buff["keywords"]),0,8);
				if (!$lescriteres[$numero][$criteres])
					$tmp = " &laquo;&nbsp;".$buff["keywords"]."&nbsp;&raquo;";
				$lescriteres[$numero][$criteres] = true;
			} else {
				$tmp = $buff["path"];
				if (strlen($buff["query"]) > 0) $tmp .= "?".$buff['query'];
		
				if (strlen($tmp) > 30)
					$tmp = "/".substr($tmp, 0, 27)."...";
				else if (strlen($tmp) > 0)
					$tmp = "/$tmp";
			}

			if ($tmp)
				$lesreferers[$numero][] = "<a href='".quote_amp($referer)."'>".quote_amp(urldecode($tmp))."</a>" . (($visites > 1)?" ($visites)":"");
			else
				$lesliensracine[$numero] += $visites;
			$lesdomaines[$numero] = $buff["hostname"];
			$lesurls[$numero] = $buff["host"];
			$lesliens[$numero] = $referer;
		}
	}
	
	if (count($nbvisites) > 0) {
		arsort($nbvisites);

		$aff = '';
		for (reset($nbvisites); $numero = key($nbvisites); next($nbvisites)) {
			if ($lesdomaines[$numero] == '') next;

			$visites = pos($nbvisites);
			$ret = "\n<li style='clear:$spip_lang_right;'>";

			if (strlen($source_vignettes) > 0) $ret .= "\n<a href=\"http://".$lesurls[$numero]."\"><img src=\"$source_vignettes".rawurlencode($lesurls[$numero])."\"\nstyle=\"float: $spip_lang_right; margin-bottom: 3px; margin-left: 3px;\" alt='' /></a>";

			if ($visites > 5) $ret .= "<span style='color: red'>$visites "._T('info_visites')."</span> ";
			else if ($visites > 1) $ret .= "$visites "._T('info_visites')." ";
			else $ret .= "<span style='color: #999999'>$visites "._T('info_visite')."</span> ";
		
			if ($lesdomaines[$numero] == "(email)") {
				$aff .= $ret;
				$aff .= "<b>".$lesdomaines[$numero]."</b>";
			}
			else if ((count($lesreferers[$numero]) > 1) || ((substr(supprimer_tags($lesreferers[$numero][0]),0,1) != '/') && (count($lesreferers[$numero]) > 0))) {
				global $couleur_foncee;
				$referers = join ("</li><li>",$lesreferers[$numero]);
				$aff .= $ret;
				$aff .= "<a href='http://".quote_amp($lesurls[$numero])."'><span style='color: $couleur_foncee; font-weight: bold;'>".$lesdomaines[$numero]."</span></a>";
				if ($rac = $lesliensracine[$numero]) $aff .= " <span class='spip_x-small'>($rac)</span>";
				$aff .= "\n<ul style='font-size:x-small;'><li>$referers</li></ul>\n";
				$aff .= "</li></ul>\n<ul style='font-size:small;clear:$spip_lang_right;'>\n";
			} else {
				$aff .= $ret;
				$lien = $lesreferers[$numero][0];
				if (preg_match("@^(<a [^>]+>)([^ ]*)( \([0-9]+\))?@i", $lien, $regs)) {
					$lien = quote_amp($regs[1]).$lesdomaines[$numero].$regs[2];
					if (!strpos($lien, '</a>')) $lien .= '</a>';
				} else
					$lien = "<a href='http://".$lesdomaines[$numero]."'>".$lesdomaines[$numero]."</a>";
				$aff .= "<b>".quote_amp($lien)."</b>";
				$aff .= "</li>\n";
			}
		}

		if (preg_match(",</ul>\s*<ul style='font-size:small;'>\s*$,",$aff,$r))
		  $aff = substr($aff,0,(0-strlen($r[0])));
		if ($aff) $aff = "<ul>$aff</ul>";

		// Le lien pour en afficher "plus"
		if ($plus AND (spip_num_rows($result) == $limit)) {
			$aff .= "<div style='text-align:right;'><b><a href='$plus'>+++</a></b></div>";
		}
	}

	return $aff;
}

# charger les fichiers utiles dans SPIP
include_spip("genie/visites");
include_spip("inc/referenceurs");



?>

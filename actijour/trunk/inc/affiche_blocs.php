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
| generer les blocs d_infos
+--------------------------------------------+
*/

/*---------------------------------------------------------------------------*\
Elements de stats generales : visites, pages, global, moyenne gen.
\*---------------------------------------------------------------------------*/
function bloc_stats_generales(
			$prev_visites,$global_jour,$date_globaljour,$global_stats,
			$prim_jour_stats,$nb_jours_stats,
			$moy_global_stats,
			$cumul_vis_art_jour,$moy_pages_jour,
			$global_pages_stats,$moy_pag_vis,
			$date_max,$visites_max
			) {

	if(is_array($prev_visites)) {
		if($prev=intval($prev_visites[1])) {
			$prev = "&nbsp;+ ".$prev;
		}
		else { $prev=''; }
	}

	$aff='';
	
	$aff.= debut_cadre_relief("statistiques-24.gif",true)
		. "<span class='verdana3 bold'>"._T('acjr:nombre_visites_')."</span>\n"
		. "<br /><span class='verdana2'>".$date_globaljour."</span>"
		. "<div class='cell_info alter-fond'>"
		. _T('acjr:global_vis_jour', array('global_jour'=>$global_jour.$prev))
		. "</div>\n"
		. "<div class='cell_info'>"
		. _T('acjr:global_vis_global', array('global_stats'=>$global_stats))
		. "</div>\n";
	
	$aff.= "<div style='margin-top:8px;'>\n"
		. "<span class='verdana3 bold'>"._T('acjr:stats_actives_')."</span>\n"
		. "<div class='cell_info'>\n"
		. _T('acjr:depuis_le_prim_jour', array('prim_jour_stats'=>$prim_jour_stats))."<br />\n"
		. _T('acjr:soit_nbre_jours', array('nb_jours_stats' => $nb_jours_stats))."<br />\n"
		. _T('acjr:soit_moyenne_par_jour', array('moy_global_stats' => $moy_global_stats))."\n"
		. "</div>\n"
		. "</div>\n";
		
	$aff.= "<div style='margin-top:8px;'>\n"
		. "<span class='verdana3 bold'>"._T('acjr:pages_article_vues')."</span><br />\n"
		. "<div class='cell_info alter-fond'>\n"
		. _T('acjr:pages_art_cumul_jour', array('cumul_vis_art_jour' => $cumul_vis_art_jour))."<br />\n"
		. _T('acjr:pages_art_moyenne_jour', array('moy_pages_jour' => $moy_pages_jour))."<br />\n"
		. "</div>\n";
	
	$aff.= "<div class='cell_info'>\n"
		. _T('acjr:pages_global_cumul_jour', array('global_pages_stats' => $global_pages_stats))."<br />\n"
		. _T('acjr:pages_global_moyenne_jour', array('moy_pag_vis' => $moy_pag_vis))."<br />\n"
		. "</div>\n"
		. "</div>\n";
	
	$aff.= "<div style='margin-top:8px;'>\n"
		. "<span class='verdana3 bold'>"._T('acjr:grosse_journee_')."</span>\n"
		. "<div class='cell_info'>\n"
		. http_img_pack('puce-verte-breve.gif','ico','','')."&nbsp;"
		. _T('acjr:date_jour_maxi_vis', array('date_max' => $date_max, 'visites_max' => $visites_max))
		. "</div>\n"
		. "</div>\n";
	$aff.= fin_cadre_relief(true);

	return $aff;
}// bloc_stats_generales



/*---------------------------------------------------------------------------*\
contribution de jean-marc.viglino@ign.fr - 20/11/06
Derniere visite des "auteurs".
\*---------------------------------------------------------------------------*/
function auteurs_visite_jour() {
	global $couleur_claire,$connect_id_auteur;
	$nb_auteurs=$GLOBALS['actijour']['nbl_aut'];
	
	$q=sql_select("id_auteur, nom, DATE_FORMAT(en_ligne,'%d/%m/%y %H:%i') AS vu, statut 
			FROM spip_auteurs 
			WHERE statut IN ('0minirezo', '1comite') AND DATE_FORMAT(en_ligne,'%Y-%m-%d') = CURDATE() 
			ORDER BY en_ligne DESC 
			LIMIT 0,$nb_auteurs"
			);
	$ifond = 0;
	$aff='';
	
	$aff.= debut_cadre_relief("annonce.gif",true);
	
	$aff.="<table align='center' border='0' cellpadding='2' cellspacing='0' width='100%'>\n"
		. "<tr><td colspan='2' class='cart_titre verdana3 bold'>"._T('acjr:auteurs_connections')
		. "</td></tr>";
	
	while ($row = sql_fetch($q)) {
        $ifond = $ifond ^ 1;
        $couleur = ($ifond) ? $couleur_claire : '#FFFFFF';
    	#if($row['id_auteur']!=$connect_id_auteur) {// .. 05/05 on affiche tout le monde !
		$aff.= "<tr bgcolor='$couleur'>"
			. "<td width='5%' rowspan='2' style='vertical-align:top;'>\n"
			. bonhomme_statut($row)."</td>\n"
			. "<td width='95%'>"
			. "<div align='left' class='verdana2 bold'>"
			. "<a href='".generer_url_ecrire("auteur_infos","id_auteur=".$row['id_auteur'])."'>"
			. $row['nom']."</a>\n"
			. "</div></td></tr>\n"
			. "<tr bgcolor='$couleur'><td width='95%'>\n"
			. "<div align='right' class='verdana1'>".$row['vu']."</div>\n"
			. "</td></tr>\n";
		#}
    }
    $aff.= "</table>\n\n";

	$aff.= "\n<br /><div class='verdana1'>"._T('acjr:info_dernieres_connections',array('nb_aut'=>$nb_auteurs))."</div>\n";
	
	$aff.= fin_cadre_relief(true);
	
	return $aff;
} // auteurs_visite_jour



/*---------------------------------------------------------------------------*\
h..25/04/08
Date derniere visite de tous user-spip".
\*---------------------------------------------------------------------------*/
function tous_auteurs_date_passage() {
	global $couleur_claire,$connect_id_auteur;
	
	// fixer le nombre de ligne du tableau (tranche)
	$fl=$GLOBALS['actijour']['nbl_aut'];

	// recup $vl dans URL
	$dl=intval(_request('vl'));
	$dl=($dl+0);
	// valeur de tranche affichꥍ
	$nba1 = $dl+1;
	
	$p_st=_request('st');
	if(!$p_st) {
		$where_st = "statut IN ('0minirezo','1comite','6forum')";
		$p_st='tous';
	}
	else {
		$where_st = "statut = "._q($p_st);
	}

	$q=sql_select("SQL_CALC_FOUND_ROWS id_auteur, statut, nom,
						DATE_FORMAT(en_ligne,'%d/%m/%y %H:%i') AS vu "
				."FROM spip_auteurs "
				."WHERE $where_st "
				."ORDER BY en_ligne DESC,nom "
				."LIMIT $dl,$fl"
				);
				
    // recup nombre total d'entrees
	$nl= sql_select("FOUND_ROWS()");
	$found = @sql_fetch($nl);
	$nb_auteurs = $found['FOUND_ROWS()'];
	
	$ifond = 0;

	$aff='';
	
	# onglet select statut
	$lst_statut = array('tous','0minirezo','1comite','6forum');
	$script = _request('exec');
	
	$aff.=debut_onglet();
	foreach($lst_statut as $statut) {
		$aff.= onglet(_T('acjr:onglet_connect_'.$statut),
			generer_url_ecrire($script,'st='.($statut=='tous'?'':$statut)),
			$statut,
			($p_st==$statut?$statut:''),'');
	}
	$aff.=fin_onglet();


	# tableau
	#
	$aff.= debut_cadre_relief("annonce.gif",true);
	
	$aff.="<table align='center' border='0' cellpadding='2' cellspacing='0' width='100%'>\n"
		. "<tr><td colspan='3' class='verdana3 bold'>"._T('acjr:tous_date_connections')
		. "</td></tr>";
    # Tranches
	$aff.="<tr><td colspan='3' class='verdana3 bold'>";
	$aff.= "<div align='center' class='iconeoff verdana2 bold' style='clear:both;'>\n"
		. tranches_liste_art($nba1,$nb_auteurs,$fl)
		. "\n</div>\n";
	$aff.="</td></tr>";

	while ($row = sql_fetch($q)) {
        $ifond = $ifond ^ 1;
        $couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
    	
		$aff.= "<tr bgcolor='$couleur'>"
			. "<td width='5%'>\n"
			. bonhomme_statut($row)."</td>\n"
			. "<td width='75%'>"
			. "<a class='verdana2 bold' href='".generer_url_ecrire("auteur_infos","id_auteur=".$row['id_auteur'])."'>"
			. entites_html($row['nom'])."</a>\n"
			."<td width='20%'>\n"
			. "<div align='right' class='verdana1'>".$row['vu']."</div>\n"
			. "</td></tr>\n";
		
    }
    $aff.= "</table>\n\n";

	$aff.= fin_cadre_relief(true);

	return $aff;
} // tous_auteurs_date_passage


/******************************************************************************\
 * nombre auteurs (statuts std spip) connectes depuis 15 mn
\******************************************************************************/ 
function nbr_auteurs_enligne() {
	global $couleur_claire,$connect_id_auteur;
	$aff='';
	$aff.= debut_cadre_relief("annonce.gif",true);
// nombre d_auteurs depuis 15 mn ()
# inc/auth.php update-set en_ligne => NOW() : "moment" de session !
# voir ecrire/action:logout.php
# spip update-set 'en_ligne' datetime -15 mn au logout de session !!??!!
# aff' nbr corresp aux auteurs affiches par spip en bandeau sup !

    $q=sql_select("COUNT(DISTINCT id_auteur) AS nb, statut ".
			"FROM spip_auteurs ".
			"WHERE en_ligne > DATE_SUB( NOW(), INTERVAL 15 MINUTE) ".
			"AND statut IN ('0minirezo', '1comite', '6forum') ". // limite statuts spip (autres!)
			"AND id_auteur != $connect_id_auteur ".
			"GROUP BY statut"
			);
	
	if(sql_count($q)) {
		$aff.= _T("acjr:auteurs_en_ligne")."<br />\n";
		While($r=sql_fetch($q)) {
			if($r['statut'] == '0minirezo') { $stat=_T('acjr:abrv_administrateur'); }
			elseif ($r['statut']=='1comite') { $stat=_T('acjr:abrv_redacteur'); }
			elseif ($r['statut']=='6forum') { $stat=_T('acjr:abrv_visiteur'); }
			$aff.= $r['nb']." $stat<br />\n";
		}
	
	}
	else {
		$aff.= _T("acjr:aucun_auteur_en_ligne")."\n";
	}
	$aff.= fin_cadre_relief(true);
	
	return $aff;
}

/*---------------------------------------------------------------------------*\
 Lister Articles du jour.
\*---------------------------------------------------------------------------*/
function liste_articles_jour($date_jour,$nb_art_visites_jour,$date_maj_art,$prev_visites='') {
	global $couleur_foncee,$couleur_claire;
	
	// fixer le nombre de ligne du tableau (tranche)
	$fl=$GLOBALS['actijour']['nbl_art'];

	// recup $vl dans URL
	$dl=intval(_request('vl'));
	$dl=($dl+0);
		
	//
	// requete liste article du jour
	$q=sql_select("sva.id_article, sva.date, sva.visites as visites_j, 
			sa.titre, sa.visites, sa.popularite, sa.statut 
			FROM spip_visites_articles sva 
			LEFT JOIN spip_articles sa ON sva.id_article = sa.id_article 
			WHERE sva.date='$date_jour' 
			ORDER BY visites_j DESC LIMIT $dl,$fl");

	$nbart=sql_count($q);
	

	$aff = debut_cadre_relief("cal-jour.gif",true);	

	// bouton relance brut de la page
	// en attendant de passer a jquery !
	if($date_jour == date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")))) {
		$aff.= "<div class='bouton_maj'>\n"
			. "<a href='".generer_url_ecrire("actijour_pg")."'>"
			. http_img_pack('puce-blanche.gif','ico','',_T('acjr:mise_a_jour'))."</a>\n"
			. "</div>\n";
	}
	
	// texte entete
	if(empty($date_maj_art)) {
		# La date du jour passé en 1er arg (jour, hier ...)
		$tbdate = recup_date($date_jour);
		$date_maj_art = date('d/m/y',mktime(0,0,0,$tbdate[1],$tbdate[2],$tbdate[0]));
	}
	$aff.= "<div class='verdana3'>"
		. _T('acjr:entete_tableau_art_jour', array(
						'nb_art_visites_jour'=>$nb_art_visites_jour, 
						'aff_date_now'=> ' - '.$date_maj_art))
		. "</div>\n";

	// affichage tableau
	if (sql_count($q)) {
		// valeur de tranche affichꥉ
		$nba1 = $dl+1;
		//	
		$ifond = 0;
	
		// Presenter valeurs de la tranche de la requete
		$aff.= "<div align='center' class='iconeoff verdana2 bold' style='clear:both;'>\n"
			. tranches_liste_art($nba1,$nb_art_visites_jour,$fl)
			. "\n</div>\n";

		// tableau
		$aff.= "<table align='center' border='0' cellpadding='1' cellspacing='1' width='100%'>\n"
			. "<tr bgcolor='$couleur_foncee' class='head_tbl'>\n"
			. "<td width='7%'>"._T('acjr:numero_court')."</td>\n"
			. "<td width='65%'>"._T('acjr:titre_article')."</td>\n"
			. "<td width=9%>"._T('acjr:visites_jour')."</td>\n"
			. "<td width=11%>"._T('acjr:total_visites')."</td>\n"
			. "<td width=8%>"._T('acjr:popularite')."</td>\n"
			. "</tr>\n";

		// corps du tableau
		while ($row=sql_fetch($q)) {
			$visites_a = $row['visites'];
			$visites_j = $row['visites_j'];			
			$id_art = $row['id_article'];
			$titre = $row['titre'];
			$etat = $row['statut'];			
			// round sur popularit鍊			$pop = round($row['popularite']);
			// Le total-visites de l'article
			#$tt_visit = $visit + $ipv;

			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
	
			$aff.= "<tr bgcolor='$couleur'><td width='7%'>\n"
				. "<div align='right' class='verdana2'>"
				. affiche_lien_graph($id_art, $titre, $etat, 'spip')
				. "</div>\n</td>"
				. "<td width='65%'>\n"
				. "<div align='left' class='verdana1' style='margin-left:5px;'><b>"
				. affiche_lien_graph($id_art, $titre, $etat)
				. "</b></div></td>\n"
				. "<td width='9%'>\n"
				. "<div align='center' class='verdana2'><b>$visites_j</b></div></td>\n"
				. "<td width='11%'>\n"
				. "<div align='right' class='verdana1' style='margin-right:3px;'><b>$visites_a</b></div></td>\n"
				. "<td width='8%'>\n"
				. "<div align='center' class='verdana1'>$pop</div>\n"
				. "</td></tr>\n";
		}
		$aff.= "</table>";
	}
	// aucun articles
	else {
		$aff.= "<div align='center' class='iconeoff bold' style='clear:both;'>"
			. _T('acjr:aucun_article_visite')."</div><br />\n";
	}
	$aff.= visites_pre_traitement($prev_visites);
	$aff.= fin_cadre_relief(true);
	
	return $aff;
} // liste_articles_jour



/*---------------------------------------------------------------------------*\
Visites et Nbr articles /j. sur les 8 derniers jours + moyenne.
\*---------------------------------------------------------------------------*/
function articles_visites_semaine($nbj='8') {
	# $nbj --> nombre de jours du tableau 
	# par defaut 8 ; tableau de la semaine dans actijour_pg.php
	
	global $couleur_foncee,$couleur_claire;
	
	$q=sql_select(
		"DATE_FORMAT(sva.date,'%d/%m') AS date_fr, 
		COUNT(sva.id_article) AS nbart, sv.visites, sva.date 
		FROM spip_visites_articles sva LEFT JOIN spip_visites sv ON sva.date = sv.date 
		WHERE sva.date > DATE_SUB(NOW(),INTERVAL $nbj DAY) 
		GROUP BY sva.date 
		ORDER BY sva.date"
	);

	# construit table semaine
	$semainier=array();
	while ($row = sql_fetch($q)) {
		$key = strtotime($row['date']);
		$semainier[$key]['date_fr']=$row['date_fr'];
		$semainier[$key]['nbart']=$row['nbart'];
		$semainier[$key]['visites']=$row['visites'];
		$i++;	
	}
	reset($semainier);
#echo "<pre>"; print_r($semainier); echo "</pre>";

	# combler les jours sans visite
	if(count($semainier)<$nbj) {
		for($i=0; $i<$nbj; $i++) {
			if(!array_key_exists($ante=ante_date_jour($i),$semainier)) {
				$semainier[$ante]['date_fr']=ante_date_jour($i,true);
				$semainier[$ante]['nbart']='0';
				$semainier[$ante]['visites']='0';
			}
		}
	}
#echo "2 - <pre>"; print_r($semainier); echo "</pre> - 2";
	ksort($semainier);
	
	#
	# affiche bloc stats semaine
	#
	$aff = debut_cadre_relief("cal-semaine.gif",true);
	# prepa tableau
	$aff.= "<span class='verdana3 bold'>"._T('acjr:huit_derniers_jours')."</span>\n"
		. "<table width='100%' border='0' cellpadding='1' cellspacing='0'>\n"
		. "<tr><td><div class='cell_huit_t' style='background-color:$couleur_foncee;'>"
		. _T('acjr:jour')."</div>\n"
		. "<div class='cell_huit_m'>"._T('acjr:nombre_art')."</div>\n"
		. "<div class='cell_huit_p' style='background-color:$couleur_claire;'>"
		. _T('acjr:visites')."</div>\n</td>";
	
	# les colonnes
	$add=0;
	foreach($semainier as $day => $vals) {
		$aff.= "<td><div class='cell_huit_t' style='background-color:$couleur_foncee;'>"
			. $vals['date_fr']."</div>\n"
			. "<div class='cell_huit_m'>".$vals['nbart']."</div>\n"
			. "<div class='cell_huit_p' style='background-color:$couleur_claire;'>".$vals['visites']
			. "</div></td>\n";
		//calcul moyenne de la "periode"
		$add+=$vals['visites'];
	}
	# moyenne visite semaine
	$moysem=round($add/8);
	
	# derniere colonne affichage : moyenne periode
	$aff.= "<td>"
		. "<div class='cell_huit_t'>&nbsp;</div>"
		. "<div class='cell_huit_m' style='background-color:$couleur_claire;'>"
		. _T('acjr:moyenne_c')
		. "<div class='cell_huit_p' style='background-color:$couleur_claire;'>".$moysem."</div>"
		. "</td></tr></table>";

	$aff.= fin_cadre_relief(true);

	return $aff;
} // articles_visites_semaine


/*---------------------------------------------------------------------------*\
visites du jour reparties dans secteur/rubrique
\*---------------------------------------------------------------------------*/
function tableau_visites_rubriques($date) {
	global $couleur_claire;
	
	$tab_rubart = rubriques_du_jour($date);
	
	$aff='';
	$aff.= debut_cadre_relief('rubrique-24.gif',true);
	$aff.= "<div class='cart_titre_bold verdana3'>"._T('acjr:repartition_visites_secteurs')."</div>";
	
	if($tab_rubart) {
		// add visites
		$nbr=0;
		foreach($tab_rubart as $s => $c) { $nbr+=$c['vis'];	}
		
		$ifond = 0;
	
		$aff.= "<table cellpadding='2' cellspacing='0' width='100%' border='0'>\n";
		
		foreach($tab_rubart as $sect => $cat) {
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
			$s_titre = typo(info_rubrique($sect));
			$prct_s = round(($cat['vis']/$nbr)*100, 1);
			
			$aff.= "<tr bgcolor='$couleur'>\n<td colspan='2'>"
				. http_img_pack('secteur-12.gif','ico','align=\'absmiddle\'','')."&nbsp;<b>"
				. supprimer_numero($s_titre)."</b></td>\n"
				. "<td width='8%'><div align='right'><b>"
				. $cat['vis']."</b></div></td>\n"
				. "<td width='12%'><div class='verdana2 bold' align='right'>"
				. $prct_s."%</div></td>\n"
				. "</tr>\n";
			
			if($cat['rub']) {
				foreach($cat['rub'] as $idr => $vis) {
					$r_titre = typo(info_rubrique($idr));
					$prct_r = round(($vis/$nbr)*100, 1);
					
					$aff.= "<tr bgcolor='$couleur'>\n<td width='2%'>&nbsp;</td>"
						. "<td>"
						. http_img_pack('rubrique-12.gif','ico','align=\'absmiddle\'','')."&nbsp;"
						. supprimer_numero($r_titre)."</td>\n"
						. "<td width='8%'><div align='right'>"
						. $vis."</div></td>\n"
						. "<td width='12%'><div class='verdana1' align='right'>"
						. $prct_r."%</div></td>\n</tr>\n";
				}
			}
		}
		$aff.= "</table>\n";
	}
	$aff.= fin_cadre_relief(true);
	
	return $aff;
}


/*---------------------------------------------------------------------------*\
Affichage des referers du jour (orig. spip inc/statistiques)
\*---------------------------------------------------------------------------*/
function liste_referers_jour($jour) {
	# nombre de referers a afficher
	$limit = intval($limit);	//secu
	if ($limit == 0) $limit = 100;
	
	$q = sql_select("referer, visites_jour AS vis 
					FROM spip_referers 
					WHERE date = \"$jour\"
					ORDER BY visites_jour 
					DESC 
					LIMIT $limit");
	
	$aff = debut_cadre_trait_couleur("referers-24.gif",true)
		. "<div class='cart_titre verdana3 bold'>"._T('acjr:liens_entrants_jour')."</div>"
		. aff_referers($q, $limit,'')
		. "<div style='clear:both;'></div>"
		. fin_cadre_trait_couleur(true);
	
	return $aff;
}


/*---------------------------------------------------------------------------*\
visites mensuelles du site en chiffres (jauge) sur n mois (18)
\*---------------------------------------------------------------------------*/
function visites_mensuelles_chiffres($global_jour) {
	global $couleur_foncee,$couleur_claire;
	
	$periode = date('m/y');		// mois /annee en cours (format de $date)
	$dday = date('j');			// numero du jour
	$nb_mois = $GLOBALS['actijour']['nbl_mensuel'];	// nombre mois affiche

	$requete="FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%m') AS d_mois, 
			FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%y') AS d_annee, 
			FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%m/%y') AS date_unix, 
			SUM(visites) AS visit_mois 
			FROM spip_visites WHERE date > DATE_SUB(NOW(),INTERVAL 2700 DAY) 
			GROUP BY date_unix ORDER BY date DESC LIMIT 0,$nb_mois";

	// calcul du $divis : MAX de visites_mois
	$r=sql_select($requete);
	$tblmax = array();
	while ($rmx = @sql_fetch($r)) {
		$tblmax[count($tblmax)+1]=$rmx['visit_mois'];
	}
	reset ($tblmax);

	if(count($tblmax)==0) { $tblmax[]=1; }
	$divis = max($tblmax)/100;
		
	//le tableau a jauges horizontales
	$aff.= debut_cadre_relief("",true)
		. "<span class='arial2'>"._T('acjr:entete_tableau_mois',array('nb_mois'=>$nb_mois))."\n</span>"
		. "<table width='100%' cellpadding='2' cellspacing='0' border='0' class='arial2'>\n"
		. "<tr><td align='left'>"._T('acjr:mois_pipe')
		. "</td><td width='50%'>"._T('acjr:moyenne_mois')."</td>\n"
		. "<td><b>"._T('acjr:visites')."</b></td></tr>";

	$ra=sql_select($requete);		
	while ($row = sql_fetch($ra)) {
		$val_m = $row['d_mois'];
		$val_a = $row['d_annee'];
		$date = $row['date_unix'];
		$visit_mois = $row['visit_mois'];
		$idefix='';
		
		//nombre de jours du mois $mois
		$mois = mktime( 0, 0, 0, $val_m, 1, $val_a ); 
		$nbr_jours = intval(date("t",$mois));
		
		// nombre de jours, moyenne, si mois en cours
		if ($date != $periode ) {
			$nbj = $nbr_jours;
			$moy_mois = floor($visit_mois/$nbj);
			$totvisit = $visit_mois;
		}
		else {
			$nbj =  ($dday==1)? $dday : $dday-1;
			$moy_mois = floor(($visit_mois-$global_jour)/$nbj);
			$totvisit = $visit_mois-$global_jour;
			$idefix="*";
		}
		$totvisit=number_format($totvisit, 0, ',', '.');
		
		//longeur jauge (ne tiens pas compte du jour en cour)
		$long = floor($visit_mois/$divis);
		
		// couleur de jauge pour mois le plus fort
		$color_texte='';
		if ($long==100) {
			$coul_jauge=$couleur_foncee;
			$color_texte = "style='color:#ffffff;'";
		}
		else { $coul_jauge=$couleur_claire; }

		$aff.= "<tr><td class='arial2' colspan='3'>"
			. "<div style='position:relative; z-index:1; width:100%;'>"
			. "<div class='cell_info_mois'>$date</div>"
			. "<div class='cell_moymens'>$moy_mois</div>"
			. "<div class='cell_info_tot' $color_texte><b>$totvisit</b>$idefix</div>"
			. "</div>";	
		# barre horiz 
		$aff.= "<div class='fond_barre'>\n"
			. "<div style='width:".$long."%; height:11px; background-color:".$coul_jauge.";'></div>\n"
			. "</div>\n"
			. "</td></tr>\n";		
		
	}	
	$aff.= "<tr><td colspan='3'><span class='verdana1'>"
		. _T('acjr:pied_tableau_mois')."</span></td></tr>\n"
		. "</table></span>\n";
	$aff.= fin_cadre_relief(true);
	
	return $aff;

}// visites_mensuelles_chiffres


/*---------------------------------------------------------------------------*\
recense les sessions tmp/visites/ - bloc -> liste_articles_jour()
\*---------------------------------------------------------------------------*/
function visites_pre_traitement($prev_visites) {

	# pour page hier
	if(!is_array($prev_visites)) return;

	$temps=$prev_visites[0];
	$visites=$prev_visites[1];
	$visites_a=$prev_visites[2];
	
	if(count($temps)) { sort($temps); }
	
	$aff='';
	if($nb_articles = count($visites_a)) {
		$heure_f = date('H\hi',$temps[0]);
		$date_f = date('d/m/Y',$temps[0]);
		
		$aff.= "<div align='center' class='iconeoff verdana2 bold' style='clear:both;'>\n"
			. _T('acjr:depuis_date_visites_pg',
					array(
						'heure'=>$heure_f,
						'date'=>$date_f==date('d/m/Y')?'':'('.$date_f.')',
						'nb_visite'=>$visites,
						'nb_articles'=>$nb_articles
						))
			. "\n</div>\n";
	}
	return $aff;
}


/*---------------------------------------------------------------------------*\
classement des 10 articles les + visites sur 8 / 30 jours
\*---------------------------------------------------------------------------*/
function topten_articles_periode($periode) {
	global $couleur_claire;
	
	if($periode=='8') {
		$top = $GLOBALS['actijour']['nbl_topsem'];
	}
	elseif($periode=='30') {
			$top = $GLOBALS['actijour']['nbl_topmois'];
	}
	else { $top='10'; }

	$q=sql_select("sva.id_article, SUM(sva.visites) AS volume, 
				MAX(sva.visites) AS picvis, sa.statut, sa.titre, sa.visites 
				FROM spip_visites_articles sva 
				LEFT JOIN spip_articles sa ON sva.id_article=sa.id_article 
				WHERE sa.statut='publie' AND sva.date > DATE_SUB(NOW(),INTERVAL $periode DAY) 
				GROUP BY sva.id_article 
				ORDER BY volume DESC 
				LIMIT 0,$top"
				);
	$ifond = 0;
	$aff='';
	
	$aff.= debut_cadre_relief("article-24.gif",true)
	. "<table align='center' border='0' cellpadding='2' cellspacing='1' width='100%'>\n"
	. "<tr><td colspan='5' class='cart_titre_bold verdana3'>"._T('acjr:top_ten_article_'.$periode.'_j')
	. "</td></tr>";
	
	$aff.="<tr class='legend_topten'><td>A</td><td>B</td><td>C</td><td>D</td><td>E</td></tr>\n";
	
	while ($row = sql_fetch($q)) {
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? $couleur_claire : '#FFFFFF';
		
		$aff.= "<tr bgcolor='$couleur'><td width='7%'>\n"
			. "<div align='right' class='verdana2'>"
			. affiche_lien_graph($row['id_article'],$row['titre'],$row['statut'],'spip')
			. "</div></td>\n"
			. "<td width='70%'>\n<div align='left' class='verdana2'><b>"
			. affiche_lien_graph($row['id_article'],$row['titre'],$row['statut'])
			. "</b></div></td>\n"
			. "<td width='6%'>\n<div align='right' class='verdana1' style='margin-right:3px;'><b>"
			. $row['volume']."</b></div></td>\n"
			. "<td width='7%'>\n<div align='right' class='verdana1' style='margin-right:3px;'><b>"
			. $row['picvis']."</b></div>\n</td>\n"
			. "<td width='10%'>\n<div align='right' class='verdana1' style='margin-right:3px;'><b>"
			. $row['visites']."</b></div>\n</td>\n</tr>\n";
	}
	$aff.= "</table>";
	$aff.= fin_cadre_relief(true);

	return $aff;
} // topten_articles_semaine



/*---------------------------------------------------------------------------*\
classement des 10 articles les + visites
\*---------------------------------------------------------------------------*/
function topten_articles_global() {
	global $couleur_claire;
	$top = $GLOBALS['actijour']['nbl_topgen'];
	
	$q=sql_select("id_article, titre, statut, visites 
				FROM spip_articles WHERE statut='publie' 
				ORDER BY visites DESC LIMIT 0,$top"
				);
	$ifond = 0;
	$aff='';
	
	$aff.= debut_cadre_relief("article-24.gif",true)
	. "<table align='center' border='0' cellpadding='2' cellspacing='1' width='100%'>\n"
	. "<tr><td colspan='5' class='cart_titre_bold verdana3'>"._T('acjr:top_ten_article_gen')
	. "</td></tr>";
	
	$aff.="<tr class='legend_topten'><td>A</td><td>B</td><td>E</td></tr>";
	
	while ($row = sql_fetch($q)) {
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? $couleur_claire : '#FFFFFF';
		$aff.="<tr bgcolor='$couleur'><td width='7%'>\n"
			. "<div align='right' class='verdana2'>"
			. affiche_lien_graph($row['id_article'],$row['titre'],$row['statut'],'spip')
			. "</div></td>\n"
			. "<td width='80%'>\n<div align='left' class='verdana2'><b>"
			. affiche_lien_graph($row['id_article'],$row['titre'],$row['statut'])
			. "</b></div></td>\n"
			. "<td width='13%'>\n<div align='right' class='verdana1' style='margin-right:3px;'><b>"
			. $row['visites']."</b></div>\n</td></tr>\n";
	}
	$aff.= "</table>";
	$aff.= fin_cadre_relief(true);
	
	return $aff;
} // topten_articles_global


/*---------------------------------------------------------------------------*\
signatures petitions du jour
\*---------------------------------------------------------------------------*/
function signatures_petitions_jour($date) {
	$r=sql_select("ss.id_article, 
				COUNT(DISTINCT ss.id_signature) AS nb_sign_pet, sa.titre 
				FROM spip_signatures ss 
				LEFT JOIN spip_articles sa ON ss.id_article = sa.id_article 
				WHERE DATE_FORMAT(ss.date_time,'%Y-%m-%d') = '$date' 
				GROUP BY ss.id_article"
				);

	$aff = debut_cadre_relief("",true)
		. "<div class='bouton_droite icone36'>\n"
		. "<a href='".generer_url_ecrire("controle_petition")."' title='"
		. _T('acjr:voir_suivi_petitions')."'>\n"
		. http_img_pack('suivi-petition-24.gif','ico','','')."</a>\n"
		. "</div>\n\n"
		. "<br /><span class='arial2 bold'>"._T('acjr:signatures_petitions')."</span>\n"
		. "<div style='clear:both;'></div>\n"
		. "<ol class='verdana1' style='padding-left:30px;'>\n";
	
	if (sql_count($r)) {
		while ($t = sql_fetch($r)) {
			$aff.="<li value='".$t['id_article']."'>"
				. $t['titre']." : <b>".$t['nb_sign_pet']."</b>"
				. "</li>\n";
		}
	}
	else {
		$aff.="<li value='0'>"._T('acjr:aucune_moment')."</li>";
	}
	$aff.="</ol>\n";
	$aff.= fin_cadre_relief(true);
	return $aff;
}



/*---------------------------------------------------------------------------*\
* articles creer/modifer, d un jour donne.. h.26/04/08
* double affichage :
	aff_det => affiche tableau complet -> page actijour_art
	!aff_det => encart dans actijour_pg.
\*---------------------------------------------------------------------------*/
function articles_creer_modifer_jour($date_jour,$aff_det=false) {
	global $couleur_foncee,$couleur_claire;
	
	$tbl_jour=recup_date($date_jour);
	$deb_jour=date('YmdHis',mktime(0,0,0,$tbl_jour[1],$tbl_jour[2],$tbl_jour[0]));
	$fin_jour=date('YmdHis',mktime(0,0,0,$tbl_jour[1],$tbl_jour[2]+1,$tbl_jour[0]));
	
	$jour=date('d/m/Y',mktime(0,0,0,$tbl_jour[1],$tbl_jour[2],$tbl_jour[0]));
    
	$aff="";
    
	#
	$q=sql_select("id_article, titre, statut, id_rubrique, 
						DATE_FORMAT(date,'%d/%m/%Y %H:%i') as f_date, 
						DATE_FORMAT(maj,'%d/%m/%Y %H:%i') as f_maj, 
						DATE_FORMAT(date_redac,'%d/%m/%Y %H:%i') as f_date_redac, 
						DATE_FORMAT(date_modif,'%d/%m/%Y %H:%i') as f_date_modif "
	            ."FROM spip_articles "
				."WHERE maj > $deb_jour AND maj < $fin_jour "
				."ORDER BY maj DESC"
				);
	$nb_art=sql_count($q);
	
	if($aff_det) {
		# tableau de page actijour_art
		
		$aff.= debut_cadre_relief("",true)
			. "<div class='cell_huit_p'>"._T('acjr:articles_modifies_crees_jour',array('jour'=>$jour))."</div>";
		
		if($nb_art) {
			$ifond = 0;
			
			$aff.= "<table align='center' border='0' cellpadding='2' cellspacing='0' width='100%'>\n"
			. "<tr bgcolor='$couleur_foncee' class='head_tbl'>\n"
			. "<td width='7%'>"._T('acjr:numero_court')."</td>\n"
			. "<td width='50%'>"._T('acjr:titre_article')."</td>\n"
			. "<td width=25%>"._T('acjr:date_maj')."</td>\n"
			. "</tr>\n";
			
			while($r=sql_fetch($q)) {
				$ifond = $ifond ^ 1;
				$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
				$url_art=generer_url_ecrire('articles','id_article='.$r['id_article']);
				
				$aff.= "<tr bgcolor='$couleur'>"
				. "<td width='7%'>\n"
				. "<div align='right' class='verdana2'>"
				. $r['id_article']
				. "</div>\n</td>"
				. "<td width='50%'>\n"
				. "<div class='verdana3' style='margin-left:5px;'><a href='".$url_art."'>"
				. http_img_pack("puce-".puce_statut($r['statut']).".gif",'ico','','')."&nbsp;&nbsp;"
				. entites_html(supprimer_numero($r['titre']))
				. "</a></div>\n"
				. "</td>\n"
				. "<td width='25%'>\n"
				. "<div align='center' class='verdana2'><b>".$r['f_maj']."</b></div></td>\n"
				. "</tr>\n"
				. "<tr bgcolor='$couleur'><td colspan='3'>"
				. "<div align='right' class='verdana1'>"
						._T('acjr:date_publication_dpt').$r['f_date']."<br />"
						._T('acjr:date_redaction_dpt').$r['f_date_redac']."</div>\n"
				. "</tr>\n"
				;
  			}
            $aff.="</table>";
		}
		else {
			$aff.=_T('acjr:aucun_article_cree');
		}
		$aff.=fin_cadre_relief(true);
 	}
	else {
		# encart sur actijour_pg
		$url=generer_url_ecrire('actijour_art','d='.$date_jour);
		$icone=http_img_pack('article-24.gif','ico','','');

		$aff.= debut_cadre_relief('',true);
		$aff.= "<div class='bouton_droite icone36'>\n"
				. "<a href='".$url."' title='"._T('acjr:voir_details')."'>\n"
				. $icone."</a>\n"
				. "</div>\n";
		$aff.="<span class='arial2 bold'>"._T('acjr:articles_crees_modifies')."</span><br />";
		#
		if($nb_art) {
			$aff.= http_img_pack(_DIR_IMG_ACJR."mini_art.png",'ico','','')."&nbsp;".$nb_art;
		}
		else { $aff.= _T('acjr:aucun'); }
		
		$aff.="<div class='nettoyeur'></div>";
		$aff.= fin_cadre_relief(true);

	}
    
	return $aff;
} // articles_creer_modifer_jour



/*---------------------------------------------------------------------------*\
nombre de message forum public (identif. GAFoSPIP/SPIPBB)
\*---------------------------------------------------------------------------*/
function activite_forum_site($nbr_post_jour) {

	$pluged = unserialize($GLOBALS['meta']['plugin']);
	
	if(is_array($pluged['GAF'])) {
		$icone = "<img src='"._DIR_PLUGINS.$pluged['GAF']['dir']."/img_pack/gaf_ico-24.gif' border='0'>";
		$url = generer_url_ecrire("gaf_admin");
		$plugin='GAFoSPIP';
	}
	elseif(is_array($pluged['SPIPBB'])) {
		$icone = "<img src='"._DIR_PLUGINS.$pluged['SPIPBB']['dir']."/img_pack/spipbb-24.png' border='0'>";
		$url = generer_url_ecrire("spipbb_admin");
		$plugin='SpipBB';
	}
	else {
		$icone = http_img_pack('suivi-forum-24.gif','ico','','');
		$url = generer_url_ecrire("controle_forum");
	}
	
	$aff= debut_cadre_relief('',true);
	$aff.= "<div class='bouton_droite icone36'>\n"
		. "<a href='".$url."' title='"
		. ($plugin ? _T('acjr:voir_plugin').$plugin : _T('acjr:voir_suivi_forums'))."'>\n"
		. $icone."</a>\n"
		. "</div>\n"
		. "<span class='bold'>"._T('acjr:forums')."</span><br />"
		;
				
	// nbr posts du jour sur vos forum
	if($nbr_post_jour) { $aff.= $nbr_post_jour."&nbsp;"; }
	else { $aff.= _T('acjr:aucun'); }
	
	if($nbr_post_jour>1) { $aff.= _T('acjr:messages'); }
	else { $aff.= _T('acjr:message'); }
	
	$aff.="<div class='nettoyeur'></div>";
	$aff.= fin_cadre_relief(true);
	
	return $aff;
}



/*---------------------------------------------------------------------------*\
Telechargement de fichiers du jour (via DW2)
\*---------------------------------------------------------------------------*/
function telechargement_dw2_jour($date) {
	$plugins= unserialize($GLOBALS['meta']['plugin']);
	$aff='';
	
	# si DW2 present ?
	if(is_array($plugins['DW2'])) {
		$icone = "<img src='"._DIR_PLUGINS.$plugins['DW2']['dir']."/img_pack/telech.gif' border='0'>";
		$url = generer_url_ecrire("dw2_admin");
		$plugin='DW2';
		
		$q=sql_select("SUM(telech) AS tot FROM spip_dw2_stats WHERE date="._q($date) );
		$r=sql_fetch($q);
		
		$aff.= debut_cadre_relief('',true);
		$aff.= "<div class='bouton_droite icone36'>\n"
				. "<a href='".$url."' title='"._T('acjr:voir_plugin').$plugin."'>\n"
				. $icone."</a>\n"
				. "</div>\n";
		$aff.="<span class='arial2 bold'>"._T('acjr:telechargements_dpt')."</span><br />";
		#
		if($r['tot']) { $aff.= $r['tot']; }
		else { $aff.= _T('acjr:aucun'); }
		
		$aff.="<div class='nettoyeur'></div>";
		$aff.= fin_cadre_relief(true);
	}
	return $aff;
}


/*---------------------------------------------------------------------------*\
 formulaire choix date affichee sur page actijour_hier
\*---------------------------------------------------------------------------*/
function formulaire_periode($date_jour,$retour,$prim_an_stats) {
	include_spip('inc/date');

	$date=recup_date($date_jour);
	
	$aff=debut_boite_info(true)
		. "<form action ='".generer_url_ecrire($retour)."' method='post'>"
		. "<div style='padding:3px;' align='center' ><b>"
		. _T('acjr:jour_affiche_dpt')."</b><br /><br />"
		
		. afficher_jour($date[2], "name='jour' size='1' class='fondl' ", true)
		. afficher_mois($date[1], "name='mois' size='1' class='fondl' ", true)."<br /><br />"
		. acjr_afficher_annee($date[0], "name='annee' size='1' class='fondl' ",$prim_an_stats)."<br /><br />"

		. "<input type='submit' class='fondo' value='". _T('acjr:text_bouton_afficher')."' />"
		. "</div>"
		. "</form>"
		. fin_boite_info(true);

	return $aff;
}

function acjr_afficher_annee($annee, $attributs, $debut=1996) {
	$res = ($annee > 1996) ? '' : my_sel($annee,$annee,$annee);
	for ($i=$debut; $i <= date("Y"); $i++) { // hack h. : for ($i=$debut; $i < date("Y")+3; $i++) {
		$res .= my_sel($i,$i,$annee);
	}
	return "<select $attributs>\n$res</select>\n";
}


?>

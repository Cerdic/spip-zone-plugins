<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.52 - 08/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Stats hier : articles, referers, forums, petitions.
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');


function exec_actijour_hier() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

//
// function requises ...
include_spip("inc/func_acj");


	// date jour courte sql spip
	$date_hier = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
	$aff_date_hier = date('d/m/y', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
	
	//
	# tbl articles vistes hier 
	$tbl_art_jour = articles_visites_jour($date_hier);
	
	# nbre articles visites hier
	$nb_art_visites_jour = count($tbl_art_jour);
	
	# total visites hier
	$global_jour = global_jour($date_hier);
	
	# nbr posts hier sur vos forum
	$nbr_post_jour = nombre_posts_forum($date_hier);
		
//
// affichage
//
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('acjr:titre_actijour'), "suivi", "actijour_pg");
	echo "<br>";
gros_titre(_T('acjr:titre_actijour'));


// Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques)
	{
	echo _T('avis_non_acces_page');
	echo fin_page();
	exit;
	}


//
// onglets
echo debut_onglet().
onglet(_T('acjr:page_activite'), generer_url_ecrire("actijour_pg"), 'page_activite', '', _DIR_PLUGIN_ACTIJOUR."/img_pack/activ_jour.gif").
onglet(_T('acjr:page_hier'), generer_url_ecrire("actijour_hier"), 'page_hier', 'page_hier', _DIR_PLUGIN_ACTIJOUR."/img_pack/activ_hier.gif").
onglet(_T('acjr:page_topten'), generer_url_ecrire("actijour_top"), 'page_topten', '', "article-24.gif").
fin_onglet();



debut_gauche();

//
// Nombre Visites hier
debut_cadre_relief("statistiques-24.gif");
	echo "<span class='verdana3 bold'>"._T('acjr:nombre_visites_')."</span>\n";
	echo "<div class='cell_info alter-fond'>"._T('acjr:global_vis_hier', array('global_jour'=>$global_jour))."</div>\n";
fin_cadre_relief();


// mess. forum public + GAFoSPIP est installe ici ?
debut_cadre_relief("forum-public-24.gif");
	if(defined('_DIR_PLUGIN_GAFOSPIP')) {
		echo "<div class='bouton_droite icone36'>\n".
			"<a href='".generer_url_ecrire("gaf_admin")."' title='"._T('acjr:voir_gafospip')."'>\n".
			"<img src='"._DIR_IMG_GAF."gaf_ico-24.gif' border='0'></a>\n".
			"</div>\n";
	}
	else {
		echo "<div class='bouton_droite icone36'>\n".
			"<a href='".generer_url_ecrire("controle_forum")."' title='"._T('acjr:voir_suivi_forums')."'>\n".
			http_img_pack('suivi-petition-24.gif','ico','','')."</a>\n".
			"</div>\n";
	}
	echo "<br />";
	// nbr posts du jour sur vos forum
	if($nbr_post_jour) { echo $nbr_post_jour."&nbsp;"; }
	else { echo _T('acjr:aucun'); }
	if($nbr_post_jour>1) { $ps=_T('acjr:s'); }
	echo _T('acjr:message', array('ps' =>$ps));
fin_cadre_relief();


//
// signatures pétitions hier
//
$q_pet="SELECT ss.id_article, COUNT(DISTINCT ss.id_signature) AS nb_sign_pet, sa.titre ".
		"FROM spip_signatures ss LEFT JOIN spip_articles sa ON ss.id_article = sa.id_article ".
		"WHERE DATE_FORMAT(ss.date_time,'%Y-%m-%d') = '$date_hier' ".
		"GROUP BY ss.id_article";
$r_pet=spip_query($q_pet);

debut_cadre_relief("suivi-petition-24.gif");
	echo "<div class='bouton_droite icone36'>\n".
			"<a href='".generer_url_ecrire("controle_petition")."' title='"._T('acjr:voir_suivi_petitions')."'>\n".
			http_img_pack('suivi-petition-24.gif','ico','','')."</a>\n".
			"</div>\n";
	echo "\n";
	echo "<br /><span class='arial2 bold'>"._T('acjr:signatures_petitions')."</span>\n";
	echo "<div style='clear:both;'></div>\n";
	echo "<ol class='verdana1' style='padding-left:30px;'>\n";
	if (spip_num_rows($r_pet)) {
		while ($t_pet = spip_fetch_array($r_pet)) {
			echo "<li value='".$t_pet['id_article']."'>".$t_pet['titre']." : <b>".$t_pet['nb_sign_pet']."</b></li>\n";
		}
	}
	else {
		echo "<li value='0'>"._T('acjr:aucune_moment')."</li>";
	}
	echo "</ol>\n";
fin_cadre_relief();



creer_colonne_droite();

//
// bon là ... j'assume mon gag ! sisi !
debut_boite_info();
	echo _T('acjr:signature_plugin')."\n";
fin_boite_info();

echo "<br />";



debut_droite();

//
// Lister Articles du jour.
//
	// fixer le nombre de ligne du tableau (tranche)
		$fl=20;

	// recup $vl dans URL
		$dl=($_GET['vl']+0);
		
	//
	// requete liste article du jour
	$query2="SELECT sva.id_article, sva.date, sva.visites as visites_j, ".
			"sa.titre, sa.visites, sa.popularite, sa.statut ".
			"FROM spip_visites_articles sva LEFT JOIN spip_articles sa ON sva.id_article = sa.id_article ".
			"WHERE sva.date='$date_hier' ".
			"ORDER BY visites_j DESC LIMIT $dl,$fl";
	$result2 = spip_query($query2);
	$nbart=spip_num_rows($result2);


debut_cadre_relief("cal-jour.gif");	

	// entete

	echo "<div class='verdana3'>"._T('acjr:entete_tableau_art_hier', array('nb_art_visites_jour'=>$nb_art_visites_jour, 'aff_date_now'=>$aff_date_hier))."</div>\n";

	// affichage tableau
	if (spip_num_rows($result2))
		{
		// valeur de tranche affichée	
		$nba1 = $dl+1;
		//	
		$ifond = 0;
	
		// Présenter valeurs de la tranche de la requête
		echo "<div align='center' class='iconeoff' style='clear:both;'><span class='verdana2 bold'>\n";
		tranches_liste_art($nba1,$nb_art_visites_jour,$fl);
		echo "\n</span></div>\n";

		// Création du tableau
		// entête ...
		echo "<table align='center' border='0' cellpadding='1' cellspacing='1' width='100%'>\n
			<tr bgcolor='$couleur_foncee' class='head_tbl'>\n".
				"<td width='7%'>"._T('acjr:numero_court')."</td>\n".
				"<td width='65%'>"._T('acjr:titre_article')."</td>\n".
				"<td width=9%>"._T('acjr:visites_jour')."</td>\n".
				"<td width=11%>"._T('acjr:total_visites')."</td>\n".
				"<td width=8%>"._T('acjr:popularite')."</td>\n".
			"</tr>\n";

		// corps du tableau
		while ($b_row=spip_fetch_array($result2))
			{
			$visites_a = $b_row['visites'];
			$visites_j = $b_row['visites_j'];			
			$id_art = $b_row['id_article'];
			$trt_art = $b_row['titre'];
			$etat = $b_row['statut'];			
			// round sur popularité
			$pop = round($b_row['popularite']);
			// Le total-visites de l'article
			#$tt_visit = $visit + $ipv;

			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
	
		echo "
		<tr bgcolor='$couleur'><td width='7%'>\n
			<div align='right' class='verdana2'>".
			affiche_lien_graph($id_art, $trt_art, $etat, 'spip').
			"</div>\n
		</td><td width='65%'>\n
            <div align='left' class='verdana1' style='margin-left:5px;'><b>".
			affiche_lien_graph($id_art, $trt_art, $etat).
			"</b></div>\n
        </td><td width='9%'>\n
            <div align='center' class='verdana2'><b>$visites_j</b></div>\n
        </td><td width='11%'>\n
            <div align='right' class='verdana1' style='margin-right:3px;'><b>$visites_a</b></div>\n
        </td><td width='8%'>\n
            <div align='center' class='verdana1'>$pop</div>\n
        </td></tr>\n";
			}
			
		echo "</table>";
		}
		// aucun articles
		else {
			echo "<div align='center' class='iconeoff bold' style='clear:both;'>".
				_T('acjr:aucun_article_visite')."</div><br />\n";
		}

fin_cadre_relief();


//
// Visites par secteur/rubrique
//
debut_cadre_relief('rubrique-24.gif');
	echo "<div class='cart_titre verdana3 bold'>"._T('acjr:repartition_visites_secteurs')."</div>";
	tableau_visites_rubriques($date_hier);
fin_cadre_relief();




//
// Affichage des referers du jour
//

// nombre de referers a afficher
$limit = intval($limit);	//secu
if ($limit == 0) $limit = 100;

// afficher quels referers ?
$jour = "veille";


$q_ref = spip_query("SELECT referer, visites_$jour AS vis FROM spip_referers WHERE visites_$jour>0 ORDER BY vis DESC LIMIT $limit");
 
debut_cadre_trait_couleur("referers-24.gif");
	echo "<div class='cart_titre verdana3 bold'>"._T('acjr:liens_entrants_jour')."</div>";
	echo aff_referers($q_ref, $limit,'');
fin_cadre_trait_couleur();



echo fin_gauche(), fin_page();

}
?>

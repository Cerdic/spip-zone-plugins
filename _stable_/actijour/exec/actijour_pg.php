<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.52 - 08/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Stats globales : pages, articles, visites.
| Divers liens, avertissements ...
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');


function exec_actijour_pg() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

//
// function requises ...
include_spip("inc/func_acj");


	// date jour courte sql spip
	$date_auj = date('Y-m-d');

//
// diverses requetes/valeurs de stats
//
	# nombre de jours depuis debut stats
	$nb_jours_stats = nb_jours_stats();

	# date debut stats
	$prim_jour_stats = prim_jour_stats();

	# total visites du jour
	$global_jour = global_jour($date_auj);
	
	# Total visite depuis debut stats $tt_absolu
	$global_stats = global_stats();
	
	# moyenne /jour depuis debut stats
	$moy_global_stats = ceil($global_stats/$nb_jours_stats);

	# jour maxi-visites depuis debut stats
	$tbl_date_vis_max = max_visites_stats();
	$visites_max = $tbl_date_vis_max[0];
	$date_max = $tbl_date_vis_max[1];

	# Cumul pages visitees
	$global_pages_stats = global_pages_stats();
		
	# moyenne page 'vues'/jour depuis debut
	$moy_pag_vis = @round($global_pages_stats/$global_stats,1);

	# tbl articles vistes du jour 
	$tbl_art_jour = articles_visites_jour($date_auj);
	
	# nbre articles visites jour
	$nb_art_visites_jour = count($tbl_art_jour);
	
	# cumul des visites des art du jour $cvaj
	$cumul_vis_art_jour = array_sum($tbl_art_jour);
	
	# moy. art visites du jour $moy_pg_j
	$moy_pages_jour = @round($cumul_vis_art_jour/$global_jour,1);
	
	# nbr posts du jour sur vos forum
	$nbr_post_jour = nombre_posts_forum($date_auj);
/*
	# nouveaux inscrits visiteur, redacteur, admin
	$nouveaux_inscrits = inscrit_auteur($date_auj);
*/


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
onglet(_T('acjr:page_activite'), generer_url_ecrire("actijour_pg"), 'page_activite', 'page_activite', _DIR_PLUGIN_ACTIJOUR."/img_pack/activ_jour.gif").
onglet(_T('acjr:page_hier'), generer_url_ecrire("actijour_hier"), 'page_hier', '', _DIR_PLUGIN_ACTIJOUR."/img_pack/activ_hier.gif").
onglet(_T('acjr:page_topten'), generer_url_ecrire("actijour_top"), 'page_topten', '', "article-24.gif").
fin_onglet();



debut_gauche();

//
// Nombre Visites, Global Site, Jour maxi ...
debut_cadre_relief("statistiques-24.gif");
	echo "<span class='verdana3 bold'>"._T('acjr:nombre_visites_')."</span>\n";
	echo "<div class='cell_info alter-fond'>"._T('acjr:global_vis_jour', array('global_jour'=>$global_jour))."</div>\n";
	echo "<div class='cell_info'>"._T('acjr:global_vis_global', array('global_stats'=>$global_stats))."</div>\n";

	echo "<div style='margin-top:8px;'>\n";
		echo "<span class='verdana3 bold'>"._T('acjr:stats_actives_')."</span>\n";
		echo "<div class='cell_info'>\n";
		echo _T('acjr:depuis_le_prim_jour', array('prim_jour_stats'=>$prim_jour_stats))."<br />\n";
		echo _T('acjr:soit_nbre_jours', array('nb_jours_stats' => $nb_jours_stats))."<br />\n";
		echo _T('acjr:soit_moyenne_par_jour', array('moy_global_stats' => $moy_global_stats))."\n";
		echo "</div>\n";
	echo "</div>\n";
	
	echo "<div style='margin-top:8px;'>\n";
		echo "<span class='verdana3 bold'>"._T('acjr:pages_article_vues')."</span><br />\n";
		echo "<div class='cell_info alter-fond'>\n";
		echo _T('acjr:pages_art_cumul_jour', array('cumul_vis_art_jour' => $cumul_vis_art_jour))."<br />\n";
		echo _T('acjr:pages_art_moyenne_jour', array('moy_pages_jour' => $moy_pages_jour))."<br />\n";
		echo "</div>\n";
	
		echo "<div class='cell_info'>\n";
		echo _T('acjr:pages_global_cumul_jour', array('global_pages_stats' => $global_pages_stats))."<br />\n";
		echo _T('acjr:pages_global_moyenne_jour', array('moy_pag_vis' => $moy_pag_vis))."<br />\n";
		echo "</div>\n";
	echo "</div>\n";
	
	echo "<div style='margin-top:8px;'>\n";
		echo "<span class='verdana3 bold'>"._T('acjr:grosse_journee_')."</span>\n";
		echo "<div class='cell_info'>\n".
			http_img_pack('puce-verte-breve.gif','ico','','')."&nbsp;".
			_T('acjr:date_jour_maxi_vis', array('date_max' => $date_max, 'visites_max' => $visites_max)).
			"</div>\n";
	echo "</div>\n";
fin_cadre_relief();


// ouvrir popup stats-spip d'un article choisi ( par son N°)
debut_cadre_enfonce(_DIR_PLUGIN_ACTIJOUR."/img_pack/activ_jour.gif");
	echo "\n<span class='verdana3 bold'>"._T('acjr:afficher_stats_art')."</span><br />\n";
	echo "<form action='".generer_url_ecrire("actijour_graph")."' method='post' id='graph' onsubmit=\"actijourpop('graph');\">\n";
	echo "<br />"._T('acjr:numero_');
	echo "<input type='text' name='id_article' size='4' maxlength='10'>&nbsp;&nbsp;\n";
	echo "<input type='submit' value='"._T('acjr:voir')."' class='fondo'>\n";
	echo "</form>\n";
fin_cadre_enfonce();

// + ouvrir popup du bargraph-spip : visites du trimestre 	
debut_cadre_enfonce("");
	echo "<div class='bouton_droite'>".
		"<a href=\"".generer_url_ecrire("actijour_graph")."\" target=\"graph_article\" 
		onclick=\"javascript:window.open(this.href, 'graph_article', 
		'width=530,height=450,menubar=no,scrollbars=yes'); return false;\" 
		title=\""._T('acjr:bargraph_trimestre_popup')."\">\n".
		http_img_pack('cal-mois.gif','ico','','')."\n</a>\n</div>\n";
	echo "<span class='verdana3'>"._T('acjr:graph_trimestre')."</span>";
fin_cadre_enfonce();


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
// Sur une contribution de jean-marc.viglino@ign.fr - 20/11/06
// Derniere visite des "auteurs".
debut_cadre_relief("annonce.gif");
	$query="SELECT id_auteur, nom, DATE_FORMAT(en_ligne,'%d/%m/%y %H:%i') AS vu, statut ".
			"FROM spip_auteurs ".
			"WHERE statut IN ('0minirezo', '1comite') ".
			"ORDER BY en_ligne DESC LIMIT 0,20";
	$result = spip_query($query);
	$ifond = 0;
	echo "<table align='center' border='0' cellpadding='2' cellspacing='0' width='100%'>\n";
	echo "<tr><td colspan='2' class='cart_titre verdana3 bold'>"._T('acjr:dernieres_connections');
	echo "</td></tr>";
	while ($row = spip_fetch_array($result)) {
        $ifond = $ifond ^ 1;
        $couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
    	if($row[id_auteur]!=$connect_id_auteur) {
		echo "<tr bgcolor='$couleur'>
			<td width='5%' rowspan='2' style='vertical-align:top;'>\n".bonhomme_statut($row)."</td>\n".
            "<td width='95%'>
            <div align='left' class='verdana2 bold'>".
			"<a href='".generer_url_ecrire("auteur_infos","id_auteur=".$row[id_auteur])."'>".$row[nom]."</a>\n
           </div></td></tr>\n".
		   "<tr bgcolor='$couleur'><td width='95%'>\n".
            #"<div align='right' class='verdana1'>".date('d M. H:i',strtotime($row[en_ligne]))."</div>\n".
            "<div align='right' class='verdana1'>".$row[vu]."</div>\n".
			"</td></tr>\n";
		}
    }
    echo "</table>\n\n";

	// nombre d_admin, redac et visiteurs depuis 15 mn
    $qcc="SELECT COUNT(DISTINCT id_auteur) AS nb, statut 
			FROM spip_auteurs 
			WHERE en_ligne > DATE_SUB( NOW(), INTERVAL 15 MINUTE) 
			AND statut IN ('0minirezo', '1comite', '6forum')
			AND id_auteur != $connect_id_auteur 
			GROUP BY statut";
	$rcc = spip_query($qcc);
	if(spip_num_rows($rcc)) {
		echo "<div class='cell_info'>"._T("acjr:auteurs_en_ligne")."<br />\n";
		While ($row = spip_fetch_array($rcc)) {
			if($row['statut'] == '0minirezo') { $stat='Admin.'; }
			elseif ($row['statut']=='1comite') { $stat='Redac.'; }
			elseif ($row['statut']=='6forum') { $stat='Visit.'; }
			echo $row['nb']." $stat<br />\n";
		}
		echo "</div>\n";	
	}
	else {
		echo "<div class='cell_info'>"._T("acjr:aucun_auteur_en_ligne")."</div>\n";
	}
	echo "\n<br /><div class='verdana1'>"._T('acjr:info_dernieres_connections')."</div>\n";
fin_cadre_relief();

/*
# pas de date d'enreg. 
# 'maj' est modifie a chaque passage de l_auteur
debut_cadre_relief("");
	echo "Nouveaux inscrits<br />";
	foreach($nouveaux_inscrits as $id => $ta) {
		echo "<a href='".generer_url_ecrire("auteurs_edit", "id_auteur=".$id)."'>";
		if($ta[2]=="0minirezo") { $ico_auteur="admin-12.gif"; $statut="Admin"; }
		elseif($ta[2]=="1comite") { $ico_auteur="redac-12.gif"; $statut="Redacteur"; }
		else { $ico_auteur="visit-12.gif"; $statut="Visiteur"; }
		echo "<img src='"._DIR_IMG_PACK.$ico_auteur."' title='".$statut."' />&nbsp;";
		echo $ta[1]." - ".$ta[0]."</a><br />";
	}
fin_cadre_relief();
*/

// Listage des Pages Rubrique
# plus de reference : arret de spip_visites_temp

// Listage des Pages Brèves
# plus de reference : arret de spip_visites_temp



creer_colonne_droite();

//
// visites mensuelles du site en chiffres
//

	$periode = date('m/y');	// mois /année en cours (format de $date)
	$dday = date('j');			// numéro du jour			

	$querym="SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%m') AS d_mois, ".
			"FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%y') AS d_annee, ".
			"FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%m/%y') AS date_unix, ".
			"SUM(visites) AS visit_mois ".
			"FROM spip_visites WHERE date > DATE_SUB(NOW(),INTERVAL 2700 DAY)".
			"GROUP BY date_unix ORDER BY date DESC LIMIT 0,18";

	// calcul du $divis : MAX de visites_mois
	$r_max=spip_query($querym);
		$tblmax = array();
		while ($rmx = @spip_fetch_array($r_max))
			{
			$tblmax[count($tblmax)+1]=$rmx['visit_mois'];
			}
		reset ($tblmax);
		#h.28/02/07 
		if(count($tblmax)==0) { $tblmax[]=1; }
		#
		$divis = max($tblmax)/100;
		
	//le tableau à barres horizontales
	debut_cadre_relief("");
		echo "<span class='arial2'>"._T('acjr:entete_tableau_mois')."\n</span>";
		echo "<table width='100%' cellpadding='2' cellspacing='0' border='0' class='arial2'>\n";
		echo "<tr><td width='19%' align='right'>"._T('acjr:mois_pipe')."</td><td><b>"._T('acjr:visites')."</b></td>\n";
		echo "<td width='50%'>"._T('acjr:moyenne_mois')."</td></tr>";

	$resultm=spip_query($querym);		
	while ($rowm = spip_fetch_array($resultm))
		{
		$val_m = $rowm['d_mois'];
		$val_a = $rowm['d_annee'];
		$date = $rowm['date_unix'];
		$visit_mois = $rowm['visit_mois'];
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
			$nbj =  ($dday==1)? $dday : $dday-1; // h.1/12 .. correct divis par 0 //
			$moy_mois = floor(($visit_mois-$global_jour)/$nbj);
			$totvisit = $visit_mois-$global_jour;
			$idefix="*";
		}
		
		//longeur jauge (ne tiens pas compte du jour en cour)
		$long = floor($visit_mois/$divis);
		
		// couleur de jauge pour mois le plus fort
		if ($long==100) {
			#$couljauge="jauge-rouge.gif";
			$coul_jauge=$couleur_foncee;
		}
		else {
			#$couljauge="jauge-vert.gif";
			$coul_jauge=$couleur_claire;
		}
				
		echo "<tr><td width='19%'><span class='arial1'>$date </span>|</td><td style='text-align:right;'><b>$totvisit</b>$idefix</td>\n";
		echo "<td width='50%' align='left' valign='middle' class='arial2'>\n";
		
		echo "<div style='position:relative; z-index:1; width:100%;'>";
			echo "<div class='cell_moymens'>$moy_mois</div>";
		echo "</div>";	
		/*
		# barre horiz. facon 1.3
		echo "<div style='height:8px; background-image:url("._DIR_IMG_PACK."jauge-fond.gif); background-repeat:no-repeat;' ";
		echo "<img src='"._DIR_IMG_PACK."$couljauge' width='$long%' height='8px' border='0'></div>\n";
		*/
		# barre horiz facon 1.4
		echo "<div class='fond_barre'>\n";
			echo "<div style='width:".$long."px; height:10px; background-color:".$coul_jauge.";'></div>\n";
		echo "</div>\n";
		echo "</td></tr>\n";
		}	
	echo "<tr><td colspan='3'><span class='verdana1'>"._T('acjr:pied_tableau_mois')."</span></td></tr>\n".
			"</table></span>\n";
fin_cadre_relief("");



//
// signatures pétitions aujourd'hui
//
$q_pet="SELECT ss.id_article, COUNT(DISTINCT ss.id_signature) AS nb_sign_pet, sa.titre ".
		"FROM spip_signatures ss LEFT JOIN spip_articles sa ON ss.id_article = sa.id_article ".
		"WHERE DATE_FORMAT(ss.date_time,'%Y-%m-%d') = '$date_auj' ".
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


//
// bon là ... j'assume mon gag ! sisi !
debut_boite_info();
	echo _T('acjr:signature_plugin')."\n";
fin_boite_info();

echo "<br />";

debut_boite_info();
	echo "\n<a href='".generer_url_ecrire("info")."'>"._T('acjr:page_phpinfo')."</a>\n";
fin_boite_info();

echo "<br />";

debut_boite_info();
	// version de mysql du serveur :
	$vers = mysql_query("select version()");
	$rep = mysql_fetch_array($vers);
	echo "MySQL v. ".$rep[0];
fin_boite_info();




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
			"WHERE sva.date='$date_auj' ".
			"ORDER BY visites_j DESC LIMIT $dl,$fl";
	$result2 = spip_query($query2);
	$nbart=spip_num_rows($result2);
	

debut_cadre_relief("cal-jour.gif");	

	// bouton relance brut de la page
	// en attendant de passer a jquery !
	echo "<div class='bouton_maj'>\n".
			"<a href='".generer_url_ecrire("actijour_pg")."'>".
			http_img_pack('puce-blanche.gif','ico','',_T('acjr:mise_a_jour'))."</a>\n".
			"</div>\n";
	
	// entete
	$aff_date_now = date('d/m/y');
	echo "<div class='verdana3'>"._T('acjr:entete_tableau_art_jour', array('nb_art_visites_jour'=>$nb_art_visites_jour, 'aff_date_now'=>$aff_date_now))."</div>\n";

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
	tableau_visites_rubriques($date_auj);
fin_cadre_relief();



//
// Affichage des referers du jour
//

// nombre de referers a afficher
$limit = intval($limit);	//secu
if ($limit == 0) $limit = 100;

// afficher quels referers ?
$where = "visites_jour>0";
$vis = "visites_jour";
$table_ref = "spip_referers";

$q_ref = spip_query("SELECT referer, $vis AS vis FROM $table_ref WHERE $where ORDER BY $vis DESC");

debut_cadre_trait_couleur("referers-24.gif");
	echo "<div class='cart_titre verdana3 bold'>"._T('acjr:liens_entrants_jour')."</div>";
	echo aff_referers($q_ref, $limit,'');
fin_cadre_trait_couleur();




//
// Visites et Nbr articles /j. sur les 8 derniers jours + moyenne.
//

$query="SELECT DATE_FORMAT(sva.date,'%d/%m') AS date_fr, COUNT(sva.id_article) AS nbart, sv.visites ".
		"FROM spip_visites_articles sva LEFT JOIN spip_visites sv ON sva.date = sv.date ".
		"WHERE sva.date > DATE_SUB(NOW(),INTERVAL 8 DAY) GROUP BY sva.date ORDER BY sva.date";
$result=spip_query($query);
	
debut_cadre_relief("cal-semaine.gif");
	// prépa tableau
	echo "<span class='verdana3 bold'>"._T('acjr:huit_derniers_jours')."</span>\n";
	echo "<table width='100% border='0' cellpadding='1' cellspacing='0'><tr><td>\n";
	echo "<div class='cell_huit_t' style='background-color:$couleur_foncee;'>"._T('acjr:jour')."</div>\n";
	echo "<div class='cell_huit_m'>"._T('acjr:nombre_art')."</div>\n";
	echo "<div class='cell_huit_p' style='background-color:$couleur_claire;'>"._T('acjr:visites')."</div>\n</td>";
	
	// les colonnes
	$add=0;
	while ($row = spip_fetch_array($result))
		{
		echo "<td><div class='cell_huit_t' style='background-color:$couleur_foncee;'>".
			 $row['date_fr']."</div>\n";
		echo "<div class='cell_huit_m'>".$row['nbart']."</div>\n";
		echo "<div class='cell_huit_p' style='background-color:$couleur_claire;'>".$row['visites']."</div></td>\n";
		//calcul moyenne de la "période"
		$add+=$row['visites'];
		$moysem=round($add/8);
		}
	// dernière colonne affichage : moyenne période
	echo "<td>";
	echo "<div class='cell_huit_t'>&nbsp;</div>";
	echo "<div class='cell_huit_m' style='background-color:$couleur_claire;'>"._T('acjr:moyenne_c')."";
	echo "<div class='cell_huit_p' style='background-color:$couleur_claire;'>".$moysem."</div>";
	echo "</td></tr></table>";
fin_cadre_relief();



echo fin_gauche(), fin_page();

} // fin fonction

?>

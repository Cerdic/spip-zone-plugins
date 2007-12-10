<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.53 - 12/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| Stats globales : pages, articles, visites.
| Divers liens, avertissements ...
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');


function exec_actijour_pg() {

# elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

#
# function requises ...
#
include_spip("inc/func_acj");
include_spip("inc/requetes_stats");
include_spip('inc/affiche_blocs');


# date jour courte sql spip
	$date_auj = date('Y-m-d');

#
# diverses requetes/valeurs de stats
#
	# nombre de jours depuis debut stats
	$nb_jours_stats = nb_jours_stats();

	# date debut stats
	$prim_jour_stats = prim_jour_stats();

	# total visites du jour
	$gj = global_jour($date_auj);
	$global_jour = $gj['visites'];
	$date_globaljour = $gj['date'];
	
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
	
	# derniere maj visites articles
	$date_maj_art = derniere_maj_articles($date_auj);
	
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


#
# affichage
#
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('acjr:titre_actijour'), "suivi", "actijour_pg");
echo "<a name='haut_page'></a>";



# Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
	exit;
}



debut_gauche();
	entete_page(_T('acjr:titre_actijour'));

/*---------------------------------------------------------------------------*\
Elements de stats generales : visites, pages, global, moyenne gen.
\*---------------------------------------------------------------------------*/
	echo bloc_stats_generales(
			$global_jour,$date_globaljour,$global_stats,
			$prim_jour_stats,$nb_jours_stats,
			$moy_global_stats,
			$cumul_vis_art_jour,$moy_pages_jour,
			$global_pages_stats,$moy_pag_vis,
			$date_max,$visites_max
			);


/*---------------------------------------------------------------------------*\
ouvrir popup stats-spip d'un article choisi ( par son num spip )
\*---------------------------------------------------------------------------*/
	debut_cadre_enfonce(_DIR_PLUGIN_ACTIJOUR."/img_pack/activ_jour.gif");
	echo "\n<span class='verdana3 bold'>"._T('acjr:afficher_stats_art')."</span><br />\n";
	echo "<form action='".generer_url_ecrire("actijour_graph")."' method='post' id='graph' onsubmit=\"actijourpop('graph');\">\n";
	echo "<br />"._T('acjr:numero_');
	echo "<input type='text' name='id_article' size='4' maxlength='10'>&nbsp;&nbsp;\n";
	echo "<input type='submit' value='"._T('acjr:voir')."' class='fondo'>\n";
	echo "</form>\n";
	fin_cadre_enfonce();

 
/*---------------------------------------------------------------------------*\
ouvrir popup du bargraph-spip : visites du trimestre 
\*---------------------------------------------------------------------------*/
	debut_cadre_enfonce("");
	echo "<div class='bouton_droite'>".
		"<a href=\"".generer_url_ecrire("actijour_graph")."\" target=\"graph_article\" 
		onclick=\"javascript:window.open(this.href, 'graph_article', 
		'width=530,height=450,menubar=no,scrollbars=yes'); return false;\" 
		title=\""._T('acjr:bargraph_trimestre_popup')."\">\n".
		http_img_pack('cal-mois.gif','ico','','')."\n</a>\n</div>\n";
	echo "<span class='verdana3'>"._T('acjr:graph_trimestre')."</span>";
	fin_cadre_enfonce();



/*---------------------------------------------------------------------------*\
contribution de jean-marc.viglino@ign.fr - 20/11/06
Derniere visite des "auteurs".
\*---------------------------------------------------------------------------*/
	echo auteurs_date_passage();


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

/*---------------------------------------------------------------------------*\
visites mensuelles du site en chiffres (jauge) sur n mois (18)
\*---------------------------------------------------------------------------*/
	echo visites_mensuelles_chiffres($global_jour);



/*---------------------------------------------------------------------------*\
nombre de message forum public (identif. GAFoSPIP/SPIPBB)
\*---------------------------------------------------------------------------*/
	echo activite_forum_site($nbr_post_jour);


/*---------------------------------------------------------------------------*\
signatures petitions aujourd'hui
\*---------------------------------------------------------------------------*/
	echo signatures_petitions_jour($date_auj);


/*---------------------------------------------------------------------------*\
Telechargement de fichiers du jour (via DW2)
\*---------------------------------------------------------------------------*/
	echo telechargement_dw2_jour($date_auj);


/*---------------------------------------------------------------------------*\
atteindre page php info
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	debut_boite_info();
		echo "\n<a href='".generer_url_ecrire("info")."'>"._T('acjr:page_phpinfo')."</a>\n";
	fin_boite_info();


/*---------------------------------------------------------------------------*\
version de mysql du serveur :
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	debut_boite_info();
		$vers = mysql_query("select version()");
		$rep = mysql_fetch_array($vers);
		echo "MySQL v. ".$rep[0];
	fin_boite_info();


/*---------------------------------------------------------------------------*\
scoty signe son mefait
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	debut_boite_info();
		echo _T('acjr:signature_plugin')."\n";
	fin_boite_info();


debut_droite();


/*---------------------------------------------------------------------------*\
Onglets pages sup.
\*---------------------------------------------------------------------------*/
echo debut_onglet().
onglet(_T('acjr:page_activite'), generer_url_ecrire("actijour_pg"), 'page_activite', 'page_activite', _DIR_PLUGIN_ACTIJOUR."img_pack/activ_jour.gif").
onglet(_T('acjr:page_hier'), generer_url_ecrire("actijour_hier"), 'page_hier', '', _DIR_PLUGIN_ACTIJOUR."img_pack/activ_hier.gif").
onglet(_T('acjr:page_topten'), generer_url_ecrire("actijour_top"), 'page_topten', '', "article-24.gif").
fin_onglet();



/*---------------------------------------------------------------------------*\
Lister Articles du jour
\*---------------------------------------------------------------------------*/
	echo liste_articles_jour($date_auj,$nb_art_visites_jour,$date_maj_art);


/*---------------------------------------------------------------------------*\
Visites du jour par secteur/rubrique
\*---------------------------------------------------------------------------*/
	echo tableau_visites_rubriques($date_auj);


/*---------------------------------------------------------------------------*\
Visites et Nbr articles /j. sur les 8 derniers jours + moyenne.
\*---------------------------------------------------------------------------*/
	echo articles_visites_semaine();


/*---------------------------------------------------------------------------*\
Affichage des referers du jour (orig. spip inc/statistiques)
\*---------------------------------------------------------------------------*/
	echo liste_referers_jour('jour');



# retour haut de page
bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin fonction

?>

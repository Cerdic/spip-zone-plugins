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
include_spip("inc/actijour_init");
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
	
	# les visites non-traitees (tmp/visites)
	$prev_visites = calcul_prevision_visites();



#
# affichage
#
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('actijour:titre_actijour'), "suivi", "actijour_pg");
echo "<a name='haut_page'></a>";



# V�rifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
	exit;
}



echo debut_gauche("", true);
	echo entete_page();

/*---------------------------------------------------------------------------*\
Elements de stats generales : visites, pages, global, moyenne gen.
\*---------------------------------------------------------------------------*/
	echo bloc_stats_generales(
			$prev_visites,$global_jour,$date_globaljour,$global_stats,
			$prim_jour_stats,$nb_jours_stats,
			$moy_global_stats,
			$cumul_vis_art_jour,$moy_pages_jour,
			$global_pages_stats,$moy_pag_vis,
			$date_max,$visites_max
			);


/*---------------------------------------------------------------------------*\
ouvrir popup stats-spip d'un article choisi ( par son num spip )
\*---------------------------------------------------------------------------*/
	echo debut_cadre_couleur(_DIR_IMG_ACJR."activ_jour.gif", true);
	echo "\n<span class='verdana3 bold'>"._T('actijour:afficher_stats_art')."</span>\n"
	. "<form action='".generer_url_ecrire("actijour_graph")."' method='post' id='graph' onsubmit=\"actijourpop('graph');\">\n"
	. _T('actijour:numero_')
	. "<input type='text' name='id_article' size='4' maxlength='10' class='fondl'>&nbsp;&nbsp;\n"
	. "<input type='submit' value='"._T('actijour:voir')."' class='fondo'>\n"
	. "</form>\n";
	echo fin_cadre_couleur(true);

 
/*---------------------------------------------------------------------------*\
ouvrir popup du bargraph-spip : visites du trimestre 
\*---------------------------------------------------------------------------*/
	echo debut_cadre_couleur("", true);
	echo "<div class='bouton_droite'>".
		"<a href=\"".generer_url_ecrire("actijour_graph")."\" target=\"graph_article\" 
		onclick=\"javascript:window.open(this.href, 'graph_article', 
		'width=530,height=450,menubar=no,scrollbars=yes'); return false;\" 
		title=\""._T('actijour:bargraph_trimestre_popup')."\">\n".
		http_img_pack('cal-mois.gif','ico','','')."\n</a>\n</div>\n";
	echo "<span class='verdana3'>"._T('actijour:graph_trimestre')."</span>";
	echo fin_cadre_couleur(true);



/*---------------------------------------------------------------------------*\
base contribution de jean-marc.viglino@ign.fr - 20/11/06
Modif 05/05/08 .. -> les connectes du jour !
\*---------------------------------------------------------------------------*/
	echo auteurs_visite_jour();

/*---------------------------------------------------------------------------*\
  nombre auteurs connectes depuis 15mn - 22/04/08
\*---------------------------------------------------------------------------*/
	echo nbr_auteurs_enligne();


// Listage des Pages Rubrique
# plus de reference : arret de spip_visites_temp

// Listage des Pages Br�ves
# plus de reference : arret de spip_visites_temp



echo creer_colonne_droite("", true);
echo "<p class='space_20'></p>";

/*---------------------------------------------------------------------------*\
visites mensuelles du site en chiffres (jauge) sur n mois (18)
\*---------------------------------------------------------------------------*/
	echo visites_mensuelles_chiffres($global_jour);


/*---------------------------------------------------------------------------*\
Affichage articles creer/modifier ce jour ((h.30/04/08)
\*---------------------------------------------------------------------------*/
	echo articles_creer_modifer_jour($date_auj);


/*---------------------------------------------------------------------------*\
nombre de message forum public (identif. GAFoSPIP/SPIPBB)
\*---------------------------------------------------------------------------*/
	echo activite_forum_site($nbr_post_jour);


/*---------------------------------------------------------------------------*\
Telechargement de fichiers du jour (via DW2)
\*---------------------------------------------------------------------------*/
	echo telechargement_dw2_jour($date_auj);


/*---------------------------------------------------------------------------*\
signatures petitions aujourd'hui
\*---------------------------------------------------------------------------*/
	echo signatures_petitions_jour($date_auj);


/*---------------------------------------------------------------------------*\
atteindre page php info
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	echo debut_boite_info(true);
		echo "\n<a href='".generer_url_ecrire("info")."'>"._T('actijour:page_phpinfo')."</a>\n";
	echo fin_boite_info(true);


/*---------------------------------------------------------------------------*\
version de mysql du serveur :
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	echo debut_boite_info(true);
		$vers = mysql_query("select version()");
		$rep = mysql_fetch_array($vers);
		echo "MySQL v. ".$rep[0];
	echo fin_boite_info(true);


/*---------------------------------------------------------------------------*\
scoty signe son mefait
\*---------------------------------------------------------------------------*/
	echo signature_plugin();


echo debut_droite("", true);


/*---------------------------------------------------------------------------*\
Onglets pages sup.
\*---------------------------------------------------------------------------*/
	echo onglets_actijour(_request('exec'));



$m=array();
/*---------------------------------------------------------------------------*\
Lister Articles du jour
\*---------------------------------------------------------------------------*/
	$m[1] = liste_articles_jour($date_auj,$nb_art_visites_jour,$date_maj_art,$prev_visites);

/*---------------------------------------------------------------------------*\
Visites du jour par secteur/rubrique
\*---------------------------------------------------------------------------*/
	$m[2] = tableau_visites_rubriques($date_auj);

/*---------------------------------------------------------------------------*\
Visites et Nbr articles /j. sur les 8 derniers jours + moyenne.
\*---------------------------------------------------------------------------*/
	$m[3] = articles_visites_semaine();

/*---------------------------------------------------------------------------*\
Affichage des referers du jour (orig. spip inc/statistiques)
\*---------------------------------------------------------------------------*/
	$m[4] = liste_referers_jour($date_auj);

	#
	# affichage des blocs ordonnes $m
	#
	$ordon_admin=$GLOBALS['actijour']['admin-'.$connect_id_auteur]['ordon_pg_m'];
	if(!$ordon_admin) { $ordon_admin='1,2,3,4'; }
	foreach(explode(',',$ordon_admin) as $bloc) {
		echo $m[$bloc];
	}


# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin exec

?>

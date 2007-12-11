<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.53 - 12/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| Stats de la veille : articles, referers, forums, petitions.
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');


function exec_actijour_hier() {

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
	$date_hier = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
	#$aff_date_hier = date('d/m/y', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
	

	# tbl articles vistes hier 
	$tbl_art_jour = articles_visites_jour($date_hier);

	# derniere maj visites articles
	$date_maj_art = derniere_maj_articles($date_hier);
		
	# nbre articles visites hier
	$nb_art_visites_jour = count($tbl_art_jour);
	
	# total visites hier
	$gj = global_jour($date_hier);
	$global_jour = $gj['visites'];
	$date_globaljour = $gj['date'];
	
	# nbr posts hier sur vos forum
	$nbr_post_jour = nombre_posts_forum($date_hier);


		
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
nombre visites hier
\*---------------------------------------------------------------------------*/
	debut_cadre_relief("statistiques-24.gif");
		echo "<span class='verdana3 bold'>"._T('acjr:nombre_visites_')."</span>\n";
		echo "<div class='cell_info alter-fond'>"
			._T('acjr:global_vis_hier', array('global_jour'=>$global_jour))."</div>\n";
	fin_cadre_relief();


/*---------------------------------------------------------------------------*\
nombre de message forum public (identif. GAFoSPIP/SPIPBB)
\*---------------------------------------------------------------------------*/
	echo activite_forum_site($nbr_post_jour);


/*---------------------------------------------------------------------------*\
signatures petitions aujourd'hui
\*---------------------------------------------------------------------------*/
	echo signatures_petitions_jour($date_hier);


/*---------------------------------------------------------------------------*\
Telechargement de fichiers du jour (via DW2)
\*---------------------------------------------------------------------------*/
	echo telechargement_dw2_jour($date_hier);
	

/*---------------------------------------------------------------------------*\
scoty signe son mefait
\*---------------------------------------------------------------------------*/
	echo signature_plugin();



debut_droite();


/*---------------------------------------------------------------------------*\
Onglets pages sup.
\*---------------------------------------------------------------------------*/
	echo onglets_actijour(_request('exec'));


/*---------------------------------------------------------------------------*\
Lister Articles du jour
\*---------------------------------------------------------------------------*/
	echo liste_articles_jour($date_hier,$nb_art_visites_jour,$date_maj_art);


/*---------------------------------------------------------------------------*\
Visites du jour par secteur/rubrique
\*---------------------------------------------------------------------------*/
	echo tableau_visites_rubriques($date_hier);


/*---------------------------------------------------------------------------*\
Affichage des referers du jour (orig. spip inc/statistiques)
\*---------------------------------------------------------------------------*/
	echo liste_referers_jour('veille');


# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

}
?>

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
include_spip("inc/actijour_init");
include_spip("inc/requetes_stats");
include_spip('inc/affiche_blocs');



	# date jour a afficher
	if(_request('annee')) {
		$date_jour=_request('annee')."-"._request('mois')."-"._request('jour');	
	}
	if(!$date_jour) {
		$date_jour = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
	}
	#$aff_date_hier = date('d/m/y', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
	

	# tbl articles vistes hier 
	$tbl_art_jour = articles_visites_jour($date_jour);

	# derniere maj visites articles
	$date_maj_art = derniere_maj_articles($date_jour);
		
	# nbre articles visites hier
	$nb_art_visites_jour = count($tbl_art_jour);
	
	# total visites hier
	$gj = global_jour($date_jour);
	$global_jour = $gj['visites'];
	$date_globaljour = $gj['date'];
	
	# nbr posts hier sur vos forum
	$nbr_post_jour = nombre_posts_forum($date_jour);


	# premiere annee de stat
	$prim_jour_stats = prim_jour_stats();
	$tbl_pjs=recup_date($prim_jour_stats);
	$prim_an_stats = $tbl_pjs[0];

		
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
selecteur date d affichage
\*---------------------------------------------------------------------------*/
	echo formulaire_periode($date_jour,_request('exec'),$prim_an_stats);





echo creer_colonne_droite("", true);
echo "<br /><br /><br />";

/*---------------------------------------------------------------------------*\
nombre visites hier
\*---------------------------------------------------------------------------*/
	debut_cadre_relief("statistiques-24.gif");
		echo "<span class='verdana3 bold'>"._T('actijour:nombre_visites_')."</span>\n";
		echo "<div class='cell_info alter-fond'>"
			._T('actijour:global_vis_jour', array('global_jour'=>$global_jour))."</div>\n";
	fin_cadre_relief();


/*---------------------------------------------------------------------------*\
Affichage articles creer/modifier ce jour ((h.30/04/08)
\*---------------------------------------------------------------------------*/
	echo articles_creer_modifer_jour($date_jour);


/*---------------------------------------------------------------------------*\
nombre de message forum public (identif. GAFoSPIP/SPIPBB)
\*---------------------------------------------------------------------------*/
	echo activite_forum_site($nbr_post_jour);


/*---------------------------------------------------------------------------*\
Telechargement de fichiers du jour (via DW2)
\*---------------------------------------------------------------------------*/
	echo telechargement_dw2_jour($date_jour);
	

/*---------------------------------------------------------------------------*\
signatures petitions aujourd'hui
\*---------------------------------------------------------------------------*/
	echo signatures_petitions_jour($date_jour);


/*---------------------------------------------------------------------------*\
scoty signe son mefait
\*---------------------------------------------------------------------------*/
	echo signature_plugin();



echo debut_droite("", true);


/*---------------------------------------------------------------------------*\
Onglets pages sup.
\*---------------------------------------------------------------------------*/
	echo onglets_actijour(_request('exec'));


/*---------------------------------------------------------------------------*\
Lister Articles du jour
\*---------------------------------------------------------------------------*/
	echo liste_articles_jour($date_jour,$nb_art_visites_jour,$date_maj_art);


/*---------------------------------------------------------------------------*\
Visites du jour par secteur/rubrique
\*---------------------------------------------------------------------------*/
	echo tableau_visites_rubriques($date_jour);


/*---------------------------------------------------------------------------*\
Affichage des referers du jour (orig. spip inc/statistiques)
\*---------------------------------------------------------------------------*/
	echo liste_referers_jour($date_jour);


# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

}
?>

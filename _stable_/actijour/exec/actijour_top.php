<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.53 - 12/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| topTen, 8j, 30j, general
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');


function exec_actijour_top() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

//
// function requises ...
include_spip("inc/func_acj");
include_spip('inc/affiche_blocs');


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
info colonnes tableaux
\*---------------------------------------------------------------------------*/
	debut_boite_info();
		echo _T('acjr:info_colonnes_topten')."\n";
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
onglet(_T('acjr:page_activite'), generer_url_ecrire("actijour_pg"), 'page_activite', '', _DIR_PLUGIN_ACTIJOUR."img_pack/activ_jour.gif").
onglet(_T('acjr:page_hier'), generer_url_ecrire("actijour_hier"), 'page_hier', '', _DIR_PLUGIN_ACTIJOUR."img_pack/activ_hier.gif").
onglet(_T('acjr:page_topten'), generer_url_ecrire("actijour_top"), 'page_topten', 'page_topten', "article-24.gif").
fin_onglet();


/*---------------------------------------------------------------------------*\
classement des 10 articles les + visites sur 8 jours
\*---------------------------------------------------------------------------*/
	echo topten_articles_periode('8');


/*---------------------------------------------------------------------------*\
classement des 10 articles les + visites sur 30 jours
\*---------------------------------------------------------------------------*/
	echo topten_articles_periode('30');


/*---------------------------------------------------------------------------*\
classement des 10 articles les + visites
\*---------------------------------------------------------------------------*/
	echo topten_articles_global();



# retour haut de page
bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin fonction
?>

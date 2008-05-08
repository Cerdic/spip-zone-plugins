<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.55 - 05/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| topTen articles : 8j, 30j, general
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
include_spip("inc/actijour_init");
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
	echo entete_page();

/*---------------------------------------------------------------------------*\
info colonnes tableaux
\*---------------------------------------------------------------------------*/
	debut_boite_info();
		echo _T('acjr:info_colonnes_topten')."\n";
	fin_boite_info();

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
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin fonction
?>

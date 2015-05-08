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
info colonnes tableaux
\*---------------------------------------------------------------------------*/
	echo debut_boite_info(true);
		echo _T('actijour:info_colonnes_topten')."\n";
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

<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.55 - 05/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| date de derniere connexion de tous spip-auteurs
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');


function exec_actijour_connect() {

# elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

#
# function requises ...
#
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
scoty signe son mefait
\*---------------------------------------------------------------------------*/
	echo signature_plugin();



creer_colonne_droite();

debut_droite();


/*---------------------------------------------------------------------------*\
Onglets pages sup.
\*---------------------------------------------------------------------------*/
	echo onglets_actijour(_request('exec'));

/*---------------------------------------------------------------------------*\
 tableau date de derniere connexion des spip-auteurs
\*---------------------------------------------------------------------------*/
	echo tous_auteurs_date_passage();


# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin exec

?>

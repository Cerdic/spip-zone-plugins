<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Tables et poids de la BDD du site
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_tabbord_base() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

//
// requis
//
include_spip('inc/tabbord_pres');
include_spip('inc/func_tabbord');


//
// prepa
//

	$GLOBALS['taille_disque'] = 0;
	$GLOBALS['ch_texte'] = "";
	$GLOBALS['base_dir'] = ".";



//
// affichage
//

#debut_page(_T('Tableau de Bord'), "suivi", "tabbord");
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('tabbord:titre_plugin'), "suivi", "tabbord_gen", '');
	echo "<br />";



// Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques)
	{
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
	}

debut_gauche();

menu_gen_tabbord();


debut_droite();


	debut_cadre_formulaire();
		
		taille_base();
		
		echo "\n<br /><span class='verdana1'>"._T('tabbord:ligne_s_').
			http_img_pack("rien.gif",'',"width='30' height='14' style='vertical-align:middle; background-color:".$couleur_claire.";'").
			"\n"._T('tabbord:tables_non_spip')."</span>\n";
			
	fin_cadre_formulaire();


//
//
echo fin_gauche(), fin_page();
}
?>

<?php
/**
* Plugin Inscription2
*
* Copyright (c) 2007
* Sergio and co
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

include_spip('public/assembler');
function exec_inscription2_migration(){
	
	global $connect_statut;
	global $connect_toutes_rubriques;
	
	include_spip('cfg_options');
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('inscription2:migration'), "", "", "");
	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	global $connect_statut, $connect_toutes_rubriques, $table_prefix;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
    //remplacer username par login
    if (lire_config('inscription2/username')) {
        ecrire_config('inscription2/login',lire_config('inscription2/username'));
        effacer_config('inscription2/username');
    }

    if (lire_config('inscription2/username_obligatoire')) {
        ecrire_config('inscription2/login_obligatoire',lire_config('inscription2/username_obligatoire'));
        effacer_config('inscription2/username_obligatoire');
    }


    if (lire_config('inscription2/username_fiche_mod')) {
        ecrire_config('inscription2/login_fiche_mod',lire_config('inscription2/username_fiche_mod'));
        effacer_config('inscription2/username_fiche_mod');
    }


    if (lire_config('inscription2/username_fiche')) {
        ecrire_config('inscription2/login_fiche',lire_config('inscription2/username_fiche'));
        effacer_config('inscription2/username_fiche');
    }

    if (lire_config('inscription2/username_table')) {
        ecrire_config('inscription2/login_table',lire_config('inscription2/table_fiche'));
        effacer_config('inscription2/username_table');
    }

    echo "CFG -> Migration username pour login : Ok";


	fin_page();
	
	}
?>

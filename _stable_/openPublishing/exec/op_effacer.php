<?php

/***************************************************************************\
 *                                                                         *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/presentation');
include_spip('inc/layer');

include_spip('inc/op_actions');

function exec_op_effacer() {
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	$surligne = "";
  
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('opconfig:op_config'), "administration", "INDY");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}	
	
	if (isset($_GET['action'])) {        
        switch ($action = $_GET['action']) {
            case "desinstall" :
		op_deluser_anonymous(op_get_id_auteur());
                op_desinstaller_base();
                break;
        }
    }
	
	
	debut_page(_T('opconfig:op_config'), "administration", "INDY");
	echo "<br/>";
	
	gros_titre(_T('opconfig:op_config'));
	
	if (op_verifier_base())
	{
    		echo barre_onglets("op", "effacer");
	} 
	
	
	
	debut_gauche();
	
	debut_boite_info();
	echo _T('opconfig:op_effacer_info');
	fin_boite_info();
	
	debut_raccourcis();
	echo _T('opconfig:op_raccourcis_documentation');
	fin_raccourcis();

	debut_droite();

	debut_cadre_enfonce("supprimer.gif", false, "", bouton_block_invisible('op_general')._T('opconfig:op_configuration_effacer'));
	if (op_verifier_base()) {
        echo _T('opconfig:op_info_base_ok');        
    } 
	else {
        echo _T("opconfig:op_info_deja_ko");
    }
	
	echo debut_block_invisible('op_general');
		if (op_verifier_base()) {
        debut_boite_alerte();
		echo _T('opconfig:op_info_desinstal');
		echo '<div align="center">';
	    echo '<form method="post" action="'.generer_url_ecrire('op_effacer',"action=desinstall").'">';
	    echo "<input type='submit' name='appliq' value='"._T('opconfig:deinstaller')."' />";
	    echo '</form></div>';
		fin_boite_alerte();
		} 
		else {
		echo " ";
    }
	echo fin_block();
	fin_cadre_enfonce();
	
	
	
	fin_page();

}
?>
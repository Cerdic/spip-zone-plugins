<?php

/***************************************************************************\
 *                                                                         *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/presentation');
include_spip('inc/layer');

include_spip('inc/op_actions');

function exec_op() {
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	$surligne = "";
  
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('opconfig:op_config'), "administration", "OP");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	if (isset($_GET['action'])) {
        
        switch ($action = $_GET['action']) {
            	case "install" :
                	op_installer_base();
			op_set_id_auteur(999);
                break;
        }
    	}	
	
	debut_page(_T('opconfig:op_config'), "administration", "OP");
	echo "<br/>";

	gros_titre(_T('opconfig:op_config'));

	if (op_verifier_base())
	{
    		barre_onglets("op", "voir");
   	} 

	debut_gauche();
	
	debut_boite_info();
	echo _T('opconfig:op_voir_info');
	fin_boite_info();

	debut_raccourcis();
	echo _T('opconfig:op_raccourcis_documentation');
	fin_raccourcis();
	
	debut_droite();

	debut_cadre_enfonce("racine-site-24.gif", false, "", bouton_block_invisible('op_general')._T('opconfig:op_configuration_voir_general'));
	if (op_verifier_base()) {
        	echo _T('opconfig:op_info_base_ok');
	} 
	else {
        	echo _T("opconfig:op_info_base_ko");
		echo '<p /><div align="center">';
		echo '<form method="post" action="'.generer_url_ecrire('op',"action=install").'">';
		echo '<input type="submit" name="appliq" value="Installer les tables Open-publishing" />';
		echo '</form></div>';
	}
	
	echo debut_block_invisible('op_general');
		if (op_verifier_base()) {       
			op_liste_config();
		} 
		else {
			echo '<p />';
        	echo _T("opconfig:op_info_base_ko_bis");
		}
	echo fin_block();
	fin_cadre_enfonce();

	fin_page();

}

function op_liste_config() {

	echo "Version install&eacute;e : ".op_get_version()." <br />";
}
?>
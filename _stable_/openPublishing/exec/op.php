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
		case "upgrade" :
			op_upgrade_base();
		break;
        }
    	}	
	
	debut_page(_T('opconfig:op_config'), "administration", "OP");
	echo "<br/>";

	gros_titre(_T('opconfig:op_config'));

	if (op_verifier_base())
	{
    		echo barre_onglets("op", "voir");
   	} 

	debut_gauche();
	
	debut_boite_info();
	echo _T('opconfig:op_voir_info');
	fin_boite_info();

	debut_raccourcis();
	echo _T('opconfig:op_raccourcis_documentation');
	fin_raccourcis();
	
	debut_droite();

	debut_cadre_enfonce("racine-site-24.gif", false, "", _T('opconfig:op_configuration_voir_general'));
	if (op_verifier_base()) {
        	echo _T('opconfig:op_info_base_ok');
	} 
	else {
        	echo _T("opconfig:op_info_base_ko");
		echo '<p /><div align="center">';
		echo '<form method="post" action="'.generer_url_ecrire('op',"action=install").'">';
		echo '<input type="submit" name="appliq" value="Installer les tables openPublishing" />';
		echo '</form></div>';
	}
	
	if (op_verifier_base()) {
		op_liste_config();
	}
	else {
		echo '<p />';
        	echo _T("opconfig:op_info_base_ko_bis");
	}

	if (op_verifier_upgrade()) {
		echo _T("opconfig:op_info_base_up");
		echo '<p /><div align="center">';
		echo '<form method="post" action="'.generer_url_ecrire('op',"action=upgrade").'">';
		echo '<input type="submit" name="appliq" value="Upgrader les tables openPublishing" />';
		echo '</form></div>';
	}

	fin_cadre_enfonce();

	fin_page();

}

function op_liste_config() {

	echo "<ul>";
	echo "<li>Version install&eacute;e : <b>".op_get_version()."</b></li>";
	echo "<li>Gestion d'un agenda : <b>".op_get_agenda()."</b></li>";
	echo "<li>Gestion des documents : <b>".op_get_document()."</b></li>";
	echo "<li>Gestion des mots-clefs<ul>";
		echo "<li>Plugin Tag Machine : <b>".op_get_tagmachine()."</b></li>";
	echo "</ul>";
	echo "<li>Post-traitement<ul>";
		echo "<li>Titre en minuscule : <b>".op_get_titre_minus()."</b></li>";
		echo "<li>Anti-spam : <b>".op_get_antispam()."</b></li>";
		echo "</ul></li>";
	echo "</ul>";
}
?>
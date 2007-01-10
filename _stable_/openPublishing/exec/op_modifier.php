<?php

/***************************************************************************\
 *                                                                         *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/presentation');
include_spip('inc/layer');
include_spip('base/abstract_sql');


include_spip('inc/op_actions');

function exec_op_modifier() {
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	$surligne = "";
	$message_modif = "";
	$modif_agenda = "";
  
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('opconfig:op_config'), "administration", "INDY");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}	
	
	if (isset($_GET['action'])) {        
        switch ($action = $_GET['action']) {
            case "config" :

			$ajout_rubrique = stripslashes(_request('ajout_rubrique'));
			$modif_agenda = stripslashes(_request('modif_agenda'));
			$num_rubrique_ajout = stripslashes(_request('num_rubrique_ajout'));
			$active_agenda = stripslashes(_request('active_agenda'));
			$num_rubrique_agenda = stripslashes(_request('num_rubrique_agenda'));
			if ($ajout_rubrique) {
				$retour = set_config_rubrique($num_rubrique_ajout);
				switch ($retour) {
				   case "ok" :
					$message_modif = 'la rubrique ' . $num_rubrique_ajout . ' a &eacute;t&eacute; correctement ajout&eacute;e.';
					break;
				   case "deja" :
					$message_modif = 'la rubrique ' . $num_rubrique_ajout . ' est d&eacute;j&agrave; dans la base.';
					break;
				   case "ko" :
					$message_modif = 'la rubrique ' . $num_rubrique_ajout . ' n\'a pas &eacute;t&eacute; ajout&eacute;e : erreur inconue.';
					break;
				}
			}
			if($modif_agenda) {
				op_set_agenda($active_agenda);
				op_set_rubrique_agenda($num_rubrique_agenda);
			}
			
                break;
        }
    }
	
	
	debut_page(_T('opconfig:op_config'), "administration", "INDY");
	echo "<br/>";
	
	gros_titre(_T('opconfig:op_config'));
	
	if (op_verifier_base())
	{
    		barre_onglets("op", "modifier");
	}

	debut_gauche();
	
	debut_boite_info();
	echo _T('opconfig:op_modifier_info');
	fin_boite_info();
	
	debut_raccourcis();
	echo _T('opconfig:op_raccourcis_documentation');
	fin_raccourcis();

	debut_droite();

	// le cadre d'acceuil
	debut_cadre_enfonce("racine-site-24.gif", false, "", _T('opconfig:op_configuration_modifier'));
	if (op_verifier_base()) {
        	echo _T('opconfig:op_info_base_ok') . '<br />';
		echo "Version install&eacute;e : ". op_get_version() . "<br />";
    	} 
	else {
        	echo _T("opconfig:op_info_deja_ko") . '<br />';
		echo '<p />';
        	echo _T("opconfig:op_info_base_ko_bis");
    	}
	fin_cadre_enfonce();
	
	if (op_verifier_base()) {
		// les messages de retours de l'action demandé par l'utilisateur
		if ($message_modif) {
			debut_cadre_enfonce("racine-site-24.gif", false, "", "résultat ...");
			echo '<b>' . $message_modif . '</b><br />';
			fin_cadre_enfonce();
		}
		echo '<form method="post" action="'.generer_url_ecrire('op_modifier',"action=config").'">';
	
		op_cadre_auteur();
		op_cadre_rubrique();
		op_cadre_renvoi();
		op_cadre_traitement();
		op_cadre_agenda();
		op_cadre_documents();

		echo '</form>';
	}
	else {
		// rien
    	}
	
	fin_page();
}

function op_cadre_rubrique() {

	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestion des rubriques");

	echo "<small>Indiquez ici les rubriques sur lesquelles vous permettez l'openPublishing. Attention, les rubriques doivent exister ! Cliquez sur la croix pour supprimer votre selection.</small><br /><br />";
		
	$rubrique_array = get_rubriques_op();

	if (mysql_num_rows($rubrique_array) > 0 ) {
		echo 'liste des rubriques open-publishing : <br />';
		echo '<table border="1" cellpadding="2">';
		while ($row = mysql_fetch_array($rubrique_array)) {
			echo '<tr><td>' . $row[0] . '</td><td><input type="submit" name="sup_rubrique_' . $row[0] . '" value="X" /></td></tr>';
		}
		echo '</table>';
	}
	else {
		echo 'vous n\'avez pas encore de rubriques open-publishing ... <br />';
	}
	echo '<br />';
	echo '<input type="text" name="num_rubrique_ajout" size="3" />&nbsp;';
	echo '<input type="submit" name="ajout_rubrique" value="ajouter cette rubrique" />';
	fin_cadre_enfonce();
}

function op_cadre_auteur() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestion de l'auteur anonymous");
	echo 'num&eacute;ro id : ' . get_id_anonymous();
	fin_cadre_enfonce();
}

function op_cadre_renvoi() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestion des renvois");
	echo 'texte de renvoi normal<br />';
	echo 'texte de renvoi lors d\'un abandon<br />';
	echo 'redirection normale<br />';
	echo 'redirection lors d\'un abandon<br />';
	fin_cadre_enfonce();
}

function op_cadre_traitement() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Post-traitement des textes");
	echo 'imposer les titres en minuscule<br />';
	echo 'activer l\'anti-spam ?';
	fin_cadre_enfonce();
}

function op_cadre_agenda() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestion de l'agenda");
	echo 'activer l\'agenda ?&nbsp;' .
		'<select name="active_agenda">';
	$r = op_get_agenda();
	if ($r == 'oui') {
		echo '<option value="oui" selected >oui</option>' .
		'<option value="non">non</option>';
	}
	else {
		echo '<option value="oui">oui</option>' .
		'<option value="non" selected>non</option>';
	}
	echo '</select><br />';
	echo 'rubrique de l\'agenda : ';
	echo '<input type="text" name="num_rubrique_agenda" size="3" value="'. op_get_rubrique_agenda() .'" /><br />';
	echo '<input type="submit" name="modif_agenda" value="appliquer les changements" />';
	fin_cadre_enfonce();
}

function op_cadre_documents() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestions des documents");
	echo 'autoriser l\'upload de document ?';
	fin_cadre_enfonce();
}

?> 

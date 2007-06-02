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
	$modif_url = "";
	$modif_agenda = "";
	$modif_document = "";
	$modif_traitement = "";
	$modif_renvoi_normal = "";
	$modif_renvoi_abandon = "";
	$modif_tagmachine = "";
  
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('opconfig:op_config'), "administration", "INDY");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}	
	
	if (isset($_GET['action'])) {        
        switch ($action = $_GET['action']) {
            case "config" :

			$rubrique_array = get_rubriques_op();
			if (count($rubrique_array) > 0) {
				$i=0;
				while ($row = spip_fetch_array($rubrique_array)) {
					$sup_rubrique_array[$i] = _request('sup_rubrique_'.$row['op_rubrique']);
					$value_rubrique[$i] = $row['op_rubrique'];
					$i=$i+1;
				}
				if (count($sup_rubrique_array) > 0) {
					$i=0;
  					foreach ($sup_rubrique_array AS $sup_rubrique ){
						if($sup_rubrique) op_sup_rubrique($value_rubrique[$i]);
						$i = $i+1;
					}
				}
			}
			
			$ajout_rubrique = stripslashes(_request('ajout_rubrique'));
			$modif_agenda = stripslashes(_request('modif_agenda'));
			$modif_document = stripslashes(_request('modif_document'));
			$modif_traitement = stripslashes(_request('modif_traitement'));
			$modif_renvoi_normal = stripslashes(_request('modif_renvoi_normal'));
			$modif_renvoi_abandon = stripslashes(_request('modif_renvoi_abandon'));
			$modif_tagmachine = stripslashes(_request('modif_tagmachine'));
			$modif_url = _request('modif_url');
			$num_rubrique_ajout = stripslashes(_request('num_rubrique_ajout'));
			$active_agenda = stripslashes(_request('active_agenda'));
			$active_document = stripslashes(_request('active_document'));
			$active_titre_minus = stripslashes(_request('active_titre_minus'));
			$active_antispam = stripslashes(_request('active_antispam'));
			$active_tagmachine = stripslashes(_request('active_tagmachine'));
			$renvoi_normal = _request('renvoi_normal');
			$renvoi_abandon = _request('renvoi_abandon');
			$url_retour = _request('url_retour');
			$url_abandon = _request('url_abandon');
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
			if($modif_document) {
				op_set_document($active_document);
			}
			if($modif_traitement) {
				op_set_titre_minus($active_titre_minus);
				op_set_antispam($active_antispam);
			}
			if($modif_renvoi_normal) {
				op_set_renvoi_normal($renvoi_normal);
			}
			if($modif_renvoi_abandon) {
				op_set_renvoi_abandon($renvoi_abandon);
			}
			if($modif_url) {
				op_set_url_retour($url_retour);
				op_set_url_abandon($url_abandon);
			}
			if($modif_tagmachine) {
				op_set_tagmachine($active_tagmachine);
			}
			
                break;
        }
    }
	
	
	debut_page(_T('opconfig:op_config'), "administration", "INDY");
	echo "<br/>";
	
	gros_titre(_T('opconfig:op_config'));
	
	if (op_verifier_base())
	{
    		echo barre_onglets("op", "modifier");
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
			debut_cadre_enfonce("racine-site-24.gif", false, "", "r&eacute;sultat ...");
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
		op_cadre_tagmachine();

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

	if (count($rubrique_array) > 0 ) {
		echo 'liste des rubriques openPublishing : <br />';
		echo '<table border="1" cellpadding="2">';
		while ($row = spip_fetch_array($rubrique_array)) {
			echo '<tr><td>' . $row['op_rubrique'] . '</td><td><input type="submit" name="sup_rubrique_' . $row['op_rubrique'] . '" value="X" /></td></tr>';
		}
		echo '</table>';
	}
	else {
		echo 'vous n\'avez pas encore de rubriques openPublishing ... <br />';
	}
	echo '<br />';
	echo '<input type="text" name="num_rubrique_ajout" size="3" />&nbsp;';
	echo '<input type="submit" name="ajout_rubrique" value="ajouter cette rubrique" />';
	fin_cadre_enfonce();
}

function op_cadre_auteur() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestion de l'auteur anonymous");
	echo 'num&eacute;ro id : ' . op_get_id_auteur();
	fin_cadre_enfonce();
}

function op_cadre_renvoi() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestion des renvois");
	echo '<small>Les textes de renvois sont les petites phrases que le plugin affiche lorsqu\'une publication c\'est' .
		'soit d&eacute;roul&eacute;e normallement, soit termin&eacute;e par un abandon (les balises HTML sont permises).</small><br />';
	echo '<small>Les redirections permettent de diriger l\'utilisateur vers une page de votre site (de type "spip.php?page=sommaire").</small><br /><br />';
	echo '<b>texte de renvoi normal</b><br />';
	echo '<textarea name="renvoi_normal" rows="5" cols="50">'.op_get_renvoi_normal().'</textarea><br />';
	echo '<input type="submit" name="modif_renvoi_normal" value="modifier ce texte" /><br /><br />';
	echo '<b>texte de renvoi lors d\'un abandon</b><br />';
	echo '<textarea name="renvoi_abandon" rows="5" cols="50">'.op_get_renvoi_abandon().'</textarea><br />';
	echo '<input type="submit" name="modif_renvoi_abandon" value="modifier ce texte" /><br /><br />';
	echo '<b>redirection normale :</b>&nbsp;';
	echo '<input type="text" name="url_retour" size="30" value="'.op_get_url_retour().'" /><br />';
	echo '<b>redirection lors d\'un abandon :</b>&nbsp;';
	echo '<input type="text" name="url_abandon" size="30" value="'.op_get_url_abandon().'" /><br />';
	echo '<input type="submit" name="modif_url" value="modifier ces adresses" />';
	fin_cadre_enfonce();
}

function op_cadre_traitement() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Post-traitement des textes");
	echo '<small>Ces traitements seront appliqu&eacute;s lorsque l\'utilisateur validera son texte.</small><br /><br />';
	echo 'imposer les titres en minuscule ?&nbsp;';
	echo '<select name="active_titre_minus">';
	$r = op_get_titre_minus();
	if ($r == 'oui') {
		echo '<option value="oui" selected >oui</option>' .
		'<option value="non">non</option>';
	}
	else {
		echo '<option value="oui">oui</option>' .
		'<option value="non" selected>non</option>';
	}
	echo '</select><br /><small>(les majuscules seront transform&eacute;es en minuscule)</small><br />';
	echo 'activer l\'anti-spam ?&nbsp;';
	echo '<select name="active_antispam">';
	$r = op_get_antispam();
	if ($r == 'oui') {
		echo '<option value="oui" selected >oui</option>' .
		'<option value="non">non</option>';
	}
	else {
		echo '<option value="oui">oui</option>' .
		'<option value="non" selected>non</option>';
	}
	echo '</select><br /><small>(les @ des adresses mails du texte seront transform&eacute;s.)</small><br />';
	echo '<input type="submit" name="modif_traitement" value="appliquer les changements" />';
	fin_cadre_enfonce();
}

function op_cadre_agenda() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestion de l'agenda");
	echo '<small>L\'orsque l\'utilisateur coche la case "Agenda", son article est publi&eacute; sous forme de '.
		'br&egrave; dans la rubrique indiqu&eacute;e ci-dessous.</small><br /><br />';
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
	echo 'autoriser l\'upload de document ?&nbsp;';
	echo '<select name="active_document">';
	$r = op_get_document();
	if ($r == 'oui') {
		echo '<option value="oui" selected >oui</option>' .
		'<option value="non">non</option>';
	}
	else {
		echo '<option value="oui">oui</option>' .
		'<option value="non" selected>non</option>';
	}
	echo '</select><br />';
	echo '<input type="submit" name="modif_document" value="appliquer les changements" />';
	fin_cadre_enfonce();
}

function op_cadre_tagmachine() {
	debut_cadre_enfonce("racine-site-24.gif", false, "", "Gestions du plugin Tag Machine");
	echo 'autoriser la gestion des mots-clefs par le plugin Tag Machine ?';
	echo '<select name="active_tagmachine">';
	$r = op_get_tagmachine();
	if ($r == 'oui') {
		echo '<option value="oui" selected >oui</option>' .
		'<option value="non">non</option>';
	}
	else {
		echo '<option value="oui">oui</option>' .
		'<option value="non" selected>non</option>';
	}
	echo '</select><br />';
	echo '<input type="submit" name="modif_tagmachine" value="appliquer les changements" />';
	fin_cadre_enfonce();
}

?> 

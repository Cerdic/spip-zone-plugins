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
	$message_modif = "";
	$modif_url = "";
	$modif_agenda = "";
	$modif_traitement = "";
	$modif_renvoi_normal = "";
	$modif_renvoi_abandon = "";
	$modif_autre = "";
	
  
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
			$modif_traitement = stripslashes(_request('modif_traitement'));
			$modif_renvoi_normal = stripslashes(_request('modif_renvoi_normal'));
			$modif_renvoi_abandon = stripslashes(_request('modif_renvoi_abandon'));
			$modif_autre = stripslashes(_request('modif_autre'));
			$modif_url = _request('modif_url');
			$num_rubrique_ajout = stripslashes(_request('num_rubrique_ajout'));
			$active_agenda = stripslashes(_request('active_agenda'));
			$active_document = stripslashes(_request('active_document'));
			$active_titre_minus = stripslashes(_request('active_titre_minus'));
			$active_antispam = stripslashes(_request('active_antispam'));
			$active_tagmachine = stripslashes(_request('active_tagmachine'));
			$active_motclefs = stripslashes(_request('active_motclefs'));
			$select_statut = stripslashes(_request('select_statut'));
			$renvoi_normal = _request('renvoi_normal');
			$renvoi_abandon = _request('renvoi_abandon');
			$url_retour = _request('url_retour');
			$url_abandon = _request('url_abandon');
			$num_rubrique_agenda = stripslashes(_request('num_rubrique_agenda'));
			if ($ajout_rubrique) {
				$retour = set_config_rubrique($num_rubrique_ajout);
				switch ($retour) {
				   case "ok" :
					$message_modif = _T('opconfig:la_rubrique') . $num_rubrique_ajout . _T('opconfig:ajout_correct');
					break;
				   case "deja" :
					$message_modif = _T('opconfig:la_rubrique') . $num_rubrique_ajout . _T('opconfig:deja_base');
					break;
				   case "ko" :
					$message_modif = _T('opconfig:la_rubrique') . $num_rubrique_ajout . _T('opconfig:ajout_incorrect');
					break;
				}
			}
			if($modif_agenda) {
				op_set_agenda($active_agenda);
				op_set_rubrique_agenda($num_rubrique_agenda);
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
			if($modif_autre) {
				op_set_document($active_document);
				op_set_tagmachine($active_tagmachine);
				op_set_motclefs($active_motclefs);
				op_set_statut($select_statut);
			}
			
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
	echo '<br /><br />';
	if (op_verifier_base()) {
		op_liste_config();
	}
	fin_boite_info();

	debut_raccourcis();
	echo _T('opconfig:op_raccourcis_documentation');
	fin_raccourcis();
	
	debut_droite();

	debut_cadre_enfonce(null, false, "", _T('opconfig:op_configuration_voir_general'));

	if (op_verifier_base()) {
		if (op_verifier_upgrade()) {
			echo _T("opconfig:op_info_base_up");
			echo '<p /><div align="center">';
			echo '<form method="post" action="'.generer_url_ecrire('op',"action=upgrade").'">';
			echo "<input type='submit' name='appliq' value='"._T('opconfig:upgrader')."' />";
			echo '</form></div>';
			fin_cadre_enfonce();
		}
		else {
			echo _T('opconfig:op_info_base_ok');
			fin_cadre_enfonce();


			// les messages de retours de l'action demandé par l'utilisateur
			if ($message_modif) {
				debut_cadre_enfonce(null, false, "", _T('opconfig:resultat'));
				echo '<b>' . $message_modif . '</b><br />';
				fin_cadre_enfonce();
			}
			echo '<form method="post" action="'.generer_url_ecrire('op',"action=config").'">';
		
			echo "<br /";op_cadre_rubrique();
			echo "<br /";op_cadre_renvoi();
			echo "<br /";op_cadre_traitement();
			echo "<br /";op_cadre_agenda();
			echo "<br /";op_cadre_autre();
	
			echo '</form>';
		}	
	} 
	else {
        	echo _T("opconfig:op_info_base_ko");
		echo '<p /><div align="center">';
		echo '<form method="post" action="'.generer_url_ecrire('op',"action=install").'">';
		echo "<input type='submit' name='appliq' value='"._T('opconfig:installer')."' />";
		echo '</form></div>';
		echo '<p />';
        	echo _T("opconfig:op_info_base_ko_bis");
		fin_cadre_enfonce();
	}	

	fin_page();
}

function op_liste_config() {

	echo "- "._T('opconfig:info_version')."<b>".op_get_version()."</b><br />";
	echo "- "._T('opconfig:info_auteur')."<b>".op_get_id_auteur()."</b><br />";
	echo "- "._T('opconfig:info_agenda')."<b>".op_get_agenda()."</b><br />";
	echo "- "._T('opconfig:info_document')."<b>".op_get_document()."</b><br />";
	echo "- "._T('opconfig:info_statut')."<b>".op_get_statut()."</b><br />";
	echo "- "._T('opconfig:info_motclefstag')."<ul>";
		echo "<li>"._T('opconfig:info_tagmachine')."<b>".op_get_tagmachine()."</b></li>";
		echo "<li>"._T('opconfig:info_motclefs')."<b>".op_get_motclefs()."</b></li>";
	echo "</ul>";
	echo "- "._T('opconfig:info_traitement')."<ul>";
		echo "<li>"._T('opconfig:info_titre')."<b>".op_get_titre_minus()."</b></li>";
		echo "<li>"._T('opconfig:info_antispam')."<b>".op_get_antispam()."</b></li>";
	echo "</ul>";
}

function op_cadre_rubrique() {

	debut_cadre_relief(null, false, "", _T('opconfig:rubrique_gestion'));

	echo '<small>'._T('opconfig:rubrique_explique').'</small><br /><br />';
		
	$rubrique_array = get_rubriques_op();

	if (count($rubrique_array) > 0 ) {
		echo _T('opconfig:rubrique_liste').'<br />';
		echo '<table border="1" cellpadding="2">';
		while ($row = spip_fetch_array($rubrique_array)) {
			echo '<tr><td>' . $row['op_rubrique'] . '</td><td><input type="submit" name="sup_rubrique_' . $row['op_rubrique'] . '" value="X" /></td></tr>';
		}
		echo '</table>';
	}
	else {
		echo _T('opconfig:rubrique_pasencore').'<br />';
	}
	echo '<br />';
	echo '<input type="text" name="num_rubrique_ajout" size="3" />&nbsp;';
	echo "<input type='submit' name='ajout_rubrique' value='"._T('opconfig:rubrique_ajouter')."' class='fondo'/>";
	fin_cadre_relief();
}


function op_cadre_renvoi() {
	debut_cadre_relief(null, false, "", _T('opconfig:renvoi_gestion'));
	echo '<small>'._T('opconfig:renvoi_explique').'</small><br />';
	echo '<small>'._T('opconfig:renvoi_explique2').'</small><br /><br />';
	echo '<b>'._T('opconfig:renvoi_normal').'</b><br />';
	echo '<textarea name="renvoi_normal" rows="5" cols="50">'.op_get_renvoi_normal().'</textarea><br />';
	echo "<input type='submit' name='modif_renvoi_normal' value='"._T('opconfig:renvoi_modif')."' class='fondo' /><br /><br />";
	echo '<b>'._T('renvoi_abandon').'</b><br />';
	echo '<textarea name="renvoi_abandon" rows="5" cols="50">'.op_get_renvoi_abandon().'</textarea><br />';
	echo "<input type='submit' name='modif_renvoi_abandon' value='"._T('opconfig:renvoi_modif')."' class='fondo' /><br /><br />";
	echo '<b>'._T('redirection_normal').'</b>&nbsp;';
	echo '<input type="text" name="url_retour" size="30" value="'.op_get_url_retour().'" /><br />';
	echo '<b>'._T('redirection_abandon').'</b>&nbsp;';
	echo '<input type="text" name="url_abandon" size="30" value="'.op_get_url_abandon().'" /><br />';
	echo "<input type='submit' name='modif_url' value='"._T('opconfig:redirection_modif')."' class='fondo' />";
	fin_cadre_relief();
}

function op_cadre_traitement() {
	debut_cadre_relief(null, false, "",_T('opconfig:post_traitement'));

	echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";
	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	echo "<h3>"._T('opconfig:titre_minuscule')."</h3>";

	$r = op_get_titre_minus();
	$texte1 = '' ;
	$texte2 = '' ;
	if ($r == 'oui') {$texte1 = "checked";}
	else {$texte2 = "checked";}

  	echo "<input type='radio' name='active_titre_minus' value='oui' $texte1 id='statut_oui'>";
	echo "<label for='statut_oui'>"._T('opconfig:titre_impo_minuscule')."</label> ";
	echo "<p><input type='radio' name='active_titre_minus' value='non' $texte2 id='statut_non'>";
	echo "<label for='statut_non'>"._T('opconfig:titre_non_minuscule')."</label></b> ";
	echo "</td></tr>";
	
	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	echo "<h3>"._T('opconfig:anti_spam')."</h3>";

	$r = op_get_antispam();
	$texte1 = '' ;
	$texte2 = '' ;
	if ($r == 'oui') {$texte1 = "checked";}
	else {$texte2 = "checked";}

	echo "<input type='radio' name='active_antispam' value='oui' $texte1 id='statut_oui'>";
	echo "<label for='statut_oui'>"._T('opconfig:antispam_oui')."</label> ";
	echo "<p><input type='radio' name='active_antispam' value='non' $texte2 id='statut_non'>";
	echo "<label for='statut_non'>"._T('opconfig:antispam_non')."</label></b> ";
	echo "</td></tr>";


	echo "<tr><td style='text-align:$spip_lang_right;'>";
	echo "<input type='submit' name='modif_traitement' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</td></tr>";
	echo "<tr><td style='text-align:$spip_lang_right;'>";
	echo '<small>'._T('opconfig:traitement_explique').'</small>';
	echo "</td></tr>";

	echo "</table>\n";

	fin_cadre_relief();
}

function op_cadre_agenda() {
	debut_cadre_relief(null, false, "", _T('opconfig:gestion_agenda'));

	echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";
	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	echo "<h3>"._T('opconfig:agenda_active')."</h3>";

	$r = op_get_agenda();
	$texte1 = '' ;
	$texte2 = '' ;
	if ($r == 'oui') {$texte1 = "checked";}
	else {$texte2 = "checked";}

	echo "<input type='radio' name='active_agenda' value='oui' $texte1 id='statut_oui'>";
	echo "<label for='statut_oui'>"._T('opconfig:agenda_oui')."</label> ";
	echo "<p><input type='radio' name='active_agenda' value='non' $texte2 id='statut_non'>";
	echo "<label for='statut_non'>"._T('opconfig:agenda_non')."</label></b> ";
	echo "</td></tr>";

	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	echo "<p><h3>"._T('opconfig:agenda_rubrique');
	echo '<input type="text" name="num_rubrique_agenda" size="3" value="'. op_get_rubrique_agenda() .'" /></h3></p>';
	echo "</td></tr>";

	echo "<tr><td style='text-align:$spip_lang_right;'>";
	echo "<input type='submit' name='modif_agenda' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</td></tr>";

	echo "<tr><td style='text-align:$spip_lang_right;'>";
	echo '<small>'._T('opconfig:agenda_explique').'</small>';
	echo "</td></tr>";

	echo "</table>\n";

	fin_cadre_relief();
}

function op_cadre_autre() {
	debut_cadre_relief(null, false, "", _T('opconfig:gestion_autre'));

	echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";

	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	echo "<h3>"._T('opconfig:upload_active')."</h3>";

	$r = op_get_document();
	$texte1 = '' ;
	$texte2 = '' ;
	if ($r == 'oui') {$texte1 = "checked";}
	else {$texte2 = "checked";}

	echo "<input type='radio' name='active_document' value='oui' $texte1 id='statut_oui'>";
	echo "<label for='statut_oui'>"._T('opconfig:upload_oui')."</label> ";
	echo "<p><input type='radio' name='active_document' value='non' $texte2 id='statut_non'>";
	echo "<label for='statut_non'>"._T('opconfig:upload_non')."</label></b> ";
	echo "</td></tr>";

	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	echo "<h3>"._T('opconfig:tagmachine_active')."</h3>";

	$r = op_get_tagmachine();
	$texte1 = '' ;
	$texte2 = '' ;
	if ($r == 'oui') {$texte1 = "checked";}
	else {$texte2 = "checked";}

	echo "<input type='radio' name='active_tagmachine' value='oui' $texte1 id='statut_oui'>";
	echo "<label for='statut_oui'>"._T('opconfig:tagmachine_oui')."</label> ";
	echo "<p><input type='radio' name='active_tagmachine' value='non' $texte2 id='statut_non'>";
	echo "<label for='statut_non'>"._T('opconfig:tagmachine_non')."</label></b> ";
	echo "</td></tr>";

	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	echo "<h3>"._T('opconfig:motclefs_active')."</h3>";

	$r = op_get_motclefs();
	$texte1 = '' ;
	$texte2 = '' ;
	if ($r == 'oui') {$texte1 = "checked";}
	else {$texte2 = "checked";}

	echo "<input type='radio' name='active_motclefs' value='oui' $texte1 id='statut_oui'>";
	echo "<label for='statut_oui'>"._T('opconfig:motclefs_oui')."</label> ";
	echo "<p><input type='radio' name='active_motclefs' value='non' $texte2 id='statut_non'>";
	echo "<label for='statut_non'>"._T('opconfig:motclefs_non')."</label></b> ";
	echo "</td></tr>";

	echo "<tr><td background='img_pack/rien.gif' class='verdana2'>";
	echo "<h3>"._T('opconfig:statut_select')."</h3>";

	$r = op_get_statut();
	$texte1 = '' ;
	$texte2 = '' ;
	$texte3 = '' ;

	if ($r == 'publie') {$texte1 = "checked";}
	else if ($r == 'prop') {$texte2 = "checked";}
	else {$texte3 = "checked";}

	echo "<input type='radio' name='select_statut' value='publie' $texte1 id='statut_publie'>";
	echo "<label for='statut_publie'>"._T('opconfig:statut_publie')."</label> ";
	echo "<p><input type='radio' name='select_statut' value='prop' $texte2 id='statut_prop'>";
	echo "<label for='statut_prop'>"._T('opconfig:statut_prop')."</label></b> ";
	echo "<p><input type='radio' name='select_statut' value='prepa' $texte3 id='statut_prepa'>";
	echo "<label for='statut_prepa'>"._T('opconfig:statut_prepa')."</label></b> ";
	echo "</td></tr>";

	echo "<tr><td style='text-align:$spip_lang_right;'>";
	echo "<input type='submit' name='modif_autre' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</td></tr>";

	echo "</table>\n";

	fin_cadre_relief();
}

?>
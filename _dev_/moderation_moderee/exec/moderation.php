<?php
	if (!defined("_ECRIRE_INC_VERSION"));
	include_spip('inc/presentation');
	include_spip('inc/config');
	include_spip("inc/meta");
	//fonction principal de la page
	function exec_moderation () {
		if ($_POST['modif']){
			modifier_config();	
		
		}
	
	
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('moderation:titre'), "", "");
		echo gros_titre(_T('moderation:titre'),'',false);
		//Gestion des autorisations d'accès(pas admin => pas le droit)
		if ($admin AND $connect_statut != "0minirezo") {
			echo _T('avis_non_acces_page');
			exit;
		}
		echo "\n<tr><td style='text-align: $spip_lang_left;' class='verdana2'>";
		//On lit la config
		$forums_publics=$GLOBALS['meta']["forums_publics"];
		//On execute d'après la config
		if ($forums_publics == "non"){
			echo _T('moderation:config_false_1');
		}
		else if ($forums_publics == "posteriori"){
			echo _T('moderation:config_false_2');
		}
		else if ($forums_publics == "abo"){
			echo _T('moderation:config_false_3');
		}
		else if ($forums_publics == "priori"){
			//Après les vérifications : l'action !
			echo _T('moderation:config_true');
			lire_metas();
			$visit_radio=$GLOBALS['meta']["moderation_plug_visit"];
			$admin_radio=$GLOBALS['meta']["moderation_plug_admin"];
			$redac_radio=$GLOBALS['meta']["moderation_plug_redac"];
			$action = generer_url_ecrire('moderation');
			//On vérifie si les métas sont écrites
			if (empty($admin_radio) OR empty($redac_radio) OR empty($visit_radio)) {
				//Et une fonction pour les ecrire si elles ne sont pas là... 
				installer_plug();
			}
			else {
				debut_form();
				formulaire_config(_T('moderation:admin_config'),'moderation_plug_admin','admin_radio','administrateurs');
				formulaire_config(_T('moderation:redac_config'),'moderation_plug_redac','redac_radio','redacteurs');
				formulaire_config(_T('moderation:visit_config'),'moderation_plug_visit','visit_radio','visiteurs');
				finform();
				echo "</td></tr>";
				echo "</table>\n";
				echo "<br />";
			}
		}
		
		echo fin_page();
	}
function debut_form() {
				echo "<form action='$action' method='post'>";
			}
function formulaire_config($texteform,$metaconfig,$radio_b,$name) {
				$action = generer_url_ecrire('moderation');
				$radio = $GLOBALS['meta']["$metaconfig"];
				$formname = $name;
				echo "<table border='1px dashed #000' cellspacing='1' cellpadding='3' width=\"50%\" style=\"margin:auto;\">";
				echo "<br />\n";
				echo "\n<tr><td style='text-align: $spip_lang_left; font-size:15px;' class='verdana2'>";
				echo $texteform;
				echo "<br />\n";
				echo bouton_radio($formname, "oui",_T('moderation:oui'), $radio == "oui");
				echo "<br />\n";
				echo bouton_radio($formname, "non",_T('moderation:non'), $radio == "non");
				echo "<br />\n";
				echo "</td></tr>";
				echo "</table>";
				if(_request('$formname') != $radio) {
					$radio = _request('$formname');
				}
				if($ecrire_ok) {
					ecrire_meta($metaconfig, $radio);
				}
				}
function finform() {
				$ecrire_ok = _request('modif');
				$action = generer_url_ecrire('moderation');
				echo "<input type='submit' name='modif' value='"._T('moderation:valider')."' class='fondo' />";					
				echo "\n</form>";
				$commencer_page = charger_fonction('commencer_page', 'inc');
				return ($commencer_page);
			}


function installer_plug() {
				$action = generer_url_ecrire('moderation');
				echo "\n<tr><td style='text-align: center' class='verdana2'>";
				echo _T('moderation:explain_install');
				echo _T('moderation:install');
				echo "<form action='$action' method='post'>";
				echo "<input type='submit' name='install' value='"._T('moderation:installer')."' class='fondo' />";
				echo "<input type='hidden' value='$action' name='redirect' />";
				$retour = _request('install');
				if($retour) {
					ecrire_meta('moderation_plug_visit', 'oui', 'oui');
					ecrire_meta('moderation_plug_redac', 'oui', 'oui');
					ecrire_meta('moderation_plug_admin', 'oui', 'oui');
					ecrire_metas();
				}
				
				echo "</td></tr>";
				echo "</form>";
			}
function modifier_config(){
		ecrire_meta('moderation_plug_visit', $_POST['visiteurs'], 'oui');
		ecrire_meta('moderation_plug_redac', $_POST['redacteurs'], 'oui');
		ecrire_meta('moderation_plug_admin', $_POST['administrateurs'], 'oui');
		ecrire_metas();
}
?>
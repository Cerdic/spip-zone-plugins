<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Ecrire param de config dans table
+--------------------------------------------+
*/

function action_dw2_ecrireconfig() {
	global $_POST;
	global $redirect;
	global $action, $arg, $hash, $id_auteur;
	# arg => version plugin en cours
	
	// controle action
	include_spip('inc/securiser_action');
	if (!verifier_action_auteur("$action $arg", $hash, $id_auteur)) {
		include_spip('inc/minipres');
		minipres(_T('info_acces_interdit'));
	}
	
	// relire la config avant modif
	include_spip("inc/dw2_lireconfig");
	lire_dw2_config();
	
	// 'ecrire/modifier' table config
	//
	foreach($_POST as $k => $v) {
		# si situation install/maj
		if($k == "change_version") {
			spip_query("REPLACE spip_dw2_config (nom, valeur) VALUES ('version_installee', $arg)");
		}
		elseif($k=="criteres_auto_doc") {
			// criteres_auto_doc est array avec systematiq '0' en prem val
			if($v[0]=='0') {
				if(count($v)==1) {
					spip_query("REPLACE spip_dw2_config (nom, valeur) VALUES ('$k','')");
				}
				else {
					$b=array_shift($v);
					$vals=implode(',',$v);
					spip_query("REPLACE spip_dw2_config (nom, valeur) VALUES ('$k','$vals')");
				}
			}
		}
		elseif($k=='redirect' or $k=='hash' or $k=='id_auteur') {}
		else {
			// si modifie .. ecrit nouvelle def.
			if($v!=$GLOBALS['dw2_param'][$k]) {
				spip_query("REPLACE spip_dw2_config (nom, valeur) VALUES ('$k','$v')");
			}
		}
	}

	redirige_par_entete(rawurldecode($redirect));
}

?>

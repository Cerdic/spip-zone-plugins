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
	//global $_POST;
	//global $action, $hash, $id_auteur;
	# arg => version plugin en cours
	//$securiser_action = charger_fonction('securiser_action', 'inc');
	//$arg = $securiser_action();

	$arg=_request('arg');
	$hash=_request('hash');
	$id_auteur=_request('id_auteur');
	$redirect=_request('redirect');
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
	
	//foreach($_POST as $k => $v) {
	//	# si situation install/maj
		
	
		if($v =_request('change_version') ) {
			sql_query("REPLACE spip_dw2_config (nom, valeur) VALUES ('version_installee', $arg)");
		}
		elseif($v= _request('criteres_auto_doc')) {
			// criteres_auto_doc est array avec systematiq '0' en prem val
			if($v[0]=='0') {
				if(count($v)==1) {
					sql_query("REPLACE spip_dw2_config (nom, valeur) VALUES ('criteres_auto_doc','')");
				}
				else {
					$b=array_shift($v);
					$vals=implode(',',$v);
					sql_query("REPLACE spip_dw2_config (nom, valeur) VALUES ('criteres_auto_doc','$vals')");
				}
			}
		}
//		elseif($v=='redirect' or $k=='hash' or $k=='id_auteur') {}
		//else {
			// si modifie .. ecrit nouvelle def.
		//faire un foreach sur toutes les valeurs possibles de config de dw2

	$liste_param = array(
//		'version_installee', // deja traite
		'anti_triche', //
		'nbr_lignes_tableau', //
		'type_categorie', //
		'extens_logo_serveur', //
		'mode_enregistre_doc', //
		'jours_affiche_nouv', //
		'mode_affiche_images', //
		'avis_maj', //
		'squelette_cata_public', //
		'mode_restreint', //
	//	'criteres_auto_doc', // array 0-3 deja traite
		'message_maj', //
		'forcer_url_dw2' //
	);
		foreach($liste_param as $k=>$v)
		{
			$nom=$v;
			$val=_request($v);
			if ($val!=$GLOBALS['dw2_param'][$val]) {
				sql_query("REPLACE spip_dw2_config (nom, valeur) VALUES ('$nom','$val')");
			}
		}
	redirige_par_entete(rawurldecode($redirect),true);
}

?>

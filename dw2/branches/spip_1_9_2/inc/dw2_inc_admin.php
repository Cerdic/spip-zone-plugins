<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Controle Admin / Controle install DW2
| en premiere instance.
| ==> redirection ...
+--------------------------------------------+
*/


if (!defined("_ECRIRE_INC_VERSION")) return;

global $connect_statut, $connect_toutes_rubriques;

#h.07/03/ corr 192
	if (!_DIR_RESTREINT) {
		include_spip('inc/headers');
	}

//
// verifier info plugin DW2 en cours
function verifier_infos_dw2($item) {
	include_spip('inc/plugin');
	$info_plugin_dw2 = plugin_get_infos('dw2');
	return $info_dw2 = $info_plugin_dw2[$item];
}

//
// verifier presence table
function dw2_showtable($nomtable) {
    $a = spip_query("SHOW TABLES LIKE '$nomtable'");
	if(!$b=spip_fetch_array($a)) return false;
	else return true;
}

	
	
#
# Qui va là ?
#
/*
| DW2 refuse tout autre acces que Admin toutes-rub
| sauf si appel du catalogue-images et sinon
| redirige sur la pge de stats des docs de redacteurs
| (au pire, si DW2 pas configure, 'visiteur' retombe sur dw2_redacteur)
*/
	if ($connect_statut !='0minirezo' OR !$connect_toutes_rubriques) {
		if(_request('exec')!='dw2_cata_img')
			redirige_par_entete(generer_url_ecrire("dw2_redacteur"));
	}
	

#
# version du plugin DW2 .. fournie par plugin.xml
# 
	define("_DW2_VERS_PLUGIN",  verifier_infos_dw2('version'));

	
#
# initier superglobale parametre
#
	$GLOBALS['dw2_param'] = array();
	include_spip("inc/dw2_lireconfig");


#
# DW2 est-il completement installe ?
#
	if(dw2_showtable("spip_dw2_config")){
		
		// OUI .. on en profite pour declarer les globales de config
		lire_dw2_config();

		// ... mais changement de version ?
		if($GLOBALS['dw2_param']['version_installee'] < _DW2_VERS_PLUGIN) {
			$GLOBALS['flag_dw2_inst'] = 'maj';
		}
		
		define("_DW2_VERS_LOC", $GLOBALS['dw2_param']['version_installee']);	

	}
	elseif (dw2_showtable("dw2_doc")) {
		// OUI mais vers. ante 2.13
		$GLOBALS['flag_dw2_inst'] = 'maj';
	}
	else {
		$GLOBALS['flag_dw2_inst'] = 'ins';
	}
	

	// install/maj -> on redirige vers page dw2_install
	// si on n'y est pas deja !
	if($GLOBALS['flag_dw2_inst'] && _request('exec')!='dw2_install')
		{ redirige_par_entete(generer_url_ecrire("dw2_install")); }

?>

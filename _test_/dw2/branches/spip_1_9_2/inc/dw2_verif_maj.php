<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| CRON .. Controle avis de MaJ sur koakidi.com
+--------------------------------------------+
*/

function controle_maj_plugin() {
	include_spip("inc/plugin");
	$ret = array();

	if($tero=spip_file_get_contents("http://www.koakidi.com/dw2.xml")) {
		//(voir : spip.. inc/plugin.php
		$arbre = parse_plugin_xml($tero);
		
		plugin_verifie_conformite("dw2",$arbre); 
		
		$ret['nom'] = applatit_arbre($arbre['nom']);
		$ret['version'] = trim(end($arbre['version']));
		if (isset($arbre['auteur']))
			$ret['auteur'] = applatit_arbre($arbre['auteur']);
		if (isset($arbre['description']))
			// h. 5/9 - fonction chaines_lang dispasrue en spip 1.9.1
			#$ret['description'] = chaines_lang(applatit_arbre($arbre['description'])); 
			$ret['description'] = applatit_arbre($arbre['description']);
		if (isset($arbre['lien']))
			$ret['lien'] = join(' ',$arbre['lien']);
		if (isset($arbre['etat']))
			$ret['etat'] = trim(end($arbre['etat']));

		return $ret;

	}
}


function cron_dw2_verif_maj($t) {
	$tbl_avis=array();
	// lit fichier koak
	$tbl_avis = controle_maj_plugin();
	
	if(count($tbl_avis)) {
	
		// version annonce par koak
		$new_dw = $tbl_avis['version'];
		
		// version installee
		$q=spip_query("SELECT valeur FROM spip_dw2_config WHERE nom='version_installee'");
		$r=spip_fetch_array($q);
		$vers_inst = $r['valeur'];
		
		$serial_avis = serialize($tbl_avis);
		
		if($new_dw > $vers_inst) {
			spip_query("UPDATE spip_dw2_config SET valeur='oui' WHERE nom='avis_maj'");
			spip_query("UPDATE spip_dw2_config SET valeur='".$serial_avis."' WHERE nom='message_maj'");
		}
		else {
			spip_query("UPDATE spip_dw2_config SET valeur='non' WHERE nom='avis_maj'");
		}
		spip_log("verif maj DW2 : ok");
		return 1;
	}
	else {
		spip_log("verif maj DW2 : reload");
		return (0 - $t);
		
	}
}

?>

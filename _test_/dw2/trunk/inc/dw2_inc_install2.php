<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Lire anc. fichier config ou le simuler
+--------------------------------------------+
*/

//
function verifier_fichier_config() {
	$fichier_config = find_in_path('dw2_config.php');
	if(@file_exists($fichier_config)) {
		return $fichier_config;
	}
}

/*
| Pour passage en 2.13 +,
| convertir anciens param de config et initialiser globale dw2_param,
| depuis ancien fichier config (lu ou simule).
*/

function convertir_anc_param() {

	# correspondance anciens params (jusqu_a 2.12)
	$dw2_conv_nom_param = array(
		'dw_version'=>'version_installee',
		'anti_triche'=>'anti_triche',
		'fl'=>'nbr_lignes_tableau',
		'typ_classcat'=>'type_categorie',
		'ext_logo_serv'=>'extens_logo_serveur', 
		'inclus_nouv_doc'=>'mode_enregistre_doc',
		'jour_inclus_doc'=>'jours_affiche_nouv',
		'presel_fi'=>'mode_affiche_images',
		'xml_maj'=>'avis_maj',
		'skl_cata'=>'squelette_cata_public'
	);
	
	if($fichier_config=verifier_fichier_config()) {
		// il est la, on recupere dw2_config.php
		include($fichier_config);
		foreach ($dw2_conv_nom_param as $anc => $nouv) {
			if(isset($$anc)) {
				// init param connu par dw_config
				$GLOBALS['dw2_param'][$nouv]=$$anc;
			}
		}
	}
	else {
		// dw2_config absent => on deduit (!) la version
		$tbla = spip_mysql_showtable('dw2_stats');
		if($tbla['key']['PRIMARY KEY']=="date,id_doc"){
			$dw_version="2.11";
		}
		else {
			$tblb = spip_mysql_showtable('dw2_doc');
			if($tblb['key']['PRIMARY KEY']=="id_document") {
				$dw_version="2.016";
			}
			else {
				$tblc = spip_mysql_showtable('dw2_serv_ftp');
				if($tblc['field']['designe']){
					$dw_version="2.013";
				}
				else {$dw_version="2.012"; }// ca fera l_affaire !
			}
		}
	}
	
	// puis init globale dw2_param pour def autres params version
	dw2_init_param($dw_version);

}

?>

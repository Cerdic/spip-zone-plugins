<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| (formulaire) Ouvre boite alerte :
| - doc restreint .. s'enregistrer [ opt = 1 ]
| - doc restreint .. Droits d'acces superieur [ opt = 2 ]
| - doc non dispo (num. de doc errone !) [ opt = 3 ]
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_DW2_ALERTE ($p) {
	return  calculer_balise_dynamique($p,'DW2_ALERTE', array('dwacces'));
}

function balise_DW2_ALERTE_stat($args, $filtres) {
	if(!$args[0]) return '';
	$args[0]= intval($args[0]);
	return $args;
}

function balise_DW2_ALERTE_dyn($opt) {
	$opt=intval($opt);
	return affichage_dw2_alerte($opt);
}

function affichage_dw2_alerte($opt) {

	# recup nom squlett catalogue si opt=3
	$result = @spip_query("SELECT valeur FROM spip_dw2_config WHERE nom='squelette_cata_public'");
	while ($row = spip_fetch_array($result)) {
		$cata = $row['valeur'];
	}
		
	return array(
		'formulaires/dw2_alerte'.$opt,
		0,#delais
		array('type' => $opt,"cata" => $cata)
		);
}
?>
